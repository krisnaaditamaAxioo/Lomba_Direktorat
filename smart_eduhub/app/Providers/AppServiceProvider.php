<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Point;
use App\Models\StudentCardTransaction;
use App\Models\Student;
use App\Models\Complaint;
use App\Observers\PointObserver;
use App\Observers\StudentCardTransactionObserver;
use App\Observers\StudentObserver;
use App\Observers\ComplaintObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Morph Map for relationships
        Relation::morphMap([
            'student' => \App\Models\Student::class,
            'user' => \App\Models\User::class,
        ]);

        Point::observe(PointObserver::class);
        StudentCardTransaction::observe(StudentCardTransactionObserver::class);
        Student::observe(StudentObserver::class);
        Complaint::observe(ComplaintObserver::class);

        // Share academic years with all views for the global filter
        view()->composer('*', function ($view) {
            if (auth()->check() || auth()->guard('student')->check()) {
                $view->with('globalAcademicYears', \App\Models\AcademicYear::orderBy('name', 'desc')->get());
                $defaultAcademicYear = date('Y') . '-' . (date('Y') + 1);
                $settingAcademicYear = \App\Models\Setting::get('academic_year', $defaultAcademicYear);
                $view->with('currentViewYear', session('view_academic_year', $settingAcademicYear));
            }
        });
    }
}
