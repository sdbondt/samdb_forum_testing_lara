<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TopicTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void {
        parent::setUp();
        $this->seed();
        $this->topic = Topic::factory()->create();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function topic_belongs_to_a_user() {
        $this->assertInstanceOf(User::class, $this->topic->user);
    }

    /** @test */
    public function topic_belongs_to_a_section() {
        $this->withoutExceptionHandling();
        $this->assertInstanceOf(Section::class, $this->topic->section);
    }

    /** @test */
    public function topic_can_add_a_post() {
        $this->topic->addPost([
            'content' => 'test',
            'user_id' => $this->user->id
        ]);
        $this->assertDatabaseHas('posts', ['content' => 'test']);
    }

    /** @test */
    public function topic_can_have_posts() {
        $this->topic->addPost([
            'content' => 'test',
            'user_id' => $this->user->id
        ]);
        $this->assertInstanceOf(Post::class, $this->topic->posts->first());
    }

    /** @test */
    public function topic_can_have_activities() {
        Post::factory()->create([
            'topic_id' => $this->topic->id
        ]);
        $this->assertCount(1, $this->topic->activities);
        $this->assertInstanceOf(Activity::class, $this->topic->activities->first());
    }
}
