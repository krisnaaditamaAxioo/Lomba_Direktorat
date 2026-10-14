<?php

namespace Tests\Unit;

use App\Models\Holiday;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HolidayModelTest extends TestCase
{
    use RefreshDatabase;

    // ---------------------------------------------------------------
    // Test 1: Hari Minggu selalu libur (tanpa record database)
    // ---------------------------------------------------------------
    public function test_sunday_is_always_a_holiday(): void
    {
        // 2025-02-02 adalah hari Minggu
        $this->assertTrue(Holiday::checkHoliday('2025-02-02'));
    }

    public function test_monday_is_not_a_holiday_when_no_record(): void
    {
        // 2025-02-03 adalah Senin, tidak ada record di DB
        $this->assertFalse(Holiday::checkHoliday('2025-02-03'));
    }

    // ---------------------------------------------------------------
    // Test 2: Holiday type 'special' (tanggal tertentu)
    // ---------------------------------------------------------------
    public function test_special_holiday_is_detected(): void
    {
        // 2025-08-18 adalah hari Senin (bukan Minggu)
        Holiday::create([
            'type' => 'special',
            'date' => '2025-08-18',
            'name' => 'Hari Kemerdekaan RI',
        ]);

        $this->assertTrue(Holiday::checkHoliday('2025-08-18'));
    }

    public function test_special_holiday_does_not_affect_other_dates(): void
    {
        Holiday::create([
            'type' => 'special',
            'date' => '2025-08-18',
            'name' => 'Hari Kemerdekaan RI',
        ]);

        // Hari sebelum dan sesudah bukan libur (dan bukan Minggu)
        $this->assertFalse(Holiday::checkHoliday('2025-08-19')); // Selasa
        $this->assertFalse(Holiday::checkHoliday('2025-08-20')); // Rabu
    }

    public function test_getHolidayName_returns_correct_name(): void
    {
        Holiday::create([
            'type' => 'special',
            'date' => '2025-08-18',
            'name' => 'Hari Kemerdekaan RI',
        ]);

        $this->assertSame('Hari Kemerdekaan RI', Holiday::getHolidayName('2025-08-18'));
    }

    public function test_getHolidayName_returns_null_for_non_holiday(): void
    {
        // 2025-02-03 = Senin biasa, tidak ada record
        $this->assertNull(Holiday::getHolidayName('2025-02-03'));
    }

    public function test_getHolidayName_returns_hari_minggu_for_sunday(): void
    {
        $this->assertSame('Hari Minggu', Holiday::getHolidayName('2025-02-02'));
    }

    // ---------------------------------------------------------------
    // Test 3: Holiday type 'range' (rentang tanggal)
    // ---------------------------------------------------------------
    public function test_range_holiday_covers_all_days_inclusive(): void
    {
        Holiday::create([
            'type'       => 'range',
            'start_date' => '2025-06-01',
            'end_date'   => '2025-06-15',
            'name'       => 'Libur Kenaikan Kelas',
        ]);

        $this->assertTrue(Holiday::checkHoliday('2025-06-01'));  // batas awal
        $this->assertTrue(Holiday::checkHoliday('2025-06-08'));  // tengah
        $this->assertTrue(Holiday::checkHoliday('2025-06-15'));  // batas akhir
    }

    public function test_range_holiday_does_not_cover_outside_dates(): void
    {
        Holiday::create([
            'type'       => 'range',
            'start_date' => '2025-06-01',
            'end_date'   => '2025-06-15',
            'name'       => 'Libur Kenaikan Kelas',
        ]);

        $this->assertFalse(Holiday::checkHoliday('2025-05-31')); // sebelum range
        $this->assertFalse(Holiday::checkHoliday('2025-06-16')); // sesudah range
    }

    // ---------------------------------------------------------------
    // Test 4: Holiday type 'monthly' (berulang tiap bulan)
    // ---------------------------------------------------------------
    public function test_monthly_holiday_repeats_every_month(): void
    {
        Holiday::create([
            'type'         => 'monthly',
            'day_of_month' => 1,
            'name'         => 'Hari Pertama Bulan',
        ]);

        $this->assertTrue(Holiday::checkHoliday('2025-01-01'));
        $this->assertTrue(Holiday::checkHoliday('2025-04-01'));
        $this->assertTrue(Holiday::checkHoliday('2025-07-01'));
    }

    public function test_monthly_holiday_does_not_affect_other_days(): void
    {
        Holiday::create([
            'type'         => 'monthly',
            'day_of_month' => 1,
            'name'         => 'Hari Pertama Bulan',
        ]);

        $this->assertFalse(Holiday::checkHoliday('2025-01-02'));
        $this->assertFalse(Holiday::checkHoliday('2025-04-15'));
    }
}
