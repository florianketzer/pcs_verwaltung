<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TwoFactorEnforcementTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::create([
            'username' => 'tester',
            'email'    => 'tester@example.com',
            'name'     => 'Tester',
            'password' => bcrypt('secret'),
            // bewusst kein two_factor_secret
        ]);
    }

    public function test_login_without_2fa_is_blocked_when_enforced(): void
    {
        config(['fortify.enforce_two_factor' => true]); // Produktions-Default
        $user = $this->makeUser();

        $this->post('/login', ['email' => $user->email, 'password' => 'secret']);

        $this->assertGuest(); // 2FA-Pflicht greift -> kein Login
    }

    public function test_login_without_2fa_works_when_enforcement_disabled(): void
    {
        config(['fortify.enforce_two_factor' => false]); // lokaler Schalter
        $user = $this->makeUser();

        $this->post('/login', ['email' => $user->email, 'password' => 'secret']);

        $this->assertAuthenticatedAs($user); // Login nur mit Passwort
    }
}
