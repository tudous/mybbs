<?php

use Illuminate\Http\Request;

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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware'=> 'serializer:array'
], function($api) {

    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.access.limit'),
        'expires' => config('api.rate_limits.access.expires'),
    ], function($api){
    // 短信验证码
    $api->post('verificationCodes', 'VerificationCodesController@store')
        ->name('api.verificationCodes.store');
    //用户注册
    $api->post('users','UsersController@store')
        ->name('api.users.store');
    //图片验证码
    $api->post('captchas','CaptchasController@store')
        ->name('api.captchas.store');
    });
    //第三方登陆
    $api->post('socials/{social_type}/authorizations','AuthorizationsController@socialStore')
        ->name('api.socials.authorizations.store');
    //登陆
    $api->post('authorizations', 'AuthorizationsController@store')
    ->name('api.authorizations.store');
    //刷新token
    $api->put('authorizations/current', 'AuthorizationsController@update')
    ->name('api.authorizations.update');

    //删除token
    $api->delete('authorizations/current', 'AuthorizationsController@delete')
    ->name('api.authorizations.delete');

     // 需要 token 验证的接口
     //put 替换某个资源，需提供完整的资源信息
     //patch 部分修改资源，提供部分资源信息
     //api.auth 这个中间件，用来区分哪些接口需要验证 token，哪些不需要
     $api->group(['middleware' => 'api.auth'], function($api) {
            // 当前登录用户信息
            $api->get('user', 'UsersController@me')
                ->name('api.user.show');
            //图片资源
             $api->post('images', 'ImagesController@store')
                ->name('api.images.store');
            //编辑登陆用户信息
             $api->patch('user', 'UsersController@update')
                ->name('api.user.update');
        });
});