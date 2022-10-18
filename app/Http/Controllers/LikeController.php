<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Validation\Rule;

class LikeController extends Controller
{
    public function store(Post $post) {
        $attr = request()->validate([
            'type' => ['sometimes', Rule::in(['like', 'dislike', 'funny', 'sad'])]
        ]);
        $type = $attr['type'] ?? 'like';
        $isExistingLike = Like::where([
            'user_id' => request()->user()->id,
            'post_id' => $post->id
        ])->first();
        if($isExistingLike ?? false) {
            request()->user()->removeLike($isExistingLike);
        } else {
            request()->user()->addLike([
                'post_id' => $post->id,
                'type' => $type
            ]);
        }
    }
}
