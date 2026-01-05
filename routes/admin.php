<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin as Admin;
use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariationController;
use App\Http\Requests\Admin as AdminRequest;
use Illuminate\Support\Facades\Route;

Route::get("/change-locale/{locale}", [Admin\ChangeLocaleController::class, 'changeLocale'])->name('change-locale');
Route::middleware(['locale'])->group(function () {

    Route::prefix('auth')->group(function () {

        Route::get('/', function () {
            return redirect()->route('login');
        });
        Route::get('/login', [Admin\UserController::class, 'login'])->name('login');
        Route::post('/login/post', [Admin\AuthController::class, 'login'])->name('login-post');
        Route::get('/sign-out', [Admin\AuthController::class, 'signOut'])->name('sign-out');
    });

    Route::middleware(['admin.guard', 'auth:admin'])->group(function () {


        // Fetch data
        Route::controller(Admin\FetchDataController::class)->prefix('fetch')->name('fetch-')->group(function () {
            Route::get('category-data', 'fetchCategoryData')->name('category-data');
            Route::get('product-data', 'fetchProductData')->name('product-data');
        });
        // Validation
        Route::prefix('validation')->name('validation-')->group(function () {
            Route::post('category', [AdminRequest\CategoryRequest::class, 'validate'])->name('category');
            Route::post('product', [AdminRequest\ProductRequest::class, 'validate'])->name('product');
            Route::post('product-variation', [AdminRequest\ProductVariationRequest::class, 'validate'])->name('product-variation');
        });

        Route::prefix('user')->group(function () {
            Route::controller(Admin\UserController::class)->prefix('user')->name('user-')->group(function () {
                Route::get('list',  'index')->name('list');
                Route::get('data',  'data')->name('data');
                Route::post('save',  'save')->name('save');
                Route::post('update',  'onUpdate')->name('update');
                Route::post('status',  'onUpdateStatus')->name('status');
                Route::post('save-password',  'onSavePassword')->name('save-password');
                Route::delete('delete',  'onDelete')->name('delete');
                Route::delete('destroy',  'onDestroy')->name('destroy');
                Route::put('restore',  'onRestore')->name('restore');
                // userPermission
                Route::get('permission',  'userPermission')->name('permission');
                Route::post('permission-save',  'userPermissionSave')->name('permission-save');
            });
            // User Role
            Route::controller(RoleController::class)->prefix('user-role')->name('user-role-')->group(function () {
                Route::get('list',  'index')->name('list');
                Route::get('data',  'data')->name('data');
                Route::post('save',  'onSave')->name('save');
                Route::post('status',  'onUpdateStatus')->name('status');
                Route::get('fetch-module-permission',  'fetchModulePermission')->name('fetch-module-permission');
                Route::post('assign-permission',  'onAssignPermission')->name('assign-permission');
            });
        });

        // product
        Route::prefix('product')->group(function () {
            Route::controller(CategoryController::class)->prefix('category')->name('category-')->group(function () {
                Route::get('list', 'index')->name('list');
                Route::get('data', 'data')->name('data');
                Route::post('save', 'save')->name('save');
                Route::get('detail', 'detail')->name('detail');
                Route::post('status', 'updateStatus')->name('status');
                Route::delete('delete', 'delete')->name('delete');
                Route::put('restore', 'restore')->name('restore');
                Route::delete('destroy', 'destroy')->name('destroy');
                Route::get('sequence', 'sequence')->name('sequence');
            });

            Route::controller(ProductController::class)->prefix('product')->name('product-')->group(function () {
                Route::get('list', 'index')->name('list');
                Route::get('data', 'data')->name('data');
                Route::post('save', 'save')->name('save');
                Route::get('detail', 'detail')->name('detail');
                Route::post('status', 'updateStatus')->name('status');
                Route::delete('delete', 'delete')->name('delete');
                Route::put('restore', 'restore')->name('restore');
                Route::delete('destroy', 'destroy')->name('destroy');
                Route::get('sequence', 'sequence')->name('sequence');
            });

            Route::controller(ProductVariationController::class)->prefix('variation')->name('product-variation-')->group(function () {
                Route::get('list', 'index')->name('list');
                Route::get('data', 'data')->name('data');
                Route::post('save', 'save')->name('save');
                Route::get('detail', 'detail')->name('detail');
                Route::post('status', 'updateStatus')->name('status');
                Route::delete('delete', 'delete')->name('delete');
            });
        });



        Route::prefix('setting')->name('setting-')->group(function () {
            // company
            Route::controller(Admin\LOV\CompanyController::class)->prefix('company')->name('company-')->group(function () {
                Route::get('list', 'index')->name('list');
                Route::post('save', 'save')->name('save');
            });

            // bank account
            Route::controller(BankAccountController::class)->prefix('bank-account')->name('bank-account-')->group(function () {
                Route::get('list', 'index')->name('list');
                Route::get('data', 'data')->name('data');
                Route::post('save', 'save')->name('save');
                Route::post('status', 'onUpdateStatus')->name('status');
                Route::delete('delete', 'onDelete')->name('delete');
                Route::put('restore', 'onRestore')->name('restore');
                Route::delete('destroy', 'onDestroy')->name('destroy');
                Route::get('max-ordering', 'getMaxOrdering')->name('max-ordering');
            });

            // banner
            Route::controller(BannerController::class)->prefix('banner')->name('banner-')->group(function () {
                Route::get('list', 'index')->name('list');
                Route::get('data', 'data')->name('data');
                Route::post('save', 'save')->name('save');
                Route::post('status', 'onUpdateStatus')->name('status');
                Route::delete('delete', 'onDelete')->name('delete');
                Route::put('restore', 'onRestore')->name('restore');
                Route::delete('destroy', 'onDestroy')->name('destroy');
                Route::get('max-ordering', 'getMaxOrdering')->name('max-ordering');
            });
        });
    });
});
