<?php

namespace App\Models;

class Reply extends Model
{
    protected $fillable = ['content'];

    public function topic()
    {
        return $this->belongsto(Topic::class);
    }

    public function user()
    {
        return $this->belongsto(User::class);
    }
}
