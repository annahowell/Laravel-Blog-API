<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'v1'], function () {
    Route::post('user', 'UserController@store');
    Route::post('user/login', 'UserController@login');

    Route::get('posts', 'PostController@index');
    Route::get('posts/{id}', 'PostController@show');
    Route::get('posts/{id}/comments', 'PostController@showWithComments');

    Route::get('tags', 'TagController@index');
    Route::get('tags/{id}', 'TagController@show');
    Route::get('tags/{id}/posts', 'TagController@showWithPosts');

    Route::get('comments/{comment}', 'CommentController@show');


    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('user/logout', 'UserController@logout');
        Route::get('user', 'UserController@index'); // admin gets all users and their roles
        Route::get('user/roles', 'UserController@showRoles'); // admin gets user info with roles and perms --done
        Route::get('user/{user}', 'UserController@show'); // admin gets user info with roles and perms --done
        Route::put('user/{user}', 'UserController@update'); // admin puts user info and roles--done
        Route::delete('user/{user}', 'UserController@destroy'); // admin deletes user account --done


        Route::post('posts', 'PostController@store');
        Route::put('posts/{post}', 'PostController@update');
        Route::delete('posts/{post}', 'PostController@destroy');

        Route::post('tags', 'TagController@store');
        Route::put('tags/{tag}', 'TagController@update');
        Route::delete('tags/{tag}', 'TagController@destroy');

        Route::post('comments', 'CommentController@store');
        Route::put('comments/{comment}', 'CommentController@update');
        Route::delete('comments/{comment}', 'CommentController@destroy');
    });
});
