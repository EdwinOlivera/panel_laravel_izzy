<?php
/**
 * File name: web.php
 * Last modified: 2020.06.07 at 07:02:57
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('terms-conditions', 'UserController@terms_conditions');
Route::get('login/{service}', 'Auth\LoginController@redirectToProvider');
Route::get('login/{service}/callback', 'Auth\LoginController@handleProviderCallback');
Auth::routes();
Route::get('testEmail', "AppSettingController@testEmail");
Route::get('payments/failed', 'PayPalController@index')->name('payments.failed');
Route::get('payments/razorpay/checkout', 'RazorPayController@checkout');
Route::post('payments/razorpay/pay-success/{userId}/{deliveryAddressId?}/{couponCode?}', 'RazorPayController@paySuccess');
Route::get('payments/razorpay', 'RazorPayController@index');

//IMPLEMTACIONES DE PIXELPAY
Route::get('payments/pixelpay', 'PixelPayController@getExpressCheckout')->name('pixelpay.express-checkout');
Route::get('payments/pixelpay/conversionDatos', 'PixelPayController@conversionDeDatos');
Route::get('payments/pixelpay/cancelPixelPay', 'PixelPayController@cancelPixelPay');
Route::get('payments/pixelpay/operacionCompletada', 'PixelPayController@completePixelPay');

//IMPLEMTACIONES DE FAC
Route::get('payments/fac', 'FACController@processFAC');
Route::get('payments/fac/conversionDatos', 'FACController@conversionDeDatos');
Route::get('payments/fac/cancelFac', 'FACController@cancelFAC');
Route::get('payments/fac/completeFac', 'FACController@processFAC');

Route::get('payments/paypal/express-checkout', 'PayPalController@getExpressCheckout')->name('paypal.express-checkout');
Route::get('payments/paypal/express-checkout-success', 'PayPalController@getExpressCheckoutSuccess');
Route::get('payments/paypal', 'PayPalController@index')->name('paypal.index');

Route::get('firebase/sw-js', 'AppSettingController@initFirebase');

Route::get('search/optionGroups', 'OptionController@searchOptionGroups');
Route::get('storage/app/public/{id}/{conversion}/{filename?}', 'UploadController@storage');

Route::middleware('auth')->group(function () {
    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
    Route::get('/', 'DashboardController@index')->name('dashboard');

    Route::post('uploads/store', 'UploadController@store')->name('medias.create');
    Route::get('users/profile', 'UserController@profile')->name('users.profile');
    Route::post('users/remove-media', 'UserController@removeMedia');
    Route::resource('users', 'UserController');
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');

    Route::group(['middleware' => ['permission:medias']], function () {
        Route::get('uploads/all/{collection?}', 'UploadController@all');
        Route::get('uploads/collectionsNames', 'UploadController@collectionsNames');
        Route::post('uploads/clear', 'UploadController@clear')->name('medias.delete');
        Route::get('medias', 'UploadController@index')->name('medias');
        Route::get('uploads/clear-all', 'UploadController@clearAll');
    });

    Route::group(['middleware' => ['permission:permissions.index']], function () {
        Route::get('permissions/role-has-permission', 'PermissionController@roleHasPermission');
        Route::get('permissions/refresh-permissions', 'PermissionController@refreshPermissions');
    });
    Route::group(['middleware' => ['permission:permissions.index']], function () {
        Route::post('permissions/give-permission-to-role', 'PermissionController@givePermissionToRole');
        Route::post('permissions/revoke-permission-to-role', 'PermissionController@revokePermissionToRole');
    });

    Route::group(['middleware' => ['permission:app-settings']], function () {
        Route::prefix('settings')->group(function () {
            Route::resource('permissions', 'PermissionController');
            Route::resource('roles', 'RoleController');
            Route::resource('customFields', 'CustomFieldController');
            Route::resource('currencies', 'CurrencyController')->except([
                'show',
            ]);
            Route::get('users/login-as-user/{id}', 'UserController@loginAsUser')->name('users.login-as-user');
            Route::patch('update', 'AppSettingController@update');
            Route::patch('translate', 'AppSettingController@translate');
            Route::get('sync-translation', 'AppSettingController@syncTranslation');
            Route::get('clear-cache', 'AppSettingController@clearCache');
            Route::get('check-update', 'AppSettingController@checkForUpdates');
            // disable special character and number in route params
            Route::get('/{type?}/{tab?}', 'AppSettingController@index')
                ->where('type', '[A-Za-z]*')->where('tab', '[A-Za-z]*')->name('app-settings');
        });
    });

    Route::post('fields/remove-media', 'FieldController@removeMedia');
    Route::resource('fields', 'FieldController')->except([
        'show',
    ]);
    Route::get('fields/edit_order', 'FieldController@modificarOrden')->name('edit_order_fields');
    Route::get('fields/sort', 'FieldController@sortOrden');
    Route::get('fields/updateFields', 'FieldController@updateFields');

    Route::post('markets/remove-media', 'MarketController@removeMedia');
    Route::get('requestedMarkets', 'MarketController@requestedMarkets')->name('requestedMarkets.index'); //adeed
    Route::get('edit_market_complete/{id}', 'MarketController@editMarketComplete')->name('markets.editMarketComplete');
    Route::get('edit_market_complete/createFromMarket/{id}', 'ProductController@createFromMarket');
    Route::get('editDepartmentsByMarket/createFromMarket/{id}', 'ProductController@createFromSupermarket');
    Route::get('editSectionsByConvenienceStore/createFromConvenienceStores/{id}', 'ProductController@createFromConvenienceStore');

    // Ruta alterna para editar un producto especifico
    Route::get('editFromMarket/{id}', 'ProductController@editFromMarket')->name('products.editFromMarket');
    Route::get('edit_market_complete/editFromMarket/{id}', 'ProductController@editFromMarket');
    Route::get('editDepartmentsByMarket/editFromMarket/{id}', 'ProductController@editFromSupemarket');
    Route::get('editSectionsByConvenienceStore/editFromConvenienceStores/{id}', 'ProductController@editFromConvenienceStore');

    Route::post('storeFromSupermarket', 'ProductController@storeFromSupermarket')->name('products.storeFromSupermarket');
    Route::post('storeFromConvenienceStores', 'ProductController@storeFromConvenienceStore')->name('products.storeFromConvenienceStores');
    
    Route::patch('updateFromSupermarket/{id}', 'ProductController@updateFromSupermarket')->name('products.updateFromSupermarket');
    Route::patch('updateFromConvenienceStore/{id}', 'ProductController@updateFromConvenienceStore')->name('products.updateFromConvenienceStore');
    
    Route::post('storeFromMarket', 'ProductController@storeFromMarket')->name('products.storeFromMarket');
    Route::patch('updateFromMarket/{id}', 'ProductController@updateFromMarket')->name('products.updateFromMarket');
    Route::post('menu/update-order', 'MarketController@updateOrderProductList');
    Route::get('markets/addPromos', 'MarketController@addPromos');

    Route::get('markets/getPromos', 'MarketController@getPromos');
    Route::get('markets/removePromo', 'MarketController@removePromo');
    Route::post('market/sortOrderPromos', 'MarketController@sortOrderPromos');

    Route::resource('markets', 'MarketController')->except([
        'show',
    ]);
    Route::resource('supermarkets', 'SupermarketController')->except([
        'show',
    ]);
    Route::get('editDepartmentsByMarket/{id}', 'SupermarketController@editDepartmentsByMarket')->name('supermarkets.editDepartmentsByMarket');
    Route::get('editSupermarketFull/{id}', 'SupermarketController@editMarketComplete')->name('supermarkets.editMarketComplete');


    Route::resource('convenience_stores', 'ConvenienceStoreController')->except([
        'show',
    ]);
    Route::get('editSectionsByConvenienceStore/{id}', 'ConvenienceStoreController@editSectionsByConvenienceStore')->name('convenience_stores.editSectionsByConvenienceStore');
    Route::get('editCategory/{id}', 'ConvenienceStoreController@editCategory')->name('convenience_stores.editCategory');

    Route::resource('sections', 'SectionController')->except([
        'show',
    ]);
    Route::get('sections/createFromMarket', 'SectionController@createDirectFromConvenienceStores');
    Route::get('sections/updateFromMarket', 'SectionController@updateDirectFromConvenienceStores');
    Route::get('sections/removeFromMarket', 'SectionController@removeFromeConvenienceStores');
    Route::get('sections/addFromMarket', 'SectionController@addSectionFromConvenienceStores');
    Route::get('sections/addProductFromConvenienceStores', 'SectionController@addProductFromConvenienceStores');

    Route::post('sections/updateSort', 'SectionController@sortSection');

    Route::get('sections/getSectionsByConvenienceStores', 'SectionController@getSectionsByMarket');


    Route::resource('departments', 'DepartmentController')->except([
        'show',
    ]);
    Route::get('departments/createFromSupermarket/{id}', 'DepartmentController@createFromSupermarket')->name('departments.createsfromsupermarket');
    Route::post('departments/storeFromSupermarket', 'DepartmentController@storeFromSupermarket')->name('departments.storeFromSupermarket');
    Route::patch('departments/updateFromSupermarket/{id}', 'DepartmentController@updateFromSupermarket')->name('departments.updateFromSupermarket');
    Route::get('departments/changeVisibiliFromSupermarket', 'DepartmentController@changeVisibiliFromSupermarket');
    Route::get('editDepartmentsByMarket/editFromSupermarket/{id}', 'DepartmentController@editFromSupermarket');
    Route::get('departments/removeFromSupermarket', 'DepartmentController@removeFromSupermarket');
    Route::get('departments/getDepartmentsByMarket', 'DepartmentController@getDepartmentsByMarket');
    Route::get('departments/sort_departments', 'DepartmentController@sortDepartments');
    Route::get('departments/addDepartmentsFormMarket', 'DepartmentController@addDepartmentsFormMarket');

    Route::resource('subdepartments', 'SubdepartmentController')->except([
        'show',
    ]);
    Route::get('subdepartments/getSubdepartmentByDepartment', 'SubdepartmentController@getSubdepartmentByDepartment');
    Route::get('subdepartments/updateFromDepartment', 'SubdepartmentController@updateFromDepartment');
    Route::get('subdepartments/removeFromDepartment', 'SubdepartmentController@removeFromDepartment');
    Route::get('subdepartments/sortSubdepartment', 'SubdepartmentController@sortSubdepartments');
    Route::get('subdepartments/addProductFormMarket', 'SubdepartmentController@addProductFormMarket');
    Route::get('subdepartments/addSubdeparmentsFormDepartment', 'SubdepartmentController@addSubdeparmentsFormDepartment');
    Route::get('editDepartmentsByMarket/createSubFromSupermarket/{id}', 'SubdepartmentController@createFromSupermarket');
    Route::post('subdepartments/storeFromDepartment', 'SubdepartmentController@storeFromDepartment')->name('subdepartments.storeFromSupermarket');
    Route::get('editDepartmentsByMarket/editFromSubSupermarket/{id}', 'SubdepartmentController@editFromSupermarket');
    Route::patch('subdepartments/updateFromSubSupermarket/{id}', 'SubdepartmentController@updateFromSupermarket')->name('subdepartments.updateFromSupermarket');



    Route::get('categories/addProductFormMarket', 'CategoryController@addProductFormMarket');
    Route::get('categories/selects', 'CategoryController@setCategoriesToMarket');
    Route::get('categories/storeFromMarket', 'CategoryController@createFromMarket');
    Route::get('categories/categoriesProduct', 'CategoryController@getCategoriesByProduct');
    Route::get('categories/updateFromMarket', 'CategoryController@updateFromMarket');
    Route::get('categories/sort', 'CategoryController@sortOrden');
    Route::get('categories/removeCategoryFromMarket', 'CategoryController@removeProductsFromCategory');
    Route::post('categories/remove-media', 'CategoryController@removeMedia');

    Route::resource('categories', 'CategoryController')->except([
        'show',
    ]);

    Route::resource('faqCategories', 'FaqCategoryController')->except([
        'show',
    ]);

    Route::resource('orderStatuses', 'OrderStatusController')->except([
        'create', 'store', 'destroy',
    ]);;

    Route::post('products/remove-media', 'ProductController@removeMedia');
    Route::get('products/getproductbycategory', 'ProductController@getProductByCategory');
    Route::get('products/getproductbysubdepartment', 'ProductController@getProductBySubdepartment');
    Route::get('products/getproductbysection', 'ProductController@getProductBySection');
    Route::get('products/removeProductsFromSection', 'ProductController@removeProductsFromSection');
    Route::get('editSectionsByConvenienceStore/createFromConvenienceStore/{id}', 'ProductController@createFromConvenienceStore');
// 
    Route::get('product/updateModal', 'ProductController@updateModal');
    Route::get('product/destroyAlt', 'ProductController@destroyAlt');
    Route::get('product/removeProductsFromCategory', 'ProductController@removeProductsFromCategory');
    Route::get('products/removeProductsFromSubdepartment', 'ProductController@removeProductsFromSubdepartment');
    Route::get('product/cambiarDisponibilidad', 'ProductController@cambiarDisponibilidad');
    Route::get('product/changeVisibiliFromSubdepartment', 'ProductController@changeVisibiliFromSubdepartment');
    Route::resource('products', 'ProductController')->except([
        'show',
    ]);

    Route::post('galleries/remove-media', 'GalleryController@removeMedia');
    Route::resource('galleries', 'GalleryController')->except([
        'show',
    ]);

    Route::resource('productReviews', 'ProductReviewController')->except([
        'show',
    ]);

    Route::resource('payments', 'PaymentController')->except([
        'create', 'store', 'edit', 'destroy',
    ]);;

    Route::resource('faqs', 'FaqController')->except([
        'show',
    ]);
    Route::resource('marketReviews', 'MarketReviewController')->except([
        'show',
    ]);

    Route::resource('favorites', 'FavoriteController')->except([
        'show',
    ]);

    Route::resource('orders', 'OrderController');

    Route::resource('encargos', 'EncargoController');

    Route::resource('notifications', 'NotificationController')->except([
        'create', 'store', 'update', 'edit',
    ]);;

    Route::resource('carts', 'CartController')->except([
        'show', 'store', 'create',
    ]);
    Route::resource('deliveryAddresses', 'DeliveryAddressController')->except([
        'show',
    ]);

    Route::resource('drivers', 'DriverController')->except([
        'show',
    ]);

    Route::resource('earnings', 'EarningController')->except([
        'show', 'edit', 'update',
    ]);

    Route::resource('driversPayouts', 'DriversPayoutController')->except([
        'show', 'edit', 'update',
    ]);

    Route::resource('marketsPayouts', 'MarketsPayoutController')->except([
        'show', 'edit', 'update',
    ]);

    Route::post('option_group/sort_orden', 'OptionGroupController@updateOrderGroupOption');
    Route::get('optionGroups/createFromMarket', 'OptionGroupController@storeFromProduct');
    Route::get('optionGroups/updateFromMarket', 'OptionGroupController@updateFromMarket');
    Route::get('optionGroups/destroyAlt', 'OptionGroupController@destroyAlt');
    Route::get('optionGroups/addOptionGroupsFromMarket', 'OptionGroupController@addOptionGroupsFromMarket');
    Route::get('optionGroups/removeOptionGroupFromProduct', 'OptionGroupController@removeOptionGroupFromProduct');

    Route::resource('optionGroups', 'OptionGroupController')->except([
        'show',
    ]);
    Route::get('optiongroup/groupbyproduct', 'OptionGroupController@getOptionGroupByProduct');
    Route::post('options/remove-media', 'OptionController@removeMedia');
    Route::get('options/optionbygroup', 'OptionController@getOptionsByGroup');
    Route::post('options/sort_orden', 'OptionController@updateOrderOptions');
    Route::get('options/createFromMarket', 'OptionController@storeFromGroupOption');
    Route::get('options/updateFroMarkert', 'OptionController@updateFromGroupOption');
    Route::get('options/destroyAlt', 'OptionController@destroyAlt');
    Route::get('options/removeOptionFromOptionGroup', 'OptionController@removeOptionFromOptionGroup');
    Route::get('options/addOptionsFromMarket', 'OptionGroupController@addOptionsFromMarket');

    Route::resource('options', 'OptionController')->except([
        'show',
    ]);
    Route::resource('typesMarket', 'TypeMarketController')->except([
        'show',
    ]);
    Route::resource('coupons', 'CouponController')->except([
        'show',
    ]);
    Route::post('slides/remove-media', 'SlideController@removeMedia');
    Route::resource('slides', 'SlideController')->except([
        'show',
    ]);

    Route::get('search/markets', 'MarketController@searchMarkets');
    Route::get('search/sectionesFromMarket', 'SectionController@searchSections');
    Route::get('search/productsFromMarket', 'ProductController@searchProductsFromMarket');
    Route::get('search/departmentFromMarket', 'DepartmentController@searchDepartment');
    Route::get('search/subdepartments', 'SubdepartmentController@searchSubdepartment');
    Route::get('search/products', 'OptionController@searchProduct');
    Route::get('search/promosFormMarket', 'MarketController@searchPromos');
    Route::get('search/categories', 'CategoryController@searchCategory');
    Route::get('search/options', 'OptionController@searchOptions');
    Route::get('search/optiongroupsFromMarket', 'OptionController@searchOptionGroupsFromMarket');
});
