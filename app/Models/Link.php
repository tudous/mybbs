<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;


class Link extends Model
{
    protected $fillable=['title','link'];

    public $cache_key='larabbs_links';

    protected $cache_expire_time=1440;

    public function getAllCache()
    {
        return Cache::remember($this->cache_key,$this->cache_expire_time,function(){
            return $this->all();
        });
    }
}

?>
