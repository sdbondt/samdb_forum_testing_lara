<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Topic;

class PostController extends Controller
{
    public function store(Topic $topic) {
        $attr = request()->validate([
            'content' => ['required', 'min:1', 'max:10000']
        ]);
        $attr['topic_id'] = $topic->id;
        return request()->user()->addPost($attr);
    }

    public function show(Post $post) {
        return $post->load('user', 'likes');
    }

    public function index(Topic $topic) {
        return $topic->posts;
    }

    public function update(Post $post) {
        $this->authorize('update', [Post::class, $post]);
        $attr = request()->validate([
            'content' => ['required', 'min:1', 'max:10000']
        ]);
        $post =  $post->update($attr);
        return $post;
    }

    public function destroy(Post $post) {
        $this->authorize('delete', [Post::class, $post]);
        $post->delete();
    }
}
