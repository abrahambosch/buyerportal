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
        Route::get('/buyers', 'BuyersController@index');
        Route::get('/buyers/create', 'BuyersController@create');

        Route::get('/product/{product}/destroy', 'ProductController@destroy')->name("product.delete");
        Route::get('/product/seller/{seller}', 'ProductController@productsbySeller')->name("product.byseller");
        Route::get('/product/import', 'ProductController@import')->name("product.import");
        Route::post('/product/importSave', 'ProductController@importSave')->name("product.importSave");
        Route::resource('product', 'ProductController');

        Route::get('/seller/{seller}/destroy', 'SellerController@destroy')->name("seller.delete");
        Route::resource('seller', 'SellerController');

        Route::resource('product_list', 'ProductListController');
        Route::get('/product_list/item/{id}', 'ProductListController@destroyItem')->name("product_list.destroyItem");

        Route::get('/seller_product/{product}/destroy', 'ProductController@destroy')->name("seller_product.delete");
        Route::get('/seller_product/buyer/{buyer}', 'ProductController@productsbySeller')->name("seller_product.bybuyer");
        Route::get('/seller_product/import', 'ProductController@import')->name("product.import");
        Route::post('/seller_product/importSave', 'ProductController@importSave')->name("product.importSave");
        Route::resource('seller_product', 'SellerProductController');

        Route::resource('seller_product_list', 'SellerProductListController');
        Route::get('/seller_product_list/{id}/image_import', 'SellerProductListController@image_import')->name("seller_product_list.image_import");
        Route::any('/seller_product_list/{id}/image_import_save', 'SellerProductListController@image_import_save')->name("seller_product_list.image_import_save");

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
