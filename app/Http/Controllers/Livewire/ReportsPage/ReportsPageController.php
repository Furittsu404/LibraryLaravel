<?php

namespace App\Http\Controllers\Livewire\ReportsPage;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsPageController extends Component
{
    use WithPagination;

    public $title = 'LISO - Reports';
    public $activePage = 'reports';

    // Filter properties
    public $selectedCourses = [];
    public $startDate = '';
    public $startTime = '00:00';
    public $endDate = '';
    public $endTime = '23:59';
    public $sex = '';
    public $userType = '';

    // Data properties
    public $hasGeneratedReport = false;
    public $topUsers = [];
    public $totalStatistics = [];
    public $userTypeStatistics = [];
    public $perPage = 25;

    // Available filter options
    public $availableCourses = [];

    public function mount()
    {
        session(['title' => $this->title]);

        // Load available courses
        $this->availableCourses = DB::table('users')
            ->whereNotNull('course')
            ->where('course', '!=', '')
            ->distinct()
            ->pluck('course')
            ->toArray();
    }

    public function applyFilters()
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'selectedCourses' => 'array',
            'sex' => 'nullable|in:male,female,other',
            'userType' => 'nullable|in:student,visitor,faculty,staff',
        ]);

        // Additional validation: Check if dates are valid calendar dates
        try {
            // Validate date format strictly
            $startDate = Carbon::createFromFormat('Y-m-d', $this->startDate);
            $endDate = Carbon::createFromFormat('Y-m-d', $this->endDate);

            // Verify the dates weren't auto-corrected (e.g., Feb 31 -> March 3)
            if ($startDate->format('Y-m-d') !== $this->startDate) {
                session()->flash('error', 'Invalid start date. Please check the date format.');
                return;
            }

            if ($endDate->format('Y-m-d') !== $this->endDate) {
                session()->flash('error', 'Invalid end date. Please check the date format.');
                return;
            }

            // Validate time format
            if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $this->startTime)) {
                session()->flash('error', 'Invalid start time format. Use HH:MM format.');
                return;
            }

            if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $this->endTime)) {
                session()->flash('error', 'Invalid end time format. Use HH:MM format.');
                return;
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Invalid date format. Please enter a valid date.');
            return;
        }

        // Generate report data
        try {
            $this->generateReport();
            $this->hasGeneratedReport = true;
        } catch (\Exception $e) {
            \Log::error('Report generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Failed to generate report. Please check your date inputs.');
            $this->hasGeneratedReport = false;
        }
    }

    public function resetFilters()
    {
        $this->selectedCourses = [];
        $this->startDate = '';
        $this->startTime = '00:00';
        $this->endDate = '';
        $this->endTime = '23:59';
        $this->sex = '';
        $this->userType = '';
        $this->hasGeneratedReport = false;
        $this->topUsers = [];
        $this->totalStatistics = [];
        $this->userTypeStatistics = [];
    }

    public function removeCourse($course)
    {
        $this->selectedCourses = array_values(array_filter($this->selectedCourses, function ($item) use ($course) {
            return $item !== $course;
        }));

        // Auto-update report after removing course
        if ($this->hasGeneratedReport && $this->isFilterValid()) {
            $this->generateReport();
        }
    }

    // Auto-update report when filters change
    public function updated($propertyName)
    {
        // Reset pagination when filters change or per page changes
        if (in_array($propertyName, ['selectedCourses', 'startDate', 'startTime', 'endDate', 'endTime', 'sex', 'userType', 'perPage'])) {
            $this->resetPage();
        }

        // Only auto-update if a report has already been generated and filters are valid
        if ($this->hasGeneratedReport && $this->isFilterValid()) {
            $this->generateReport();
        }
    }

    private function isFilterValid()
    {
        // Check if required filters are set
        return !empty($this->startDate) && !empty($this->endDate);
    }

    private function generateReport()
    {
        // Build base query - this is reusable for all calculations
        $baseQuery = $this->buildBaseQuery();

        // Calculate statistics using optimized database queries
        $this->calculateTopUsers($baseQuery);
        $this->calculateTotalStatistics($baseQuery);
        $this->calculateUserTypeStatistics($baseQuery);
    }

    private function buildBaseQuery()
    {
        try {
            // Parse dates more carefully
            $startDate = Carbon::createFromFormat('Y-m-d', $this->startDate);
            $endDate = Carbon::createFromFormat('Y-m-d', $this->endDate);

            // Set the time portions
            $startDateTime = $startDate->setTimeFromTimeString($this->startTime);
            $endDateTime = $endDate->setTimeFromTimeString($this->endTime);

        } catch (\Exception $e) {
            \Log::error('Date parsing failed in buildBaseQuery', [
                'startDate' => $this->startDate,
                'startTime' => $this->startTime,
                'endDate' => $this->endDate,
                'endTime' => $this->endTime,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Invalid date format provided');
        }

        $query = DB::table('attendance')
            ->leftJoin('users', 'attendance.user_id', '=', 'users.id')
            ->leftJoin('users_archive', 'attendance.user_id', '=', 'users_archive.id')
            ->whereBetween('attendance.login_time', [$startDateTime, $endDateTime])
            ->where('attendance.library_section', session('current_section', 'entrance'));

        // Apply filters - check both users and users_archive tables
        if (!empty($this->selectedCourses)) {
            $query->where(function ($q) {
                $q->whereIn('users.course', $this->selectedCourses)
                    ->orWhereIn('users_archive.course', $this->selectedCourses);
            });
        }

        if (!empty($this->sex)) {
            $query->where(function ($q) {
                $q->where('users.sex', $this->sex)
                    ->orWhere('users_archive.sex', $this->sex);
            });
        }

        if (!empty($this->userType)) {
            $query->where(function ($q) {
                $q->where('users.user_type', $this->userType)
                    ->orWhere('users_archive.user_type', $this->userType);
            });
        }

        return $query;
    }

    private function getPaginatedReportData()
    {
        if (!$this->hasGeneratedReport || empty($this->startDate) || empty($this->endDate)) {
            return collect([]);
        }

        // Build query and get paginated results
        $query = $this->buildBaseQuery();

        // Get paginated results with COALESCE to check both users and users_archive
        return $query->select(
            DB::raw('COALESCE(users.id, users_archive.id, 0) as user_id'),
            DB::raw('COALESCE(users.lname, users_archive.lname, "Unknown") as lname'),
            DB::raw('COALESCE(users.fname, users_archive.fname, "User") as fname'),
            DB::raw('COALESCE(users.mname, users_archive.mname, "") as mname'),
            DB::raw('COALESCE(users.course, users_archive.course, "N/A") as course'),
            DB::raw('COALESCE(users.sex, users_archive.sex, "unknown") as sex'),
            DB::raw('COALESCE(users.user_type, users_archive.user_type, "unknown") as user_type'),
            'attendance.login_time',
            'attendance.logout_time'
        )
            ->orderBy('attendance.login_time', 'desc')
            ->paginate($this->perPage);
    }

    private function calculateTopUsers($baseQuery)
    {
        // Use database aggregation to get top 10 users directly
        $topUsers = (clone $baseQuery)
            ->select(
                DB::raw('COALESCE(users.id, users_archive.id, 0) as user_id'),
                DB::raw('COALESCE(users.lname, users_archive.lname, "Unknown") as lname'),
                DB::raw('COALESCE(users.fname, users_archive.fname, "User") as fname'),
                DB::raw('COALESCE(users.mname, users_archive.mname, "") as mname'),
                DB::raw('COALESCE(users.course, users_archive.course, "N/A") as course'),
                DB::raw('COUNT(*) as login_count')
            )
            ->groupBy(
                'users.id',
                'users.lname',
                'users.fname',
                'users.mname',
                'users.course',
                'users_archive.id',
                'users_archive.lname',
                'users_archive.fname',
                'users_archive.mname',
                'users_archive.course'
            )
            ->orderByDesc('login_count')
            ->limit(10)
            ->get();

        $this->topUsers = $topUsers->map(function ($user) {
            return [
                'user_id' => $user->user_id,
                'name' => $user->lname . ', ' . $user->fname . ' ' . $user->mname,
                'course' => $user->course,
                'login_count' => $user->login_count,
            ];
        })->toArray();
    }

    private function calculateTotalStatistics($baseQuery)
    {
        // Get total counts with single query using conditional aggregation
        $stats = (clone $baseQuery)
            ->select([
                DB::raw('COUNT(*) as total_logins'),
                DB::raw('COUNT(CASE WHEN COALESCE(users.sex, users_archive.sex) = "female" THEN 1 END) as total_female_logins'),
                DB::raw('COUNT(CASE WHEN COALESCE(users.sex, users_archive.sex) = "male" THEN 1 END) as total_male_logins'),
                DB::raw('COUNT(DISTINCT COALESCE(users.id, users_archive.id)) as unique_logins'),
                DB::raw('COUNT(DISTINCT CASE WHEN COALESCE(users.sex, users_archive.sex) = "female" THEN COALESCE(users.id, users_archive.id) END) as unique_female_logins'),
                DB::raw('COUNT(DISTINCT CASE WHEN COALESCE(users.sex, users_archive.sex) = "male" THEN COALESCE(users.id, users_archive.id) END) as unique_male_logins'),
            ])
            ->first();

        \Log::info('Total statistics calculated', [
            'total_logins' => $stats->total_logins ?? 0,
            'query' => $baseQuery->toSql(),
            'bindings' => $baseQuery->getBindings()
        ]);

        $this->totalStatistics = [
            'total_logins' => $stats->total_logins ?? 0,
            'total_female_logins' => $stats->total_female_logins ?? 0,
            'total_male_logins' => $stats->total_male_logins ?? 0,
            'unique_logins' => $stats->unique_logins ?? 0,
            'unique_female_logins' => $stats->unique_female_logins ?? 0,
            'unique_male_logins' => $stats->unique_male_logins ?? 0,
        ];
    }

    private function calculateUserTypeStatistics($baseQuery)
    {
        // Get user type statistics with single query using conditional aggregation
        $stats = (clone $baseQuery)
            ->select([
                DB::raw('COUNT(CASE WHEN COALESCE(users.user_type, users_archive.user_type) = "student" THEN 1 END) as student_total'),
                DB::raw('COUNT(DISTINCT CASE WHEN COALESCE(users.user_type, users_archive.user_type) = "student" THEN COALESCE(users.id, users_archive.id) END) as student_unique'),
                DB::raw('COUNT(CASE WHEN COALESCE(users.user_type, users_archive.user_type) = "faculty" THEN 1 END) as faculty_total'),
                DB::raw('COUNT(DISTINCT CASE WHEN COALESCE(users.user_type, users_archive.user_type) = "faculty" THEN COALESCE(users.id, users_archive.id) END) as faculty_unique'),
                DB::raw('COUNT(CASE WHEN COALESCE(users.user_type, users_archive.user_type) = "visitor" THEN 1 END) as visitor_total'),
                DB::raw('COUNT(DISTINCT CASE WHEN COALESCE(users.user_type, users_archive.user_type) = "visitor" THEN COALESCE(users.id, users_archive.id) END) as visitor_unique'),
                DB::raw('COUNT(CASE WHEN COALESCE(users.user_type, users_archive.user_type) = "staff" THEN 1 END) as staff_total'),
                DB::raw('COUNT(DISTINCT CASE WHEN COALESCE(users.user_type, users_archive.user_type) = "staff" THEN COALESCE(users.id, users_archive.id) END) as staff_unique'),
            ])
            ->first();

        $this->userTypeStatistics = [
            'student' => [
                'total' => $stats->student_total ?? 0,
                'unique' => $stats->student_unique ?? 0,
            ],
            'faculty' => [
                'total' => $stats->faculty_total ?? 0,
                'unique' => $stats->faculty_unique ?? 0,
            ],
            'visitor' => [
                'total' => $stats->visitor_total ?? 0,
                'unique' => $stats->visitor_unique ?? 0,
            ],
            'staff' => [
                'total' => $stats->staff_total ?? 0,
                'unique' => $stats->staff_unique ?? 0,
            ],
        ];
    }

    public function exportReport()
    {
        if (!$this->hasGeneratedReport) {
            session()->flash('error', 'Please generate a report first before exporting.');
            return;
        }

        // Prepare CSV data
        $filename = 'report_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        // Build query for export
        $query = $this->buildBaseQuery();

        $callback = function () use ($query) {
            $file = fopen('php://output', 'w');

            // Add headers
            fputcsv($file, ['User ID', 'Name', 'Course', 'sex', 'User Type', 'Login Time', 'Logout Time']);

            // Use chunking to handle large datasets efficiently
            $query->select(
                DB::raw('COALESCE(users.id, users_archive.id, 0) as user_id'),
                DB::raw('COALESCE(users.lname, users_archive.lname, "Unknown") as lname'),
                DB::raw('COALESCE(users.fname, users_archive.fname, "User") as fname'),
                DB::raw('COALESCE(users.mname, users_archive.mname, "") as mname'),
                DB::raw('COALESCE(users.course, users_archive.course, "N/A") as course'),
                DB::raw('COALESCE(users.sex, users_archive.sex, "unknown") as sex'),
                DB::raw('COALESCE(users.user_type, users_archive.user_type, "unknown") as user_type'),
                'attendance.login_time',
                'attendance.logout_time'
            )
                ->orderBy('attendance.login_time', 'desc')
                ->chunk(1000, function ($records) use ($file) {
                    foreach ($records as $record) {
                        fputcsv($file, [
                            $record->user_id,
                            $record->lname . ', ' . $record->fname . ' ' . $record->mname,
                            $record->course,
                            $record->sex,
                            $record->user_type,
                            $record->login_time,
                            $record->logout_time ?? 'N/A',
                        ]);
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportStatisticsWithAttendance()
    {
        if (!$this->hasGeneratedReport) {
            session()->flash('error', 'Please generate a report first before exporting.');
            return;
        }

        $currentSection = session('current_section', 'entrance');
        $sectionName = $this->getSectionName($currentSection);

        // Store filter data in session for the download route
        session([
            'export_filters' => [
                'selectedCourses' => $this->selectedCourses,
                'startDate' => $this->startDate,
                'startTime' => $this->startTime,
                'endDate' => $this->endDate,
                'endTime' => $this->endTime,
                'sex' => $this->sex,
                'userType' => $this->userType,
                'librarySection' => $currentSection,
                'sectionName' => $sectionName,
            ],
            'export_statistics' => [
                'totalStatistics' => $this->totalStatistics,
                'userTypeStatistics' => $this->userTypeStatistics,
            ]
        ]);

        // Redirect to download route (outside Livewire)
        return redirect()->route('reports.export.download');
    }

    public function printPdf()
    {
        if (!$this->hasGeneratedReport) {
            session()->flash('error', 'Please generate a report first before printing.');
            return;
        }

        $currentSection = session('current_section', 'entrance');
        $sectionName = $this->getSectionName($currentSection);

        \Log::info('PrintPDF - Section Info', [
            'currentSection' => $currentSection,
            'sectionName' => $sectionName,
            'session_all' => session()->all()
        ]);

        // Generate a unique token for this print request
        $token = uniqid('print_', true);

        // Store filter data in cache for 5 minutes (instead of session)
        cache()->put('print_filters_' . $token, [
            'selectedCourses' => $this->selectedCourses,
            'startDate' => $this->startDate,
            'startTime' => $this->startTime,
            'endDate' => $this->endDate,
            'endTime' => $this->endTime,
            'sex' => $this->sex,
            'userType' => $this->userType,
            'librarySection' => $currentSection,
            'sectionName' => $sectionName,
        ], now()->addMinutes(5));

        cache()->put('print_statistics_' . $token, [
            'totalStatistics' => $this->totalStatistics,
            'userTypeStatistics' => $this->userTypeStatistics,
        ], now()->addMinutes(5));

        // Dispatch event to open print window with token
        $this->dispatch('open-print-window', url: route('reports.print.pdf', ['token' => $token]));
    }

    private function getSectionName($sectionCode)
    {
        $sections = [
            'entrance' => 'Entrance',
            'periodicals' => 'Periodicals',
            'humanities' => 'Humanities',
            'multimedia' => 'Multimedia',
            'filipiniana' => 'Filipiniana',
            'makers' => 'Maker Space',
            'science' => 'Science & Technology'
        ];

        return $sections[$sectionCode] ?? 'Unknown Section';
    }

    public function render()
    {
        $paginatedReportData = $this->getPaginatedReportData();

        return view('components.reportsPage.reportsTable', [
            'activePage' => $this->activePage,
            'paginatedReportData' => $paginatedReportData
        ]);
    }
}
