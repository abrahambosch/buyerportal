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
        Route::get('/product/seller/{seller}', 'ProductController@productsbySeller')->name("product.byseller");
        Route::get('/product/import', 'ProductController@import')->name("product.import");
        Route::post('/product/importSave', 'ProductController@importSave')->name("product.importSave");
        Route::resource('product', 'ProductController');

        // Purchase Orders
        Route::get('/po/{po}/destroy', 'PurchaseOrderController@destroy')->name("po.delete");
        Route::resource('po', 'PurchaseOrderController');

        // Supplier
        Route::get('/seller/{seller}/destroy', 'SellerController@destroy')->name("seller.delete");
        Route::resource('seller', 'SellerController');

        // Product List
        Route::resource('product_list', 'ProductListController');
        Route::get('/product_list/item/{id}/destroy', 'ProductListController@destroyItem')->name("product_list.destroyItem");

        // Supplier Product
        Route::get('/seller_product/{product}/destroy', 'SellerProductController@destroy')->name("seller_product.delete");
        Route::get('/seller_product/buyer/{buyer}', 'SellerProductController@productsbyBuyer')->name("seller_product.bybuyer");
        Route::get('/seller_product/import', 'SellerProductController@import')->name("seller_product.import");
        Route::post('/seller_product/importSave', 'SellerProductController@importSave')->name("seller_product.importSave");
        Route::get('/seller_product/image_import', 'SellerProductController@image_import')->name("seller_product.image_import");
        Route::any('/seller_product/image_import_save', 'SellerProductController@image_import_save')->name("seller_product.image_import_save");
        Route::resource('seller_product', 'SellerProductController');

        // Supplier Product List
        Route::resource('seller_product_list', 'SellerProductListController');
        Route::get('/seller_product_list/{id}/image_import', 'SellerProductListController@image_import')->name("seller_product_list.image_import");
        Route::any('/seller_product_list/{id}/image_import_save', 'SellerProductListController@image_import_save')->name("seller_product_list.image_import_save");

        // Media
        Route::get('media', 'MediaItemController@index');
        Route::get('media/get/{filename}', [
            'as' => 'getmedia', 'uses' => 'MediaItemController@get']);
        Route::post('media/add',[
            'as' => 'addmedia', 'uses' => 'MediaItemController@add']);

    });

//    Route::get('/seller', 'SellersController@index')->name("index");
//    Route::get('/seller/create', 'SellersController@create')->name("create");
//    Route::post('/seller/store', 'SellersController@store')->name("store");
//    Route::get('/seller/{id}/edit', 'SellersController@edit')->name("edit");
//    Route::get('/seller/{id}', 'SellersController@show')->name("show");
//    Route::put('/seller/{id}', 'SellersController@update')->name("update");
//    Route::delete('/seller/{id}', 'SellersController@destroy')->name("destroy");
});
