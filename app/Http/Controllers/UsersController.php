<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Handlers\ImageUploadHandler;
use auth;
use App\Policies\UserPolicy;


class UsersController extends Controller
{
    public function __construct(){
        //权限控制,除show方法，其他所有的动作都需要登录才能访问，否则重定向
        $this->middleware('auth',['except'=>['show']]);

    }


    public function show(User $user)
    {
        
        return view('user.show',compact('user'));
    }
    public function edit(User $user)
    {
        try {
            $this->authorize('update',$user);
            return view('user.edit',compact('user'));
        } catch (\Exception $e) {

            abort(500);
        }


    }
    public function update(UserRequest $request,ImageUploadHandler $imageUploadHandler,User $user)
    {
        $this->authorize('update',$user);
        //获取数据
        $data=$request->all();

        if($request->avatar){
            $reslut=$imageUploadHandler->save($request->avatar,'avatar',$user->id,350);
            if($reslut){
                $data['avatar']=$reslut['path'];
            }
        }

        $user->update($data);
        return redirect()->route('users.show',$user->id)->with('success','修改资料成功');
    }
}
