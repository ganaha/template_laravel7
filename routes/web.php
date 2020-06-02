<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
Auth::routes(['verify' => true]);

Route::get('/', 'HomeController@index')->name('home');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function () {
    Auth::routes(['verify' => true]);

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
Route::get('email/verify-email', function () {
    $user = App\User::find(1);
    return (new App\Notifications\VerifyEmailNotification($user))
                ->toMail($user);
});
Route::get('email/reset-password', function () {
    $user = App\User::find(1);
    return (new App\Notifications\ResetPasswordNotification($user))
                ->toMail($user);
});
Route::get('email/admin/reset-password', function () {
    $admin = App\Admin::find(1);
    return (new App\Notifications\Admin\ResetPasswordNotification($admin))
                ->toMail($admin);
});