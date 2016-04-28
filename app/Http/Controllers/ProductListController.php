<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use App\ProductList;
use App\ProductListItem;
use App\Supplier;
use App\Services\ProductService;

class ProductListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $supplier="")
    {
        if (Auth::user()->user_type == 'buyer') {
            if (!empty($supplier)) {
                $lists = ProductList::where(['user_id' => Auth::id(), 'supplier_id' => $supplier])->get();
            } else {
                $lists = ProductList::where(['user_id' => Auth::id()])->get();
            }
        }
        else {
            $supplier = Auth::id();
            $lists = ProductList::where(['supplier_id' => $supplier])->get();
        }


        return view('product_list/index', ['user' => Auth::user(), 'lists' => $lists, 'supplier_id' => $supplier]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function supplierIndex(Request $request, $user="")
    {
        if (Auth::user()->user_type != 'supplier') {
            throw new \Exception("can not be on this page unless you are logged in as a supplier");
        }
        
        $supplier = Supplier::find(Auth::id());     // make sure we have a supplier object
        
        $params = ['supplier_id' => $supplier->id];
        if (!empty($user)) {
            $params['user_id'] = $user; 
        }
        $lists = ProductList::where($params)->get();
        
        return view('product_list/supplier-index', ['supplier' => $supplier, 'lists' => $lists, 'buyer_id' => $user]);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('product_list/create', ['user' => Auth::user()]);
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
        $request_arr['user_id'] = Auth::id();
        try {
            $product_list = ProductList::create($request_arr);
            $products = Product::where(['user_id' => Auth::id(), 'supplier_id' => $product_list->supplier_id])->get();
            foreach($products as $product) {
                ProductListItem::create([
                    'product_list_id' => $product_list->id,
                    'user_id' => Auth::id(),
                    'supplier_id' => $product_list->supplier_id,
                    'product_id' => $product->product_id
                ]);
            }
            return redirect()->route('product_list.index')->with('status', 'Product List created');
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
        $product_list = ProductList::where(['id' => $id])->firstOrFail();
        return view('product_list/edit', ['product_list' => $product_list, 'edit' => false, 'user' => Auth::user(), 'fields' => $fields]);
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
        $product_list = ProductList::where(['id' => $id])->firstOrFail();
        return view('product_list/edit', ['product_list' => $product_list, 'edit' => true, 'user' => Auth::user(), 'fields' => $fields]);
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
        $product_list = ProductList::findOrFail($id);

        $this->validate($request, [
            //'email' => 'required|email|unique:users',
            //'title' => 'required|unique:posts|max:255',
            'list_name' => 'required',
        ]);

        foreach (['list_name'] as $field) {
            $product_list->$field = $request->get($field);
        }
        $product_list->save();
        return redirect()->route('product_list.index')->with('status', 'Product updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product_list = ProductList::findOrFail($id);
        $product_list->delete();
        return redirect()->route('product_list.index')->with('status', 'Product List deleted');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyItem($id)
    {
        $item = ProductListItem::where('id',"=",$id)->firstOrFail();
        $product_list_id = $item->product_list->id;
        $item->delete();
        return redirect()->route('product_list.edit', ['id' => $product_list_id])->with('status', 'Product deleted');
    }

    public function import(Request $request)
    {
//        $suppliers = DB::table('users')
//            ->join('buyer_supplier', 'users.id', '=', 'buyer_supplier.user_id')
//            ->select('users.*')
//            ->where('buyer_supplier.buyer_id', '=', Auth::id())
//            ->get();
        $user = Auth::user();
//        foreach ($user->suppliers as $supplier) {
//            echo "Supplier = " . print_r($supplier, true) . "<br>";
//        }
        
        return view('product/import', ['suppliers' => $user->suppliers]);
    }
    
    public function importSave(Request $request)
    {
        if (!$request->hasFile('importFile')) {
            dd("no file submitted");
        }
        if (!$request->hasFile('importFile') || !$request->file('importFile')->isValid()) {
            dd("importFile is invalid");
            //return redirect()->route('product.import')->withErrors('status', 'Problem uploading file');
        }
        $fileObj = $request->file('importFile');
        //var_export($fileObj);
        $supplier = $request->get('supplier');
        $filename = $fileObj->getRealPath();
        $products = $this->csv_to_array($filename);
        foreach ($products as $product) {
            $product['user_id'] = Auth::id();
            $product['supplier_id'] = $supplier;
            print_r($product); echo "<br>";
            Product::create($product);
        }

        return redirect()->route('product.index')->with('status', 'Products Imported');
    }

    protected function csv_to_array($filename='', $delimiter=',')
    {
        if(!file_exists($filename) || !is_readable($filename))
            return FALSE;

        $header = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter, '"')) !== FALSE)
            {
                if(!$header)
                    $header = $row;
                else if (count($row) == count($header)){
                    print_r($header) . "<br>";
                    print_r($row) . "<br>";
                    $data[] = array_combine($header, $row);
                }

            }
            fclose($handle);
        }
        return $data;
    }

}
