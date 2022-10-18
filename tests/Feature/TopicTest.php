<?php

namespace Tests\Feature;

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
        $this->section = Section::factory()->create();
        $this->topic = Topic::factory()->create();
        $this->user = User::factory()->create();
    }   

    /** @test */
    public function user_can_create_a_topic() {
        $this->signIn();
        $attr = [
            'title' => 'test',
            'body' => 'test'
        ];
        $this->postJson(route('topics.store', $this->section->id), $attr);

        $this->assertDatabaseHas('topics', $attr);
    }

    /** @test */
    public function unauthenticated_user_cannot_create_a_topic() {
        $this->withoutExceptionHandling();
        $attr = [
            'title' => 'test',
            'body' => 'test'
        ];
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->postJson(route('topics.store', $this->section->id), $attr);
    }

    /** @test */
    public function topics_need_title_and_body() {
        $this->signIn();
        $this->postJson(route('topics.store', $this->section->id), [
            'body' => 'test'
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('title');

        $this->postJson(route('topics.store', $this->section->id), [
            'title' => 'test',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('body');
    }

    /** @test */
    public function user_can_see_a_topic() {
        $this->withoutExceptionHandling();
        $this->signIn();
        $res = $this->getJson(route('topics.show', $this->topic->id))->json();

        $this->assertEquals($res['title'], $this->topic->title);
    }

    /** @test */
    public function guest_cannot_see_a_topic() {
        $this->withoutExceptionHandling();
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->getJson(route('topics.show', $this->topic->id))->json();
    }

    /** @test */
    public function user_can_see_all_topics_for_a_section() {
        $this->withoutExceptionHandling();
        $this->signIn();
        $res = $this->getJson(route('topics.index', $this->topic->section->id))->json();

        $this->assertCount(1, $res);
    }

    /** @test */
    public function guest_cannot_see_all_sections_topics() {
        $this->withoutExceptionHandling();
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->getJson(route('topics.index', $this->topic->section->id))->json();
    }

    /** @test */
    public function user_can_update_their_topic() {
        $this->signIn($this->topic->user);
        $updatedTopic = [
            'title' => 'updated',
            'body' => 'updated'
        ];
        $this->patchJson(route('topics.update', $this->topic->id), $updatedTopic)->json();

        $this->assertDatabaseHas('topics', $updatedTopic);
    }

    /** @test */
    public function guest_cannot_update_users_topic() {
        $this->withoutExceptionHandling();
        $updatedTopic = [
            'title' => 'updated',
            'body' => 'updated'
        ];
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->patchJson(route('topics.update', $this->topic->id), $updatedTopic)->json();
    }

    /** @test */
    public function other_user_cannot_update_users_topic() {
        $this->withoutExceptionHandling();
        $updatedTopic = [
            'title' => 'updated',
            'body' => 'updated'
        ];
        $this->signIn();
        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
        $this->patchJson(route('topics.update', $this->topic->id), $updatedTopic)->json();
    }

    /** @test */
    public function user_can_delete_topic() {
        $this->signIn($this->topic->user);
        $this->deleteJson(route('topics.destroy', $this->topic->id));

        $this->assertDatabaseMissing('topics', [
            'title' => $this->topic->title,
            'body' => $this->topic->body
        ]);
    }

    /** @test */
    public function guest_cannot_delete_topic() {
        $this->withoutExceptionHandling();
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->deleteJson(route('topics.destroy', $this->topic->id));
    }

    /** @test */
    public function other_user_cannot_delete_topic() {
        $this->withoutExceptionHandling();
        $this->signIn();
        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
        $this->deleteJson(route('topics.destroy', $this->topic->id));
    }

    /** @test */
    public function adding_topic_creates_activity() {
        $this->withoutExceptionHandling();
        $this->signIn($this->user);
        $attr = [
            'title' => 'test',
            'body' => 'test'
        ];
        $this->postJson(route('topics.store', $this->section->id), $attr);

        $this->assertDatabaseHas('activities', ['action' => 'TOPIC_CREATED', 'user_id' => $this->user->id]);

    }
    
}
