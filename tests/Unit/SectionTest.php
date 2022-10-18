<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Section;
use App\Models\Topic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void {
        parent::setUp();
        $this->section = Section::factory()->create();
    }

    /** @test */
    public function section_can_add_topics() {
        $this->withoutExceptionHandling();
        $this->signIn();
        $attr = [
            'title' => 'test',
            'body' => 'test',
            'user_id' => auth()->user()->id
        ];

        $this->section->addTopic($attr);
        $this->assertDatabaseHas('topics', $attr);
    }

    /** @test */
    public function section_can_have_topics() {
        $this->signIn();
        $attr = [
            'title' => 'test',
            'body' => 'test',
            'user_id' => auth()->user()->id
        ];

        $this->section->addTopic($attr);
        $this->assertInstanceOf(Topic::class, $this->section->topics->first());
    }

    /** @test */
    public function section_can_have_activities() {
        Topic::factory()->create([
            'section_id' => $this->section->id
        ]);
        $this->assertCount(1, $this->section->activities);
        $this->assertInstanceOf(Activity::class, $this->section->activities->first());
    }
}
