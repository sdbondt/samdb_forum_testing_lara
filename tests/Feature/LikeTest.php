<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void {
        parent::setUp();
        $this->seed();
        $this->user = User::factory()->create();
        $this->post = Post::factory()->create();
    }

    /** @test */
    public function user_can_like_a_post() {
        $this->signIn();
        $this->postJson(route('posts.like', $this->post->id));
        $this->assertDatabaseHas('likes', ['post_id' => $this->post->id]);
    }

    /** @test */
    public function guests_cannot_like_a_post() {
        $this->withoutExceptionHandling();
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->postJson(route('posts.like', $this->post->id));
    }

    /** @test */
    public function users_can_remove_a_like() {
        $this->signIn();
        $this->postJson(route('posts.like', $this->post->id));
        $this->postJson(route('posts.like', $this->post->id));
        $this->assertDatabaseMissing('likes', ['post_id' => $this->post->id]);
    }

    /** @test */
    public function like_type_must_be_enum_value() {
        $this->signIn();
        $this->postJson(route('posts.like', $this->post->id), [
            'type' => 'test'
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('type');
    }

    /** @test */
    public function adding_likes_creates_activity() {
        $this->signIn($this->user);
        $this->postJson(route('posts.like', $this->post->id));
        $this->assertDatabaseHas('activities', ['action' => 'POST_LIKED', 'user_id' => $this->user->id]);
    }
}
