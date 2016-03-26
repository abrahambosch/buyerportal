<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use App\ProductList;
use App\ProductListItem;
use App\Seller;

class SellerProductListController extends Controller
{
    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $user="")
    {
        if (Auth::user()->user_type != 'seller') {
            throw new \Exception("can not be on this page unless you are logged in as a seller");
        }
        
        $seller = Seller::find(Auth::id());     // make sure we have a seller object
        
        $params = ['seller_id' => $seller->id];
        if (!empty($user)) {
            $params['user_id'] = $user; 
        }
        $lists = ProductList::where($params)->get();
        
        return view('seller_product_list/index', ['seller' => $seller, 'lists' => $lists, 'buyer_id' => $user]);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('seller_product_list/create', ['user' => Auth::user()]);
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
            $products = Product::where(['user_id' => Auth::id(), 'seller_id' => $product_list->seller_id])->get();
            foreach($products as $product) {
                ProductListItem::create([
                    'product_list_id' => $product_list->id,
                    'user_id' => Auth::id(),
                    'seller_id' => $product_list->seller_id,
                    'product_id' => $product->product_id
                ]);
            }
            return redirect()->route('seller_product_list.index')->with('status', 'Product List created');
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
    public function image_import($id)
    {
        $seller = Seller::find(Auth::id());     // make sure we have a seller object
        $product_list = ProductList::where('id',"=",$id)->firstOrFail();
        return view('seller_product_list/image_import', ['product_list' => $product_list, 'edit' => false, 'seller' => $seller]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function image_import_submit($id)
    {
        $seller = Seller::find(Auth::id());     // make sure we have a seller object
        $product_list = ProductList::where('id',"=",$id)->firstOrFail();


        $product_list->user_id;

        if($_POST) {
            $allowed = array('jpg', 'jpeg');

            if(isset($_FILES['uploadctl']) && $_FILES['uploadctl']['error'] == 0){

                $extension = pathinfo($_FILES['uploadctl']['name'], PATHINFO_EXTENSION);

                if(!in_array(strtolower($extension), $allowed)){
                    echo '{"status":"error"}';
                    exit;
                }

                if(move_uploaded_file($_FILES['uploadctl']['tmp_name'], base_path() . "/public/products/".$_FILES['upl']['name'] . $extension)){
                    echo '{"status":"success"}';
                    exit;
                }
                echo '{"status":"error"}';
            }
            exit();
        }
        die;
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $seller = Seller::find(Auth::id());     // make sure we have a seller object
        $product_list = ProductList::where('id',"=",$id)->firstOrFail();
        return view('seller_product_list/edit', ['product_list' => $product_list, 'edit' => false, 'seller' => $seller]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $seller = Seller::find(Auth::id());     // make sure we have a seller object
        $product_list = ProductList::where('id',"=",$id)->firstOrFail();
        return view('seller_product_list/edit', ['product_list' => $product_list, 'edit' => true, 'seller' => $seller]);
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
        return redirect()->route('seller_product_list.edit', ['id' => $product_list_id])->with('status', 'Product deleted');
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
        
        return view('product/import', ['sellers' => $user->sellers]);
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
