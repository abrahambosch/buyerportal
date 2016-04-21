<?php

namespace App\Http\Controllers;

use App\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Seller;
use App\Services\Import\ImportServiceFactory;

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
        }
        else {
            $seller = Auth::id();
            $results = PurchaseOrder::where(['seller_id' => $seller])->get();
        }
        
        return view('purchase_order/index', ['user' => Auth::user(), 'results' => $results, 'seller_id' => $seller]);
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
    public function show(ProductService $productService, $id)
    {
        $fields = $productService->getBuyerListingFields();
        $purchase_order = PurchaseOrder::where(['id' => $id])->firstOrFail();
        return view('purchase_order/edit', ['purchase_order' => $purchase_order, 'edit' => false, 'user' => Auth::user(), 'fields' => $fields]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductService $productService, $id)
    {
        $fields = $productService->getBuyerListingFields();
        $purchase_order = PurchaseOrder::where(['id' => $id])->firstOrFail();
        return view('purchase_order/edit', ['purchase_order' => $purchase_order, 'edit' => true, 'user' => Auth::user(), 'fields' => $fields]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $purchase_order = PurchaseOrder::findOrFail($id);

        $this->validate($request, [
            //'email' => 'required|email|unique:users',
            //'title' => 'required|unique:posts|max:255',
            'list_name' => 'required',
        ]);

        foreach (['list_name'] as $field) {
            $purchase_order->$field = $request->get($field);
        }
        $purchase_order->save();
        return redirect()->route('purchase_order.index')->with('status', 'Product updated');
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
        return redirect()->route('purchase_order.index')->with('status', 'Product List deleted');
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
        return redirect()->route('purchase_order.edit', ['id' => $purchase_order_id])->with('status', 'Product deleted');
    }


    public function import(Request $request)
    {
        $user = Auth::user();
        if (Auth::user()->user_type == 'buyer') {
            $seller = Seller::find(Auth::id());
        }
        else {
            $seller = Auth::user();
            $results = PurchaseOrder::where(['seller_id' => $seller])->get();
        }
        $import_type = "berlington";
        return view('purchase_order/import', ['user' => Auth::user(), 'seller_id' => $seller->id, 'import_type' => $import_type]);
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
        if (!$request->hasFile('importFile') || !$request->file('importFile')->isValid()) {
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
        $importService->importSave($filename, $buyer_id, $seller_id);

        return redirect()->route('purchase_order.index')->with('status', 'Offer/Purchase Order Imported');
    }

    
}
