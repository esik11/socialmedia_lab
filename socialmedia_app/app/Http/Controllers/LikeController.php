<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Add a like to a post.
     */
    public function store($postId)
{
    $post = Post::findOrFail($postId);

    // Check if the user already liked the post
    if ($post->likes()->where('user_id', Auth::id())->exists()) {
        return response()->json(['message' => 'Cannot like the post again'], 400); // Custom message
    }

    $like = Like::create([
        'post_id' => $post->id,
        'user_id' => Auth::id(),
    ]);

    // Return post data with updated like information
    $post->load('likes'); // Ensure the likes count is updated
    return response()->json([
        'message' => 'Post liked successfully!',
        'liked_by_user' => true,
        'likes_count' => $post->likes->count(),
    ], 201);
}

    /**
     * Remove a like from a post.
     */
    public function destroy($postId)
    {
        $post = Post::findOrFail($postId);
    
        // Find the like by the current user
        $like = $post->likes()->where('user_id', Auth::id())->first();
    
        if (!$like) {
            return response()->json(['message' => 'Like not found'], 404);
        }
    
        $like->delete();
    
        // Return post data with updated like information
        $post->load('likes'); // Ensure the likes count is updated
        return response()->json([
            'message' => 'Like removed successfully!',
            'liked_by_user' => false,
            'likes_count' => $post->likes->count(),
        ]);
    }
}
