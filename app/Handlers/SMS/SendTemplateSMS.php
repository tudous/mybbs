<?php

namespace App\Handlers\SMS;

use App\Model\M3Result;

class SendTemplateSMS
{
  //主帐号
  private $accountSid='8aaf07085df473bd015e00183c7d0257';

  //主帐号Token
  private $accountToken='1530c75156ef41c1984182e95eaa06ad';

  //应用Id
  private $appId='8aaf07085df473bd015e00183e05025e';

  //请求地址，格式如下，不需要写https://
  private $serverIP='sandboxapp.cloopen.com';

  //请求端口
  private $serverPort='8883';

  //REST版本号
  private $softVersion='2013-12-26';

  /**
    * 发送模板短信
    * @param to 手机号码集合,用英文逗号分开
    * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
    * @param $tempId 模板Id
    */
  public function sendTemplateSMS($to,$datas,$tempId)
  {
       //$m3_result = new M3Result;

       // 初始化REST SDK
       $rest = new CCPRestSDK($this->serverIP,$this->serverPort,$this->softVersion);
       $rest->setAccount($this->accountSid,$this->accountToken);
       $rest->setAppId($this->appId);

       // 发送模板短信
      //  echo "Sending TemplateSMS to $to <br/>";
       $result = $rest->sendTemplateSMS($to,$datas,$tempId);

       $arr=array('status'=>1,'message'=>'发送失败');
       if($result == NULL ) {
           $arr['status']=1;
           $arr['message']='发送失败';

       }
       if($result->statusCode != 0) {
           $arr['status'] = $result->statusCode;
           $arr['message'] = $result->statusMsg;
       }else{
           $arr['status']=0;
           $arr['message']='发送成功';
       }

       return $arr;
  }
}

//sendTemplateSMS("13823716423", array(1234, 5), 1);
