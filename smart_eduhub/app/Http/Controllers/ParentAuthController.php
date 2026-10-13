<?php

namespace App\Http\Controllers;

use App\Models\ParentUser;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ParentAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('parent')->check()) {
            return redirect()->route('parent.dashboard');
        }
        return view('parent.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('parent')->attempt($credentials, true)) {
            $request->session()->regenerate();
            return redirect()->intended(route('parent.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau Password yang Anda masukkan tidak cocok.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::guard('parent')->check()) {
            return redirect()->route('parent.dashboard');
        }

        $students = Student::active()->orderBy('nama_lengkap', 'asc')->get();
        if ($students->isEmpty()) {
            $students = Student::orderBy('nama_lengkap', 'asc')->get();
        }

        return view('parent.register', compact('students'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:parents,email',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:L,P',
            'student_id' => 'required|exists:students,id',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $parent = ParentUser::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'student_id' => $validated['student_id'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::guard('parent')->login($parent, true);

        return redirect()->route('parent.dashboard')->with('success', 'Pendaftaran Wali Murid berhasil! Selamat datang.');
    }

    public function logout(Request $request)
    {
        Auth::guard('parent')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('parent.login');
    }

    public function showForgotPassword()
    {
        if (Auth::guard('parent')->check()) {
            return redirect()->route('parent.dashboard');
        }
        return view('parent.forgot-password');
    }

    public function processForgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'verification_field' => 'required|in:phone,student_name,gender',
            'verification_value' => 'required|string',
        ]);

        $parent = ParentUser::where('email', $validated['email'])->first();
        if (!$parent) {
            return back()->withErrors([
                'email' => 'Akun wali murid dengan email tersebut tidak ditemukan.',
            ])->onlyInput('email', 'verification_field');
        }

        $field = $validated['verification_field'];
        $inputValue = trim($validated['verification_value']);
        $match = false;

        if ($field === 'phone') {
            $cleanParent = preg_replace('/\D+/', '', (string) $parent->phone);
            $cleanInput = preg_replace('/\D+/', '', $inputValue);
            $match = !empty($cleanParent) && $cleanParent === $cleanInput;
        } elseif ($field === 'student_name') {
            $student = $parent->student;
            $studentName = $student ? trim($student->nama_lengkap) : '';
            $match = !empty($studentName) && strcasecmp($studentName, $inputValue) === 0;
        } elseif ($field === 'gender') {
            $match = strtoupper($inputValue) === $parent->gender;
        }

        if (!$match) {
            return back()->withErrors([
                'verification_value' => 'Data verifikasi tidak cocok dengan akun Anda.',
            ])->onlyInput('email', 'verification_field');
        }

        $token = Str::random(64);
        $request->session()->put('reset_parent_password_token', [
            'token' => $token,
            'parent_id' => $parent->id,
            'email' => $parent->email,
            'expires_at' => now()->addMinutes(30),
        ]);

        return redirect()->route('parent.reset-password', [
            'token' => $token,
            'email' => $parent->email,
        ])->with('success', 'Verifikasi berhasil. Silakan masukkan password baru Anda.');
    }

    public function showResetPassword(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');
        $sessionToken = $request->session()->get('reset_parent_password_token');

        if (!$sessionToken
            || $sessionToken['token'] !== $token
            || $sessionToken['email'] !== $email
            || now()->greaterThan($sessionToken['expires_at'])) {
            return redirect()->route('parent.forgot-password')
                ->withErrors(['reset_token' => 'Link reset password tidak valid atau sudah kedaluwarsa. Silakan coba lagi.']);
        }

        return view('parent.reset-password', compact('token', 'email'));
    }

    public function processResetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $sessionToken = $request->session()->get('reset_parent_password_token');
        if (!$sessionToken
            || $sessionToken['token'] !== $validated['token']
            || $sessionToken['email'] !== $validated['email']
            || now()->greaterThan($sessionToken['expires_at'])) {
            return redirect()->route('parent.forgot-password')
                ->withErrors(['reset_token' => 'Link reset password tidak valid atau sudah kedaluwarsa. Silakan coba lagi.']);
        }

        $parent = ParentUser::find($sessionToken['parent_id']);
        if (!$parent) {
            return redirect()->route('parent.forgot-password')
                ->withErrors(['reset_token' => 'Data akun wali murid tidak ditemukan.']);
        }

        $parent->update(['password' => Hash::make($validated['password'])]);
        $request->session()->forget('reset_parent_password_token');

        return redirect()->route('parent.login')
            ->with('success', 'Password Anda berhasil direset. Silakan login dengan password baru Anda.');
    }
}
