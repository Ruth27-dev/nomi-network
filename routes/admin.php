<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin as Admin;
use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\LOV\SocialMediaController;
use App\Http\Controllers\Admin\Pages\AboutUsController;
use App\Http\Controllers\Admin\Pages\AchievementSummaryController;
use App\Http\Controllers\Admin\Pages\CareerController;
use App\Http\Controllers\Admin\Pages\ContactUsController;
use App\Http\Controllers\Admin\Pages\MissionVisionController;
use App\Http\Controllers\Admin\Pages\OurCoreValueController;
use App\Http\Controllers\Admin\Pages\ReportDocumentCategoryController;
use App\Http\Controllers\Admin\Pages\ReportDocumentController;
use App\Http\Controllers\Admin\Pages\OurProgramController;
use App\Http\Controllers\Admin\Pages\OurStoryController;
use App\Http\Controllers\Admin\Pages\PrivacyPolicyController;
use App\Http\Controllers\Admin\Pages\ProductionController;
use App\Http\Controllers\Admin\Pages\UpcomingEventController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductDiscountController;
use App\Http\Controllers\Admin\ProductVariationController;
use App\Http\Controllers\Admin\ShippingMethodController;
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
            Route::get('product-variation-data', 'fetchProductVariationData')->name('product-variation-data');
        });
        // Validation
        Route::prefix('validation')->name('validation-')->group(function () {
            Route::post('category', [AdminRequest\CategoryRequest::class, 'validate'])->name('category');
            Route::post('product', [AdminRequest\ProductRequest::class, 'validate'])->name('product');
            Route::post('product-variation', [AdminRequest\ProductVariationRequest::class, 'validate'])->name('product-variation');
            Route::post('product-discount', [AdminRequest\DiscountRequest::class, 'validate'])->name('product-discount');
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

            Route::controller(ProductDiscountController::class)->prefix('discount')->name('product-discount-')->group(function () {
                Route::get('list', 'index')->name('list');
                Route::get('data', 'data')->name('data');
                Route::post('save', 'save')->name('save');
                Route::get('detail', 'detail')->name('detail');
                Route::post('status', 'updateStatus')->name('status');
                Route::delete('delete', 'delete')->name('delete');
                Route::put('restore', 'restore')->name('restore');
                Route::delete('destroy', 'destroy')->name('destroy');
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

            // shipping method
            Route::controller(ShippingMethodController::class)->prefix('shipping-method')->name('shipping-method-')->group(function () {
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

        Route::prefix('page')->name('page-')->group(function () {
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

            // achievement summary
            Route::controller(AchievementSummaryController::class)->prefix('achievement-summary')->name('achievement-summary-')->group(function () {
                Route::get('list', 'index')->name('list');
                Route::get('data', 'data')->name('data');
                Route::post('save', 'save')->name('save');
                Route::post('status', 'onUpdateStatus')->name('status');
                Route::delete('delete', 'onDelete')->name('delete');
                Route::put('restore', 'onRestore')->name('restore');
                Route::delete('destroy', 'onDestroy')->name('destroy');
                Route::get('max-ordering', 'getMaxOrdering')->name('max-ordering');
            });

            // privacy policy
            Route::controller(PrivacyPolicyController::class)->prefix('privacy-policy')->name('privacy-policy-')->group(function () {
                Route::get('list', 'index')->name('list');
                Route::post('save', 'save')->name('save');
            });
            // contact us
            Route::controller(ContactUsController::class)->prefix('contact-us')->name('contact-us-')->group(function () {
                Route::get('list', 'index')->name('list');
                Route::post('save', 'save')->name('save');
            });

            // about us
            Route::controller(AboutUsController::class)->prefix('about-us')->name('about-us-')->group(function () {
                Route::get('list', 'index')->name('list');
                Route::post('save', 'save')->name('save');
            });

            // our program
            Route::controller(OurProgramController::class)->prefix('our-program')->name('our-program-')->group(function () {
                Route::get('list', 'index')->name('list');
                Route::post('save', 'save')->name('save');
            });

            // upcoming event
            Route::controller(UpcomingEventController::class)->prefix('upcoming-event')->name('upcoming-event-')->group(function () {
                Route::get('list', 'index')->name('list');
                Route::post('save', 'save')->name('save');
            });

            // Production
            Route::controller(ProductionController::class)->prefix('production')->name('production-')->group(function () {
                Route::get('list', 'index')->name('list');
                Route::post('save', 'save')->name('save');
            });

            // Our Core Values
            Route::controller(OurCoreValueController::class)->prefix('our-core-value')->name('our-core-value-')->group(function () {
                Route::get('list', 'index')->name('list');
                Route::post('save', 'save')->name('save');
            });

            // Career
            Route::controller(CareerController::class)->prefix('career')->name('career-')->group(function () {
                Route::get('list', 'index')->name('list');
                Route::get('data', 'data')->name('data');
                Route::post('save', 'save')->name('save');
                Route::post('status', 'onUpdateStatus')->name('status');
                Route::delete('delete', 'onDelete')->name('delete');
                Route::put('restore', 'onRestore')->name('restore');
                Route::delete('destroy', 'onDestroy')->name('destroy');
                Route::get('max-ordering', 'getMaxOrdering')->name('max-ordering');
            });

            // Reports & Documents Category
            Route::controller(ReportDocumentCategoryController::class)->prefix('report-document-category')->name('report-document-category-')->group(function () {
                Route::get('list', 'index')->name('list');
                Route::get('data', 'data')->name('data');
                Route::post('save', 'save')->name('save');
                Route::post('status', 'onUpdateStatus')->name('status');
                Route::delete('delete', 'onDelete')->name('delete');
                Route::put('restore', 'onRestore')->name('restore');
                Route::delete('destroy', 'onDestroy')->name('destroy');
                Route::get('max-ordering', 'getMaxOrdering')->name('max-ordering');
            });

            // Reports & Documents
            Route::controller(ReportDocumentController::class)->prefix('report-document')->name('report-document-')->group(function () {
                Route::get('list', 'index')->name('list');
                Route::get('data', 'data')->name('data');
                Route::post('save', 'save')->name('save');
                Route::post('status', 'onUpdateStatus')->name('status');
                Route::delete('delete', 'onDelete')->name('delete');
                Route::put('restore', 'onRestore')->name('restore');
                Route::delete('destroy', 'onDestroy')->name('destroy');
                Route::get('max-ordering', 'getMaxOrdering')->name('max-ordering');
            });

            // Mission & Vision
            Route::controller(MissionVisionController::class)->prefix('mission-vision')->name('mission-vision-')->group(function () {
                Route::get('list', 'index')->name('list');
                Route::get('data', 'data')->name('data');
                Route::post('save', 'save')->name('save');
                Route::post('status', 'onUpdateStatus')->name('status');
                Route::delete('delete', 'onDelete')->name('delete');
                Route::put('restore', 'onRestore')->name('restore');
                Route::delete('destroy', 'onDestroy')->name('destroy');
                Route::get('max-ordering', 'getMaxOrdering')->name('max-ordering');
            });

            // Social Media
            Route::controller(SocialMediaController::class)->prefix('social-media')->name('social-media-')->group(function () {
                Route::get('list', 'index')->name('list');
                Route::get('data', 'data')->name('data');
                Route::post('save', 'save')->name('save');
                Route::post('status', 'onUpdateStatus')->name('status');
                Route::delete('delete', 'onDelete')->name('delete');
                Route::put('restore', 'onRestore')->name('restore');
                Route::delete('destroy', 'onDestroy')->name('destroy');
                Route::get('max-ordering', 'getMaxOrdering')->name('max-ordering');
            });
            // our story
            Route::controller(OurStoryController::class)->prefix('our-story')->name('our-story-')->group(function () {
                Route::get('list', 'index')->name('list');
                Route::post('save', 'save')->name('save');
            });
        });
    });
});
