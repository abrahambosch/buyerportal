<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use App\Services\Import\ImportServiceFactory;
use App\Services\ProductService;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ProductService $productService)
    {
        $fields = $productService->getBuyerListingFields();
        $products = Product::where('user_id', Auth::id())->get();
        return view('product/index', ['productService' => $productService, 'user' => Auth::user() , 'products' => $products, 'supplier_id' => '', 'fields' => $fields]);
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function productsbySupplier(Request $request, ProductService $productService, $supplier)
    {
        $fields = $productService->getBuyerListingFields();
        $products = Product::where(['user_id' => Auth::id(), 'supplier_id' => $supplier])->get();
        return view('product/index', ['productService' => $productService, 'user' => Auth::user(), 'products' => $products, 'supplier_id' => $supplier, 'fields' => $fields]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(ProductService $productService )
    {
        $fields = $productService->getBuyerFields();
        return view('product/create', ['user' => Auth::user(), 'fields' => $fields]);
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
            $user = Product::create($request_arr);
            return redirect()->route('product.index')->with('status', 'Product created');
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
        $fields = $productService->getBuyerFields();
        $product = Product::where('product_id',"=",$id)->firstOrFail();
        return view('product/edit', ['product' => $product, 'edit' => false, 'user' => Auth::user(), 'fields' => $fields]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductService $productService, $id)
    {
        $fields = $productService->getBuyerFields();
        $product = Product::where('product_id',"=",$id)->firstOrFail();
        return view('product/edit', ['product' => $product, 'edit' => true, 'user' => Auth::user(), 'fields' => $fields]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductService $productService, Request $request, $id)
    {
        $product = Product::where('product_id',"=",$id)->firstOrFail();

        $this->validate($request, [
            //'email' => 'required|email|unique:users',
            //'title' => 'required|unique:posts|max:255',
            'style' => 'required',
        ]);
        $fields = $productService->getBuyerFields();
        foreach ($fields as $field=>$label) {
            $product->$field = $request->get($field);
        }
        $product->save();
        return redirect()->route('product.index')->with('status', 'Product updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::where('product_id',"=",$id)->firstOrFail();
        $product->delete();
        return redirect()->route('product.index')->with('status', 'Product deleted');
    }

    public function import(Request $request)
    {
        $user = Auth::user();
        $import_type = "berlington";
        return view('product/import', ['user' => $user, 'import_type' => $import_type]);
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
        $supplier_id = $request->get('supplier');
        $filename = $fileObj->getRealPath();

        $import_type = $request->get('import_type');
        $importService = ImportServiceFactory::create($import_type);
        $importService->importSave($filename, Auth::id(), $supplier_id);

        
        return redirect()->route('product.index')->with('status', 'Products Imported');
    }

}
