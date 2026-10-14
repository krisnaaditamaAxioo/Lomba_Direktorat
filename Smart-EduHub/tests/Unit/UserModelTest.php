<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserModelTest extends TestCase
{
    private function user(string $role): User
    {
        $u = new User();
        $u->role = $role;
        return $u;
    }

    // ---------------------------------------------------------------
    // Role detection
    // ---------------------------------------------------------------
    public function test_isAdmin_true_only_for_admin(): void
    {
        $this->assertTrue($this->user('admin')->isAdmin());
        $this->assertFalse($this->user('guru')->isAdmin());
        $this->assertFalse($this->user('teller')->isAdmin());
        $this->assertFalse($this->user('wali_kelas')->isAdmin());
    }

    public function test_isGuru_true_only_for_guru(): void
    {
        $this->assertTrue($this->user('guru')->isGuru());
        $this->assertFalse($this->user('admin')->isGuru());
        $this->assertFalse($this->user('bk')->isGuru());
    }

    public function test_isTeller_true_only_for_teller(): void
    {
        $this->assertTrue($this->user('teller')->isTeller());
        $this->assertFalse($this->user('admin')->isTeller());
        $this->assertFalse($this->user('bums')->isTeller());
    }

    public function test_isBums_true_only_for_bums(): void
    {
        $this->assertTrue($this->user('bums')->isBums());
        $this->assertFalse($this->user('teller')->isBums());
    }

    // ---------------------------------------------------------------
    // Finance staff check
    // ---------------------------------------------------------------
    public function test_isFinanceStaff_true_for_admin_teller_bums(): void
    {
        foreach (['admin', 'teller', 'bums'] as $role) {
            $this->assertTrue(
                $this->user($role)->isFinanceStaff(),
                "$role seharusnya isFinanceStaff"
            );
        }
    }

    public function test_isFinanceStaff_false_for_non_finance_roles(): void
    {
        foreach (['guru', 'wali_kelas', 'wakasis', 'bk'] as $role) {
            $this->assertFalse(
                $this->user($role)->isFinanceStaff(),
                "$role seharusnya bukan finance staff"
            );
        }
    }

    // ---------------------------------------------------------------
    // Finance permissions — semua finance roles harus bisa semua operasi
    // ---------------------------------------------------------------
    public function test_finance_permissions_granted_to_finance_roles(): void
    {
        $methods = [
            'canAccessFinanceDashboard',
            'canTopupFinance',
            'canProcessFinancePayment',
            'canUpdateFinanceCardStatus',
            'canWithdrawFinance',
            'canManageFinanceCards',
            'canEditFinanceTransactions',
            'canDeleteFinanceTransactions',
            'canApproveFinanceRequests',
            'canViewFinanceRekap',
        ];

        foreach (['admin', 'teller', 'bums'] as $role) {
            $user = $this->user($role);
            foreach ($methods as $method) {
                $this->assertTrue(
                    $user->$method(),
                    "$role harus bisa $method()"
                );
            }
        }
    }

    public function test_finance_permissions_denied_to_non_finance_roles(): void
    {
        foreach (['guru', 'wali_kelas', 'wakasis', 'bk'] as $role) {
            $user = $this->user($role);
            $this->assertFalse($user->canTopupFinance(), "$role tidak boleh topup");
            $this->assertFalse($user->canAccessFinanceDashboard(), "$role tidak boleh akses finance dashboard");
        }
    }

    // ---------------------------------------------------------------
    // roleLabel accessor
    // ---------------------------------------------------------------
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
                $this->user($role)->role_label,
                "roleLabel untuk '$role' harus '$expected'"
            );
        }
    }

    public function test_roleLabel_unknown_role_returns_ucwords_fallback(): void
    {
        $user = $this->user('kepala_sekolah');
        $this->assertSame('Kepala Sekolah', $user->role_label);
    }
}
