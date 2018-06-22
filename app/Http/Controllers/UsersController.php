<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Handlers\ImageUploadHandler;


class UsersController extends Controller
{
    public function show(User $user)
    {
        return view('user.show',compact('user'));
    }
    public function edit(User $user)
    {

        return view('user.edit',compact('user'));
    }
    public function update(UserRequest $request,ImageUploadHandler $imageUploadHandler,User $user)
    {

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
