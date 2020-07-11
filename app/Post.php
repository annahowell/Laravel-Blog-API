<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'body', 'user_id'];


    /**
     * Get the tags for the post.
     */
    public function tags()
    {
        return $this->belongsToMany('App\Tag', 'post_tags')->withTimestamps();
    }

    /**
     * Get the comments for the post.
     */
    public function comments()
    {
        return $this->hasMany('App\Comment', 'post_id');
    }

    /**
     * Get the user of the post.
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
