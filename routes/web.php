<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\MemveraRoutesController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\LeadTagController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    // Route::group(['middleware' => ['user']], function () {
        Route::prefix('leads')->controller(LeadsController::class)->group(function () {
            Route::get("", "leads_index_page")->name('dashboards.leads.index');
            Route::get('view/{id?}', 'lead_view_page')->name('dashboards.leads.view');
            Route::get('search', 'search')->name('dashboards.leads.search');
            Route::get("create", "create_lead_page")->name('dashboards.leads.create');
            Route::post("create", "store")->name('dashboards.leads.store');
            Route::get("get/{pipeline_id?}", "get")->name('dashboards.leads.get');
            Route::get("getActivities/{leadId?}", "getActivities")->name('dashboards.leads.getActivities');
            Route::put('edit/{id?}', 'update')->name('dashboards.leads.update');
            Route::put('mass-destroy', 'massDestroy')->name('dashboards.leads.mass_delete');
            Route::put('mass-update', 'massUpdate')->name('dashboards.leads.mass_update');
            Route::delete('{id}', 'destroy')->name('dashboards.leads.delete');
        });
        
        Route::prefix('leads')->controller(LeadTagController::class)->group(function () {
            Route::post('tags/{id}', 'store')->name('leads.tags.store');
            Route::delete('tags/delete/{lead_id}/{tag_id?}', 'delete')->name('leads.tags.delete');
        });

        Route::prefix('contacts/persons')->controller(PersonController::class)->group(function () {
            Route::get('create', 'create')->name('contacts.persons.create');
            Route::post('create', 'store')->name('contacts.persons.store');
            Route::get('edit/{id}', 'edit')->name('contacts.persons.edit');
            Route::post('edit/{id}', 'update')->name('contacts.persons.update');
            Route::get('search', 'search')->name('contacts.persons.search');
            Route::get('{route?}', 'index')->name('contacts.persons.index');
            Route::delete('{id}', 'destroy')->name('contacts.persons.delete');
            Route::put('mass-destroy', 'massDestroy')->name('contacts.persons.mass_delete');
        });

        Route::prefix('contacts/organizations')->controller(OrganizationController::class)->group(function () {
            Route::get('create', 'create')->name('contacts.organizations.create');
            Route::post('create', 'store')->name('contacts.organizations.store');
            Route::get('edit/{id}', 'edit')->name('contacts.organizations.edit');
            Route::post('edit/{id}', 'update')->name('contacts.organizations.update');
            Route::get('{route?}', 'index')->name('contacts.organizations.index');
            Route::get('search', 'search')->name('contacts.organizations.search');
            Route::delete('{id}', 'destroy')->name('contacts.organizations.delete');
            Route::put('mass-destroy', 'massDestroy')->name('contacts.organizations.mass_delete');
        });

        Route::prefix('attributes')->controller(AttributeController::class)->group(function () {
            Route::get("lookup/{lookup?}", "lookup")->name('settings.attributes.lookup');
            Route::get("lookup_entity/{lookup?}", "lookup_entity")->name('settings.attributes.lookup_entity');
        });

        Route::prefix('products')->controller(ProductController::class)->group(function () {            
            Route::get('search', 'search')->name('products.search');
            Route::get('', 'index')->name('products.index');
            Route::get('create', 'create')->name('products.create');
            Route::post('create', 'store')->name('products.store');
            Route::get('edit/{id}', 'edit')->name('products.edit');
            Route::post('edit/{id}', 'update')->name('products.update');
            Route::get('search', 'search')->name('products.search');
            Route::delete('{id}', 'destroy')->name('products.delete');
            Route::put('mass-destroy', 'massDestroy')->name('products.mass_delete');
        });
        Route::prefix('quotes')->controller(QuoteController::class)->group(function () {            
            Route::get('/', 'index_page')->name('quotes.index_page');
            Route::get('create/{id?}', 'create')->name('quotes.create');
            Route::post('create', 'store')->name('quotes.store');
            Route::get('edit/{id?}', 'edit')->name('quotes.edit');
            Route::put('edit/{id}', 'update')->name('quotes.update');
            Route::get('print/{id?}', 'print')->name('quotes.print');
            Route::delete('{id}', 'destroy')->name('quotes.delete');
            Route::put('mass-destroy', 'massDestroy')->name('quotes.mass_delete');
        });
        
        Route::prefix('activities')->controller(ActivityController::class)->group(function () {            
            Route::post('store', 'store')->name('activities.store');
            Route::get('edit/{id}', 'edit')->name('activities.edit');
            Route::get('file-download/{id?}', 'download')->name('activities.file_download');
            Route::post('file-upload', 'upload')->name('activities.file_upload');
            Route::get('search-participants', 'searchParticipants')->name('activities.search_participants');
        });

        Route::prefix('mail')->controller(EmailController::class)->group(function () {
            Route::post('create', 'store')->name('mail.store');
            Route::post('edit/{id?}', 'update')->name('mail.update');
            Route::get('attachment-download/{id?}', 'download')->name('mail.attachment_download');
            Route::get('{route?}', 'index')->name('mail.index');
            Route::get('{route?}/{id?}', 'view')->name('mail.view');
            Route::delete('{id?}', 'destroy')->name('mail.delete');
            Route::put('mass-update', 'massUpdate')->name('mail.mass_update');
            Route::put('mass-destroy', 'massDestroy')->name('mail.mass_delete');
        });

        Route::prefix('email_templates')->controller(EmailTemplateController::class)->group(function () {
            Route::get('create', 'create')->name('email_templates.create');
            Route::post('create', 'store')->name('email_templates.store');
            Route::get('edit/{id?}', 'edit')->name('email_templates.edit');
            Route::post('edit/{id}', 'update')->name('email_templates.update');
            Route::get('{route?}', 'index')->name('email_templates.index');
            Route::delete('{id}', 'destroy')->name('email_templates.delete');
        });

        Route::prefix('settings/permissions')->controller(PermissionsController::class)->group(function () {
            Route::get('create', 'create')->name('permissions.create');
            Route::post('create', 'store')->name('permissions.store');
            Route::get('edit/{id?}', 'edit')->name('permissions.edit');
            Route::post('edit/{id}', 'update')->name('permissions.update');
            Route::get('{route?}', 'index')->name('permissions.index');
            Route::get('{route?}/{id?}', 'show')->name('permissions.view');
            Route::delete('{id}', 'destroy')->name('permissions.delete');
            Route::put('mass-update', 'massUpdate')->name('permissions.mass_update');
            Route::put('mass-destroy', 'massDestroy')->name('permissions.mass_delete');
        });

        Route::prefix('settings/roles')->controller(RolesController::class)->group(function () {
            Route::get('create', 'create')->name('roles.create');
            Route::post('create', 'store')->name('roles.store');
            Route::get('edit/{id?}', 'edit')->name('roles.edit');
            Route::post('edit/{id}', 'update')->name('roles.update');
            Route::get('{route?}', 'index')->name('roles.index');
            Route::get('{route?}/{id?}', 'show')->name('roles.view');
            Route::delete('{id}', 'destroy')->name('roles.delete');
            Route::put('mass-update', 'massUpdate')->name('roles.mass_update');
            Route::put('mass-destroy', 'massDestroy')->name('roles.mass_delete');
        });
        
        Route::prefix('settings/users')->controller(UsersController::class)->group(function () {
            Route::get('create', 'create')->name('users.create');
            Route::post('create', 'store')->name('users.store');
            Route::get('edit/{id?}', 'edit')->name('users.edit');
            Route::post('edit/{id}', 'update')->name('users.update');
            Route::get('{route?}', 'index')->name('users.index');
            Route::get('{route?}/{id?}', 'show')->name('users.view');
            Route::delete('{id}', 'destroy')->name('users.delete');
            Route::put('mass-update', 'massUpdate')->name('users.mass_update');
            Route::put('mass-destroy', 'massDestroy')->name('users.mass_delete');
        });
      
        Route::prefix('tags')->controller(TagController::class)->group(function () {            
            Route::post('create', 'store')->name('settings.tags.store');
        });

        Route::controller(MemveraRoutesController::class)->group(function () {

            // dashboards
            Route::get('/', 'dashboard');

            // pages routes
            Route::get("/pages/starter", "pages_starter");
            Route::get("/pages/maintenance", "pages_maintenance");
            Route::get("/pages/coming-soon", "pages_coming_soon");

            // auth sample page routes
            Route::get("/auth/signin-basic", "auth_signin_basic");
            Route::get("/auth/signin-cover", "auth_signin_cover");
            Route::get("/auth/signup-basic", "auth_signup_basic");
            Route::get("/auth/signup-cover", "auth_signup_cover");
            Route::get("/auth/reset-pwd-basic", "auth_reset_pwd_basic");
            Route::get("/auth/reset-pwd-cover", "auth_reset_pwd_cover");
            Route::get("/auth/create-pwd-basic", "auth_create_pwd_basic");
            Route::get("/auth/create-pwd-cover", "auth_create_pwd_cover");
            Route::get("/auth/lockscreen-basic", "auth_lockscreen_basic");
            Route::get("/auth/lockscreen-cover", "auth_lockscreen_cover");
            Route::get("/auth/twostep-basic", "auth_twostep_basic");
            Route::get("/auth/twostep-cover", "auth_twostep_cover");
            Route::get("/auth/404", "auth_404");
            Route::get("/auth/500", "auth_500");
            Route::get("/auth/404-basic", "auth_404_basic");
            Route::get("/auth/404-cover", "auth_404_cover");
            Route::get("/auth/ofline", "auth_ofline");
            Route::get("/auth/logout-basic", "auth_logout_basic");
            Route::get("/auth/logout-cover", "auth_logout_cover");
            Route::get("/auth/success-msg-basic", "auth_success_msg_basic");
            Route::get("/auth/success-msg-cover", "auth_success_msg_cover");
        });
    // });
});
