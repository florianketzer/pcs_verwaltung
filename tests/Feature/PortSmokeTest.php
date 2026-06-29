<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_pages_render(): void
    {
        $user = User::create([
            'username' => 'tester',
            'email' => 'tester@example.com',
            'name' => 'Tester',
            'password' => bcrypt('secret'),
        ]);

        $routes = [
            '/',
            '/customers',
            '/materials',
            '/servicecontracts',
            '/importcsv',
            '/user/profile',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($user)->get($route);
            $this->assertContains(
                $response->getStatusCode(),
                [200, 302],
                "Route {$route} returned {$response->getStatusCode()}"
            );
            if ($response->getStatusCode() === 200) {
                fwrite(STDERR, "OK 200  {$route}\n");
            } else {
                fwrite(STDERR, "REDIR   {$route} -> ".$response->headers->get('Location')."\n");
            }
        }
    }
}
