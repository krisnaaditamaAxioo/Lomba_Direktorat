<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Point;
use App\Models\PointRule;
use App\Models\Setting;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendancePenaltyTest extends TestCase
{
    use RefreshDatabase;

    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('academic_year', '2025-2026');
        Setting::set('check_in_time', '08:00');

        $year = AcademicYear::create(['name' => '2025-2026', 'is_active' => true]);
        $this->student = Student::factory()->create(['status' => 0]);
        $this->student->academicYears()->attach($year->id);
    }

    // ---------------------------------------------------------------
    // Helper: buat record absensi tanpa scan (langsung ke DB)
    // ---------------------------------------------------------------
    private function createAttendance(string $status, string $date): void
    {
        Attendance::create([
            'student_id'    => $this->student->id,
            'date'          => $date,
            'status'        => $status,
            'academic_year' => '2025-2026',
        ]);
    }

    // ---------------------------------------------------------------
    // Test 1: Poin pelanggaran diberikan ketika threshold tercapai
    // ---------------------------------------------------------------
    public function test_penalty_point_applied_when_absent_threshold_reached(): void
    {
        PointRule::create([
            'name'              => 'Sering Absen',
            'condition_type'    => 'attendance_status',
            'attendance_status' => 'absent',
            'threshold'         => 3,
            'point_type'        => 'violation',
            'point_score'       => 8,
            'reason_template'   => 'Absen {count} kali dalam sebulan',
            'sort_order'        => 1,
            'is_active'         => true,
        ]);

        // Buat 3 record absen di bulan yang sama (ambil bulan saat ini)
        $base = now()->startOfMonth();
        for ($i = 0; $i < 3; $i++) {
            $this->createAttendance('absent', $base->copy()->addWeekdays($i)->toDateString());
        }

        // Scan di hari berikutnya — trigger penalty check
        $nextDay = $base->copy()->addWeekdays(3)->toDateString();
        $this->travelTo(now()->setDateFrom($nextDay)->setTime(7, 0));

        $this->postJson(route('attendance.process-scan'), [
            'id_siswa' => $this->student->id_siswa,
        ]);

        $this->assertDatabaseHas('points', [
            'student_id' => $this->student->id,
            'type'       => 'violation',
            'score'      => 8,
        ]);
    }

    // ---------------------------------------------------------------
    // Test 2: Poin tidak diberikan jika threshold belum tercapai
    // ---------------------------------------------------------------
    public function test_no_penalty_when_below_threshold(): void
    {
        PointRule::create([
            'name'              => 'Sering Absen',
            'condition_type'    => 'attendance_status',
            'attendance_status' => 'absent',
            'threshold'         => 5,
            'point_type'        => 'violation',
            'point_score'       => 8,
            'reason_template'   => 'Absen {count} kali dalam sebulan',
            'sort_order'        => 1,
            'is_active'         => true,
        ]);

        // Hanya 2 absen, threshold 5
        $base = now()->startOfMonth();
        $this->createAttendance('absent', $base->toDateString());
        $this->createAttendance('absent', $base->copy()->addWeekdays(1)->toDateString());

        $this->travelTo(now()->setDateFrom($base->copy()->addWeekdays(2))->setTime(7, 0));

        $this->postJson(route('attendance.process-scan'), [
            'id_siswa' => $this->student->id_siswa,
        ]);

        $this->assertDatabaseMissing('points', [
            'student_id' => $this->student->id,
            'type'       => 'violation',
        ]);
    }

    // ---------------------------------------------------------------
    // Test 3: Poin tidak dobel untuk rule yang sama di bulan yang sama
    // ---------------------------------------------------------------
    public function test_penalty_not_duplicated_in_same_month(): void
    {
        PointRule::create([
            'name'              => 'Sering Terlambat',
            'condition_type'    => 'attendance_status',
            'attendance_status' => 'late',
            'threshold'         => 3,
            'point_type'        => 'violation',
            'point_score'       => 5,
            'reason_template'   => 'Terlambat {count} kali',
            'sort_order'        => 1,
            'is_active'         => true,
        ]);

        $base = now()->startOfMonth();
        for ($i = 0; $i < 3; $i++) {
            $this->createAttendance('late', $base->copy()->addWeekdays($i)->toDateString());
        }

        // Scan pertama (threshold tercapai, poin dibuat)
        $day4 = $base->copy()->addWeekdays(3)->toDateString();
        $this->travelTo(now()->setDateFrom($day4)->setTime(7, 0));
        $this->postJson(route('attendance.process-scan'), ['id_siswa' => $this->student->id_siswa]);

        // Scan kedua (threshold masih tercapai, tapi tidak boleh dobel)
        $day5 = $base->copy()->addWeekdays(4)->toDateString();
        $this->travelTo(now()->setDateFrom($day5)->setTime(7, 0));
        $this->postJson(route('attendance.process-scan'), ['id_siswa' => $this->student->id_siswa]);

        $count = Point::where('student_id', $this->student->id)
            ->where('type', 'violation')
            ->count();

        $this->assertSame(1, $count, 'Poin pelanggaran tidak boleh dobel di bulan yang sama');
    }

    // ---------------------------------------------------------------
    // Test 4: PointRule nonaktif tidak diterapkan
    // ---------------------------------------------------------------
    public function test_inactive_point_rule_is_not_applied(): void
    {
        PointRule::create([
            'name'              => 'Aturan Nonaktif',
            'condition_type'    => 'attendance_status',
            'attendance_status' => 'absent',
            'threshold'         => 1,
            'point_type'        => 'violation',
            'point_score'       => 10,
            'reason_template'   => 'Absen {count} kali',
            'sort_order'        => 1,
            'is_active'         => false, // nonaktif
        ]);

        $base = now()->startOfMonth();
        $this->createAttendance('absent', $base->toDateString());

        $this->travelTo(now()->setDateFrom($base->copy()->addWeekdays(1))->setTime(7, 0));
        $this->postJson(route('attendance.process-scan'), ['id_siswa' => $this->student->id_siswa]);

        $this->assertDatabaseMissing('points', [
            'student_id' => $this->student->id,
            'type'       => 'violation',
        ]);
    }

    // ---------------------------------------------------------------
    // Test 5: Poin dari bulan berbeda tidak saling mempengaruhi
    // ---------------------------------------------------------------
    public function test_penalty_resets_each_month(): void
    {
        PointRule::create([
            'name'              => 'Absen Bulanan',
            'condition_type'    => 'attendance_status',
            'attendance_status' => 'absent',
            'threshold'         => 2,
            'point_type'        => 'violation',
            'point_score'       => 5,
            'reason_template'   => 'Absen {count} kali',
            'sort_order'        => 1,
            'is_active'         => true,
        ]);

        // 1 absen bulan lalu (tidak mencapai threshold 2)
        $lastMonth = now()->subMonth()->startOfMonth()->toDateString();
        $this->createAttendance('absent', $lastMonth);

        // 1 absen bulan ini (tidak mencapai threshold 2)
        $thisMonth = now()->startOfMonth()->toDateString();
        $this->createAttendance('absent', $thisMonth);

        $this->travelTo(now()->startOfMonth()->addWeekdays(1)->setTime(7, 0));
        $this->postJson(route('attendance.process-scan'), ['id_siswa' => $this->student->id_siswa]);

        // Threshold bulan ini belum tercapai (hanya 1), tidak ada poin
        $this->assertSame(0, Point::where('student_id', $this->student->id)->count());
    }
}
