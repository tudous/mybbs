<?php
namespace App\Models\Traits;

use Redis;
use Carbon\Carbon;
use Log;

trait LastActivedAtHelper
{
    //缓存
    protected $hash_prefix='larabbs_last_actived_at_';
    protected $field_prefix='user_';

    //中间件记录时间来调用这个函数
    public function recordLastActivedAt()
    {
        //获取今天的日期
         $date = Carbon::now()->toDateString();

        // Redis 哈希表的命名，如：larabbs_last_actived_at_2017-10-21
        $hash = $this->hash_prefix . $date;

        // 字段名称，如：user_1
        $field = $this->field_prefix . $this->id;

        //dd(Redis::hGetAll($hash));


        // 当前时间，如：2017-10-21 08:35:15
        $now = Carbon::now()->toDateTimeString();

        // 数据写入 Redis ，字段已存在会被更新
        Redis::hSet($hash, $field, $now);
    }

    //控制台命令调用，用来同步数据
    public function syncUserActivedAt()
    {
        // 获取日期
        $yesterday_date = Carbon::now()->toDateString();

        //组织表名
        $hash=$this->hash_prefix.$yesterday_date;
        //从表里取出数据
        $datas=Redis::hGetAll($hash);

        //遍历数据，同步到数据库
        foreach ($datas as $user_id => $actived_at) {
            //从遍历出的$user_id中将前缀替换
            $user_id=str_replace($this->field_prefix,'', $user_id);
            //当用户存在才更新到数据库中
            if ($user=$this->find($user_id)) {
                $user->last_actived_at=$actived_at;
                $user->save();
            }
        }
        //删除redis中的数据
        Redis::del($hash);

    }

    // 模型访问器
    public function getLastActivedAtAttribute($value)
    {
        // 获取今天的日期
        $date = Carbon::now()->toDateString();

        // Redis 哈希表的命名，如：larabbs_last_actived_at_2017-10-21
        $hash = $this->hash_prefix . $date;

        // 字段名称，如：user_1
        $field = $this->field_prefix . $this->id;

        // 三元运算符，优先选择 Redis 的数据，否则使用数据库中
        $datetime = Redis::hGet($hash, $field) ? : $value;

        // 如果存在的话，返回时间对应的 Carbon 实体
        if ($datetime) {
            return new Carbon($datetime);
        } else {
        // 否则使用用户注册时间
            return $this->created_at;
        }



    }


}






 ?>