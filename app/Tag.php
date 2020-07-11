<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['title', 'color'];

    public function posts()
    {
        return $this->belongsToMany('App\Post', 'post_tags')->withTimestamps();
    }
}
