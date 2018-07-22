<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CaptchasRequest;
use Gregwar\Captcha\CaptchaBuilder;

class CaptchasController extends Controller
{
    public function store(CaptchasRequest $request,CaptchaBuilder $CaptchaBuilder)
    {
        $key='captcha-'.str_random(15);
        $phone=$request->phone;

        $captcha=$CaptchaBuilder->build();
        $expireAt=now()->addMinutes(2);
        \Cache::put($key,['phone'=>$phone,'code'=>$captcha->getPhrase()],$expireAt);

        $result=[
            'captcha_key'=>$key,
            'expire_at'=>$expireAt->toDateTimeString(),
            'captcha_content_image'=>$captcha->inline(),
        ];
        return $this->response->array($result)->setStatusCode(201);
    }
}
