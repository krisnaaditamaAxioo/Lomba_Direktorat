<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('students')) {
            DB::statement("
                UPDATE students
                SET angkatan = CASE
                    WHEN angkatan IN ('1', '2', '3') THEN angkatan
                    WHEN grade IN ('1', 'X', '10') THEN '1'
                    WHEN grade IN ('2', 'XI', '11') THEN '2'
                    WHEN grade IN ('3', 'XII', '12') THEN '3'
                    ELSE angkatan
                END
            ");

            Schema::table('students', function (Blueprint $table) {
                if (Schema::hasColumn('students', 'major_id')) {
                    $table->dropConstrainedForeignId('major_id');
                }
                if (Schema::hasColumn('students', 'class_room_id')) {
                    $table->dropConstrainedForeignId('class_room_id');
                }
                if (Schema::hasColumn('students', 'no_w_kelas')) {
                    $table->dropColumn('no_w_kelas');
                }
                if (Schema::hasColumn('students', 'grade')) {
                    $table->dropColumn('grade');
                }
            });
        }

        if (Schema::hasTable('registrations')) {
            if (! Schema::hasColumn('registrations', 'angkatan')) {
                Schema::table('registrations', function (Blueprint $table) {
                    $table->string('angkatan')->nullable()->after('nis_siswa');
                });
            }

            DB::statement("
                UPDATE registrations
                SET angkatan = CASE
                    WHEN grade IN ('1', 'X', '10') THEN '1'
                    WHEN grade IN ('2', 'XI', '11') THEN '2'
                    WHEN grade IN ('3', 'XII', '12') THEN '3'
                    ELSE angkatan
                END
            ");

            Schema::table('registrations', function (Blueprint $table) {
                if (Schema::hasColumn('registrations', 'major_id')) {
                    $table->dropConstrainedForeignId('major_id');
                }
                if (Schema::hasColumn('registrations', 'class_room_id')) {
                    $table->dropConstrainedForeignId('class_room_id');
                }
                if (Schema::hasColumn('registrations', 'grade')) {
                    $table->dropColumn('grade');
                }
            });
        }

        if (Schema::hasTable('academic_year_student')) {
            Schema::table('academic_year_student', function (Blueprint $table) {
                if (Schema::hasColumn('academic_year_student', 'class_room_id')) {
                    $table->dropConstrainedForeignId('class_room_id');
                }
            });
        }

        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                if (Schema::hasColumn('courses', 'major_id')) {
                    $table->dropConstrainedForeignId('major_id');
                }
            });
        }

        Schema::dropIfExists('class_rooms');
        Schema::dropIfExists('majors');
    }

    public function down(): void
    {
        if (! Schema::hasTable('majors')) {
            Schema::create('majors', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('class_rooms')) {
            Schema::create('class_rooms', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('semester')->default(0);
                $table->foreignId('major_id')->nullable()->constrained('majors')->nullOnDelete();
                $table->string('grade')->nullable();
                $table->foreignId('wali_kelas_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('academic_year')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (! Schema::hasColumn('students', 'no_w_kelas')) {
                    $table->string('no_w_kelas')->nullable()->after('role');
                }
                if (! Schema::hasColumn('students', 'class_room_id')) {
                    $table->foreignId('class_room_id')->nullable()->after('no_w_kelas')->constrained('class_rooms')->nullOnDelete();
                }
                if (! Schema::hasColumn('students', 'major_id')) {
                    $table->foreignId('major_id')->nullable()->after('class_room_id')->constrained('majors')->nullOnDelete();
                }
                if (! Schema::hasColumn('students', 'grade')) {
                    $table->string('grade')->nullable()->after('parent_phone');
                }
            });
        }

        if (Schema::hasTable('registrations')) {
            Schema::table('registrations', function (Blueprint $table) {
                if (! Schema::hasColumn('registrations', 'major_id')) {
                    $table->foreignId('major_id')->nullable()->after('previous_school')->constrained('majors');
                }
                if (! Schema::hasColumn('registrations', 'class_room_id')) {
                    $table->foreignId('class_room_id')->nullable()->after('major_id')->constrained('class_rooms')->nullOnDelete();
                }
                if (! Schema::hasColumn('registrations', 'grade')) {
                    $table->string('grade')->nullable()->after('nis_siswa');
                }
                if (Schema::hasColumn('registrations', 'angkatan')) {
                    $table->dropColumn('angkatan');
                }
            });
        }

        if (Schema::hasTable('academic_year_student')) {
            Schema::table('academic_year_student', function (Blueprint $table) {
                if (! Schema::hasColumn('academic_year_student', 'class_room_id')) {
                    $table->foreignId('class_room_id')->nullable()->after('academic_year_id')->constrained('class_rooms')->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                if (! Schema::hasColumn('courses', 'major_id')) {
                    $table->foreignId('major_id')->nullable()->constrained('majors')->nullOnDelete();
                }
            });
        }
    }
};
