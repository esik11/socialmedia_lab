<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    // Fillable fields for mass assignment
    protected $fillable = [
        'post_id', 
        'user_id', 
        'content'
    ];

    /**
     * Define the relationship between a comment and a post.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Define the relationship between a comment and a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the date the comment was created in a readable format.
     */
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->diffForHumans();
    }
}
