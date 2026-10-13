<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('class_rooms') || ! Schema::hasTable('users')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            $rows = DB::table('class_rooms')->whereNotNull('wali_kelas_id')->get();
            foreach ($rows as $row) {
                $exists = DB::table('users')->where('id', $row->wali_kelas_id)->exists();
                if (! $exists) {
                    DB::table('class_rooms')->where('id', $row->id)->update(['wali_kelas_id' => null]);
                }
            }

            return;
        }

        $legacyKeyOffset = 1000000;

        // Move legacy management users into users so existing class room assignments
        // can be remapped before the new foreign key is attached.
        if (Schema::hasTable('management_users')) {
            DB::statement("
                UPDATE class_rooms
                SET wali_kelas_id = wali_kelas_id + {$legacyKeyOffset}
                WHERE wali_kelas_id IS NOT NULL
                  AND wali_kelas_id < {$legacyKeyOffset}
            ");

            $legacyUsers = DB::table('management_users')->get();

            foreach ($legacyUsers as $legacyUser) {
                $userId = DB::table('users')
                    ->where('email', $legacyUser->email)
                    ->value('id');

                if (! $userId && ! empty($legacyUser->username)) {
                    $userId = DB::table('users')
                        ->where('username', $legacyUser->username)
                        ->value('id');
                }

                if (! $userId) {
                    $userId = DB::table('users')->insertGetId([
                        'name' => $legacyUser->name,
                        'username' => $legacyUser->username,
                        'email' => $legacyUser->email,
                        'password' => $legacyUser->password,
                        'role' => $legacyUser->role,
                        'no_wa' => $legacyUser->no_wa,
                        'foto' => $legacyUser->foto,
                        'created_at' => $legacyUser->created_at ?? now(),
                        'updated_at' => $legacyUser->updated_at ?? now(),
                    ]);
                }

                DB::table('class_rooms')
                    ->where('wali_kelas_id', $legacyUser->id + $legacyKeyOffset)
                    ->update(['wali_kelas_id' => $userId]);
            }
        }

        // Clean up any remaining orphan IDs before adding the new foreign key.
        DB::statement("
            UPDATE class_rooms cr
            LEFT JOIN users u ON u.id = cr.wali_kelas_id
            SET cr.wali_kelas_id = NULL
            WHERE cr.wali_kelas_id IS NOT NULL
              AND u.id IS NULL
        ");

        $databaseName = DB::getDatabaseName();
        $foreignKey = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $databaseName)
            ->where('TABLE_NAME', 'class_rooms')
            ->where('COLUMN_NAME', 'wali_kelas_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->value('CONSTRAINT_NAME');

        if ($foreignKey) {
            DB::statement("ALTER TABLE `class_rooms` DROP FOREIGN KEY `{$foreignKey}`");
        }

        $hasUsersForeignKey = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $databaseName)
            ->where('TABLE_NAME', 'class_rooms')
            ->where('COLUMN_NAME', 'wali_kelas_id')
            ->where('REFERENCED_TABLE_NAME', 'users')
            ->exists();

        if (! $hasUsersForeignKey) {
            Schema::table('class_rooms', function (Blueprint $table) {
                $table->foreign('wali_kelas_id')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        if (! Schema::hasTable('class_rooms') || ! Schema::hasTable('management_users')) {
            return;
        }

        $databaseName = DB::getDatabaseName();
        $foreignKey = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $databaseName)
            ->where('TABLE_NAME', 'class_rooms')
            ->where('COLUMN_NAME', 'wali_kelas_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->value('CONSTRAINT_NAME');

        if ($foreignKey) {
            DB::statement("ALTER TABLE `class_rooms` DROP FOREIGN KEY `{$foreignKey}`");
        }

        Schema::table('class_rooms', function (Blueprint $table) {
            $table->foreign('wali_kelas_id')
                ->references('id')
                ->on('management_users')
                ->nullOnDelete();
        });
    }
};
