<?php

namespace App\Http\Controllers;

use App\Product;
use App\Seller;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

use App\Services\ProductService;

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
    public function index(ProductService $productService, Request $request, $user_id="")
    {
        if (Auth::user()->user_type != 'seller') {
            throw new \Exception("can not be on this page unless you are logged in as a seller");
        }
        $fields = $productService->getListingFields();
        $seller = Seller::find(Auth::id());     // make sure we have a seller object
        $products = Product::where('seller_id', $seller->id)->get();
        return view('seller_product/index', ['seller' => $seller, 'products' => $products, 'buyer_id' => $user_id, 'fields' => $fields]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function productsbyBuyer(ProductService $productService, Request $request, $buyer)
    {
        $fields = $productService->getListingFields();
        $user = User::find($buyer);
        $seller = Seller::find(Auth::id());
        $products = Product::where(['user_id' => $buyer, 'seller_id' => Auth::id()])->get();
        return view('seller_product/index', ['seller' => $seller, 'products' => $products, 'buyer_id' => $user->id, 'fields' => $fields]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $fields = $this->getFields();
        return view('seller_product/create', ['user' => Auth::user(), 'fields' => $fields]);
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
    public function show(ProductService $productService, $id)
    {
        $fields = $productService->getFields();
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
        $fields = $productService->getFields();
        $product = Product::where('product_id',"=",$id)->firstOrFail();
        return view('seller_product/edit', ['product' => $product, 'edit' => true, 'user' => Auth::user(), 'fields' => $fields]);
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

        $fields = $productService->getFields();
        foreach ($fields as $field=>$label) {
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


    private function getFields()
    {
        $fields = [
            "factory" => "Factory",
            'style' => 'Item#',
            'product_description' => 'Description',
            'dimentions_json' => 'Dimentions',
            "master_pack" => "Master Pack",
            "cube" => "Cube (ft2)",
            "packing" => "Packing",
            "quantity" => "Qty",
            "unit_cost" => "POE",    // unit cost
            "fob" => "FOB",
            "total" => "Total $",
            "total_cft" => "Total CFT",
            "total_cmb" => "Total CMB",
            "unit_retail" => "Unit Retail",
            "notes" => "Production Notes",
            "fob_cost" => "FOB (Cost)",
            "frt" => "FRT",
            "duty" => "Duty",
            "elc" => "ELC",
            "poe_percent" => "POE%",
            "fob_percent" => "FOB%",
            "hts" => "HTS",
            "duty_percent" => "Duty %",
            "port" => "Port",
            "weight" => "Weight (kg)",
            'upc'=>'Cust UPC',
            'sku' => 'Cust SKU',
            'material' => 'Material',
            'factory_item' => 'Factory Item #',
            'samples_requested' => 'Samples Requested',
            'carton_size_l' => 'Carton Size L(")',
            'carton_size_w' => 'Carton Size W(")',
            'carton_size_h' => 'Carton Size H(")',
            'factory_lead_time' => 'Factory Lead Time',
        ];
        return $fields;
    }
}
