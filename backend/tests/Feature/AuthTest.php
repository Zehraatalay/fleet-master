<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_vehicles(): void
    {
        $response = $this->getJson('/api/vehicles');

        $response->assertUnauthorized();
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'TestPassword123!',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'TestPassword123!',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
                'token',
            ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
        ]);
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'CorrectPassword123!',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'WrongPassword123!',
        ]);

        $response->assertUnauthorized();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
    public function test_authenticated_user_can_access_vehicles(): void
{
    $user = User::factory()->create();

    $token = $user->createToken('fleet-master')->plainTextToken;

    $response = $this->withToken($token)
        ->getJson('/api/vehicles');

    $response->assertOk();
}

public function test_logout_revokes_current_token(): void
{
    $user = User::factory()->create();

    $token = $user->createToken('fleet-master')->plainTextToken;

    // 1. Logout işlemini gerçekleştir
    $logoutResponse = $this->withToken($token)
        ->postJson('/api/logout');

    $logoutResponse->assertOk()
        ->assertJson([
            'message' => 'Çıkış yapıldı.',
        ]);

    // 2. Token'ın veritabanından silindiğini doğrula
    $this->assertDatabaseCount('personal_access_tokens', 0);

    // 3. Test ortamındaki authentication guard durumunu sıfırla
    Auth::forgetGuards();

    // 4. Aynı token ile tekrar erişmeyi dene
    $this->withToken($token)
        ->getJson('/api/vehicles')
        ->assertUnauthorized();
}
public function test_login_is_rate_limited_after_five_attempts(): void
{
    RateLimiter::clear('127.0.0.1');

    User::factory()->create([
        'email' => 'test@example.com',
        'password' => 'CorrectPassword123!',
    ]);

    for ($i = 0; $i < 5; $i++) {
        $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'WrongPassword123!',
        ])->assertUnauthorized();
    }

    $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'WrongPassword123!',
    ])->assertStatus(429);
}
}