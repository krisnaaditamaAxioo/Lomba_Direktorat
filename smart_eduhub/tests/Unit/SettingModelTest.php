<?php

namespace Tests\Unit;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_returns_default_when_key_not_exists(): void
    {
        $result = Setting::get('kunci_tidak_ada', 'nilai_default');
        $this->assertSame('nilai_default', $result);
    }

    public function test_get_returns_null_when_no_default_given(): void
    {
        $result = Setting::get('kunci_tidak_ada');
        $this->assertNull($result);
    }

    public function test_set_creates_new_setting(): void
    {
        Setting::set('academic_year', '2025-2026');

        $this->assertSame('2025-2026', Setting::get('academic_year'));
    }

    public function test_get_returns_stored_value(): void
    {
        Setting::set('check_in_time', '07:30');

        $this->assertSame('07:30', Setting::get('check_in_time'));
    }

    public function test_set_updates_existing_setting_without_duplicate(): void
    {
        Setting::set('academic_year', '2024-2025');
        Setting::set('academic_year', '2025-2026');

        // Nilai terupdate
        $this->assertSame('2025-2026', Setting::get('academic_year'));

        // Tidak ada duplikat key
        $count = Setting::where('key', 'academic_year')->count();
        $this->assertSame(1, $count);
    }

    public function test_multiple_different_keys_are_independent(): void
    {
        Setting::set('key_a', 'nilai_a');
        Setting::set('key_b', 'nilai_b');

        $this->assertSame('nilai_a', Setting::get('key_a'));
        $this->assertSame('nilai_b', Setting::get('key_b'));
    }

    public function test_get_returns_default_even_after_other_keys_set(): void
    {
        Setting::set('other_key', 'other_value');

        $this->assertSame('fallback', Setting::get('missing_key', 'fallback'));
    }
}
