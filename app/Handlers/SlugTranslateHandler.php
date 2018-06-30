<?php
namespace App\Handlers;

use GuzzleHttp\Client;
use Overtrue\Pinyin\Pinyin;
use Log;

class SlugTranslateHandler
{
    public function translate($text)
    {
        $http=new Client;
        //初始化配置
        $api='http://api.fanyi.baidu.com/api/trans/vip/translate?';
        $appid=config('services.baidu_tanslate.appid');
        $key=config('services.baidu_tanslate.key');
        $salt=time();
        Log::info('appid'.$appid);
        Log::info('appid'.$key);

        if(empty($appid)||empty($key)){
            return $this->pinyin($text);
        }
        // 根据文档，生成 sign
        // http://api.fanyi.baidu.com/api/trans/product/apidoc
        // appid+q+salt+密钥 的MD5值
        $sign = md5($appid. $text . $salt . $key);

        // 构建请求参数
        $query = http_build_query([
            "q"     =>  $text,
            "from"  => "zh",
            "to"    => "en",
            "appid" => $appid,
            "salt"  => $salt,
            "sign"  => $sign,
        ]);
        //发送HTTP GET请求
        $response=$http->get($api.$query);
        $result=json_decode($response->getBody(),true);
        Log::info('hello');
        Log::info($result);

        //尝试获取翻译结果
        if (isset($result['trans_result'][0]['dst'])) {
            return str_slug($result['trans_result'][0]['dst']);
        }else{
            //如果百度没有结果，使用拼英
            return $this->pinyin($text);
        }
    }

   public function pinyin($text)
   {
        return str_slug(app(Pinyin::class)->permalink($text));
   }


}
