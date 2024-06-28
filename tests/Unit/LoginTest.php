<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private $validUserData = [
        'email' => 'test@example.com',
        'password' => 'password',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        // Creating a user here to be used by multiple tests ensures that each test method is still responsible for only one aspect of the functionality.
        User::factory()->create([
            'email' => $this->validUserData['email'],
            'password' => bcrypt($this->validUserData['password']),
        ]);
    }

    public function test_login_success()
    {
        $response = $this->attemptLogin($this->validUserData);

        $response->assertStatus(200)
            ->assertJsonFragment(['email' => $this->validUserData['email']]);
    }

    public function test_login_failure_due_to_invalid_credentials()
    {
        $userData = $this->validUserData;
        $userData['password'] = 'invalidpassword';

        $response = $this->attemptLogin($userData);

        $response->assertStatus(401)
            ->assertJson(['message' => __('messages.login_error')]);
    }

    // public function test_logout_success()
    // {
    //     $this->actingAs(User::first());

    //     $response = $this->postJson('/api/logout');

    //     $response->assertStatus(200)
    //         ->assertJson(['message' => __('messages.logout_success')]);
    // }

    private function attemptLogin(array $userData)
    {
        return $this->postJson('/api/login', [
            'email' => $userData['email'],
            'password' => $userData['password'],
        ]);
    }
}
