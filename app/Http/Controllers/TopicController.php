<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Topic;

class TopicController extends Controller
{
    public function store(Section $section) {
        $attr = request()->validate([
            'title' => ['required', 'min:1', 'max:255'],
            'body' => ['required'],
        ]);
        $attr['section_id'] = $section->id;
        $topic = request()->user()->addTopic($attr);
        return $topic;
    }

    public function show(Topic $topic) {
        return $topic->load('posts');
    }

    public function index(Section $section) {
        return $section->topics;
    }

    public function destroy(Topic $topic) {
        $this->authorize('delete', [Topic::class, $topic]);
        $topic->delete();
    }

    public function update(Topic $topic) {
        $this->authorize('update', [Topic::class, $topic]);
        $attr = request()->validate([
            'title' => ['sometimes', 'min:1', 'max:255'],
            'body' => ['sometimes']
        ]);
        $topic->update($attr);
        return $topic;
    }
}
