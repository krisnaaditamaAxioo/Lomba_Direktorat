<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Point;
use App\Models\Setting;
use App\Models\Student;
use App\Models\StudentNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PointTest extends TestCase
{
    use RefreshDatabase;

    private User    $admin;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('academic_year', '2025-2026');

        $year = AcademicYear::create(['name' => '2025-2026', 'is_active' => true]);

        $this->admin   = User::factory()->create(['role' => 'admin']);
        $this->student = Student::factory()->create();
        $this->student->academicYears()->attach($year->id);
    }

    // ---------------------------------------------------------------
    // Test 1: Admin bisa buat poin prestasi
    // ---------------------------------------------------------------
    public function test_admin_can_create_achievement_point(): void
    {
        $this->actingAs($this->admin)
            ->post(route('points.store'), [
                'student_id' => $this->student->id,
                'type'       => 'achievement',
                'score'      => 10,
                'reason'     => 'Juara 1 lomba matematika',
                'date'       => now()->toDateString(),
            ])->assertRedirect(route('points'));

        $this->assertDatabaseHas('points', [
            'student_id'    => $this->student->id,
            'type'          => 'achievement',
            'score'         => 10,
            'academic_year' => '2025-2026',
        ]);
    }

    // ---------------------------------------------------------------
    // Test 2: Admin bisa buat poin pelanggaran
    // ---------------------------------------------------------------
    public function test_admin_can_create_violation_point(): void
    {
        $this->actingAs($this->admin)
            ->post(route('points.store'), [
                'student_id' => $this->student->id,
                'type'       => 'violation',
                'score'      => 5,
                'reason'     => 'Terlambat masuk kelas',
                'date'       => now()->toDateString(),
            ])->assertRedirect(route('points'));

        $this->assertDatabaseHas('points', [
            'student_id' => $this->student->id,
            'type'       => 'violation',
        ]);
    }

    // ---------------------------------------------------------------
    // Test 3: Membuat poin otomatis kirim notifikasi ke siswa
    // ---------------------------------------------------------------
    public function test_creating_point_sends_notification_to_student(): void
    {
        $this->actingAs($this->admin)
            ->post(route('points.store'), [
                'student_id' => $this->student->id,
                'type'       => 'violation',
                'score'      => 5,
                'reason'     => 'Tidak mengerjakan tugas',
                'date'       => now()->toDateString(),
            ]);

        $this->assertDatabaseHas('student_notifications', [
            'student_id' => $this->student->id,
            'type'       => 'point',
        ]);

        $notif = StudentNotification::where('student_id', $this->student->id)->first();
        $this->assertStringContainsString('Pelanggaran', $notif->title);
    }

    public function test_achievement_notification_title_contains_prestasi(): void
    {
        $this->actingAs($this->admin)
            ->post(route('points.store'), [
                'student_id' => $this->student->id,
                'type'       => 'achievement',
                'score'      => 15,
                'reason'     => 'Juara olimpiade sains',
                'date'       => now()->toDateString(),
            ]);

        $notif = StudentNotification::where('student_id', $this->student->id)->first();
        $this->assertStringContainsString('Prestasi', $notif->title);
    }

    // ---------------------------------------------------------------
    // Test 4: Admin bisa hapus poin
    // ---------------------------------------------------------------
    public function test_admin_can_delete_point(): void
    {
        $point = Point::create([
            'student_id'    => $this->student->id,
            'type'          => 'violation',
            'score'         => 5,
            'reason'        => 'Test delete',
            'date'          => now()->toDateString(),
            'academic_year' => '2025-2026',
        ]);

        $this->actingAs($this->admin)
            ->delete(route('points.destroy', $point))
            ->assertRedirect(route('points'));

        $this->assertDatabaseMissing('points', ['id' => $point->id]);
    }

    // ---------------------------------------------------------------
    // Test 5: Validasi — type tidak valid ditolak
    // ---------------------------------------------------------------
    public function test_invalid_type_is_rejected(): void
    {
        $this->actingAs($this->admin)
            ->post(route('points.store'), [
                'student_id' => $this->student->id,
                'type'       => 'invalid_type',
                'score'      => 10,
                'reason'     => 'Test',
                'date'       => now()->toDateString(),
            ])->assertSessionHasErrors('type');

        $this->assertDatabaseCount('points', 0);
    }

    // ---------------------------------------------------------------
    // Test 6: Validasi — student_id tidak ada ditolak
    // ---------------------------------------------------------------
    public function test_nonexistent_student_id_is_rejected(): void
    {
        $this->actingAs($this->admin)
            ->post(route('points.store'), [
                'student_id' => 9999,
                'type'       => 'achievement',
                'score'      => 10,
                'reason'     => 'Test',
                'date'       => now()->toDateString(),
            ])->assertSessionHasErrors('student_id');
    }

    // ---------------------------------------------------------------
    // Test 7: Unauthenticated tidak bisa akses
    // ---------------------------------------------------------------
    public function test_unauthenticated_cannot_access_points_list(): void
    {
        $this->get(route('points'))->assertRedirect(route('login'));
    }

    public function test_unauthenticated_cannot_create_point(): void
    {
        $this->post(route('points.store'), [])->assertRedirect(route('login'));
    }

    // ---------------------------------------------------------------
    // Test 8: Poin tersimpan dengan academic_year dari setting
    // ---------------------------------------------------------------
    public function test_point_stored_with_current_academic_year(): void
    {
        Setting::set('academic_year', '2024-2025');

        $this->actingAs($this->admin)
            ->post(route('points.store'), [
                'student_id' => $this->student->id,
                'type'       => 'achievement',
                'score'      => 10,
                'reason'     => 'Tes tahun ajaran',
                'date'       => now()->toDateString(),
            ]);

        $this->assertDatabaseHas('points', [
            'student_id'    => $this->student->id,
            'academic_year' => '2024-2025',
        ]);
    }
}
