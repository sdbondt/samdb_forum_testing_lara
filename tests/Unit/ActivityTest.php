<?php

namespace Tests\Unit;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use DatabaseTransactions;
    public function setUp(): void {
        parent::setUp();
        $this->seed();
    }

    /** @test */
    public function activity_belongs_to_a_user() {
        $this->withoutExceptionHandling();
        $this->signIn();
        $topic = Topic::factory()->create();
        $this->assertInstanceOf(User::class, $topic->section->activities->first()->user);
    }
}
