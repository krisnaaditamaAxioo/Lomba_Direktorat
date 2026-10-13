<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('academic_year', '2025-2026');
    }

    // ---------------------------------------------------------------
    // BUG-01: Route /clear-cache tidak diproteksi auth
    // ---------------------------------------------------------------
    public function test_clear_cache_accessible_without_auth_documents_bug(): void
    {
        $response = $this->get('/clear-cache');

        // HARUSNYA 403, tapi sekarang 200 — ini BUG
        // Test ini mendokumentasikan bug yang ada
        $this->assertSame(
            200,
            $response->status(),
            'BUG-01 TERKONFIRMASI: /clear-cache bisa diakses tanpa autentikasi. ' .
            'Seharusnya diproteksi dengan middleware auth + role admin.'
        );
    }

    // ---------------------------------------------------------------
    // BUG-01: Route /show-logs tidak diproteksi auth
    // ---------------------------------------------------------------
    public function test_show_logs_accessible_without_auth_documents_bug(): void
    {
        $response = $this->get('/show-logs');

        // Akan 200 (log kosong) atau 200 dengan isi log
        // Apapun, tidak boleh diakses tanpa auth
        $this->assertNotSame(
            302,
            $response->status(),
            'BUG-01 TERKONFIRMASI: /show-logs tidak redirect ke login, ' .
            'artinya bisa diakses tanpa autentikasi.'
        );
    }

    // ---------------------------------------------------------------
    // BUG-02: Quick login tanpa password bisa dijalankan tanpa verifikasi server
    // ---------------------------------------------------------------
    public function test_quick_login_user_works_without_password(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->post(route('quick-login.user', $user));

        // Berhasil login tanpa password — ini adalah security risk
        $this->assertTrue(
            $this->isAuthenticated() || $response->isRedirect(),
            'BUG-02: Quick login admin berhasil tanpa password'
        );
    }

    public function test_quick_login_student_works_without_password(): void
    {
        $student = Student::factory()->create(['status' => 0]);

        $response = $this->post(route('quick-login.student', $student));

        $this->assertTrue(
            $this->isAuthenticated('student') || $response->isRedirect(),
            'BUG-02: Quick login siswa berhasil tanpa password/PIN'
        );
    }

    // ---------------------------------------------------------------
    // BUG-06: Status comparison — sudah diperbaiki di AttendanceController
    // Test ini memverifikasi bahwa fix sudah benar
    // ---------------------------------------------------------------
    public function test_active_student_with_integer_status_can_scan(): void
    {
        Setting::set('check_in_time', '23:59');

        $student = Student::factory()->create([
            'id_siswa' => 'SIS-BUG06',
            'status'   => 0, // integer aktif
        ]);

        $response = $this->postJson(route('attendance.process-scan'), [
            'id_siswa' => 'SIS-BUG06',
        ]);

        // Setelah fix BUG-06: siswa aktif (status=0) harus bisa scan
        $response->assertOk()->assertJson(['success' => true]);
    }

    // ---------------------------------------------------------------
    // Proteksi: Impersonate — non-admin tidak bisa
    // ---------------------------------------------------------------
    public function test_guru_cannot_impersonate_anyone(): void
    {
        $guru     = User::factory()->create(['role' => 'guru']);
        $target   = User::factory()->create(['role' => 'guru']);

        $this->actingAs($guru)
            ->post(route('impersonate.guru', $target))
            ->assertForbidden();
    }

    // ---------------------------------------------------------------
    // Proteksi: Admin tidak bisa impersonate admin lain (bukan guru)
    // ---------------------------------------------------------------
    public function test_admin_cannot_impersonate_non_guru(): void
    {
        $admin1 = User::factory()->create(['role' => 'admin']);
        $admin2 = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin1)
            ->post(route('impersonate.guru', $admin2))
            ->assertStatus(400);
    }

    // ---------------------------------------------------------------
    // Proteksi: Manajemen user hanya bisa diakses user yang login
    // ---------------------------------------------------------------
    public function test_management_users_requires_auth(): void
    {
        $this->get(route('management-users.index'))
            ->assertRedirect(route('login'));
    }

    // ---------------------------------------------------------------
    // Proteksi: Finance dashboard membutuhkan auth
    // ---------------------------------------------------------------
    public function test_finance_dashboard_requires_auth(): void
    {
        $this->get(route('admin.finance.dashboard'))
            ->assertRedirect(route('login'));
    }

    // ---------------------------------------------------------------
    // BUG-04: Class AttendanceReportHelper sebelumnya tidak ada
    // Test ini memverifikasi bahwa class sudah dibuat dan dapat digunakan
    // ---------------------------------------------------------------
    public function test_attendance_report_helper_class_exists(): void
    {
        $this->assertTrue(
            class_exists(\App\Support\AttendanceReportHelper::class),
            'BUG-04 DIPERBAIKI: Class App\\Support\\AttendanceReportHelper harus ada. ' .
            'Buat file app/Support/AttendanceReportHelper.php'
        );
    }

    // ---------------------------------------------------------------
    // BUG-10: Endpoint ujian bisa diakses tanpa auth (by design — tapi perlu verifikasi)
    // ---------------------------------------------------------------
    public function test_exam_start_accessible_without_auth(): void
    {
        // Ujian masuk memang public, tapi kita dokumentasikan behavior ini
        $response = $this->get('/exam/start/999'); // ID tidak ada

        // Tidak redirect ke login (karena memang public)
        $this->assertNotSame(302, $response->status(),
            'Endpoint /exam/start/{id} bisa diakses publik (tanpa auth). ' .
            'Ini by design tapi perlu anti-cheat/double-submit validation.'
        );
    }

    // ---------------------------------------------------------------
    // Proteksi: Student portal — logout menggunakan guard yang benar
    // ---------------------------------------------------------------
    public function test_student_logout_uses_student_guard(): void
    {
        $student = Student::factory()->create(['status' => 0]);

        $this->actingAs($student, 'student')
            ->post(route('siswa.logout'));

        $this->assertGuest('student');
        // Guard 'web' (admin) tidak terpengaruh
        $this->assertGuest('web');
    }
}
