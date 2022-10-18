<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Like;
use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PostTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void {
        parent::setUp();
        $this->seed();
        $this->post = Post::factory()->create();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function post_belongs_to_a_user() {
        $this->assertInstanceOf(User::class, $this->post->user);
    }

    /** @test */
    public function post_belongs_to_a_topic() {
        $this->assertInstanceOf(Topic::class, $this->post->topic);
    }

    /** @test */
    public function post_can_have_likes() {
        $this->user->addLike([
            'post_id' => $this->post->id,
        ]);
        $this->assertCount(1, $this->post->likes);
    }

    /** @test */
    public function post_can_have_activities() {
        Like::factory()->create([
            'post_id' => $this->post->id
        ]);
        $this->assertCount(1, $this->post->activities);
        $this->assertInstanceOf(Activity::class, $this->post->activities->first());
    }
}
