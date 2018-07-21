<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Handlers\SMS\SendTemplateSMS;
use App\Http\Requests\Api\VerificationCodeRequest;



class VerificationCodesController extends Controller
{
     public function store(VerificationCodeRequest $request)
    {
        $phone = $request->phone;
        $SendTemplateSMS=new SendTemplateSMS;

        $code='';
        $_str='1234567890';
        for($i=0;$i<6;$i++){
           $code.=$_str[mt_rand(0,9)];
        }

        $sendResult=$SendTemplateSMS->sendTemplateSMS($phone, array($code, 30), 1);
        if($sendResult['status'] != 0){
            return $sendResult;
        }

        $key = 'verificationCode_'.str_random(15);
        $expiredAt = now()->addMinutes(30);
        // 缓存验证码 10分钟过期。
        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

        return $this->response->array([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }
}
