<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_signup() {
        $res = $this->postJson(route('user.signup'), [
            'password' => env('TEST_PASSWORD'),
            'username' => 'test',
            'email' => 'test@hotmail.com'
        ]);

        $this->assertDatabaseHas('users', ['email' => 'test@hotmail.com']);
        $this->assertArrayHasKey('token', $res->json());
    }

    /** @test */
    public function user_needs_email_username_password() {
        $this->postJson(route('user.signup'), [
            'username' => 'test',
            'email' => 'test@hotmail.com'
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);

        $this->postJson(route('user.signup'), [
            'password' => env('TEST_PASSWORD'),
            'email' => 'test@hotmail.com'
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['username']);

        $this->postJson(route('user.signup'), [
            'password' => env('TEST_PASSWORD'),
            'username' => 'test',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function user_needs_unique_username_and_email() {
        $this->postJson(route('user.signup'), [
            'password' => env('TEST_PASSWORD'),
            'username' => 'test',
            'email' => 'test@hotmail.com'
        ]);

        $this->postJson(route('user.signup'), [
            'password' => env('TEST_PASSWORD'),
            'username' => 'test',
            'email' => 'test2@hotmail.com'
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['username']);

        $this->postJson(route('user.signup'), [
            'password' => env('TEST_PASSWORD'),
            'username' => 'test2',
            'email' => 'test@hotmail.com'
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function password_must_be_7_characters_contain_uppercase_lowercase_and_digit() {
        $this->postJson(route('user.signup'), [
            'password' => 'test12',
            'username' => 'test',
            'email' => 'test@hotmail.com'
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
          
        $this->postJson(route('user.signup'), [
            'password' => 'test123',
            'username' => 'test',
            'email' => 'test@hotmail.com'
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);

        $this->postJson(route('user.signup'), [
            'password' => 'TEST123',
            'username' => 'test',
            'email' => 'test@hotmail.com'
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);

        $this->postJson(route('user.signup'), [
            'password' => 'testTEST',
            'username' => 'test',
            'email' => 'test@hotmail.com'
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
        
    }

    /** @test */
    public function user_can_login() {
        $user = User::factory()->create([
            'password' => env('TEST_PASSWORD')
        ]);

        $res = $this->postJson(route('user.login'), [
            'email' => $user->email,
            'password' => env('TEST_PASSWORD')
        ]);

        $this->assertArrayHasKey('token', $res->json());
    }

    /** @test */
    public function user_needs_valid_email_and_password() {
        $user = User::factory()->create([
            'password' => env('TEST_PASSWORD')
        ]);

        $res = $this->postJson(route('user.login'), [
            'email' => $user->email,
            'password' => 'wrongpassword'
        ])->json();
        $this->assertEquals('Invalid credentials.', $res);

        $res = $this->postJson(route('user.login'), [
            'email' => 'test@hotmail.com',
            'password' => env('TEST_PASSWORD')
        ])->json();
        $this->assertEquals('Invalid credentials.', $res);
    }

    /** @test */
    public function user_can_logout() {
        $user = User::factory()->create([
            'password' => env('TEST_PASSWORD')
        ]);

        Sanctum::actingAs($user);
        $res = $this->postJson(route('user.logout'))->json();
        $this->assertEquals('You are now logged out.', $res);
    }


    /** @test */
    public function only_authenticated_users_can_logout() {
        $this->withoutExceptionHandling();
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $res = $this->postJson(route('user.logout'));
    }
}
