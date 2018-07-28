<?php

namespace App\Http\Controllers\Api;

use App\Transformers\CategoryTransformer;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index()
    {
    /*
    增加路由
    创建 transformer
    controller 处理数据，使用 transformer 转换后返回
    */

        return $this->response->collection(Category::all(), new CategoryTransformer());
    }
}
