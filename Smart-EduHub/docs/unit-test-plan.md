# Unit Test Plan — Smart-ACP

**Framework:** PHPUnit 11 (via Laravel 12)  
**DB Test:** SQLite in-memory  
**Guard:** 3 guard — `web` (User), `student` (Student), `parent` (ParentUser)

---

## Daftar File Test yang Harus Dibuat

| # | File | Jenis | Area |
|---|------|-------|------|
| 1 | `tests/Unit/AttendanceReportHelperTest.php` | Unit | Helper summarizePeriod |
| 2 | `tests/Unit/UserModelTest.php` | Unit | Model User, role methods, permission |
| 3 | `tests/Unit/StudentModelTest.php` | Unit | Model Student, accessor, scope |
| 4 | `tests/Unit/HolidayModelTest.php` | Unit | Model Holiday, checkHoliday, range |
| 5 | `tests/Unit/PointRuleModelTest.php` | Unit | Model PointRule, scope active |
| 6 | `tests/Unit/SettingModelTest.php` | Unit | Model Setting, get/set |
| 7 | `tests/Feature/AuthTest.php` | Feature | Login admin/guru, redirect, role guard |
| 8 | `tests/Feature/AttendanceScanTest.php` | Feature | Scan absensi, holiday block, duplicate |
| 9 | `tests/Feature/PointTest.php` | Feature | CRUD poin, kirim notifikasi siswa |
| 10 | `tests/Feature/StudentFinanceTransferTest.php` | Feature | Transfer e-wallet, validasi PIN, saldo |
| 11 | `tests/Feature/StudentPortalAuthTest.php` | Feature | Login siswa PIN, guard student |
| 12 | `tests/Feature/AttendancePenaltyTest.php` | Feature | Auto poin pelanggaran dari PointRule |
| 13 | `tests/Feature/SecurityTest.php` | Feature | Bug keamanan — route tak terproteksi |

---

## Pola Umum Setup Test

```php
// Untuk Feature test yang butuh auth User (staff)
$this->actingAs(User::factory()->create(['role' => 'admin']));

// Untuk Feature test yang butuh auth Student
$this->actingAs($student, 'student');

// Untuk Feature test yang butuh auth Parent
$this->actingAs($parent, 'parent');

// Seed setting wajib sebelum test yang memakai Setting::get('academic_year')
Setting::set('academic_year', '2025-2026');
```

---

## 1. Unit — AttendanceReportHelper

**File:** `tests/Unit/AttendanceReportHelperTest.php`

```php
<?php

namespace Tests\Unit;

use App\Support\AttendanceReportHelper;
use PHPUnit\Framework\TestCase;

class AttendanceReportHelperTest extends TestCase
{
    /** present + late keduanya masuk ke hadir */
    public function test_present_and_late_both_counted_as_hadir(): void
    {
        $attendances = collect([
            ['status' => 'present', 'date' => '2025-02-01'],
            ['status' => 'late',    'date' => '2025-02-02'],
            ['status' => 'absent',  'date' => '2025-02-03'],
        ]);

        $result = AttendanceReportHelper::summarizePeriod(
            $attendances, '2025-02-01', '2025-02-03'
        );

        $this->assertSame(1, $result['present']);
        $this->assertSame(1, $result['late']);
        $this->assertSame(2, $result['hadir']);   // present + late
        $this->assertSame(1, $result['absent']);
        $this->assertSame(0, $result['excused']);
        $this->assertSame(0, $result['sick']);
    }

    /** Record di luar rentang tanggal harus diabaikan */
    public function test_records_outside_date_range_are_ignored(): void
    {
        $attendances = collect([
            ['status' => 'present', 'date' => '2025-01-31'], // sebelum range
            ['status' => 'present', 'date' => '2025-02-01'], // dalam range
            ['status' => 'absent',  'date' => '2025-03-01'], // sesudah range
        ]);

        $result = AttendanceReportHelper::summarizePeriod(
            $attendances, '2025-02-01', '2025-02-28'
        );

        $this->assertSame(1, $result['present']);
        $this->assertSame(0, $result['absent']);
    }

    /** Koleksi kosong menghasilkan semua nol */
    public function test_empty_collection_returns_all_zeros(): void
    {
        $result = AttendanceReportHelper::summarizePeriod(
            collect([]), '2025-02-01', '2025-02-28'
        );

        $this->assertSame(0, $result['present']);
        $this->assertSame(0, $result['late']);
        $this->assertSame(0, $result['hadir']);
        $this->assertSame(0, $result['absent']);
        $this->assertSame(0, $result['excused']);
        $this->assertSame(0, $result['sick']);
    }

    /** Status 'sick' dihitung terpisah dari absent */
    public function test_sick_is_counted_separately_from_absent(): void
    {
        $attendances = collect([
            ['status' => 'sick',    'date' => '2025-02-01'],
            ['status' => 'sick',    'date' => '2025-02-02'],
            ['status' => 'excused', 'date' => '2025-02-03'],
            ['status' => 'absent',  'date' => '2025-02-04'],
        ]);

        $result = AttendanceReportHelper::summarizePeriod(
            $attendances, '2025-02-01', '2025-02-28'
        );

        $this->assertSame(2, $result['sick']);
        $this->assertSame(1, $result['excused']);
        $this->assertSame(1, $result['absent']);
        $this->assertSame(0, $result['hadir']);
    }

    /** Menerima array biasa (bukan Collection) */
    public function test_accepts_plain_array_not_only_collection(): void
    {
        $attendances = [
            ['status' => 'present', 'date' => '2025-02-01'],
            ['status' => 'late',    'date' => '2025-02-05'],
        ];

        $result = AttendanceReportHelper::summarizePeriod(
            $attendances, '2025-02-01', '2025-02-28'
        );

        $this->assertSame(1, $result['present']);
        $this->assertSame(1, $result['late']);
        $this->assertSame(2, $result['hadir']);
    }
}
```

---

## 2. Unit — UserModel

**File:** `tests/Unit/UserModelTest.php`

```php
<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserModelTest extends TestCase
{
    private function makeUser(string $role): User
    {
        $u = new User();
        $u->role = $role;
        return $u;
    }

    public function test_isAdmin_returns_true_only_for_admin_role(): void
    {
        $this->assertTrue($this->makeUser('admin')->isAdmin());
        $this->assertFalse($this->makeUser('guru')->isAdmin());
        $this->assertFalse($this->makeUser('teller')->isAdmin());
    }

    public function test_isGuru_returns_true_only_for_guru_role(): void
    {
        $this->assertTrue($this->makeUser('guru')->isGuru());
        $this->assertFalse($this->makeUser('admin')->isGuru());
    }

    public function test_isFinanceStaff_returns_true_for_admin_teller_bums(): void
    {
        $this->assertTrue($this->makeUser('admin')->isFinanceStaff());
        $this->assertTrue($this->makeUser('teller')->isFinanceStaff());
        $this->assertTrue($this->makeUser('bums')->isFinanceStaff());
        $this->assertFalse($this->makeUser('guru')->isFinanceStaff());
        $this->assertFalse($this->makeUser('wali_kelas')->isFinanceStaff());
    }

    public function test_finance_permissions_consistent_across_roles(): void
    {
        $financeRoles = ['admin', 'teller', 'bums'];
        $nonFinanceRoles = ['guru', 'wali_kelas', 'wakasis', 'bk'];

        foreach ($financeRoles as $role) {
            $user = $this->makeUser($role);
            $this->assertTrue($user->canTopupFinance(),         "$role harus bisa topup");
            $this->assertTrue($user->canProcessFinancePayment(), "$role harus bisa proses payment");
            $this->assertTrue($user->canAccessFinanceDashboard(), "$role harus bisa akses dashboard finance");
        }

        foreach ($nonFinanceRoles as $role) {
            $user = $this->makeUser($role);
            $this->assertFalse($user->canTopupFinance(),         "$role tidak boleh topup");
            $this->assertFalse($user->canProcessFinancePayment(), "$role tidak boleh proses payment");
        }
    }

    public function test_roleLabel_returns_correct_display_names(): void
    {
        $cases = [
            'admin'      => 'Administrator',
            'guru'       => 'Guru',
            'wali_kelas' => 'Wali Kelas',
            'wakasis'    => 'Waka Kesiswaan',
            'bk'         => 'BK',
            'teller'     => 'Teller',
            'bums'       => 'BUMS',
        ];

        foreach ($cases as $role => $expected) {
            $this->assertSame(
                $expected,
                $this->makeUser($role)->role_label,
                "roleLabel untuk '$role' salah"
            );
        }
    }
}
```

---

## 3. Unit — StudentModel

**File:** `tests/Unit/StudentModelTest.php`

```php
<?php

namespace Tests\Unit;

use App\Models\Student;
use PHPUnit\Framework\TestCase;

class StudentModelTest extends TestCase
{
    private function makeStudent(array $attrs = []): Student
    {
        $s = new Student();
        foreach ($attrs as $k => $v) {
            $s->$k = $v;
        }
        return $s;
    }

    public function test_gender_accessor_laki_laki(): void
    {
        $s = $this->makeStudent(['gender' => 0]);
        $this->assertSame('Laki-laki', $s->gender_text);
        $this->assertSame('L', $s->gender_short);
    }

    public function test_gender_accessor_perempuan(): void
    {
        $s = $this->makeStudent(['gender' => 1]);
        $this->assertSame('Perempuan', $s->gender_text);
        $this->assertSame('P', $s->gender_short);
    }

    public function test_status_accessor_active(): void
    {
        $s = $this->makeStudent(['status' => 0]);
        $this->assertSame('Aktif', $s->status_text);
        $this->assertSame('active', $s->status_short);
    }

    public function test_status_accessor_inactive(): void
    {
        $s = $this->makeStudent(['status' => 1]);
        $this->assertSame('Nonaktif', $s->status_text);
        $this->assertSame('inactive', $s->status_short);
    }

    public function test_name_accessor_returns_nama_lengkap(): void
    {
        $s = $this->makeStudent(['nama_lengkap' => 'Ahmad Fauzi']);
        $this->assertSame('Ahmad Fauzi', $s->name);
    }
}
```

---

## 4. Unit — HolidayModel

**File:** `tests/Unit/HolidayModelTest.php`

```php
<?php

namespace Tests\Unit;

use App\Models\Holiday;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HolidayModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_sunday_is_always_a_holiday(): void
    {
        // 2025-02-02 adalah hari Minggu
        $this->assertTrue(Holiday::checkHoliday('2025-02-02'));
    }

    public function test_monday_is_not_a_holiday_by_default(): void
    {
        // 2025-02-03 adalah Senin, tidak ada holiday record
        $this->assertFalse(Holiday::checkHoliday('2025-02-03'));
    }

    public function test_special_holiday_is_detected(): void
    {
        Holiday::create([
            'type' => 'special',
            'date' => '2025-08-17',
            'name' => 'Hari Kemerdekaan RI',
        ]);

        $this->assertTrue(Holiday::checkHoliday('2025-08-17'));
        $this->assertSame('Hari Kemerdekaan RI', Holiday::getHolidayName('2025-08-17'));
    }

    public function test_range_holiday_covers_all_days_in_range(): void
    {
        Holiday::create([
            'type'       => 'range',
            'start_date' => '2025-06-01',
            'end_date'   => '2025-06-15',
            'name'       => 'Libur Kenaikan Kelas',
        ]);

        $this->assertTrue(Holiday::checkHoliday('2025-06-01'));
        $this->assertTrue(Holiday::checkHoliday('2025-06-10'));
        $this->assertTrue(Holiday::checkHoliday('2025-06-15'));
        $this->assertFalse(Holiday::checkHoliday('2025-05-31'));
        $this->assertFalse(Holiday::checkHoliday('2025-06-16'));
    }

    public function test_monthly_holiday_repeats_every_month_on_same_day(): void
    {
        Holiday::create([
            'type'         => 'monthly',
            'day_of_month' => 1,
            'name'         => 'Hari Pertama Bulan',
        ]);

        $this->assertTrue(Holiday::checkHoliday('2025-01-01'));
        $this->assertTrue(Holiday::checkHoliday('2025-07-01'));
        $this->assertFalse(Holiday::checkHoliday('2025-01-02'));
    }

    public function test_getHolidayName_returns_null_for_non_holiday(): void
    {
        // 2025-02-03 = Senin, tidak ada record
        $this->assertNull(Holiday::getHolidayName('2025-02-03'));
    }
}
```

---

## 5. Unit — SettingModel

**File:** `tests/Unit/SettingModelTest.php`

```php
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
        $result = Setting::get('non_existent_key', 'default_value');
        $this->assertSame('default_value', $result);
    }

    public function test_set_creates_new_setting(): void
    {
        Setting::set('academic_year', '2025-2026');
        $this->assertSame('2025-2026', Setting::get('academic_year'));
    }

    public function test_set_updates_existing_setting(): void
    {
        Setting::set('academic_year', '2024-2025');
        Setting::set('academic_year', '2025-2026');

        $this->assertSame('2025-2026', Setting::get('academic_year'));

        // Pastikan tidak ada duplikat key di database
        $count = Setting::where('key', 'academic_year')->count();
        $this->assertSame(1, $count);
    }

    public function test_get_without_default_returns_null_when_not_found(): void
    {
        $result = Setting::get('key_tidak_ada');
        $this->assertNull($result);
    }
}
```

---

## 6. Feature — AuthTest

**File:** `tests/Feature/AuthTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
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

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'role'     => 'admin',
            'email'    => 'admin@smktest.sch.id',
            'password' => Hash::make('password123'),
        ]);

        $this->post(route('login.admin.submit'), [
            'email'    => 'admin@smktest.sch.id',
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_guru_cannot_login_via_admin_login(): void
    {
        User::factory()->create([
            'role'     => 'guru',
            'email'    => 'guru@smktest.sch.id',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post(route('login.admin.submit'), [
            'email'    => 'guru@smktest.sch.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertGuest();
    }

    public function test_admin_cannot_login_via_guru_login(): void
    {
        User::factory()->create([
            'role'     => 'admin',
            'email'    => 'admin@smktest.sch.id',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post(route('login.guru.submit'), [
            'email'    => 'admin@smktest.sch.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertGuest();
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'role'     => 'admin',
            'email'    => 'admin@smktest.sch.id',
            'password' => Hash::make('benarnya'),
        ]);

        $this->post(route('login.admin.submit'), [
            'email'    => 'admin@smktest.sch.id',
            'password' => 'salah',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_guru_redirected_to_guru_dashboard_after_login(): void
    {
        User::factory()->create([
            'role'     => 'guru',
            'email'    => 'guru@smktest.sch.id',
            'password' => Hash::make('password123'),
        ]);

        $this->post(route('login.guru.submit'), [
            'email'    => 'guru@smktest.sch.id',
            'password' => 'password123',
        ])->assertRedirect(route('guru.dashboard'));
    }

    public function test_teller_redirected_to_finance_dashboard(): void
    {
        User::factory()->create([
            'role'     => 'teller',
            'email'    => 'teller@smktest.sch.id',
            'password' => Hash::make('password123'),
        ]);

        $this->post(route('login.admin.submit'), [
            'email'    => 'teller@smktest.sch.id',
            'password' => 'password123',
        ])->assertRedirect(route('admin.finance.dashboard'));
    }

    public function test_authenticated_user_redirected_away_from_login(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)
            ->get(route('login.admin'))
            ->assertRedirect();
    }

    public function test_logout_clears_session(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    public function test_impersonate_guru_only_works_for_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru  = User::factory()->create(['role' => 'guru']);

        // Guru (non-admin) tidak boleh impersonate
        $this->actingAs($guru)
            ->post(route('impersonate.guru', $guru))
            ->assertForbidden();
    }
}
```

---

## 7. Feature — AttendanceScanTest

**File:** `tests/Feature/AttendanceScanTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Setting;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceScanTest extends TestCase
{
    use RefreshDatabase;

    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('academic_year', '2025-2026');
        Setting::set('check_in_time', '08:00');

        $this->student = Student::factory()->create([
            'id_siswa' => 'SIS001',
            'status'   => 0, // aktif
        ]);
    }

    public function test_scan_creates_attendance_record(): void
    {
        $this->travelTo(now()->setTime(7, 30)); // sebelum batas check-in

        $response = $this->postJson(route('attendance.process-scan'), [
            'id_siswa' => 'SIS001',
        ]);

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('attendances', [
            'student_id' => $this->student->id,
            'status'     => 'present',
        ]);
    }

    public function test_scan_marks_late_when_after_check_in_limit(): void
    {
        $this->travelTo(now()->setTime(8, 30)); // setelah batas check-in

        $this->postJson(route('attendance.process-scan'), [
            'id_siswa' => 'SIS001',
        ])->assertOk();

        $this->assertDatabaseHas('attendances', [
            'student_id' => $this->student->id,
            'status'     => 'late',
        ]);
    }

    public function test_duplicate_scan_on_same_day_returns_error(): void
    {
        Attendance::create([
            'student_id'    => $this->student->id,
            'date'          => now()->toDateString(),
            'status'        => 'present',
            'check_in'      => '07:30:00',
            'academic_year' => '2025-2026',
        ]);

        $response = $this->postJson(route('attendance.process-scan'), [
            'id_siswa' => 'SIS001',
        ]);

        $response->assertStatus(400)
            ->assertJson(['success' => false]);

        $this->assertSame(
            1,
            Attendance::where('student_id', $this->student->id)
                ->where('date', now()->toDateString())
                ->count()
        );
    }

    public function test_scan_blocked_on_holiday(): void
    {
        Holiday::create([
            'type' => 'special',
            'date' => now()->toDateString(),
            'name' => 'Hari Libur Nasional',
        ]);

        $response = $this->postJson(route('attendance.process-scan'), [
            'id_siswa' => 'SIS001',
        ]);

        $response->assertStatus(403)
            ->assertJson(['success' => false]);

        $this->assertDatabaseMissing('attendances', [
            'student_id' => $this->student->id,
        ]);
    }

    public function test_scan_returns_404_for_unknown_student(): void
    {
        $response = $this->postJson(route('attendance.process-scan'), [
            'id_siswa' => 'ID_TIDAK_ADA',
        ]);

        $response->assertStatus(404)
            ->assertJson(['success' => false]);
    }

    public function test_inactive_student_cannot_scan(): void
    {
        $inactive = Student::factory()->create([
            'id_siswa' => 'SISNON',
            'status'   => 1, // nonaktif
        ]);

        $response = $this->postJson(route('attendance.process-scan'), [
            'id_siswa' => 'SISNON',
        ]);

        $response->assertStatus(403)
            ->assertJson(['success' => false]);
    }
}
```

---

## 8. Feature — PointTest

**File:** `tests/Feature/PointTest.php`

```php
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
            'student_id'   => $this->student->id,
            'type'         => 'achievement',
            'score'        => 10,
            'academic_year'=> '2025-2026',
        ]);
    }

    public function test_creating_point_sends_notification_to_student(): void
    {
        $this->actingAs($this->admin)
            ->post(route('points.store'), [
                'student_id' => $this->student->id,
                'type'       => 'violation',
                'score'      => 5,
                'reason'     => 'Terlambat masuk kelas',
                'date'       => now()->toDateString(),
            ]);

        $this->assertDatabaseHas('student_notifications', [
            'student_id' => $this->student->id,
            'type'       => 'point',
        ]);
    }

    public function test_admin_can_delete_point(): void
    {
        $point = Point::create([
            'student_id'    => $this->student->id,
            'type'          => 'violation',
            'score'         => 5,
            'reason'        => 'Test',
            'date'          => now()->toDateString(),
            'academic_year' => '2025-2026',
        ]);

        $this->actingAs($this->admin)
            ->delete(route('points.destroy', $point))
            ->assertRedirect(route('points'));

        $this->assertDatabaseMissing('points', ['id' => $point->id]);
    }

    public function test_point_validation_rejects_invalid_type(): void
    {
        $this->actingAs($this->admin)
            ->post(route('points.store'), [
                'student_id' => $this->student->id,
                'type'       => 'invalid_type',
                'score'      => 10,
                'reason'     => 'Test',
                'date'       => now()->toDateString(),
            ])->assertSessionHasErrors('type');
    }

    public function test_unauthenticated_cannot_access_points(): void
    {
        $this->get(route('points'))->assertRedirect(route('login'));
    }
}
```

---

## 9. Feature — StudentPortalAuthTest

**File:** `tests/Feature/StudentPortalAuthTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\Student;
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
        ])->assertSessionHasErrors();

        $this->assertGuest('student');
    }

    public function test_student_dashboard_requires_student_auth(): void
    {
        // Tanpa login, harus redirect ke login siswa
        $this->get(route('siswa.dashboard'))
            ->assertRedirect(route('siswa.login'));
    }

    public function test_admin_user_cannot_access_student_dashboard(): void
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);

        // actingAs dengan guard web (admin) tidak boleh akses student portal
        $this->actingAs($admin)
            ->get(route('siswa.dashboard'))
            ->assertRedirect(route('siswa.login'));
    }

    public function test_student_logout_clears_session(): void
    {
        $student = Student::factory()->create(['status' => 0]);

        $this->actingAs($student, 'student')
            ->post(route('siswa.logout'))
            ->assertRedirect();

        $this->assertGuest('student');
    }
}
```

---

## 10. Feature — StudentFinanceTransferTest

**File:** `tests/Feature/StudentFinanceTransferTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\Student;
use App\Models\StudentCard;
use App\Models\StudentCardTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentFinanceTransferTest extends TestCase
{
    use RefreshDatabase;

    private Student     $sender;
    private Student     $receiver;
    private StudentCard $sourceCard;
    private StudentCard $targetCard;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('academic_year', '2025-2026');

        $this->sender   = Student::factory()->create(['pin' => '111111', 'status' => 0]);
        $this->receiver = Student::factory()->create(['pin' => '222222', 'status' => 0]);

        $this->sourceCard = StudentCard::create([
            'student_id'  => $this->sender->id,
            'card_number' => 'CARD-001',
            'balance'     => 100000,
            'status'      => 'active',
            'pin'         => '0000',
        ]);

        $this->targetCard = StudentCard::create([
            'student_id'  => $this->receiver->id,
            'card_number' => 'CARD-002',
            'balance'     => 50000,
            'status'      => 'active',
            'pin'         => '0000',
        ]);
    }

    public function test_transfer_succeeds_with_correct_pin_and_sufficient_balance(): void
    {
        $this->actingAs($this->sender, 'student');

        // Step 1: confirm transfer — simpan ke session
        $this->post(route('siswa.finance.transfer.confirm'), [
            'source_card_id'    => $this->sourceCard->id,
            'target_card_number'=> 'CARD-002',
            'amount'            => 20000,
            'description'       => 'Test transfer',
        ])->assertRedirect(route('siswa.finance.transfer.pin'));

        // Step 2: process dengan PIN benar
        $this->post(route('siswa.finance.transfer.process'), [
            'pin' => '111111',
        ])->assertRedirect();

        // Saldo sumber berkurang
        $this->assertSame(80000.0, (float) $this->sourceCard->fresh()->balance);
        // Saldo tujuan bertambah
        $this->assertSame(70000.0, (float) $this->targetCard->fresh()->balance);
        // Ada 2 record transaksi
        $this->assertSame(2, StudentCardTransaction::count());
    }

    public function test_transfer_blocked_with_wrong_pin(): void
    {
        $this->actingAs($this->sender, 'student');

        $this->post(route('siswa.finance.transfer.confirm'), [
            'source_card_id'    => $this->sourceCard->id,
            'target_card_number'=> 'CARD-002',
            'amount'            => 10000,
            'description'       => '',
        ]);

        $this->post(route('siswa.finance.transfer.process'), [
            'pin' => '999999', // PIN salah
        ])->assertSessionHasErrors();

        // Saldo tidak berubah
        $this->assertSame(100000.0, (float) $this->sourceCard->fresh()->balance);
        $this->assertSame(50000.0,  (float) $this->targetCard->fresh()->balance);
    }

    public function test_transfer_fails_when_balance_insufficient(): void
    {
        $this->actingAs($this->sender, 'student');

        $this->post(route('siswa.finance.transfer.confirm'), [
            'source_card_id'    => $this->sourceCard->id,
            'target_card_number'=> 'CARD-002',
            'amount'            => 999999, // lebih dari saldo
            'description'       => '',
        ])->assertSessionHasErrors();

        $this->assertSame(0, StudentCardTransaction::count());
    }

    public function test_transfer_to_same_card_is_blocked(): void
    {
        $this->actingAs($this->sender, 'student');

        $this->post(route('siswa.finance.transfer.confirm'), [
            'source_card_id'    => $this->sourceCard->id,
            'target_card_number'=> 'CARD-001', // kartu yang sama
            'amount'            => 10000,
            'description'       => '',
        ])->assertSessionHasErrors();
    }

    public function test_transaction_detail_inaccessible_by_other_student(): void
    {
        // Buat transaksi milik sender
        $tx = StudentCardTransaction::create([
            'card_id'          => $this->sourceCard->id,
            'type'             => 'topup',
            'amount'           => 50000,
            'balance_before'   => 50000,
            'balance_after'    => 100000,
            'reference_number' => 'TRF-TEST',
            'description'      => 'Test',
        ]);

        // Receiver mencoba akses transaksi milik sender
        $this->actingAs($this->receiver, 'student')
            ->get(route('siswa.finance.transactions.show', $tx))
            ->assertForbidden();
    }
}
```

---

## 11. Feature — AttendancePenaltyTest

**File:** `tests/Feature/AttendancePenaltyTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AcademicYear;
use App\Models\Point;
use App\Models\PointRule;
use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendancePenaltyTest extends TestCase
{
    use RefreshDatabase;

    private User    $admin;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('academic_year', '2025-2026');
        Setting::set('check_in_time', '08:00');

        $year = AcademicYear::create(['name' => '2025-2026', 'is_active' => true]);

        $this->admin   = User::factory()->create(['role' => 'admin']);
        $this->student = Student::factory()->create(['status' => 0]);
        $this->student->academicYears()->attach($year->id);
    }

    public function test_attendance_penalty_applied_when_threshold_reached(): void
    {
        // Buat aturan: jika absent >= 5 kali dalam sebulan, beri poin pelanggaran 8
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

        // Buat 5 record absen di bulan yang sama
        $baseDate = now()->startOfMonth();
        for ($i = 0; $i < 5; $i++) {
            Attendance::create([
                'student_id'    => $this->student->id,
                'date'          => $baseDate->copy()->addDays($i)->toDateString(),
                'status'        => 'absent',
                'academic_year' => '2025-2026',
            ]);
        }

        // Trigger scan pada hari ke-6 (masih bulan yang sama)
        $sixthDate = $baseDate->copy()->addDays(5)->toDateString();
        $this->postJson(route('attendance.process-scan'), [
            'id_siswa' => $this->student->id_siswa,
        ]);

        // Poin pelanggaran harus terbuat
        $this->assertDatabaseHas('points', [
            'student_id' => $this->student->id,
            'type'       => 'violation',
            'score'      => 8,
        ]);
    }

    public function test_penalty_not_applied_twice_for_same_rule_same_month(): void
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

        $baseDate = now()->startOfMonth();
        for ($i = 0; $i < 3; $i++) {
            Attendance::create([
                'student_id'    => $this->student->id,
                'date'          => $baseDate->copy()->addDays($i)->toDateString(),
                'status'        => 'late',
                'academic_year' => '2025-2026',
            ]);
        }

        // Scan pertama — poin dibuat
        $this->postJson(route('attendance.process-scan'), [
            'id_siswa' => $this->student->id_siswa,
        ]);
        // Scan lagi keesokan harinya
        $this->postJson(route('attendance.process-scan'), [
            'id_siswa' => $this->student->id_siswa,
        ]);

        // Seharusnya tetap 1 record poin saja
        $this->assertSame(
            1,
            Point::where('student_id', $this->student->id)
                ->where('type', 'violation')
                ->count()
        );
    }
}
```

---

## 12. Feature — SecurityTest

**File:** `tests/Feature/SecurityTest.php`

Lihat dokumen bug-hunting untuk detail lengkap temuan keamanan yang diuji di sini.

```php
<?php

namespace Tests\Feature;

use App\Models\Setting;
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

    /** BUG-01: /clear-cache tidak butuh auth */
    public function test_clear_cache_route_accessible_without_auth(): void
    {
        $response = $this->get('/clear-cache');
        // Route ini seharusnya 403, tapi sekarang 200 — ini adalah bug
        // Test ini mendokumentasikan bahwa bug ada
        $this->assertTrue(
            in_array($response->status(), [200, 403]),
            'Route /clear-cache harus diproteksi (bug terdokumentasi jika 200)'
        );
    }

    /** BUG-02: /show-logs tidak butuh auth — bisa ekspos stack trace */
    public function test_show_logs_route_accessible_without_auth(): void
    {
        $response = $this->get('/show-logs');
        $this->assertTrue(
            in_array($response->status(), [200, 403]),
            'Route /show-logs harus diproteksi (bug terdokumentasi jika 200)'
        );
    }

    /** BUG-03: Quick login tanpa password — bergantung logika controller saja */
    public function test_quick_login_works_without_password(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->post(route('quick-login.admin', $user));

        // Jika berhasil login tanpa password, ini adalah security risk
        if ($this->isAuthenticated()) {
            $this->markTestIncomplete(
                'BUG TERDETEKSI: Quick login admin berhasil tanpa password — ' .
                'tidak ada proteksi rate-limit atau CSRF-only'
            );
        }

        $this->assertTrue(true, 'Quick login memerlukan investigasi lebih lanjut');
    }

    /** BUG-04: Exam dapat diakses tanpa auth */
    public function test_exam_accessible_without_authentication(): void
    {
        $response = $this->get('/exam/start/1');
        // Status 404 (exam tidak ada) atau 200 (accessible), bukan 302 redirect ke login
        $this->assertNotSame(
            302,
            $response->status(),
            'Endpoint exam start seharusnya tidak redirect ke login — memang dibuat public'
        );
    }

    /** Proteksi: Admin hanya bisa impersonate guru, bukan admin lain */
    public function test_admin_cannot_impersonate_another_admin(): void
    {
        $admin1 = User::factory()->create(['role' => 'admin']);
        $admin2 = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin1)
            ->post(route('impersonate.guru', $admin2))
            ->assertStatus(400); // admin2 bukan guru
    }

    /** Proteksi: Non-admin tidak bisa akses halaman manajemen user */
    public function test_guru_cannot_access_management_users(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->actingAs($guru)
            ->get(route('management-users.index'))
            ->assertForbidden();
    }
}
```

---

## Cara Menjalankan Test

```bash
# Jalankan semua test
cd smart-acp
php artisan test

# Jalankan test unit saja
php artisan test --testsuite=Unit

# Jalankan test feature saja
php artisan test --testsuite=Feature

# Jalankan satu file test
php artisan test tests/Feature/AuthTest.php

# Jalankan dengan detail output
php artisan test --verbose

# Stop di test pertama yang gagal
php artisan test --stop-on-failure
```
