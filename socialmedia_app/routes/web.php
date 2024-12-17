<?php
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

      // Post Routes
      Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
      Route::get('/posts', [PostController::class, 'index'])->name('posts.index'); 
      Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
      Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');

      Route::post('/posts/{postId}/like', [LikeController::class, 'store'])->name('post.like');

      // Unlike a post
      Route::delete('/posts/{postId}/like', [LikeController::class, 'destroy'])->name('post.unlike');

      Route::post('/posts/{postId}/comment', [CommentController::class, 'store'])->name('post.comment');

      // Get all comments for a post
      Route::get('/posts/{postId}/comments', [CommentController::class, 'show'])->name('post.comments');
      Route::put('/posts/{post}/comments/{comment}', [CommentController::class, 'update']);
      Route::delete('/posts/{post}/comments/{comment}', [CommentController::class, 'destroy']);
      
      

});


require __DIR__.'/auth.php';
