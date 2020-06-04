<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
// @see vendor/laravel/ui/src/AuthRouteMethods.php
Auth::routes(['verify' => true]);
Route::post('email/verify/register', 'Auth\VerificationController@register')->name('email.verify.register');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/welcome', function () {
        return view('welcome');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function () {
    Auth::routes();

    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/', 'HomeController@index')->name('home');
        Route::post('logout', 'Auth\LoginController@logout')->name('logout');
    });
});


/*
|--------------------------------------------------------------------------
| Preview Email
|--------------------------------------------------------------------------
*/
Route::get('test/verify-email', function () {
    $user = App\User::find(1);
    return (new App\Notifications\VerifyEmailNotification($user))
                ->toMail($user);
});
Route::get('test/reset-password', function () {
    $user = App\User::find(1);
    return (new App\Notifications\ResetPasswordNotification($user))
                ->toMail($user);
});
Route::get('test/admin/reset-password', function () {
    $admin = App\Admin::find(1);
    return (new App\Notifications\Admin\ResetPasswordNotification($admin))
                ->toMail($admin);
});