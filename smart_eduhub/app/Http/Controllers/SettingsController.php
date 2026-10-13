<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;
use App\Models\AcademicYear;
use ZipArchive;

class SettingsController extends Controller
{
    private function checkAdmin()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke menu Pengaturan.');
        }
    }

    public function index()
    {
        $this->checkAdmin();
        $settings = [
            'timezone' => Setting::get('timezone', 'Asia/Jakarta'),
            'language' => Setting::get('language', 'id'),
            'check_in_time' => Setting::get('check_in_time', '08:00'),
            'academic_year' => Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)),
            'semester' => Setting::get('semester', 'Odd'),
            'fast_payment_amount' => Setting::get('fast_payment_amount', '5000'),
            'whatsapp_group_link' => Setting::get('whatsapp_group_link', ''),
            'maintenance_mode' => Setting::get('maintenance_mode', false),
            'maintenance_message' => Setting::get('maintenance_message', ''),
            'trainer_name' => Setting::get('trainer_name', ''),
            'trainer_no_serti' => Setting::get('trainer_no_serti', ''),
            'trainer_nbm' => Setting::get('trainer_nbm', ''),
        ];
        
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        
        return view('settings.index', compact('settings', 'academicYears'));
    }

    public function storeAcademicYear(Request $request)
    {
        $this->checkAdmin();
        $validated = $request->validate([
            'name' => 'required|string|unique:academic_years,name',
        ]);

        AcademicYear::create($validated);

        return redirect()->back()->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function activateAcademicYear(AcademicYear $academicYear)
    {
        $this->checkAdmin();
        
        // Deactivate all
        AcademicYear::where('id', '!=', $academicYear->id)->update(['is_active' => false]);
        
        // Activate selected
        $academicYear->update(['is_active' => true]);
        
        // Update global setting for backward compatibility
        Setting::set('academic_year', $academicYear->name);

        return redirect()->back()->with('success', "Tahun ajaran $academicYear->name telah diaktifkan.");
    }

    public function destroyAcademicYear(AcademicYear $academicYear)
    {
        $this->checkAdmin();
        
        if ($academicYear->is_active) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus tahun ajaran yang sedang aktif.');
        }

        $academicYear->delete();

        return redirect()->back()->with('success', 'Tahun ajaran berhasil dihapus.');
    }

    public function update(Request $request)
    {
        $this->checkAdmin();
        $validated = $request->validate([
            'timezone' => 'required|string',
            'language' => 'required|string',
            'check_in_time' => 'required|string',
            'school_name' => 'nullable|string',
            'school_address' => 'nullable|string',
            'semester' => 'nullable|string',
            'fast_payment_amount' => 'required|numeric|min:1000',
            'whatsapp_group_link' => 'nullable|url',
            'maintenance_mode' => 'nullable',
            'maintenance_message' => 'nullable|string|max:1000',
            'trainer_name' => 'nullable|string|max:100',
            'trainer_no_serti' => 'nullable|string|max:50',
            'trainer_nbm' => 'nullable|string|max:50',
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'maintenance_mode') {
                Setting::set($key, (bool)$value);
            } else {
                Setting::set($key, $value);
            }
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public function setAcademicYear(Request $request)
    {
        $request->validate([
            'academic_year' => 'required|string',
        ]);

        session(['view_academic_year' => $request->academic_year]);

        return redirect()->back()->with('success', 'Viewing academic year changed to ' . $request->academic_year);
    }

    public function backup(Request $request)
    {
        $this->checkAdmin();
        $validated = $request->validate([
            'tables' => 'required|array',
            'tables.*' => 'string|in:students,attendances,points,exam_questions,exam_results,courses,modules,student_permits,letters,registrations,student_cards,student_card_requests,student_card_transactions,holidays,angkatans,point_rules,class_rooms,academic_years',
        ]);

        $tables = $validated['tables'];
        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupFile = $backupDir . '/backup_' . $timestamp . '.sql';

        $sqlContent = "-- Backup Smart Axioo\n";
        $sqlContent .= "-- Generated: " . now()->toDateTimeString() . "\n";
        $sqlContent .= "-- Tables: " . implode(', ', $tables) . "\n\n";

        foreach ($tables as $table) {
            $sqlContent .= "--\n";
            $sqlContent .= "-- Table structure for `$table`\n";
            $sqlContent .= "--\n\n";

            $createTable = DB::select("SHOW CREATE TABLE `$table`");
            $sqlContent .= $createTable[0]->{'Create Table'} . ";\n\n";

            $sqlContent .= "--\n";
            $sqlContent .= "-- Dumping data for `$table`\n";
            $sqlContent .= "--\n\n";

            $rows = DB::table($table)->get();
            if ($rows->count() > 0) {
                foreach ($rows as $row) {
                    $values = array_map(function ($value) {
                        if ($value === null) {
                            return 'NULL';
                        }
                        return "'" . addslashes($value) . "'";
                    }, (array)$row);

                    $columns = array_keys((array)$row);
                    $sqlContent .= "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n";
                }
            }
            $sqlContent .= "\n";
        }

        file_put_contents($backupFile, $sqlContent);

        $zipFilename = 'backup_' . $timestamp . '.zip';
        $zipPath = $backupDir . '/' . $zipFilename;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $zip->addFile($backupFile, 'backup_' . $timestamp . '.sql');
            $zip->close();
        }

        unlink($backupFile);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
    
    public function backupAll()
    {
        $this->checkAdmin();
        
        // Daftar semua tabel yang ingin dibackup
        $tables = [
            'students',
            'attendances', 
            'points',
            'exam_questions',
            'exam_results',
            'courses',
            'modules',
            'student_permits',
            'letters',
            'registrations',
            'student_cards',
            'student_card_requests',
            'student_card_transactions',
            'holidays',
            'angkatans',
            'point_rules',
            'class_rooms',
            'academic_years',
            'settings',
            'users',
            'management_users',
            'majors'
        ];
        
        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupFile = $backupDir . '/backup_semua_data_' . $timestamp . '.sql';

        $sqlContent = "-- Backup SELURUH DATA APLIKASI\n";
        $sqlContent .= "-- Generated: " . now()->toDateTimeString() . "\n\n";

        foreach ($tables as $table) {
            try {
                $sqlContent .= "--\n";
                $sqlContent .= "-- Table structure for `$table`\n";
                $sqlContent .= "--\n\n";

                $createTable = DB::select("SHOW CREATE TABLE `$table`");
                $sqlContent .= $createTable[0]->{'Create Table'} . ";\n\n";

                $sqlContent .= "--\n";
                $sqlContent .= "-- Dumping data for `$table`\n";
                $sqlContent .= "--\n\n";

                $rows = DB::table($table)->get();
                if ($rows->count() > 0) {
                    foreach ($rows as $row) {
                        $values = array_map(function ($value) {
                            if ($value === null) {
                                return 'NULL';
                            }
                            return "'" . addslashes($value) . "'";
                        }, (array)$row);

                        $columns = array_keys((array)$row);
                        $sqlContent .= "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n";
                    }
                }
                $sqlContent .= "\n";
            } catch (\Exception $e) {
                // Skip jika tabel tidak ada
                $sqlContent .= "-- Skipped table `$table`: " . $e->getMessage() . "\n\n";
            }
        }

        file_put_contents($backupFile, $sqlContent);

        $zipFilename = 'backup_semua_data_' . $timestamp . '.zip';
        $zipPath = $backupDir . '/' . $zipFilename;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $zip->addFile($backupFile, 'backup_semua_data_' . $timestamp . '.sql');
            $zip->close();
        }

        unlink($backupFile);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
    
    public function resetAll()
    {
        $this->checkAdmin();
        
        // Daftar tabel yang ingin direset (semua kecuali yang ingin dipertahankan)
        // Urutkan agar tabel yang memiliki foreign key dihapus terlebih dahulu
        $tablesToReset = [
            'student_card_transactions',
            'student_card_requests',
            'student_cards',
            'attendances',
            'points',
            'exam_results',
            'exam_questions',
            'modules',
            'courses',
            'student_permits',
            'letters',
            'registrations',
            'students',
            'holidays',
            'point_rules',
            'class_rooms',
            'angkatans',
            'academic_years'
        ];
        
        try {
            // Nonaktifkan foreign key checks sementara
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            foreach ($tablesToReset as $table) {
                try {
                    // Cek apakah tabel ada
                    if (Schema::hasTable($table)) {
                        // Hapus semua data
                        DB::table($table)->delete();
                        
                        // Reset auto increment
                        try {
                            DB::statement("ALTER TABLE `$table` AUTO_INCREMENT = 1");
                        } catch (\Exception $e) {
                            // Skip jika tidak bisa reset auto increment
                        }
                    }
                } catch (\Exception $e) {
                    // Skip jika error
                }
            }
            
            // Reset settings ke default
            if (Schema::hasTable('settings')) {
                DB::table('settings')->truncate();
            }
            
            // Aktifkan kembali foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return redirect()->back()->with('success', 'Semua data berhasil direset! Admin tetap tersimpan.');
        } catch (\Exception $e) {
            // Pastikan foreign key checks tetap aktif meskipun error
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            } catch (\Exception $ex) {
                // Ignore
            }
            return redirect()->back()->with('error', 'Gagal mereset data: ' . $e->getMessage());
        }
    }

    public function reset(Request $request)
    {
        $this->checkAdmin();
        $validated = $request->validate([
            'tables' => 'required|array',
            'tables.*' => 'string|in:students,attendances,points,exam_questions,exam_results,courses,modules,student_permits,letters,registrations,student_cards,student_card_requests,student_card_transactions,holidays,angkatans,point_rules,class_rooms,academic_years',
        ]);

        $tables = $validated['tables'];
        
        try {
            // Nonaktifkan foreign key checks sementara
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->delete();
                    try {
                        DB::statement("ALTER TABLE `$table` AUTO_INCREMENT = 1");
                    } catch (\Exception $e) {
                        // Skip
                    }
                }
            }
            
            // Aktifkan kembali foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return redirect()->back()->with('success', 'Data berhasil direset untuk tabel: ' . implode(', ', $tables));
        } catch (\Exception $e) {
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            } catch (\Exception $ex) {
                // Ignore
            }
            return redirect()->back()->with('error', 'Gagal mereset data: ' . $e->getMessage());
        }
    }
}
