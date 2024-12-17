<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    // The table associated with the model
    protected $table = 'likes';

    // Fillable fields for mass assignment
    protected $fillable = [
        'post_id',
        'user_id'
    ];

    /**
     * Define the relationship between a like and a post.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Define the relationship between a like and a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
