<?php

Route::name('admin.')->group(function () {
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
