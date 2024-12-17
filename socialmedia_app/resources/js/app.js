import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

angular.module('socialApp', [], function ($interpolateProvider) {
    $interpolateProvider.startSymbol('[[');
    $interpolateProvider.endSymbol(']]');
})
.controller('PostController', function ($scope, $http) {
    $scope.posts = [];
    $scope.newComment = {};  // Store comments by postId
    $scope.userId = window.userId;
    $scope.editingPost = null; // Store the post being edited
    // Add CSRF token to HTTP requests
    $http.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Function to create a post
    $scope.createPost = function() {
        $http.post('/posts', $scope.newPost)
            .then(function(response) {
                $scope.posts.unshift(response.data); // Add the new post to the top
                $scope.newPost = {}; // Clear the form
            }, function(error) {
                alert('Error creating post');
            });
    };

    // Function to get all posts (including user information)
// Function to get all posts (including user information and comments)
$scope.getPosts = function() {
    $http.get('/posts')
        .then(function(response) {
            console.log(response.data);  
            $scope.posts = response.data;

            // For each post, ensure comments are fetched and attached
            $scope.posts.forEach(post => {
                // Check if the post already has comments; otherwise, initialize it
                if (!post.comments) {
                    post.comments = [];
                }
                // Optionally, you can also check and re-fetch comments explicitly for each post
                $http.get(`/posts/${post.id}/comments`) // Adjust this endpoint if needed
                    .then(function(commentResponse) {
                        post.comments = commentResponse.data; // Update the post's comments with the latest
                    }, function(commentError) {
                        console.error('Error fetching comments:', commentError);
                    });
            });
        }, function(error) {
            alert('Error fetching posts');
        });
};


    // Function to like a post
    $scope.likePost = function(postId) {
        $http.post('/posts/' + postId + '/like')
            .then(function(response) {
                // Find the updated post and apply the changes
                const updatedPost = response.data;
                
                // Find the post in $scope.posts and update it
                const post = $scope.posts.find(p => p.id === postId);
                if (post) {
                    post.liked_by_user = updatedPost.liked_by_user; // Update liked status
                    post.likes_count = updatedPost.likes_count; // Update likes count
                }
            }, function(error) {
                if (error.data.message === 'Cannot like the post again') {
                    alert('Cannot like the post again');
                } else {
                    alert('Error liking post');
                }
            });
    };
    
    
        // Function to unlike a post
        $scope.unlikePost = function(postId) {
            $http.delete('/posts/' + postId + '/like')
                .then(function(response) {
                    // Update the post data (liked status and like count)
                    const post = $scope.posts.find(p => p.id === postId);
                    if (post) {
                        post.liked_by_user = false;
                        post.likes_count = response.data.likes_count;
                    }
                }, function(error) {
                    alert('Error unliking post');
                });
        };
    // Function to add a comment to a post
    $scope.addComment = function(postId) {
        const comment = {
            content: $scope.newComment[postId] // Store comment based on postId
        };
        
        if (!comment.content || !comment.content.trim()) {
            alert('Please enter a comment');
            return;
        }

        $http.post('/posts/' + postId + '/comment', comment)
            .then(function(response) {
                // Find the post that was commented on and update its comments
                const post = $scope.posts.find(p => p.id === postId);
                if (post) {
                    post.comments = response.data.comments; // Update the comments array with the response
                }
                $scope.newComment[postId] = ''; // Clear the comment field for that specific post
            }, function(error) {
                alert('Error adding comment');
            });
    };
    $scope.editPost = function(post) {
        if (post.isEditing) {
            // Save edited post
            $http.put('/posts/' + post.id, { content: post.editingContent }).then(function(response) {
                // On success, update the post content and stop editing
                post.content = post.editingContent;
                post.isEditing = false;
            });
        } else {
            // Start editing
            post.isEditing = true;
            post.editingContent = post.content;  // Show the original content in the textarea
        }
    };
    $scope.editComment = function(postId, comment) {
        if (comment.isEditing) {
            // Save the edited comment
            $http.put(`/posts/${postId}/comments/${comment.id}`, { content: comment.editingContent }).then(function(response) {
                // Update the comment content and exit editing mode
                comment.content = comment.editingContent;
                comment.isEditing = false;
            });
        } else {
            // Enter editing mode
            comment.isEditing = true;
            comment.editingContent = comment.content; // Pre-fill the textarea with the current content
        }
    };

    // Delete Comment
    $scope.deleteComment = function(postId, commentId) {
        if (confirm("Are you sure you want to delete this comment?")) {
            $http.delete(`/posts/${postId}/comments/${commentId}`).then(function(response) {
                // Remove the comment from the post's comments array
                const post = $scope.posts.find(p => p.id === postId);
                post.comments = post.comments.filter(c => c.id !== commentId);
            });
        }
    };

    
    
    // Initialize by fetching posts
    $scope.getPosts();
});
