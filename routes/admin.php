<?php

use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CustomPageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;


Route::get('admin/login', [AdminLoginController::class, 'showLoginForm'])->name('login.admin');
Route::post('admin/login', [AdminLoginController::class, 'login'])->name('admin.login');
Route::post('admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

Route::middleware('web')->group(function () {
    Route::get('/admin', fn() => redirect()->route('admin.dashboard'));
    Route::get('/admin/', fn() => redirect()->route('admin.dashboard'));
});



// Admin Routes
Route::group(['as' => 'admin.', 'prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['auth:admin'], 'where' => ['locale' => '[a-zA-Z]{2}']], function () {
    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/cc', [DashboardController::class, 'cacheClear'])->name('cacheClear');


    // admin profile routes
    Route::get('profile', [DashboardController::class, 'adminProfile'])->name('profile');
    Route::get('profile-edit', [DashboardController::class, 'profileEdit'])->name('profile.edit');
    Route::post('profile-update', [DashboardController::class, 'profileUpdate'])->name('profile.update');
    Route::post('password-update', [DashboardController::class, 'passwordUpdate'])->name('password.update');

    // Settings route
    Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
        Route::get('', [AdminSettingsController::class, 'index'])->name('index');
        Route::post('store', [AdminSettingsController::class, 'store'])->name('store');
    });

    // admins routes
    Route::group(['prefix' => 'admins', 'as' => 'admins.'], function () {
        Route::get('', [UserController::class, 'index'])->name('index');
        Route::post('store', [UserController::class, 'store'])->name('store');
        Route::get('{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('{user}', [UserController::class, 'update'])->name('update');
        Route::post('{user}/suspend', [UserController::class, 'suspend'])->name('suspend');
        Route::post('{user}/change-password', [UserController::class, 'changePassword'])->name('change-password');
    });

    Route::group(['prefix' =>  'roles', 'as' => 'roles.'], function () {
        Route::get('', [RolesController::class, 'index'])->name('index');
        Route::get('/create', [RolesController::class, 'create'])->name('create');
        Route::post('/store', [RolesController::class, 'store'])->name('store');
        Route::get('{id}/edit', [RolesController::class, 'edit'])->name('edit');
        Route::post('{id}/update', [RolesController::class, 'update'])->name('update');
    });

    // Faq
    Route::group(['prefix' => 'faq', 'as' => 'faq.'], function () {
        Route::get('', [FaqController::class, 'index'])->name('index');
        Route::post('/store', [FaqController::class, 'store'])->name('store');
        Route::get('/view/{id}', [FaqController::class, 'show'])->name('view');
        Route::get('/edit/{id}', [FaqController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [FaqController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [FaqController::class, 'destroy'])->name('delete');
    });

    //user
    Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('{id}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::post('{id}/update', [CustomerController::class, 'update'])->name('update');
        Route::get('view/{id}', [CustomerController::class, 'view'])->name('view');
        Route::get('/login-as/{id}', [CustomerController::class, 'authAs'])->name('login');
        Route::post('update-password', [CustomerController::class, 'updatePassword'])->name('password.change');
    });



    //Contact
    Route::group(['prefix' => 'contact', 'as' => 'contact.'], function () {
        Route::get('', [ContactController::class, 'index'])->name('index');
        Route::get('/delete/{id}', [ContactController::class, 'delete'])->name('delete');
    });

    //newsletter
    Route::group(['prefix' => 'subscribers', 'as' => 'subscribers.'], function () {
        Route::get('/newsletter-subscribers', [NewsletterController::class, 'index'])->name('index');
        Route::get('/newsletter/delete/{id}', [NewsletterController::class, 'delete'])->name('delete');
    });
    //tesitomonial
    Route::group(['prefix' => 'testimonial', 'as' => 'testimonial.'], function () {
        Route::get('/', [TestimonialController::class, 'index'])->name('index');
        Route::get('/create', [TestimonialController::class, 'create'])->name('create');
        Route::post('/store', [TestimonialController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [TestimonialController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [TestimonialController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [TestimonialController::class, 'delete'])->name('delete');
    });

    //Custom Page
    Route::group(['prefix' => 'cpage', 'as' => 'cpage.'], function () {
        Route::get('/', [CustomPageController::class, 'index'])->name('index');
        Route::get('/create', [CustomPageController::class, 'create'])->name('create');
        Route::post('/store', [CustomPageController::class, 'store'])->name('store');
        Route::get('{id}/view', [CustomPageController::class, 'view'])->name('view');
        Route::get('{id}/edit', [CustomPageController::class, 'edit'])->name('edit');
        Route::post('{id}/update', [CustomPageController::class, 'update'])->name('update');
        Route::get('{id}/delete', [CustomPageController::class, 'getDelete'])->name('delete');
    });
});
