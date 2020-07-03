<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\RecordResource;
use App\Http\Resources\RecordsCollection;
use App\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// ログイン(トークン発行)
Route::post('/login', function (LoginRequest $request) {
    // $request->validate([
    //     'email' => 'required|email',
    //     'password' => 'required'
    // ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }
    $token = $user->createToken('my-token')->plainTextToken; 
    return new RecordResource(['token' => $token]);
});

// 仮登録
Route::post('/register', 'Api\Auth\RegisterController@register');
Route::get('/email/verify/{id}/{hash}', 'Api\Auth\VerificationController@verify')->name('api.verification.verify');

Route::middleware('auth:sanctum')->group(function () {
    // ログインユーザー情報
    Route::get('/user', function (Request $request) {
        return new RecordResource($request->user());
    });
    // ユーザー一覧取得
    Route::get('/users', function (Request $request) {
        return new RecordsCollection(User::paginate(2));
    });
    Route::get('/logout', function (Request $request) {
        \Auth::user()->tokens()->delete();
        return new RecordResource([]);
    });
});
