<?php

namespace App\Handlers;


use Image;

class ImageUploadHandler
{
    //只允许的后缀
    protected $allowed_ext=['png','jpg','gif','jpeg'];


    //遗留问题：删除之前上传的图片

    public function save($file,$folder,$file_prefix,$max_width = false)
    {
        // 构建存储的文件夹规则，值如：uploads/images/avatars/201709/21/
        $folder_name="uploads/images/$folder/".date('Y-m-d',time());
        //物理路径
        $upload_path=public_path().'/'.$folder_name;
        //获取文件的后缀名
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';

        //拼接文件名字
        $file_name=$file_prefix.'_'.time().'_'.str_random().'.'.$extension;
        //如果上传非图片，停止
        if(!in_array($extension,$this->allowed_ext)){
            return flase;
        }
        //将图片移动
        $file->move($upload_path,$file_name);

        // 如果限制了图片宽度，就进行裁剪
        if ($max_width && $extension != 'gif') {

            // 此类中封装的函数，用于裁剪图片
            $this->reduceSize($upload_path . '/' . $file_name, $max_width);
        }

        return [
            'path' => config('app.url') . "/$folder_name/$file_name"
        ];

    }

    public function reduceSize($file_path, $max_width)
    {
         // 先实例化，传参是文件的磁盘物理路径
        $image = Image::make($file_path);

        // 进行大小调整的操作
        $image->resize($max_width, null, function ($constraint) {

            // 设定宽度是 $max_width，高度等比例双方缩放
            $constraint->aspectRatio();

            // 防止裁图时图片尺寸变大
            $constraint->upsize();
        });

        // 对图片修改后进行保存
        $image->save();
    }
}
