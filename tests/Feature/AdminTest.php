<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void {
        parent::setUp();
        $this->seed();
        $this->admin = User::factory()->create([
            'email' => env('ADMIN_EMAIL')
        ]);
        $this->topic = Topic::factory()->create();
        $this->post = Post::factory()->create();
        $this->signIn($this->admin);
    }

    /** @test */
    public function admin_can_update_topics() {
        $attr = [
            'title' => 'updated'
        ];
        $this->patchJson(route('topics.update', $this->topic->id), $attr);
        $this->assertDatabaseHas('topics', $attr);
    }

    /** @test */
    public function admin_can_delete_topics() {
        $this->deleteJson(route('topics.destroy', $this->topic->id));
        $this->assertDatabaseMissing('topics', [
            'id' => $this->topic->id
        ]);
    }

    /** @test */
    public function admin_can_update_posts() {
        $attr = [
            'content' => 'test'
        ];
        $this->patchJson(route('posts.update', $this->post->id), $attr);
        $this->assertDatabaseHas('posts', $attr);
    }

    /** @test */
    public function admin_can_delete_posts() {
        $this->deleteJson(route('posts.destroy', $this->post->id));
        $this->assertDatabaseMissing('posts', ['id' => $this->post->id]);
    }
}
