<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('academic_year', '2025-2026');
    }

    // ---------------------------------------------------------------
    // Login Admin
    // ---------------------------------------------------------------
    public function test_admin_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'role'     => 'admin',
            'email'    => 'admin@test.id',
            'password' => Hash::make('password123'),
        ]);

        $this->post(route('login.admin.submit'), [
            'email'    => 'admin@test.id',
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'role'     => 'admin',
            'email'    => 'admin@test.id',
            'password' => Hash::make('benar123'),
        ]);

        $this->post(route('login.admin.submit'), [
            'email'    => 'admin@test.id',
            'password' => 'salah999',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_login_fails_with_missing_fields(): void
    {
        $this->post(route('login.admin.submit'), [])
            ->assertSessionHasErrors(['email', 'password']);
    }

    // ---------------------------------------------------------------
    // Pemisahan role: guru tidak bisa pakai login admin, dan sebaliknya
    // ---------------------------------------------------------------
    public function test_guru_cannot_login_via_admin_route(): void
    {
        User::factory()->create([
            'role'     => 'guru',
            'email'    => 'guru@test.id',
            'password' => Hash::make('password123'),
        ]);

        $this->post(route('login.admin.submit'), [
            'email'    => 'guru@test.id',
            'password' => 'password123',
        ]);

        $this->assertGuest();
    }

    public function test_admin_cannot_login_via_guru_route(): void
    {
        User::factory()->create([
            'role'     => 'admin',
            'email'    => 'admin@test.id',
            'password' => Hash::make('password123'),
        ]);

        $this->post(route('login.guru.submit'), [
            'email'    => 'admin@test.id',
            'password' => 'password123',
        ]);

        $this->assertGuest();
    }

    // ---------------------------------------------------------------
    // Redirect berdasarkan role setelah login
    // ---------------------------------------------------------------
    public function test_guru_redirected_to_guru_dashboard(): void
    {
        User::factory()->create([
            'role'     => 'guru',
            'email'    => 'guru@test.id',
            'password' => Hash::make('password123'),
        ]);

        $this->post(route('login.guru.submit'), [
            'email'    => 'guru@test.id',
            'password' => 'password123',
        ])->assertRedirect(route('guru.dashboard'));
    }

    public function test_teller_redirected_to_finance_dashboard(): void
    {
        User::factory()->create([
            'role'     => 'teller',
            'email'    => 'teller@test.id',
            'password' => Hash::make('password123'),
        ]);

        $this->post(route('login.admin.submit'), [
            'email'    => 'teller@test.id',
            'password' => 'password123',
        ])->assertRedirect(route('admin.finance.dashboard'));
    }

    // ---------------------------------------------------------------
    // Logout
    // ---------------------------------------------------------------
    public function test_logout_clears_session_and_redirects(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    // ---------------------------------------------------------------
    // Halaman login tidak bisa diakses oleh user yang sudah login
    // ---------------------------------------------------------------
    public function test_authenticated_user_cannot_access_login_page(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)
            ->get(route('login.admin'))
            ->assertRedirect(); // redirect ke dashboard
    }

    // ---------------------------------------------------------------
    // Impersonate: hanya admin yang bisa, dan hanya ke guru
    // ---------------------------------------------------------------
    public function test_admin_can_impersonate_guru(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru  = User::factory()->create(['role' => 'guru']);

        $this->actingAs($admin)
            ->post(route('impersonate.guru', $guru))
            ->assertRedirect(route('guru.dashboard'));

        $this->assertAuthenticatedAs($guru);
    }

    public function test_non_admin_cannot_impersonate(): void
    {
        $guru1 = User::factory()->create(['role' => 'guru']);
        $guru2 = User::factory()->create(['role' => 'guru']);

        $this->actingAs($guru1)
            ->post(route('impersonate.guru', $guru2))
            ->assertForbidden();
    }

    public function test_admin_cannot_impersonate_another_admin(): void
    {
        $admin1 = User::factory()->create(['role' => 'admin']);
        $admin2 = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin1)
            ->post(route('impersonate.guru', $admin2))
            ->assertStatus(400); // admin2 bukan guru
    }

    // ---------------------------------------------------------------
    // Halaman yang butuh auth redirect ke login jika belum login
    // ---------------------------------------------------------------
    public function test_dashboard_requires_authentication(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }
}
