<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = ['type', 'date', 'day_of_month', 'start_date', 'end_date', 'name'];

    // Static method to check if a date is a holiday
    public static function checkHoliday($date)
    {
        $carbonDate = \Carbon\Carbon::parse($date);
        
        // Check if it's Sunday
        if ($carbonDate->isSunday()) {
            return true;
        }
        
        $dayOfMonth = $carbonDate->day;

        return self::where(function ($q) use ($date) {
            $q->where('type', 'special')->where('date', $date);
        })->orWhere(function ($q) use ($dayOfMonth) {
            $q->where('type', 'monthly')->where('day_of_month', $dayOfMonth);
        })->orWhere(function ($q) use ($date) {
            $q->where('type', 'range')
              ->where('start_date', '<=', $date)
              ->where('end_date', '>=', $date);
        })->exists();
    }

    // Get holiday name for a date
    public static function getHolidayName($date)
    {
        $carbonDate = \Carbon\Carbon::parse($date);
        
        if ($carbonDate->isSunday()) {
            return 'Hari Minggu';
        }
        
        $dayOfMonth = $carbonDate->day;

        $holiday = self::where(function ($q) use ($date) {
            $q->where('type', 'special')->where('date', $date);
        })->orWhere(function ($q) use ($dayOfMonth) {
            $q->where('type', 'monthly')->where('day_of_month', $dayOfMonth);
        })->orWhere(function ($q) use ($date) {
            $q->where('type', 'range')
              ->where('start_date', '<=', $date)
              ->where('end_date', '>=', $date);
        })->first();

        return $holiday?->name;
    }

    // Scope to check if a date is a holiday (kept for compatibility)
    public function scopeIsHoliday($query, $date)
    {
        return self::checkHoliday($date);
    }
}
