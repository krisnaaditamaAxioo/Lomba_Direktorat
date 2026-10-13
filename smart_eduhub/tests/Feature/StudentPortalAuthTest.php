<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentPortalAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('academic_year', '2025-2026');
    }

    // ---------------------------------------------------------------
    // Login siswa dengan PIN
    // ---------------------------------------------------------------
    public function test_student_can_login_with_correct_pin(): void
    {
        $student = Student::factory()->create([
            'username' => 'ahmad.fauzi',
            'pin'      => '123456',
            'status'   => 0,
        ]);

        $this->post(route('siswa.login'), [
            'username' => 'ahmad.fauzi',
            'pin'      => '123456',
        ])->assertRedirect(route('siswa.dashboard'));

        $this->assertAuthenticatedAs($student, 'student');
    }

    public function test_student_login_fails_with_wrong_pin(): void
    {
        Student::factory()->create([
            'username' => 'ahmad.fauzi',
            'pin'      => '123456',
            'status'   => 0,
        ]);

        $this->post(route('siswa.login'), [
            'username' => 'ahmad.fauzi',
            'pin'      => '999999',
        ])->assertSessionHasErrors('username');

        $this->assertGuest('student');
    }

    public function test_student_login_fails_with_unknown_username(): void
    {
        $this->post(route('siswa.login'), [
            'username' => 'tidak_ada',
            'pin'      => '123456',
        ])->assertSessionHasErrors('username');

        $this->assertGuest('student');
    }

    public function test_student_can_login_using_nisn(): void
    {
        $student = Student::factory()->create([
            'nisn'   => '9876543210',
            'pin'    => '111222',
            'status' => 0,
        ]);

        $this->post(route('siswa.login'), [
            'username' => '9876543210', // NISN dipakai sebagai username
            'pin'      => '111222',
        ])->assertRedirect(route('siswa.dashboard'));

        $this->assertAuthenticatedAs($student, 'student');
    }

    // ---------------------------------------------------------------
    // Proteksi route siswa: harus auth dengan guard 'student'
    // ---------------------------------------------------------------
    public function test_student_dashboard_requires_student_auth(): void
    {
        // Tanpa login redirect ke halaman login (URL bisa /login atau /siswa/login)
        $this->get(route('siswa.dashboard'))
            ->assertRedirect();
    }

    public function test_student_points_page_requires_student_auth(): void
    {
        $this->get(route('siswa.points'))
            ->assertRedirect();
    }

    public function test_student_attendance_page_requires_student_auth(): void
    {
        $this->get(route('siswa.attendance'))
            ->assertRedirect();
    }

    // ---------------------------------------------------------------
    // Isolasi guard: admin (web) tidak bisa akses portal siswa
    // ---------------------------------------------------------------
    public function test_admin_user_cannot_access_student_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // actingAs dengan guard web (admin) tidak memenuhi guard 'student' → redirect
        $this->actingAs($admin)
            ->get(route('siswa.dashboard'))
            ->assertRedirect();
    }

    // ---------------------------------------------------------------
    // Logout siswa
    // ---------------------------------------------------------------
    public function test_student_logout_clears_session(): void
    {
        $student = Student::factory()->create(['status' => 0]);

        $this->actingAs($student, 'student')
            ->post(route('siswa.logout'))
            ->assertRedirect();

        $this->assertGuest('student');
    }

    // ---------------------------------------------------------------
    // Siswa yang sudah login tidak perlu login lagi
    // ---------------------------------------------------------------
    public function test_logged_in_student_redirected_from_login_page(): void
    {
        $student = Student::factory()->create(['status' => 0]);

        $this->actingAs($student, 'student')
            ->get(route('siswa.login'))
            ->assertRedirect(route('siswa.dashboard'));
    }
}
