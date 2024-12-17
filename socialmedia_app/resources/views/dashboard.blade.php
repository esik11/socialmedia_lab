<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12" ng-app="socialApp" ng-controller="PostController">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Post Creation Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg mb-4">Create a Post</h3>
                    <form ng-submit="createPost()" class="post-form">
                        <!-- CSRF token -->
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <textarea ng-model="newPost.content" class="form-control mb-2" placeholder="What's on your mind?" required></textarea>
                        <button type="submit" class="btn btn-primary">Post</button>
                    </form>
                </div>
            </div>

            <!-- Display Recent Posts -->
            <div class="post-container mt-4">
                <h3 class="font-bold text-xl mb-4">Recent Posts</h3>
                <div ng-repeat="post in posts" class="post-card bg-white shadow-sm rounded-lg p-4 mb-4">
                    <!-- Display post content -->
                    <div ng-if="!post.isEditing">
                        <p class="text-gray-800 text-lg mb-2" ng-bind="post.content"></p>
                    </div>

                    <!-- Editable post content -->
                    <div ng-if="post.isEditing">
                        <textarea ng-model="post.editingContent" class="form-control mb-2" required></textarea>
                    </div>

                    <button ng-if="post.user_id === userId" ng-click="editPost(post)" class="btn btn-warning">
                        <span ng-if="!post.isEditing">Edit</span>
                        <span ng-if="post.isEditing">Save</span>
                    </button>

                    <!-- Display username of the user who posted -->
                    <p class="text-sm text-gray-600 mb-3">Posted by: <span ng-bind="post.user.name"></span></p>

                    <!-- Display the time the post was created -->
                    <small class="text-gray-500" ng-bind="post.created_at"></small>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <!-- Like Button -->
                        <div>
                            <button ng-click="likePost(post.id)" class="btn btn-sm" ng-class="{'btn-primary': post.liked_by_user, 'btn-outline-primary': !post.liked_by_user}">
                                <i class="bi bi-heart" ng-class="{'bi-heart-fill': post.liked_by_user, 'bi-heart': !post.liked_by_user}"></i> Like
                            </button>
                            <button ng-click="unlikePost(post.id)" class="btn btn-sm" ng-class="{'btn-danger': post.liked_by_user, 'btn-outline-danger': !post.liked_by_user}">
                                <i class="bi bi-heart-fill" ng-class="{'bi-heart-fill': post.liked_by_user, 'bi-heart': !post.liked_by_user}"></i> Unlike
                            </button>
                        </div>

                        <!-- Display the number of likes -->
                        <p class="text-sm text-gray-600">Likes: <span ng-bind="post.likes_count"></span></p>
                    </div>
                    <!-- Comment Section -->
                    <div class="comment-section mt-4">
                        <textarea ng-model="newComment[post.id]" class="form-control mb-2" placeholder="Add a comment" required></textarea>
                        <button ng-click="addComment(post.id)" class="btn btn-secondary btn-sm">Comment</button>

                        <!-- Display Comments -->
                        <div class="comment-list mt-3">
    <div class="comment-card mb-3 p-3 bg-light rounded" ng-repeat="comment in post.comments">
        <div class="d-flex align-items-center mb-2">
            <strong ng-bind="comment.user.name"></strong>
            <small class="ml-2 text-muted" ng-bind="comment.created_at | date:'short'"></small>
        </div>

        <!-- Comment Content or Editable Field -->
        <div ng-if="!comment.isEditing">
            <p class="comment-content mb-2" ng-bind="comment.content"></p>
        </div>
        <div ng-if="comment.isEditing">
            <textarea ng-model="comment.editingContent" class="form-control mb-2"></textarea>
        </div>

        <!-- Edit and Delete Buttons -->
        <div>
            <button ng-if="comment.user_id === userId" ng-click="editComment(post.id, comment)" class="btn btn-sm btn-warning">
                <span ng-if="!comment.isEditing">Edit</span>
                <span ng-if="comment.isEditing">Save</span>
            </button>
            <button ng-if="comment.user_id === userId" ng-click="deleteComment(post.id, comment.id)" class="btn btn-sm btn-danger">Delete</button>
        </div>
    </div>
</div>

        </div>
    </div>
</x-app-layout>
