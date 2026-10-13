<?php

namespace App\Support;

/**
 * Helper untuk merangkum data absensi dalam suatu periode.
 */
class AttendanceReportHelper
{
    /**
     * Rangkum koleksi/array absensi dalam rentang tanggal tertentu.
     *
     * @param  iterable  $attendances  Koleksi atau array dengan key/property 'status' dan 'date'
     * @param  string    $startDate    Format 'Y-m-d'
     * @param  string    $endDate      Format 'Y-m-d'
     * @return array{present: int, late: int, hadir: int, absent: int, excused: int, sick: int}
     */
    public static function summarizePeriod($attendances, string $startDate, string $endDate): array
    {
        $summary = [
            'present' => 0,
            'late'    => 0,
            'hadir'   => 0,  // present + late
            'absent'  => 0,
            'excused' => 0,
            'sick'    => 0,
        ];

        foreach ($attendances as $record) {
            $status = is_array($record) ? ($record['status'] ?? null) : ($record->status ?? null);
            $date   = is_array($record) ? ($record['date']   ?? null) : ($record->date   ?? null);

            // Abaikan record di luar rentang tanggal
            if ($date !== null && ($date < $startDate || $date > $endDate)) {
                continue;
            }

            switch ($status) {
                case 'present':
                    $summary['present']++;
                    $summary['hadir']++;
                    break;
                case 'late':
                    $summary['late']++;
                    $summary['hadir']++;
                    break;
                case 'absent':
                    $summary['absent']++;
                    break;
                case 'excused':
                    $summary['excused']++;
                    break;
                case 'sick':
                    $summary['sick']++;
                    break;
            }
        }

        return $summary;
    }
}
