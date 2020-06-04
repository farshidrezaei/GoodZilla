<?php

use App\Article;
use App\Http\Controllers\ArticleController;
use App\User;
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

Route::get( 'search/{key?}', static function ( Request $request, $key = '' ) {
    return
        Article::search( mb_strtolower( $key ) )
            ->rule( static function ( $builder ) {

                return [
                    'must' => [
                        'wildcard' => [
                            'title' => "*$builder->query*",
                        ],
                    ],
                ];
            } )
            ->get();
} );


Route::prefix( 'v1' )
    ->group( static function () {
        Route::prefix( 'auth' )
            ->namespace( 'Auth' )
            ->middleware( 'api' )
            ->group( static function () {
                Route::post( 'check-email', 'AuthController@checkEmail' )->middleware( 'guest' );
                Route::post( 'check-verification-code', 'AuthController@checkVerificationCode' )->middleware( 'guest' );
                Route::post( 'logout', 'AuthController@logout' );
                Route::post( 'refresh', 'AuthController@refresh' );
                Route::get( 'user', 'AuthController@user' );
                Route::post( 'resend-verification-code', 'AuthController@resendVerificationCode' )->middleware( 'guest' );
            } );

        Route::prefix( 'admin' )
            ->namespace( 'Admin' )
            ->middleware( 'api' )
            ->group( static function () {
                Route::delete( 'users/delete', 'UserController@delete' );
                Route::apiResource( 'users', 'UserController' );

                Route::delete( 'articles/delete', 'ArticleController@delete' );
                Route::apiResource( 'articles', 'ArticleController' );
            } );


        Route::prefix( 'panel' )
            ->namespace( 'Panel' )
            ->middleware( 'api' )
            ->group( static function () {
                Route::get( 'bookmarks', 'UserController@bookmarks' );
                Route::put( 'profile', 'UserController@update' );
            } );

        Route::middleware( 'api' )
            ->group( static function () {
                Route::put( 'articles/{id}/toggle-like', 'ArticleController@toggleLike' );
                Route::put( 'articles/{id}/toggle-bookmark', 'ArticleController@toggleBookmark' );
                Route::post( 'articles/{id}/comment', 'ArticleController@comment' );
                Route::apiResource( 'articles', 'ArticleController' );
            } );
    } );
