<?php

namespace Tests\Unit;

use App\Models\Student;
use PHPUnit\Framework\TestCase;

class StudentModelTest extends TestCase
{
    private function student(array $attrs = []): Student
    {
        $s = new Student();
        foreach ($attrs as $k => $v) {
            $s->$k = $v;
        }
        return $s;
    }

    // ---------------------------------------------------------------
    // Accessor: gender_text dan gender_short
    // ---------------------------------------------------------------
    public function test_gender_text_laki_laki(): void
    {
        $s = $this->student(['gender' => 0]);
        $this->assertSame('Laki-laki', $s->gender_text);
    }

    public function test_gender_text_perempuan(): void
    {
        $s = $this->student(['gender' => 1]);
        $this->assertSame('Perempuan', $s->gender_text);
    }

    public function test_gender_short_laki_laki(): void
    {
        $s = $this->student(['gender' => 0]);
        $this->assertSame('L', $s->gender_short);
    }

    public function test_gender_short_perempuan(): void
    {
        $s = $this->student(['gender' => 1]);
        $this->assertSame('P', $s->gender_short);
    }

    // ---------------------------------------------------------------
    // Accessor: status_text dan status_short
    // ---------------------------------------------------------------
    public function test_status_text_aktif(): void
    {
        $s = $this->student(['status' => 0]);
        $this->assertSame('Aktif', $s->status_text);
    }

    public function test_status_text_nonaktif(): void
    {
        $s = $this->student(['status' => 1]);
        $this->assertSame('Nonaktif', $s->status_text);
    }

    public function test_status_short_active(): void
    {
        $s = $this->student(['status' => 0]);
        $this->assertSame('active', $s->status_short);
    }

    public function test_status_short_inactive(): void
    {
        $s = $this->student(['status' => 1]);
        $this->assertSame('inactive', $s->status_short);
    }

    // ---------------------------------------------------------------
    // Accessor: name mengembalikan nama_lengkap
    // ---------------------------------------------------------------
    public function test_name_accessor_returns_nama_lengkap(): void
    {
        $s = $this->student(['nama_lengkap' => 'Ahmad Fauzi']);
        $this->assertSame('Ahmad Fauzi', $s->name);
    }

    // ---------------------------------------------------------------
    // Bug documentation: perbandingan status di processScan
    // BUG-06: status disimpan integer (0), tapi di-compare ke string 'active'
    // ---------------------------------------------------------------
    public function test_status_integer_0_is_NOT_equal_to_string_active_strict(): void
    {
        $s = $this->student(['status' => 0]);

        // Strict comparison (===) yang dipakai di processScan:
        // $student->status !== 'active' → TRUE untuk status=0
        // Artinya siswa aktif (status=0) AKAN DITOLAK scan — ini BUG
        $this->assertNotSame('active', $s->status);

        // Yang benar: gunakan status_short accessor
        $this->assertSame('active', $s->status_short);
    }

    public function test_status_integer_1_is_NOT_equal_to_string_inactive_strict(): void
    {
        $s = $this->student(['status' => 1]);
        $this->assertNotSame('inactive', $s->status);
        $this->assertSame('inactive', $s->status_short);
    }
}
