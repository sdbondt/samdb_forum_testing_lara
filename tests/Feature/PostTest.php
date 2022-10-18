<?php

namespace Tests\Feature;

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
        $this->topic = Topic::factory()->create();
        $this->post = Post::factory()->create();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function user_can_add_posts() {
        $this->signIn();
        $attr = ['content' => 'test'];
        $this->postJson(route('posts.store', $this->topic->id), $attr);
        $this->assertDatabaseHas('posts', $attr);
    }

    /** @test */
    public function guests_cannot_add_posts() {
        $this->withoutExceptionHandling();
        $attr = ['content' => 'test'];
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->postJson(route('posts.store', $this->topic->id), $attr);
    }

    /** @test */
    public function posts_need_content() {
        $this->signIn();
        $this->postJson(route('posts.store', $this->topic->id))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('content');
    }

    /** @test */
    public function user_can_see_a_post() {
        $this->signIn();
        $res = $this->getJson(route('posts.show', $this->post->id))->json();
        $this->assertEquals($res['content'], $this->post->content);
    }

    /** @test */
    public function guests_cannot_see_a_post() {
        $this->withoutExceptionHandling();
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->getJson(route('posts.show', $this->post->id));
    }

    /** @test */
    public function user_can_see_topics_posts() {
        $this->signIn();
        $this->topic->addPost([
            'content' => 'test',
            'user_id' => auth()->user()->id
        ]);
        $res = $this->getJson(route('posts.index', $this->topic->id))->json();
        $this->assertCount(1, $res);
        $this->assertEquals('test', $res[0]['content']);
    }

    /** @test */
    public function guests_cannot_see_topics_posts() {
        $this->withoutExceptionHandling();
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->getJson(route('posts.index', $this->topic->id))->json();
    }

    /** @test */
    public function users_can_update_their_post() {
        $this->signIn($this->post->user);
        $attr = [
            'content' => 'update'
        ];
        $this->patchJson(route('posts.update', $this->post->id), $attr);
        $this->assertDatabaseHas('posts', $attr);
    }

    /** @test */
    public function updated_posts_need_content() {
        $this->signIn($this->post->user);
        $this->patchJson(route('posts.update', $this->post->id))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('content');
    }

    /** @test */
    public function guests_cannot_update_users_posts() {
        $this->withoutExceptionHandling();
        $this->signIn();
        $attr = [
            'content' => 'update'
        ];
        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
        $this->patchJson(route('posts.update', $this->post->id), $attr);
    }

    /** @test */
    public function users_can_update_their_posts() {
        $this->withoutExceptionHandling();
        $this->signIn($this->post->user);
        $this->deleteJson(route('posts.destroy', $this->post->id));
        $this->assertEquals(1,1);
    }

    /** @test */
    public function guests_cannot_delete_users_posts() {
        $this->withoutExceptionHandling();
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->deleteJson(route('posts.destroy', $this->post->id));
    }

    /** @test */
    public function other_users_cannot_delete_users_posts() {
        $this->withoutExceptionHandling();
        $this->signIn();
        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
        $this->deleteJson(route('posts.destroy', $this->post->id));
    }

    /** @test */
    public function adding_post_creates_activity() {
        $this->signIn($this->user);
        $attr = ['content' => 'test'];
        $this->postJson(route('posts.store', $this->topic->id), $attr);
        $this->assertDatabaseHas('activities', ['action' => 'POST_CREATED', 'user_id' => $this->user->id]);
    }
}
