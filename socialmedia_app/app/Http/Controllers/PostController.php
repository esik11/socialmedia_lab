<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    // Method to create a post
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:500', // Validate the content field
        ]);

        $post = Post::create([
            'user_id' => auth()->id(), // Get the authenticated user ID
            'content' => $request->content,
        ]);

        return response()->json(['message' => 'Post created successfully!', 'post' => $post], 201);
    }

    // Method to retrieve all posts
    public function index()
    {
        // Eager load both 'user' and 'comments.user' relationships
        $posts = Post::with('user', 'comments.user')->latest()->get();
        return response()->json($posts);
    }
    
    // Method to delete a post
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        if ($post->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully!']);
    }

    // Show the post for editing (with route model binding)
    public function edit(Post $post)
    {
        // Ensure the authenticated user is the owner of the post
        if ($post->user_id != Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($post);
    }

    // Update the post content
    public function update(Request $request, Post $post)
    {
        // Ensure the authenticated user is the owner of the post
        if ($post->user_id != Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validate the request data
        $validated = $request->validate([
            'content' => 'required|string|max:500',  // Adjust validation as needed
        ]);

        // Update the post content
        $post->content = $validated['content'];
        $post->save();

        return response()->json([
            'message' => 'Post updated successfully!',
            'post' => $post
        ]);
    }
}
