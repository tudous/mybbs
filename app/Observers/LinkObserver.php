<?php
namespace App\Observers;


use App\Models\Link;
use Cache;

class LinkObserver
{
    public function saved(Link $link)
    {
        //保存数据后自动清空缓存
        Cache::forget($link->cache_key);
    }
}






 ?>