<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        $isMaintenance = \App\Models\Setting::get('maintenance_mode', false);
        if (!$isMaintenance) {
            return $next($request);
        }

        // Bypass untuk user yang sudah login sebagai admin
        if (\Auth::check()) {
            $user = \Auth::user();
            // Cek role admin: role = 'admin' atau user ada di tabel management_users sebagai admin
            if (optional($user)->role === 'admin') {
                return $next($request);
            }
            // Cek lewat management_users (jika struktur lama)
            try {
                $isAdmin = \DB::table('management_users')
                    ->where('email', $user->email)
                    ->whereIn('role', ['admin', 'super_admin', 'owner'])
                    ->exists();
                if ($isAdmin) {
                    return $next($request);
                }
            } catch (\Exception $e) {
                // ignore jika tabel tidak ada
            }
        }

        // Izinkan akses ke route login admin dan halaman maintenance sendiri
        $allowedPaths = ['login', 'admin/login', 'maintenance', 'cek-status', 'daftar', 'join', 'cek-status/proses', 'join/status'];
        foreach ($allowedPaths as $path) {
            if ($request->is($path) || $request->is($path . '/*')) {
                return $next($request);
            }
        }

        // Untuk request API / JSON
        if ($request->expectsJson() || $request->is('api/*') || $request->is('siswa/api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Sistem sedang dalam perbaikan (maintenance). Silakan coba kembali beberapa saat lagi.',
                'maintenance' => true,
            ], 503);
        }

        // Tampilkan halaman maintenance
        $appName = config('app.name', 'Smart Axioo');
        $maintenanceMessage = \App\Models\Setting::get('maintenance_message', '');
        $logoDataUri = null;
        $logoPath = public_path('images/axioo.png');
        if (is_file($logoPath)) {
            $logoDataUri = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        return response()->view('errors.maintenance', [
            'appName' => $appName,
            'message' => $maintenanceMessage,
            'logoDataUri' => $logoDataUri,
        ], 503);
    }
}
