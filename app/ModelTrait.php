<?php

namespace App;

use App\Models\Activity;
use App\Models\Like;
use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;

trait ModelTrait {

    public function scopeFilter($query, array $filters) {
        if($filters['q'] ?? false) {
            if(class_basename($this) == 'Topic') {
                 $query->where(fn($query) => $query->where('title', 'like', '%' . $filters['q'] . '%')->orWhere('body', 'like', '%' . $filters['q'] . '%'));
            }

            if(class_basename($this) == 'Post') {
                $query->where(fn($query) => $query->where('content', 'like', '%' . $filters['q'] . '%'));
            }

        }
    }

    public function user() {
        if(in_array(class_basename($this), ['Post', 'Topic', 'Like', 'Activity'])) {
            return $this->belongsTo(User::class);
        }
    }

    public function section() {
        if(in_array(class_basename($this), ['Topic', 'Activity'])) {
            return $this->belongsTo(Section::class);
        }
    }
    
    public function posts() {
        if(in_array(class_basename($this), ['User', 'Topic'])) {
            return $this->hasMany(Post::class);
        }
    }

    public function post() {
        if(in_array(class_basename($this), ['Like', 'Activity'])) {
            return $this->belongsTo(Post::class);
        }
    }

    public function subject() {
        if(class_basename($this) == 'Activity') {
            return $this->morphTo();
        }
    }
   
    public function addPost($val) {
        if(in_array(class_basename($this), ['User', 'Topic'])) {
            $this->posts()->create([
                'content' => $val['content'],
                'user_id' => class_basename($this) == 'User' ? $this->id: $val['user_id'],
                'topic_id' => class_basename($this) == 'Topic' ? $this->id: $val['topic_id']
            ]);
        } 
    }

    public function topic() {
        if(in_array(class_basename($this), ['Post', 'Activity'])) {
            return $this->belongsTo(Topic::class);
        }  
    }

    public function topics() {
        if(in_array(class_basename($this), ['User', 'Section'])) {
            return $this->hasMany(Topic::class);
        }
    }

    public function addTopic($val) {
        if(in_array(class_basename($this), ['User', 'Section'])) {
            return $this->topics()->create([
                'title' => $val['title'],
                'body' => $val['body'],
                'section_id' => class_basename($this) == 'Section' ? $this->id: $val['section_id'],
                'user_id' => class_basename($this) == 'User' ? $this->id: $val['user_id']
            ]);
        }
    }

    public function likes() {
        if(in_array(class_basename($this), ['User', 'Post'])) {
            return $this->hasMany(Like::class);
        }
    }

    public function addLike($val) {
        if(class_basename($this) == 'User') {
            $this->likes()->create([
                'type' => $val['type'] ?? 'like',
                'post_id' => $val['post_id']
            ]);
        }
    }

    public function removeLike($like) {
        if(class_basename($this) == 'User') {
            $like->delete();
        }  
    }

    public function activities() {
        if(class_basename($this) == 'User') {
            return $this->hasMany(Activity::class);
        }

        if(in_array(class_basename($this), ['Section', 'Topic', 'Post'])) {
            return $this->morphMany(Activity::class, 'subject');
        }
    }

    public function getActivityType() {
        if(class_basename($this) == 'Section') {
            return Activity::TOPIC_CREATED;
        } else if(class_basename($this) == 'Topic'){
            return Activity::POST_CREATED;
        } else {
            return Activity::POST_LIKED;
        }
    }


    public function createActivity($user) {
        if(in_array(class_basename($this), ['Section', 'Topic', 'Post'])) {
            $this->activities()->create([
                'user_id' => $user->id,
                'action' => $this->getActivityType()
            ]);
        }
    }
}