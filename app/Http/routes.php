<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
use Illuminate\Support\Facades\Auth;



//Route::get('/', function () {
//    return view('welcome');
//});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/



Route::group(['middleware' => ['web']], function () {
    Route::get('/', 'WelcomeController@index')->name("welcome");
    Route::get('auth/github', 'Auth\AuthController@redirectToProvider');
    Route::get('auth/github/callback', 'Auth\AuthController@handleProviderCallback');
    //
});

Route::group(['middleware' => 'web'], function () {
    Route::auth();

    Route::group(['middleware' => 'auth'], function () {
        Route::get('/home', 'HomeController@index');

        // Buyers
        Route::get('/buyers', 'BuyersController@index');
        Route::get('/buyers/create', 'BuyersController@create');

        // Products
        Route::get('/product/{product}/destroy', 'ProductController@destroy')->name("product.delete");
        Route::get('/product/supplier/{supplier}', 'ProductController@productsbySupplier')->name("product.bysupplier");
        Route::get('/product/import', 'ProductController@import')->name("product.import");
        Route::post('/product/importSave', 'ProductController@importSave')->name("product.importSave");
        Route::resource('product', 'ProductController');
        

        // Purchase Orders
        Route::get('/purchase_order/{purchase_order}/destroy', 'PurchaseOrderController@destroy')->name("purchase_order.delete");
        Route::get('/purchase_order/supplier/{supplier}', 'PurchaseOrderController@index')->name("purchase_order.bysupplier");
        Route::get('/purchase_order/import', 'PurchaseOrderController@import')->name("purchase_order.import");
        Route::post('/purchase_order/importSave', 'PurchaseOrderController@importSave')->name("purchase_order.importSave");
        Route::get('/purchase_order/item/{id}/destroy', 'PurchaseOrderController@destroyItem')->name("purchase_order.destroyItem");
        Route::get('/purchase_order/chooseProducts/{id}', 'PurchaseOrderController@chooseProducts')->name("purchase_order.chooseProducts");
        Route::put('/purchase_order/chooseProductsStore/{id}', 'PurchaseOrderController@chooseProductsStore')->name("purchase_order.chooseProductsStore");
        Route::get('/purchase_order/{purchase_order_id}/worksheet', 'PurchaseOrderController@worksheet')->name("purchase_order.worksheet");
        Route::get('/purchase_order/{purchase_order_id}/getNewRoom/', 'PurchaseOrderController@getNewRoom')->name("purchase_order.getNewRoom");
        Route::get('/purchase_order/{purchase_order_id}/getNewWorksheet', 'PurchaseOrderController@getNewWorksheet')->name("purchase_order.getNewWorksheet");
        Route::resource('purchase_order', 'PurchaseOrderController');

        // Supplier
        Route::get('/supplier/{supplier}/destroy', 'SupplierController@destroy')->name("supplier.delete");
        Route::resource('supplier', 'SupplierController');

        // Product List
        Route::resource('product_list', 'ProductListController');
        Route::get('/product_list/item/{id}/destroy', 'ProductListController@destroyItem')->name("product_list.destroyItem");

        // Supplier Product
        Route::get('/supplier_product/{product}/destroy', 'SupplierProductController@destroy')->name("supplier_product.delete");
        Route::get('/supplier_product/buyer/{buyer}', 'SupplierProductController@productsbyBuyer')->name("supplier_product.bybuyer");
        Route::get('/supplier_product/import', 'SupplierProductController@import')->name("supplier_product.import");
        Route::post('/supplier_product/importSave', 'SupplierProductController@importSave')->name("supplier_product.importSave");
        Route::get('/supplier_product/image_import', 'SupplierProductController@image_import')->name("supplier_product.image_import");
        Route::any('/supplier_product/image_import_save', 'SupplierProductController@image_import_save')->name("supplier_product.image_import_save");
        Route::resource('supplier_product', 'SupplierProductController');

        // Supplier Product List
        Route::resource('supplier_product_list', 'SupplierProductListController');
        Route::get('/supplier_product_list/{id}/image_import', 'SupplierProductListController@image_import')->name("supplier_product_list.image_import");
        Route::any('/supplier_product_list/{id}/image_import_save', 'SupplierProductListController@image_import_save')->name("supplier_product_list.image_import_save");

        // Media
        Route::get('media', 'MediaItemController@index');
        Route::get('media/get/{filename}', [
            'as' => 'getmedia', 'uses' => 'MediaItemController@get']);
        Route::post('media/add',[
            'as' => 'addmedia', 'uses' => 'MediaItemController@add']);

    });

//    Route::get('/supplier', 'SuppliersController@index')->name("index");
//    Route::get('/supplier/create', 'SuppliersController@create')->name("create");
//    Route::post('/supplier/store', 'SuppliersController@store')->name("store");
//    Route::get('/supplier/{id}/edit', 'SuppliersController@edit')->name("edit");
//    Route::get('/supplier/{id}', 'SuppliersController@show')->name("show");
//    Route::put('/supplier/{id}', 'SuppliersController@update')->name("update");
//    Route::delete('/supplier/{id}', 'SuppliersController@destroy')->name("destroy");
});
