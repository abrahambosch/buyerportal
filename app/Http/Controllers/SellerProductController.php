<?php

namespace App\Http\Controllers;

use App\Product;
use App\Seller;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
// todo: finish this controller
class SellerProductController extends Controller
{
    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $user_id="")
    {
        if (Auth::user()->user_type != 'seller') {
            throw new \Exception("can not be on this page unless you are logged in as a seller");
        }
        
        $seller = Seller::find(Auth::id());     // make sure we have a seller object
        $products = Product::where('seller_id', $seller->id)->get();
        return view('seller_product/index', ['seller' => $seller, 'products' => $products, 'buyer_id' => $user_id]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function productsbySeller(Request $request, $seller)
    {
        $products = Product::where(['user_id' => Auth::id(), 'seller_id' => $seller])->get();
        return view('seller_product/index', ['user' => Auth::user(), 'products' => $products, 'seller_id' => $seller]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('seller_product/create', ['user' => Auth::user()]);
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
            return redirect()->route('seller_product.index')->with('status', 'Product created');
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
    public function show($id)
    {
        $product = Product::where('product_id',"=",$id)->firstOrFail();
        return view('product/edit', ['product' => $product, 'edit' => false, 'user' => Auth::user()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::where('product_id',"=",$id)->firstOrFail();
        return view('seller_product/edit', ['product' => $product, 'edit' => true, 'user' => Auth::user()]);
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
        $product = Product::where('product_id',"=",$id)->firstOrFail();

        $this->validate($request, [
            //'email' => 'required|email|unique:users',
            //'title' => 'required|unique:posts|max:255',
            'product_name' => 'required',
        ]);

        foreach (['product_name', 'product_description', 'upc', 'sku', 'cost', 'price'] as $field) {
            $product->$field = $request->get($field);
        }
        $product->save();
        return redirect()->route('seller_product.index')->with('status', 'Product updated');
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
        return redirect()->route('seller_product.index')->with('status', 'Product deleted');
    }

    public function import(Request $request)
    {
//        $sellers = DB::table('users')
//            ->join('buyer_seller_map', 'users.id', '=', 'buyer_seller_map.user_id')
//            ->select('users.*')
//            ->where('buyer_seller_map.buyer_id', '=', Auth::id())
//            ->get();
        $user = Auth::user();
//        foreach ($user->sellers as $seller) {
//            echo "Seller = " . print_r($seller, true) . "<br>";
//        }
        
        return view('seller_product/import', ['sellers' => $user->sellers]);
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
        $seller = $request->get('seller');
        $filename = $fileObj->getRealPath();
        $products = $this->csv_to_array($filename);
        foreach ($products as $product) {
            $product['user_id'] = Auth::id();
            $product['seller_id'] = $seller;
            print_r($product); echo "<br>";
            Product::create($product);
        }

        return redirect()->route('seller_product.index')->with('status', 'Products Imported');
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
