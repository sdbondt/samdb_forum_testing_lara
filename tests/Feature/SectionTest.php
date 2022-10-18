<?php

namespace Tests\Feature;

use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void {
        parent::setUp();
        $this->seed();
        $this->admin = User::factory()->create([
            'email' => env('ADMIN_EMAIL')
        ]);
    }

    /** @test */
    public function user_can_see_all_sections() {
        $this->signIn();
        $res = $this->getJson(route('sections.index'))->json();

        $this->assertCount(4, $res);
    }

    /** @test */
    public function guest_cannot_see_all_sections() {
        $this->withoutExceptionHandling();
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->getJson(route('sections.index'))->json();
    }

    /** @test */
    public function user_can_see_a_section() {
        $this->withoutExceptionHandling();
        $section = Section::all()->first();
        $this->signIn();
        $res = $this->getJson(route('sections.show', $section->id))->assertOk()->json();
    }

    /** @test */
    public function user_cannot_see_a_section() {
        $this->withoutExceptionHandling();
        $section = Section::all()->first();
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->getJson(route('sections.show', $section->id))->json();
    }

    /** @test */
    public function admin_can_add_sections() {
        $this->withoutExceptionHandling();
        $this->signIn($this->admin);
        $attr = [
            'subject' => 'test'
        ];
        $this->postJson(route('sections.store'), $attr)->json();
        
        $this->assertDatabaseHas('sections', $attr);
    }

    /** @test */
    public function subject_must_be_unique() {
        $this->withoutExceptionHandling();
        $this->signIn($this->admin);
        $this->expectException('Illuminate\Validation\ValidationException');
        $this->postJson(route('sections.store'), [
            'subject' => 'news'
        ]);
    }

    /** @test */
    public function only_admin_can_add_sections() {
        $this->withoutExceptionHandling();
        $this->signIn();
        $this->expectException('Illuminate\Auth\Access\AuthorizationException');
        $this->postJson(route('sections.store'), [
            'subject' => 'test'
        ]);
    }

    /** @test */
    public function guest_cannot_add_sections() {
        $this->withoutExceptionHandling();
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->postJson(route('sections.store'), [
            'subject' => 'test'
        ]);
    }

    /** @test */
    public function admin_can_update_sections() {
        $this->signIn($this->admin);
        $attr = [
            'subject' => 'update'
        ];
        $section = Section::all()->first();
        $this->patchJson(route('sections.update', $section->id), $attr);
        $this->assertDatabaseHas('sections', $attr);
    }

    /** @test */
    public function only_admin_can_update_section() {
        $this->withoutExceptionHandling();
        $this->signIn();
        $attr = [
            'subject' => 'update'
        ];
        $section = Section::all()->first();
        $this->expectException('Illuminate\Auth\Access\AuthorizationException');
        $this->patchJson(route('sections.update', $section->id), $attr);
    }

    /** @test */
    public function guests_cannot_update_sections() {
        $this->withoutExceptionHandling();
        $attr = [
            'subject' => 'update'
        ];
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->patchJson(route('sections.update', 1), $attr);
    }

    /** @test */
    public function admin_can_delete_sections() {
        $this->signIn($this->admin);
        $attr = [
            'subject' => 'delete'
        ];
        $section = $this->postJson(route('sections.store'), $attr)->json();
        $this->deleteJson(route('sections.destroy', $section['id']));
        $this->assertDatabaseMissing('sections', $attr);
    }

    /** @test */
    public function only_admin_can_delete_sections() {
        $this->withoutExceptionHandling();
        $this->signIn();
        $section = Section::all()->first();
        $this->expectException('Illuminate\Auth\Access\AuthorizationException');
        $this->deleteJson(route('sections.destroy', $section->id));
    }

    /** @test */
    public function guests_cannot_delete_sections() {
        $this->withoutExceptionHandling();
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->deleteJson(route('sections.destroy', 1));
    }
}
