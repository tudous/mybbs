<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Topic;
use Auth;
use Spatie\Permission\Traits\HasRoles;
use Log;

class User extends Authenticatable implements JWTSubject
{
    use Traits\ActiveUserHelper;
    use Traits\LastActivedAtHelper;
    use HasRoles;
    use Notifiable {
        notify as protected laravelNotify;
    }

    public function notify($instance)
    {
        //如果要通知的人是当前用户，就不通知
        if($this->id == Auth::id()){
            return;
        }
        $this->increment('notification_count');
        $this->laravelNotify($instance);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','introduction','avatar','phone','weixin_openid','weixin_unionid'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function topics()
    {
       return  $this->hasMany(Topic::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    //清空未读提示
    public function markAsRead()
    {
        $this->notification_count=0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }

    public function isAuthorOf($model)
    {
        return $this->id==$model->user_id;
    }

    //密码修改器
    public function setPasswordAttribute($value)
    {
        Log::info($value);
        if(strlen($value) != 60){
            $value=bcrypt($value);
        }
        $this->attributes['password']=$value;
    }

    public function setAvatarAttribute($path)
    {
        Log::info($path);
        if(!starts_with($path,'http')){
            $path=config('app.url')."/uploads/images/avatar/$path";
        }
        $this->attributes['avatar']=$path;
    }


    //getJWTIdentifier 返回了 User 的 id，getJWTCustomClaims 是我们需要额外再 JWT 载荷中增加的自定义内容，这里返回空数组。
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
