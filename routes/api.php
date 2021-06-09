<?php
/**
 * File name: api.php
 * Last modified: 2020.04.30 at 08:21:08
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::prefix('driver')->group(function () {
    Route::post('login', 'API\Driver\UserAPIController@login');
    Route::post('loginWithGoogle', 'API\UserAPIController@loginWithGoogle');
    Route::post('loginWithFB', 'API\UserAPIController@loginWithFacebook');
    Route::post('registerWithGoogle', 'API\UserAPIController@registerWithGoogle');
    Route::post('registerWitFB', 'API\UserAPIController@registerWithFacebook');
    Route::post('register', 'API\Driver\UserAPIController@register');
    Route::post('send_reset_link_email', 'API\UserAPIController@sendResetLinkEmail');
    Route::get('user', 'API\Driver\UserAPIController@user');
    Route::get('logout', 'API\Driver\UserAPIController@logout');
    Route::get('settings', 'API\Driver\UserAPIController@settings');
    Route::get('encargos_settings', 'API\Driver\UserAPIController@settings');
});

Route::prefix('manager')->group(function () {
    Route::post('login', 'API\Manager\UserAPIController@login');
    Route::post('loginWithFB', 'API\UserAPIController@loginWithFacebook');
    Route::post('registerWitFB', 'API\UserAPIController@registerWithFacebook');
    Route::post('loginWithGoogle', 'API\UserAPIController@loginWithGoogle');
    Route::post('registerWithGoogle', 'API\UserAPIController@registerWithGoogle');
    Route::post('register', 'API\Manager\UserAPIController@register');
    Route::post('send_reset_link_email', 'API\UserAPIController@sendResetLinkEmail');
    Route::get('user', 'API\Manager\UserAPIController@user');
    Route::get('logout', 'API\Manager\UserAPIController@logout');
    Route::get('settings', 'API\Manager\UserAPIController@settings');
    Route::get('encargos_settings', 'API\Manager\UserAPIController@settings');

});

Route::post('login', 'API\UserAPIController@login');
Route::post('login/driver', 'API\UserAPIController@loginDriver');
Route::post('loginWithFB', 'API\UserAPIController@loginWithFacebook');
Route::post('registerWitFB', 'API\UserAPIController@registerWithFacebook');
Route::post('loginWithGoogle', 'API\UserAPIController@loginWithGoogle');
Route::post('register', 'API\UserAPIController@register');
Route::post('registerWithGoogle', 'API\UserAPIController@registerWithGoogle');
Route::post('send_reset_link_email', 'API\UserAPIController@sendResetLinkEmail');
Route::get('user', 'API\UserAPIController@user');
Route::get('logout', 'API\UserAPIController@logout');
Route::get('settings', 'API\UserAPIController@settings');
Route::get('encargos_settings', 'API\UserAPIController@encargos_settings');

Route::resource('fields', 'API\FieldAPIController');
Route::resource('categories', 'API\CategoryAPIController');
Route::get('categories/market/{id}', 'API\CategoryAPIController@getCategoriesMarket');
Route::resource('markets', 'API\MarketAPIController');
Route::resource('drivers_alt', 'API\DriverAPIController');
Route::resource('departments', 'API\DepartmentAPIController');
Route::resource('subdepartments', 'API\SubdepartmentAPIController');
Route::resource('sections', 'API\SectionAPIController');

Route::resource('faq_categories', 'API\FaqCategoryAPIController');
Route::get('products/categories', 'API\ProductAPIController@categories');
Route::post('products/update/{id}', 'API\ProductAPIController@update');
Route::resource('products', 'API\ProductAPIController');
Route::resource('galleries', 'API\GalleryAPIController');
Route::resource('product_reviews', 'API\ProductReviewAPIController');

Route::resource('faqs', 'API\FaqAPIController');
Route::resource('market_reviews', 'API\MarketReviewAPIController');
Route::post('market/update/{id}', 'API\MarketAPIController@update');

Route::resource('currencies', 'API\CurrencyAPIController');
Route::resource('slides', 'API\SlideAPIController')->except([
    'show',
]);

Route::resource('option_groups', 'API\OptionGroupAPIController');

Route::resource('options', 'API\OptionAPIController');

Route::resource('polygon_zone', 'API\PolygonZoneAPIController');

Route::middleware('auth:api')->group(function () {
    Route::group(['middleware' => ['role:driver']], function () {
        Route::prefix('driver')->group(function () {
            Route::resource('orders', 'API\OrderAPIController');
            //Nuevo
            // Route::resource('encargos', 'API\EncargoAPIController');

            Route::resource('notifications', 'API\NotificationAPIController');
            Route::post('users/{id}', 'API\UserAPIController@update');
            Route::resource('faq_categories', 'API\FaqCategoryAPIController');
            Route::resource('faqs', 'API\FaqAPIController');
        });
    });
    Route::group(['middleware' => ['role:manager']], function () {
        Route::prefix('manager')->group(function () {
            Route::post('users/{id}', 'API\UserAPIController@update');
            Route::get('users/drivers_of_market/{id}', 'API\Manager\UserAPIController@driversOfMarket');
            Route::get('dashboard/{id}', 'API\DashboardAPIController@manager');
            Route::resource('markets', 'API\Manager\MarketAPIController');
        });
    });
    Route::post('users/{id}', 'API\UserAPIController@update');

    Route::resource('order_statuses', 'API\OrderStatusAPIController');
    Route::post('payments/pixelpay', 'API\PaymentAPIController@callBackPixelPay')->name('payments.callBackPixelPay');

    Route::get('payments/byMonth', 'API\PaymentAPIController@byMonth')->name('payments.byMonth');
    Route::resource('payments', 'API\PaymentAPIController');

    Route::get('favorites/exist', 'API\FavoriteAPIController@exist');
    Route::resource('favorites', 'API\FavoriteAPIController');

    Route::resource('orders', 'API\OrderAPIController');
    Route::get('orden/verificar', 'API\OrderAPIController@revisarOrden');
    Route::get('orden/check', 'API\OrderAPIController@checkStatusOrder');
    Route::get('orden/check_user', 'API\OrderAPIController@checkStatusOrderUser');

    Route::resource('product_orders', 'API\ProductOrderAPIControllerFixed');

    Route::resource('notifications', 'API\NotificationAPIController');

    Route::get('carts/count', 'API\CartAPIController@count')->name('carts.count');
    Route::resource('carts', 'API\CartAPIController');
    Route::get('carts/deleteAll/{id}', 'API\CartAPIController@destroyAll');

    Route::resource('delivery_addresses', 'API\DeliveryAddressAPIController');

    Route::resource('drivers', 'API\DriverAPIController');

    Route::resource('earnings', 'API\EarningAPIController');

    Route::resource('driversPayouts', 'API\DriversPayoutAPIController');

    Route::resource('marketsPayouts', 'API\MarketsPayoutAPIController');

    //Nuevo
    Route::resource('encargos', 'API\EncargoAPIController');
    Route::resource('product_encargos', 'API\ProductEncargoAPIController');
    Route::resource('encargo_statuses', 'API\EncargoStatusAPIController');

    Route::resource('coupons', 'API\CouponAPIController')->except([
        'show',
    ]);
});
