<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\ParentUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return redirect()->route('login.admin');
    }

    public function showAdminLogin()
    {
        return view('auth.login', ['loginRole' => 'admin']);
    }

    public function showGuruLogin()
    {
        return view('auth.login', ['loginRole' => 'guru']);
    }

    public function login(Request $request)
    {
        return $this->loginAdmin($request);
    }

    public function loginAdmin(Request $request)
    {
        return $this->loginForRole($request, 'admin');
    }

    public function loginGuru(Request $request)
    {
        return $this->loginForRole($request, 'guru');
    }

    private function loginForRole(Request $request, string $role)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            $roleMatches = $role === 'guru'
                ? $user->isGuru()
                : in_array($user->role, ['admin', 'wali_kelas', 'wakasis', 'bk', 'teller', 'bums'], true);

            if (!$roleMatches) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Akun ini tidak dapat digunakan untuk login ' . ($role === 'guru' ? 'guru.' : 'admin.'),
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->route($this->dashboardRouteFor($user));
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak cocok untuk login ' . ($role === 'guru' ? 'guru.' : 'admin.'),
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function showRegister()
    {
        // Redirect to login or show a basic register view if it exists
        return view('auth.login'); // For now, just show login or create a simple register view
    }

    public function showProfile()
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function impersonateGuru(Request $request, User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if (!$user->isGuru()) {
            abort(400, 'User yang dipilih bukan guru.');
        }

        $request->session()->put('impersonate_admin_id', auth()->id());

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('guru.dashboard')->with('success', 'Berhasil masuk sebagai ' . $user->name);
    }

    public function quickLogin(Request $request, User $user)
    {
        abort_unless(in_array($user->role, ['admin', 'guru', 'wali_kelas', 'wakasis', 'bk', 'teller', 'bums'], true), 400);

        return $this->loginQuicklyAs($request, $user);
    }

    public function quickLoginAsRole(Request $request, User $user, string $role)
    {
        $roleMatches = $role === 'guru'
            ? $user->isGuru()
            : in_array($user->role, ['admin', 'wali_kelas', 'wakasis', 'bk', 'teller', 'bums'], true);
        abort_unless($roleMatches, 404);

        return $this->loginQuicklyAs($request, $user);
    }

    private function loginQuicklyAs(Request $request, User $user)
    {
        $request->session()->put('quick_login_from_login_page', true);
        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route($this->dashboardRouteFor($user))
            ->with('success', 'Berhasil login sebagai ' . $user->name);
    }

    private function dashboardRouteFor(User $user): string
    {
        if ($user->isGuru()) {
            return 'guru.dashboard';
        }

        if ($user->isTeller()) {
            return 'admin.finance.dashboard';
        }

        if ($user->isBums()) {
            return 'admin.finance.payments.history';
        }

        return 'dashboard';
    }

    public function quickLoginAsGuru(Request $request, User $user)
    {
        return $this->quickLoginAsRole($request, $user, 'guru');
    }

    public function quickLoginAsAdmin(Request $request, User $user)
    {
        return $this->quickLoginAsRole($request, $user, 'admin');
    }

    public function quickLoginStudent(Request $request, Student $student)
    {
        $request->session()->put('quick_login_from_login_page', true);

        Auth::guard('student')->login($student, true);
        $request->session()->regenerate();

        return redirect()->intended(route('siswa.dashboard'))->with('success', 'Berhasil login sebagai ' . $student->name);
    }

    public function quickLoginParent(Request $request, ParentUser $parent)
    {
        $request->session()->put('quick_login_from_login_page', true);

        Auth::guard('parent')->login($parent, true);
        $request->session()->regenerate();

        return redirect()->intended(route('parent.dashboard'))->with('success', 'Berhasil login sebagai ' . $parent->name);
    }

    public function stopImpersonateGuru(Request $request)
    {
        $adminId = $request->session()->pull('impersonate_admin_id');
        $fromLoginPage = $request->session()->pull('quick_login_from_login_page');

        if ($fromLoginPage) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/login')->with('success', 'Kembali ke halaman login.');
        }

        if (!$adminId) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/login');
        }

        $admin = User::find($adminId);
        if (!$admin || !$admin->isAdmin()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/login');
        }

        Auth::login($admin, true);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Kembali ke akun Administrator.');
    }
}
