<?php

namespace App\Http\Controllers\Api;

use App\Models\Image;
use App\Http\Requests\Api\ImageRequest;
use App\Transformers\ImageTransformer;
use App\Handlers\ImageUploadHandler;

class ImagesController extends Controller
{
    public function store(ImageRequest $request,ImageUploadHandler $uploader,Image $image)
    {
        //$this->user() 等同于\Auth::guard('api')->user()
        $user=$this->user();

         $size = $request->type == 'avatar' ? 362 : 1024;
        $result = $uploader->save($request->image, str_plural($request->type), $user->id, $size);
        //str_plural 函数将字符串转化为复数形式，该函数当前只支持英文：

        $image->path = $result['path'];
        $image->type = $request->type;
        $image->user_id = $user->id;
        $image->save();

        return $this->response->item($image, new ImageTransformer())->setStatusCode(201);
    }
}
