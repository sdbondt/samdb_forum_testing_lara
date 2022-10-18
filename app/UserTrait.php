<?php

namespace App;

use App\Models\Activity;
use App\Models\Like;
use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;

trait UserTrait {
    public function allTopics() {
        return Topic::latest('updated_at')->get();
    }

    public function likedPosts() {
        return Like::whereHas('post', function($query) {
            return $query->where('user_id', $this->id);
        })->get();
    }

    public function userActivities() {
        return Activity::where('user_id', $this->id)->orWhereHasMorph('subject', [Topic::class, Section::class, Post::class], function($query) {
            $query->where('user_id', $this->id);
            })->get();
    }
}