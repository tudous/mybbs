<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Http\Requests\Api\AuthorizationRequest;
use App\Models\User;
use Auth;

class AuthorizationsController extends Controller
{
    /*
    第三方登陆
    客户端要么提交授权码（code），要么提交 access_token 和 openid
    无论哪种方式，服务器都会调用微信接口，获取授权用户数据，从而确认数据的有效性。这一步很重要，客户端提交的一切都是不可信任的，切记不能由客户端直接换取用户信息，提交 openid 或 unionid 到服务器，直接入库。
    根据 openid 或 unionid 去数据库查询是否该用户已经存在，如果不存在，则创建用户
    最后由我们服务器为该用户颁发授权凭证。
    */
    public function socialStore($type,SocialAuthorizationRequest $request)
    {
        if(!in_array($type,['weixin'])){
            return $this->response->errorBadRequest();
        }

        $driver=\Socialite::driver($type);

        try{
            if($code=$request->code){
                $response=$driver->getAccessTokenResponse($code);
                $token=array_get($response,'access_token');
            }else{
                $token=$request->access_token;
                if($type=='weixin'){

                    $driver->setOpenId($request->openid);
                }

            }
            $oauthUser=$driver->userFromToken($token);

          } catch (\Exception $e) {
             return $this->response->errorUnauthorized('参数错误，未获取用户信息');
          }

          switch ($type) {
            case 'weixin':
                $unionid = $oauthUser->offsetExists('unionid') ? $oauthUser->offsetGet('unionid') : null;

                if ($unionid) {
                    $user = User::where('weixin_unionid', $unionid)->first();
                } else {
                    $user = User::where('weixin_openid', $oauthUser->getId())->first();
                }

                // 没有用户，默认创建一个用户
                if (!$user) {
                    $user = User::create([
                        'name' => $oauthUser->getNickname(),
                        'avatar' => $oauthUser->getAvatar(),
                        'weixin_openid' => $oauthUser->getId(),
                        'weixin_unionid' => $unionid,
                    ]);
                }

                break;
        }
          return $this->respondWithToken($token)->setStatusCode(201);
      }


      //账号密码登陆
      public function store(AuthorizationRequest $request)
      {

         $username=$request->username;

         //组织数据
         filter_var($username,FILTER_VALIDATE_EMAIL)?
         $credentials['email'] = $username :
         $credentials['phone'] = $username ;

         $credentials['password']=$request->password;

         //API验证
         if (!$token = \Auth::guard('api')->attempt($credentials)) {
            return $this->response->errorUnauthorized('用户名或密码错误');
        }

        return $this->respondWithToken($token)->setStatusCode(201);

      }

      public function respondWithToken($token)
      {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
        ]);
      }

      public function update()
      {
            $token=Auth::guard('api')->refresh();
            return $this->respondWithToken($token);
      }
      public function delete()
      {
            Auth::guard('api')->logout();
            return $this->response->noContent();
      }

}
