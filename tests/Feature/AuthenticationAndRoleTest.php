<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AuthenticationAndRoleTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_dapat_login_dengan_kredensial_valid()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role' => 'penumpang'
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function user_tidak_bisa_login_dengan_password_salah()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'salah',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    #[Test]
    public function admin_dapat_mengakses_halaman_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Admin');
    }

    #[Test]
    public function penumpang_tidak_dapat_mengakses_halaman_admin()
    {
        $penumpang = User::factory()->create(['role' => 'penumpang']);

        $response = $this->actingAs($penumpang)->get('/admin');

        // Middleware AdminMiddleware harusnya melempar 403
        $response->assertStatus(403);
    }
}