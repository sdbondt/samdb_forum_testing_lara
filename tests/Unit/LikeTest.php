<?php

namespace Tests\Unit;

use App\Models\Like;
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
        $this->like = Like::factory()->create();
    }

    /** @test */
    public function like_belongs_to_a_user() {
        $this->assertInstanceOf(User::class, $this->like->user);
    }

    /** @test */
    public function like_belongs_to_post() {
        $this->assertInstanceOf(Post::class, $this->like->post);
    }
}
