<?php
namespace App\Handlers;

use GuzzleHttp\Client;
use Overtrue\Pinyin\Pinyin;
 
class SlugTranslateHandler
{
    public function translate($text)
    {
        $http=new Client;
        //初始化配置
        $api='http://api.fanyi.baidu.com/api/trans/vip/translate?';
        $appid=config('services.baidu_tanslae.appid');
        $key=config('services.baidu_tanslate.key');
        $salt=time();

        if(empty($appid)||empty($key)){
            return $this->pinyin($text);
        }
        //发送HTTP GET请求
        $response=$http->get($api.$query);
        $result=json_decode($response->getBody(),true);


        //尝试获取翻译结果
        if (isset($result['trans_result'][0]['dst'])) {
            return str_slug($result['tans_result'][0]['dst']);
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
