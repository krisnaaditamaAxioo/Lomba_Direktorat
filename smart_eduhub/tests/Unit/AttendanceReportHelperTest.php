<?php

namespace Tests\Unit;

use App\Support\AttendanceReportHelper;
use PHPUnit\Framework\TestCase;

class AttendanceReportHelperTest extends TestCase
{
    // ---------------------------------------------------------------
    // Test 1: present + late keduanya masuk ke hadir, absent terpisah
    // ---------------------------------------------------------------
    public function test_present_and_late_counted_as_hadir_absent_stays_separate(): void
    {
        $attendances = collect([
            ['status' => 'present', 'date' => '2024-02-01'],
            ['status' => 'late',    'date' => '2024-02-02'],
            ['status' => 'absent',  'date' => '2024-02-03'],
        ]);

        $result = AttendanceReportHelper::summarizePeriod(
            $attendances, '2024-02-01', '2024-02-03'
        );

        $this->assertSame(1, $result['present']);
        $this->assertSame(1, $result['late']);
        $this->assertSame(2, $result['hadir']);   // present + late
        $this->assertSame(1, $result['absent']);
        $this->assertSame(0, $result['excused']);
        $this->assertSame(0, $result['sick']);
    }

    // ---------------------------------------------------------------
    // Test 2: record di luar rentang tanggal diabaikan
    // ---------------------------------------------------------------
    public function test_records_outside_date_range_are_ignored(): void
    {
        $attendances = collect([
            ['status' => 'present', 'date' => '2024-01-31'], // sebelum range
            ['status' => 'present', 'date' => '2024-02-15'], // dalam range
            ['status' => 'absent',  'date' => '2024-03-01'], // sesudah range
        ]);

        $result = AttendanceReportHelper::summarizePeriod(
            $attendances, '2024-02-01', '2024-02-28'
        );

        $this->assertSame(1, $result['present']);
        $this->assertSame(0, $result['absent']);
        $this->assertSame(1, $result['hadir']);
    }

    // ---------------------------------------------------------------
    // Test 3: koleksi kosong menghasilkan semua nol
    // ---------------------------------------------------------------
    public function test_empty_collection_returns_all_zeros(): void
    {
        $result = AttendanceReportHelper::summarizePeriod(
            collect([]), '2024-02-01', '2024-02-28'
        );

        $this->assertSame(0, $result['present']);
        $this->assertSame(0, $result['late']);
        $this->assertSame(0, $result['hadir']);
        $this->assertSame(0, $result['absent']);
        $this->assertSame(0, $result['excused']);
        $this->assertSame(0, $result['sick']);
    }

    // ---------------------------------------------------------------
    // Test 4: sick dan excused dihitung terpisah dari absent
    // ---------------------------------------------------------------
    public function test_sick_and_excused_counted_separately_from_absent(): void
    {
        $attendances = collect([
            ['status' => 'sick',    'date' => '2024-02-01'],
            ['status' => 'sick',    'date' => '2024-02-02'],
            ['status' => 'excused', 'date' => '2024-02-03'],
            ['status' => 'absent',  'date' => '2024-02-04'],
        ]);

        $result = AttendanceReportHelper::summarizePeriod(
            $attendances, '2024-02-01', '2024-02-28'
        );

        $this->assertSame(2, $result['sick']);
        $this->assertSame(1, $result['excused']);
        $this->assertSame(1, $result['absent']);
        $this->assertSame(0, $result['hadir']);
    }

    // ---------------------------------------------------------------
    // Test 5: menerima array biasa (bukan Collection)
    // ---------------------------------------------------------------
    public function test_accepts_plain_array(): void
    {
        $attendances = [
            ['status' => 'present', 'date' => '2024-02-01'],
            ['status' => 'late',    'date' => '2024-02-05'],
        ];

        $result = AttendanceReportHelper::summarizePeriod(
            $attendances, '2024-02-01', '2024-02-28'
        );

        $this->assertSame(1, $result['present']);
        $this->assertSame(1, $result['late']);
        $this->assertSame(2, $result['hadir']);
    }

    // ---------------------------------------------------------------
    // Test 6: menerima objek (bukan array)
    // ---------------------------------------------------------------
    public function test_accepts_objects_not_only_arrays(): void
    {
        $makeObj = fn($status, $date) => (object)['status' => $status, 'date' => $date];

        $attendances = [
            $makeObj('present', '2024-02-10'),
            $makeObj('absent',  '2024-02-11'),
        ];

        $result = AttendanceReportHelper::summarizePeriod(
            $attendances, '2024-02-01', '2024-02-28'
        );

        $this->assertSame(1, $result['present']);
        $this->assertSame(1, $result['absent']);
    }

    // ---------------------------------------------------------------
    // Test 7: record tepat di batas range (inklusif)
    // ---------------------------------------------------------------
    public function test_boundary_dates_are_inclusive(): void
    {
        $attendances = collect([
            ['status' => 'present', 'date' => '2024-02-01'], // batas awal
            ['status' => 'late',    'date' => '2024-02-29'], // batas akhir (2024 tahun kabisat)
        ]);

        $result = AttendanceReportHelper::summarizePeriod(
            $attendances, '2024-02-01', '2024-02-29'
        );

        $this->assertSame(1, $result['present']);
        $this->assertSame(1, $result['late']);
        $this->assertSame(2, $result['hadir']);
    }
}
