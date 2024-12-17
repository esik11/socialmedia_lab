<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Add a comment to a post.
     */
    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);
    
        $post = Post::findOrFail($postId);
    
        // Create the comment
        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);
    
        // Fetch updated comments for the post
        $comments = $post->comments()->with('user')->latest()->get();
    
        return response()->json([
            'message' => 'Comment added successfully!',
            'comment' => $comment,
            'comments' => $comments,  // Return the updated comments array
        ], 201);
    }
    
    
    
    /**
     * Get comments for a specific post.
     */
    public function show($postId)
    {
        $post = Post::findOrFail($postId);
        $comments = $post->comments()->with('user')->latest()->get();

        return response()->json($comments);
    }
    public function userPosts()
    {
        $userPosts = Post::where('user_id', Auth::id())->latest()->get();
        return response()->json($userPosts);
    }
    public function update(Request $request, $postId, $commentId)
{
    $comment = Comment::where('id', $commentId)->where('post_id', $postId)->firstOrFail();

    // Ensure the user owns the comment
    if ($comment->user_id !== auth()->id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $request->validate([
        'content' => 'required|string|max:500',
    ]);

    $comment->update(['content' => $request->content]);

    return response()->json(['message' => 'Comment updated successfully.']);
}
public function destroy($postId, $commentId)
{
    $comment = Comment::where('id', $commentId)->where('post_id', $postId)->firstOrFail();

    // Ensure the user owns the comment
    if ($comment->user_id !== auth()->id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $comment->delete();

    return response()->json(['message' => 'Comment deleted successfully.']);
}

}
