<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Web as Web;

Route::prefix('web')->group(function () {

   

    Route::post('/login', [Web\AuthController::class, 'login']);
    Route::post('/register', [Web\AuthController::class, 'register']);
    Route::post('/reset-password', [Web\AuthController::class, 'resetPassword']);
    Route::post('/unique-phone', [Web\AuthController::class, 'checkUniquePhone']);

    Route::middleware('auth:api_web')->group(function () {
        Route::post('/logout', [Web\AuthController::class, 'logout']);
        Route::post('/profile', [Web\AuthController::class, 'profile']);
        Route::post('/update-profile', [Web\AuthController::class, 'updateProfile']);

        Route::prefix('order')->group(function () {
            Route::post('/address-list', [Web\OrderController::class, 'addressList']);
            Route::post('/address-save', [Web\OrderController::class, 'saveAddress']);
            Route::post('/create', [Web\OrderController::class, 'create']);
            Route::post('/list', [Web\OrderController::class, 'orders']);
            Route::post('/detail', [Web\OrderController::class, 'detail']);
            Route::post('/cancel', [Web\OrderController::class, 'cancel']);
        });
    });

    Route::prefix('list-of-value')->name('list-of-value-')->group(function () {
        Route::post('/banner', [Web\ListOfValueController::class, 'banner']);
        Route::post('/achievement-summary', [Web\ListOfValueController::class, 'achievementSummary']);
        Route::post('/our-program', [Web\ListOfValueController::class, 'ourProgram']);
        Route::post('/upcoming-event', [Web\ListOfValueController::class, 'upcomingEvent']);
        Route::post('/privacy-policy', [Web\ListOfValueController::class, 'privacyPolicy']);
        Route::post('/contact-us', [Web\ListOfValueController::class, 'contactUs']);
        Route::post('/social-media', [Web\ListOfValueController::class, 'socialMedia']);
        Route::post('/mission-vision', [Web\ListOfValueController::class, 'missionVision']);
        Route::post('/our-core-value', [Web\ListOfValueController::class, 'ourCoreValue']);
        Route::post('/career', [Web\ListOfValueController::class, 'career']);
        Route::post('/about-us', [Web\ListOfValueController::class, 'aboutUs']);
        Route::post('/our-story', [Web\ListOfValueController::class, 'ourStory']);
        Route::post('/report-document-category', [Web\ListOfValueController::class, 'reportDocumentCategory']);
        Route::post('/report-document', [Web\ListOfValueController::class, 'reportDocument']);
        Route::post('/production', [Web\ListOfValueController::class, 'production']);
        Route::post('/company', [Web\ListOfValueController::class, 'company']);
        Route::post('/bank-account', [Web\ListOfValueController::class, 'bankAccount']);
        Route::post('/shipping-method', [Web\ListOfValueController::class, 'shippingMethod']);
    });

    Route::prefix('catalog')->group(function () {
        Route::post('/categories', [Web\ProductCatalogController::class, 'categories']);
        Route::post('/category-tree', [Web\ProductCatalogController::class, 'categoryTree']);
        Route::post('/products', [Web\ProductCatalogController::class, 'products']);
        Route::post('/product-detail', [Web\ProductCatalogController::class, 'productDetail']);
        Route::post('/stock-summary', [Web\ProductCatalogController::class, 'stockSummary']);
        Route::post('/stock-history', [Web\ProductCatalogController::class, 'stockHistory']);
    });
});
