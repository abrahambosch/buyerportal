<?php

namespace App\Http\Controllers;

use App\Product;
use App\Seller;
use App\User;
use App\MediaItem;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

use App\Services\ProductService;
use App\Services\ImportService;

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
    public function create(ProductService $productService)
    {
        $fields = $productService->getFields();
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
        $seller = Seller::find(Auth::id());
//        foreach ($user->sellers as $seller) {
//            echo "Seller = " . print_r($seller, true) . "<br>";
//        }
        $import_type = "berlington";
        return view('seller_product/import', ['seller' => $seller, 'import_type' => $import_type]);
    }
    
    public function importSave(Request $request, ImportService $importService)
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
        $seller = Seller::find(Auth::id());
        $seller_id = $seller->id;
        $buyer_id = $request->get('buyer');
        $filename = $fileObj->getRealPath();
        $import_type = $request->get('import_type');
        if ($import_type == 'berlington') {
            $importService->berlingtonImportSave($filename, $buyer_id, $seller_id);
        }
        else {
            $importService->csvImportSave($filename, $buyer_id, $seller_id);
        }

        //return redirect()->route('seller_product.index')->with('status', 'Products Imported');
    }



    ///////////////////////////////////////////////////////////////////////////////
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function image_import()
    {
        $seller = Seller::find(Auth::id());     // make sure we have a seller object
        $buyer_id = $seller->users()->first()->id;
        return view('seller_product/image_import', ['edit' => false, 'seller' => $seller, 'buyer_id' => $buyer_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function image_import_save(ProductService $productService)
    {

        $seller = Seller::find(Auth::id());     // make sure we have a seller object

        require base_path('app/Libraries/UploadHandler.php');
        $uploadHandler = new \UploadHandler([
            'script_url' => route('seller_product.image_import_save'),
            'isImageOkFunction' => function($filename) use($productService) {
                $product_id = $productService->getProductIdFromFileName($filename);
                if (!empty($product_id)) {
                    return true;
                }
                return false;
            },
            'upload_dir' => $this->getUploadDir($seller->id),
            'upload_url' => $this->getUploadUrl($seller->id)
        ], true, null, function ($obj, $files) use ($seller, $productService){
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
                    $product_id = $productService->getProductIdFromFileName($f->name);
                    if (!empty($product_id)) {  // only create if the product is found.
                        $item = MediaItem::create([
                            'filename' => $f->name,
                            'mime' => $f->type,
                            'original_filename' => $f->name,
                            'title' => '',
                            'url' => $f->url,
                            'thumbnail' => $f->thumbnailUrl,
                            'order_num' => 0,
                            'product_id' => $product_id,
                            'user_id' => $seller->users()->first()->id,
                            'seller_id' => $seller->id
                        ]);
                        fwrite($fh, json_encode($item)."\n");   // todo: remove this
                    }
                    else {
                        if (file_exists($f->name)) {
                            unlink($f->name);
                        }
                    }
                }
            }

            fclose($fh);

        });

        die;
    }



    private function getUploadDir($seller_id)
    {
        return base_path('public/products/'.$seller_id) . "/";
    }

    private function getUploadUrl($seller_id)
    {
        return url('products/'.$seller_id) . "/";
    }

}
