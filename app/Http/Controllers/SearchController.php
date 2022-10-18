<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Topic;
use App\Models\User;

class SearchController extends Controller
{
    public function index() {
        $attr = request()->validate([
            'q' => ['required', 'min:1']
        ]);

        $topics = Topic::latest()->filter($attr)->get();
        $posts = Post::latest()->filter($attr)->get();

        return [
            'posts' => $posts,
            'topics' => $topics,
        ];

    }
}
