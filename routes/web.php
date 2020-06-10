<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Pusher\Pusher;

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

    // Video Chat
    Route::get('/video', function () {
        $user = \Auth::user();
        $others = \App\User::where('id', '<>', $user->id)->get();
        return view('video.index', compact('user', 'others'));
    })->name('video');

    Route::post('auth/video_chat', function (Request $request) {
        $user = $request->user();
        $socket_id = $request->socket_id;
        $channel_name = $request->channel_name;
        $pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            [
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'encrypted' => true
            ]
        );
        return response(
            $pusher->presence_auth($channel_name, $socket_id, $user->id)
        );
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