<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Like;
use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;
    public function setUp(): void {
        parent::setUp();
        $this->seed();
        $this->user = User::factory()->create();
        $this->topic = Topic::factory()->create();
        $this->post = Post::factory()->create();
    }

    /** @test */
    public function user_can_have_topics() {
        Topic::factory()->create([
            'user_id' => $this->user->id
        ]);

        $this->assertCount(1, $this->user->topics);
        $this->assertInstanceOf(Topic::class, $this->user->topics->first());
    }   
    
    /** @test */
    public function user_can_add_topics() {
        $topicContent = [
            'title' => 'title',
            'body' => 'body',
            'section_id' => Section::all()->first()->id
        ];
        $this->user->addTopic($topicContent);

        $this->assertDatabaseHas('topics', $topicContent);
        $this->assertCount(1, $this->user->topics);
    }

    /** @test */
    public function user_can_add_posts() {
        $this->user->addPost([
            'content' => 'test',
            'topic_id' => $this->topic->id
        ]);
        $this->assertDatabaseHas('posts', ['content' => 'test']);
    }

    /** @test */
    public function user_can_have_posts() {
        $this->user->addPost([
            'content' => 'test',
            'topic_id' => $this->topic->id
        ]);
        $this->assertInstanceOf(Post::class, $this->user->posts->first());
    }

    /** @test */
    public function user_can_add_likes() {
        $this->user->addLike([
            'post_id' => $this->post->id
        ]);
        $this->assertDatabaseHas('likes', [
            'post_id' => $this->post->id,
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function user_can_have_likes() {
        $this->user->addLike([
            'post_id' => $this->post->id
        ]);
        $this->assertCount(1, $this->user->likes);
        $this->assertInstanceOf(Like::class, $this->user->likes->first());
    }

    /** @test */
    public function user_can_remove_likes() {
        $this->user->addLike([
            'post_id' => $this->post->id
        ]);
        $this->user->removeLike($this->post);
        $this->assertDatabaseMissing('likes', [
            'post_id' => $this->post->id,
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function user_can_have_liked_posts() {
        $this->withoutExceptionHandling();
        Like::factory()->create([
            'post_id' => $this->post->id
        ]);
        $this->assertCount(1, $this->post->user->likedPosts());
    }

    /** @test */
    public function user_can_have_activities() {
        Post::factory()->create([
            'user_id' => $this->user->id
        ]);
        $this->assertCount(1, $this->user->activities);
        $this->assertInstanceOf(Activity::class, $this->user->activities->first());
    }

    /** @test */
    public function user_can_see_all_related_activities() {
        $user = User::factory()->create();
        $topic = Topic::factory()->create([
            'user_id' => $user->id
        ]);
        Post::factory()->create([
            'topic_id' => $topic->id,
        ]);
        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);
        Like::factory()->create([
            'post_id' => $post->id
        ]);
        $this->assertCount(4, $user->userActivities());
    }
}
