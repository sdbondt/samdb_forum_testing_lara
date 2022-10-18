<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Topic;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use DatabaseTransactions;
   public function setUp(): void {
        parent::setUp();
        $this->seed();
   }

   /** @test */
   public function user_can_search_posts() {
    $this->signIn();
    Post::factory()->create([
        'content' => 'test'
    ]);
    $res = $this->getJson(route('search', [
        'q' => 'tes'
    ]))->json();
    
    $this->assertCount(1, $res['posts']);
    $this->assertEquals('test', $res['posts'][0]['content']);
   }

   /** @test */
   public function user_can_search_topics() {
    $this->withoutExceptionHandling();
    $this->signIn();
    Topic::factory()->create([
        'body' => 'test'
    ]);

    $res = $this->getJson(route('search', [
        'q' => 'tes'
    ]))->json();
    
    $this->assertCount(1, $res['topics']);
    $this->assertEquals('test', $res['topics'][0]['body']);
   }

   /** @test */
   public function search_term_is_required() {
    $this->signIn();
    $res = $this->getJson(route('search'))
    ->assertUnprocessable()
    ->assertJsonValidationErrors('q');
   }
}
