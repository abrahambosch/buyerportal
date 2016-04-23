<?php

namespace App\Http\Controllers;

use App\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Seller;
use App\Product;
use App\PurchaseOrderItem;
use App\Services\Import\ImportServiceFactory;
use App\Services\ProductService;
use App\Services\PurchaseOrderService;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $seller="")
    {
        // list purchase orders
        if (Auth::user()->user_type == 'buyer') {
            if (!empty($seller)) {
                $results = PurchaseOrder::where(['buyer_id' => Auth::id(), 'seller_id' => $seller])->get();
            } else {
                $results = PurchaseOrder::where(['buyer_id' => Auth::id()])->get();
            }
            return view('purchase_order/index', ['user' => Auth::user(), 'results' => $results, 'seller_id' => $seller]);
        }
        else {
            $seller = Seller::find(Auth::id());
            $buyer = $seller->users()->first();
            //dd($buyer);
            $results = PurchaseOrder::where(['seller_id' => $seller->id])->get();
            return view('purchase_order/index', ['user' => Auth::user(), 'seller' => $seller, 'results' => $results, 'buyer_id' => $buyer->id]);
        }
    }

    /**
     * Choose
     *
     * @return \Illuminate\Http\Response
     */
    public function chooseProducts(Request $request, ProductService $productService, $id)
    {
        $purchase_order = PurchaseOrder::where(['id' => $id])->firstOrFail();
        $fields = $productService->getBuyerListingFields();
        $products = Product::where(['user_id' => $purchase_order->buyer_id, 'seller_id' => $purchase_order->seller_id])->get();
        return view('purchase_order/choose', ['purchase_order' => $purchase_order, 'productService' => $productService, 'user' => Auth::user() , 'products' => $products, 'seller_id' => '', 'fields' => $fields]);
    }

    /**
     * Choose
     *
     * @return \Illuminate\Http\Response
     */
    public function chooseProductsStore(Request $request, ProductService $productService, $id)
    {
        $newProducts = $request->get("newproducts");
        if (is_array($newProducts) && !empty($newProducts)) {
            foreach ($newProducts as $product_id=>$junk) {
                $productService->addOrUpdateItem($id, $product_id);
            }
            return redirect()->route('purchase_order.edit', ['id' => $id])->with('status', 'Products added');
        }
        return redirect()->route('purchase_order.edit', ['id' => $id])->with('status', "You didn't choose any products. ");
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('purchase_order/create', ['user' => Auth::user()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request_arr = $request->except("_token");
        $request_arr['buyer_id'] = Auth::id();
        try {
            $purchase_order = PurchaseOrder::create($request_arr);
            $products = Product::where(['user_id' => Auth::id(), 'seller_id' => $purchase_order->seller_id])->get();
            foreach($products as $product) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchase_order->id,
                    'buyer_id' => Auth::id(),
                    'seller_id' => $purchase_order->seller_id,
                    'product_id' => $product->product_id
                ]);
            }
            return redirect()->route('purchase_order.index')->with('status', 'Product List created');
        } catch (Exception $e) {
            return back()->withInput();
            //echo "failed to create buyer:" . $e->getMessage() . "<br>";
            //return Response::json(['error' => 'User already exists.'], Illuminate\Http\Response::HTTP_CONFLICT);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(PurchaseOrderService $purchaseOrderService, $id)
    {
        $fields = $purchaseOrderService->getPoFields();
        $purchase_order = PurchaseOrder::where(['id' => $id])->firstOrFail();
        return view('purchase_order/edit', ['purchase_order' => $purchase_order, 'edit' => true, 'user' => Auth::user(), 'fields' => $fields]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(PurchaseOrderService $purchaseOrderService, $id)
    {
        $params = [];
        $params['edit'] = true;
        $params['user'] = Auth::user();
        $params['fields'] = $purchaseOrderService->getPoFields();
        $params['purchase_order'] = PurchaseOrder::where(['id' => $id])->firstOrFail();
        if (Auth::user()->user_type == 'seller') {
            $params['seller'] = Seller::find(Auth::id());
        }
        else {
            $params['seller'] = null;
        }

        return view('purchase_order/edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PurchaseOrderService $purchaseOrderService, Request $request, $id)
    {
        $purchase_order = PurchaseOrder::findOrFail($id);
        $fields = $purchaseOrderService->getPoFields();
        $validateArr = [
            //'email' => 'required|email|unique:users',
            //'title' => 'required|unique:posts|max:255',
            'po_num' => 'required',
            'order_date' => 'required',
        ];

        if (Auth::user()->user_type == 'seller') {
            $validateArr['buyer_id'] = 'required';
            $fields['seller_notes'] = "Seller Notes";
        }
        else {
            $validateArr['seller_id'] = 'required';
            $fields['buyer_notes'] = "Buyer Notes";
        }

        $this->validate($request, $validateArr);

        foreach ($fields as $field=>$label) {
            $purchase_order->$field = $request->get($field);
        }
        $purchase_order->save();

        // todo: add updating of line items here.

        return redirect()->route('purchase_order.index')->with('status', 'Offer/PO updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $purchase_order = PurchaseOrder::findOrFail($id);
        $purchase_order->delete();
        return redirect()->route('purchase_order.index')->with('status', 'Offer/PO deleted');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyItem($id)
    {
        $item = PurchaseOrderItem::where('id',"=",$id)->firstOrFail();
        $purchase_order_id = $item->purchase_order->id;
        $item->delete();
        return redirect()->route('purchase_order.edit', ['id' => $purchase_order_id])->with('status', 'Item deleted');
    }


    public function import(Request $request)
    {
        $import_type = "berlington";
        if (Auth::user()->user_type == 'buyer') {   // if a buyer, allow them to select a supplier.
            return view('purchase_order/import', ['user' => Auth::user(), 'import_type' => $import_type, 'seller_id' => $request->get('seller')]);
        }
        else {  // if a supplier .. allow them to select a buyer.
            $seller = Seller::find(Auth::id());
            return view('purchase_order/import', ['user' => Auth::user(), 'seller' => $seller, 'import_type' => $import_type, 'buyer_id' => $request->get('buyer')]);
        }
    }

    public function importSave(Request $request)
    {
        if (Auth::user()->user_type == 'buyer') {
            $this->validate($request, [
                'importFile' => 'required',
                'seller' => 'required',
            ]);
        }
        else {
            $this->validate($request, [
                'importFile' => 'required',
                'buyer' => 'required',
            ]);
        }

        if (!$request->hasFile('importFile')) {
            return back()->withInput()->withErrors(['inputFile' => 'inputFile is required']);
        }
        if (!$request->file('importFile')->isValid()) {
            return back()->withInput()->withErrors(['inputFile' => 'inputFile is invalid']);
        }

        if (Auth::user()->user_type == 'buyer') {
            $seller_id = $request->get('seller');
            $buyer_id = Auth::id();
        }
        else {  // supplier
            $seller_id = Auth::id();
            $buyer_id = $request->get('buyer');
        }
        
        $fileObj = $request->file('importFile');

        $filename = $fileObj->getRealPath();
        $import_type = $request->get('import_type');
        $importService = ImportServiceFactory::create($import_type);
        $importService->importSave($filename, $buyer_id, $seller_id, true);

        return redirect()->route('purchase_order.index')->with('status', 'Offer/Purchase Order Imported');
    }


    public function getNewRoom(PurchaseOrderService $purchaseOrderService, $purchase_order_id)
    {
        try {
            $ethercalc_id = $purchaseOrderService->getNewRoom();
            $purchaseOrder = PurchaseOrder::findOrFail($purchase_order_id);
            $purchaseOrder->ethercalc_id = $ethercalc_id;
            $purchaseOrder->save(); 
            return response()->json(['status' => 1, 'ethercalc_id' => $ethercalc_id]);
        }
        catch (\Exception $e) {
            return response()->json(['status' => 0, 'error' => $e->getMessage()]);
        }
    }



    public function worksheet(Request $request)
    {
        $iframeurl = "http://ec2-52-37-114-239.us-west-2.compute.amazonaws.com:8000/0rujm8k4sp4m";
        return view('purchase_order/worksheet', ['iframeurl' => $iframeurl, 'user' => Auth::user()]);
    }
    
}
