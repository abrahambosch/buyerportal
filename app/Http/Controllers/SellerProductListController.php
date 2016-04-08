<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use App\ProductList;
use App\ProductListItem;
use App\Seller;
use App\MediaItem;
use App\Services\ProductService;

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
    public function image_import_save($id)
    {
        $seller = Seller::find(Auth::id());     // make sure we have a seller object
        $product_list = ProductList::where('id',"=",$id)->firstOrFail();
        //$product_list->user_id;

        require base_path('app/Libraries/UploadHandler.php');
        $uploadHandler = new \UploadHandler([
            'script_url' => route('seller_product_list.image_import_save', ['id' => $product_list->id]),
            'upload_dir' => $this->getUploadDir($seller->id),
            'upload_url' => $this->getUploadUrl($seller->id)
        ], true, null, function ($obj, $files) use ($seller){
            /*
Array
(
    [0] => stdClass Object
        (
            [name] => rose_page2_blackbluewhite_full (8).jpg
            [size] => 33235
            [type] => image/jpeg
            [url] => http://local.buyerseller.com/products/2/rose_page2_blackbluewhite_full%20%288%29.jpg
            [thumbnailUrl] => http://local.buyerseller.com/products/2/thumbnail/rose_page2_blackbluewhite_full%20%288%29.jpg
            [deleteUrl] => http://local.buyerseller.com/seller_product_list/4/image_import_save?file=rose_page2_blackbluewhite_full%20%288%29.jpg
            [deleteType] => DELETE
        )

)

             */

            $fh = fopen("/tmp/imageupload.log", "a");
            //fwrite($fh, json_encode($files, true) . "\n");

            
            if(is_array($files) && count($files)) {
                foreach ($files as $f) {
                    $item = MediaItem::create([
                        'filename' => $f->name,
                        'mime' => $f->type,
                        'original_filename' => $f->name,
                        'title' => '',
                        'url' => $f->url,
                        'thumbnail' => $f->thumbnailUrl,
                        'order_num' => 0,
                        'product_id' => $this->getProductIdFromFileName($f->name),
                        'user_id' => $seller->users()->first()->id,
                        'seller_id' => $seller->id
                    ]);
                    fwrite($fh, json_encode($item)."\n");
                }
            }

            fclose($fh);

        });
        
        die;
    }

    private function getProductIdFromFileName($name)
    {
        $name = trim($name);
        if (preg_match('/^(\w+)\W+/', $name, $matches)) {
            $style = $matches[1];
            $product = Product::where(['style' => $style])->first();
            if (!$product) return null;
            else return $product->product_id;
        }

       return null;
    }

    private function getUploadDir($seller_id)
    {
        return base_path('public/products/'.$seller_id) . "/";
    }

    private function getUploadUrl($seller_id)
    {
        return url('products/'.$seller_id) . "/";
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ProductService $productService, $id)
    {
        $fields = $productService->getListingFields();
        $seller = Seller::find(Auth::id());     // make sure we have a seller object
        $product_list = ProductList::where('id',"=",$id)->firstOrFail();
        return view('seller_product_list/edit', ['product_list' => $product_list, 'edit' => false, 'seller' => $seller, 'fields' => $fields]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductService $productService, $id)
    {
        $fields = $productService->getListingFields();
        $seller = Seller::find(Auth::id());     // make sure we have a seller object
        $product_list = ProductList::where('id',"=",$id)->firstOrFail();
        return view('seller_product_list/edit', ['product_list' => $product_list, 'edit' => true, 'seller' => $seller, 'fields' => $fields]);
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
