<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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
    })->name('welcome');

    // Public
    Route::get('/chat/public', function () {
        $username = \Auth::user()->name;
        return view('chat.public', compact('username'));
    })->name('chat.public');

    Route::post('/chat/send', function (Request $request) {
        event(new \App\Events\PublicChannelEvent($request->message));
    });

    // Private
    Route::get('/chat/private', function () {
        $user = \Auth::user();
        return view('chat.private', compact('user'));
    })->name('chat.private');

    // Presence
    Route::get('/chat/presence/{id}', function ($id) {
        $username = \Auth::user()->name;
        return view('chat.presence', compact('username', 'id'));
    })->name('chat.presence');
    Route::post('/chat/presence/{id}', function (Request $request, $id) {
        event(new \App\Events\PresenceChannelEvent($request->message, $id));
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

        Route::get('welcome', function () {
            return view('admin.welcome');
        })->name('welcome');

        Route::resource('users', 'UsersController');

        Route::post('users/{user}/pusher', 'UsersController@pusher')->name('users.pusher');
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