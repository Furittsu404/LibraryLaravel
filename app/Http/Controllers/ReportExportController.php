<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;
use Symfony\Component\Process\Process;

class ReportExportController extends Controller
{
    public function download(Request $request)
    {
        // Retrieve filter data from session
        $filters = session('export_filters');
        $statistics = session('export_statistics');

        if (!$filters || !$statistics) {
            \Log::error('Export session data missing', [
                'filters' => $filters,
                'statistics' => $statistics
            ]);
            return redirect()->route('admin.reports.index')->with('error', 'Export session expired. Please generate report again.');
        }

        \Log::info('Starting export', [
            'filters' => $filters,
            'statistics_keys' => array_keys($statistics)
        ]);

        try {
            // Create temporary directory for files
            $tempDir = storage_path('app/temp_exports/' . uniqid());
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            \Log::info('Created temp directory', ['path' => $tempDir]);

            // 1. Generate PDF Statistics Report
            $pdfData = $this->preparePdfData($filters, $statistics);
            \Log::info('PDF data prepared');

            $pdf = Pdf::loadView('pdf.statistics-report', $pdfData);
            $pdfPath = $tempDir . '/Library_Statistics_Report.pdf';
            $pdf->save($pdfPath);
            \Log::info('PDF saved', ['path' => $pdfPath, 'exists' => file_exists($pdfPath)]);

            // 2. Generate Excel Attendance Report(s)
            $excelFiles = $this->generateAttendanceExcels($tempDir, $filters);
            \Log::info('Excel files generated', ['count' => count($excelFiles)]);

            // 3. Create ZIP file using system command (more reliable)
            $zipFileName = 'Library_Reports_' . date('Y-m-d_His') . '.zip';
            $zipPath = $tempDir . '/' . $zipFileName;

            \Log::info('Creating ZIP', ['tempDir' => $tempDir]);

            // Use PHP's ZipArchive (cross-platform, works on Windows and Linux)
            $zip = new ZipArchive();

            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \Exception('Could not create ZIP file');
            }

            // Add PDF to ZIP
            $zip->addFile($pdfPath, 'Library_Statistics_Report.pdf');
            \Log::info('Added PDF to ZIP');

            // Add Excel files to ZIP
            foreach ($excelFiles as $file) {
                $zip->addFile($file['path'], basename($file['path']));
            }
            \Log::info('Added Excel files to ZIP', ['count' => count($excelFiles)]);

            $zip->close();
            \Log::info('ZIP closed');

            if (file_exists($zipPath) && filesize($zipPath) > 0) {
                $fileSize = filesize($zipPath);
                \Log::info('ZIP file ready', ['size' => $fileSize]);

                // Clear session data
                session()->forget(['export_filters', 'export_statistics']);
                \Log::info('Session cleared, attempting download');

                // Download the ZIP file and clean up
                return response()->download($zipPath, $zipFileName, [
                    'Content-Type' => 'application/zip',
                    'Content-Disposition' => 'attachment; filename="' . $zipFileName . '"'
                ])->deleteFileAfterSend(true);
            } else {
                throw new \Exception('ZIP file was not created successfully or is empty');
            }
        } catch (\Exception $e) {
            // Clean up temp directory if it exists
            if (isset($tempDir) && file_exists($tempDir)) {
                $this->deleteTempDirectory($tempDir);
            }

            \Log::error('Export failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.reports.index')->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    public function printPdf(Request $request)
    {
        // Get token from request
        $token = $request->get('token');

        if (!$token) {
            \Log::error('Print PDF: No token provided');
            return redirect()->route('admin.reports.index')->with('error', 'Invalid print request. Please try again.');
        }

        // Retrieve filter data from cache using token
        $filters = cache()->get('print_filters_' . $token);
        $statistics = cache()->get('print_statistics_' . $token);

        \Log::info('Print PDF called', [
            'token' => $token,
            'has_filters' => !empty($filters),
            'has_statistics' => !empty($statistics),
            'filters' => $filters,
        ]);

        if (!$filters || !$statistics) {
            \Log::error('Print cache data missing', [
                'token' => $token,
                'filters' => $filters,
                'statistics' => $statistics
            ]);
            return redirect()->route('admin.reports.index')->with('error', 'Print session expired. Please generate report again.');
        }

        try {
            // Generate PDF Statistics Report
            $pdfData = $this->preparePdfData($filters, $statistics);

            // ensure sectionName is present for the view
            $pdfData['sectionName'] = $filters['sectionName']
                ?? $this->mapSectionCodeToName($filters['librarySection'] ?? null)
                ?? 'All Sections';

            $pdf = Pdf::loadView('pdf.statistics-report', $pdfData);

            // Clear cache data after use
            cache()->forget('print_filters_' . $token);
            cache()->forget('print_statistics_' . $token);

            // Get PDF output as base64
            $pdfOutput = base64_encode($pdf->output());

            \Log::info('PDF generated successfully', ['pdf_size' => strlen($pdfOutput)]);

            // Return view with embedded PDF and auto-print script
            return view('pdf.print-window', ['pdfData' => $pdfOutput]);

        } catch (\Exception $e) {
            \Log::error('Print PDF failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Clear cache on error
            cache()->forget('print_filters_' . $token);
            cache()->forget('print_statistics_' . $token);

            return redirect()->route('admin.reports.index')->with('error', 'Print failed: ' . $e->getMessage());
        }
    }

    private function preparePdfData($filters, $statistics)
    {
        $startDateTime = Carbon::parse($filters['startDate'] . ' ' . $filters['startTime']);
        $endDateTime = Carbon::parse($filters['endDate'] . ' ' . $filters['endTime']);

        // Get statistics data
        $dailyStats = $this->getDailyStatistics($filters);
        $monthlyStats = $this->getMonthlyStatistics($filters);
        $hourlyStats = $this->getHourlyStatistics($filters);
        $courseStats = $this->getCourseStatistics($filters);

        return [
            'startDate' => $startDateTime->format('F d, Y h:i A'),
            'endDate' => $endDateTime->format('F d, Y h:i A'),
            'filters' => [
                'courses' => $filters['selectedCourses'] ?? [],
                'sex' => $filters['sex'] ?? '',
                'userType' => $filters['userType'] ?? '',
            ],
            'totalStatistics' => $statistics['totalStatistics'],
            'categoryStats' => $statistics['userTypeStatistics'],
            'dailyStats' => $dailyStats,
            'monthlyStats' => $monthlyStats,
            'hourlyStats' => $hourlyStats,
            'courseStats' => $courseStats,
        ];
    }

    private function buildBaseQuery($filters)
    {
        $startDateTime = Carbon::parse($filters['startDate'] . ' ' . $filters['startTime']);
        $endDateTime = Carbon::parse($filters['endDate'] . ' ' . $filters['endTime']);

        $query = DB::table('attendance')
            ->leftJoin('users', 'attendance.user_id', '=', 'users.id')
            ->leftJoin('users_archive', 'attendance.user_id', '=', 'users_archive.id')
            ->whereBetween('attendance.login_time', [$startDateTime, $endDateTime]);

        // Filter by library section
        if (isset($filters['librarySection'])) {
            $query->where('attendance.library_section', $filters['librarySection']);
        }

        // Apply filters - check both users and users_archive tables
        if (!empty($filters['selectedCourses'])) {
            $query->where(function ($q) use ($filters) {
                $q->whereIn('users.course', $filters['selectedCourses'])
                    ->orWhereIn('users_archive.course', $filters['selectedCourses']);
            });
        }

        if (!empty($filters['sex'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('users.sex', $filters['sex'])
                    ->orWhere('users_archive.sex', $filters['sex']);
            });
        }

        if (!empty($filters['userType'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('users.user_type', $filters['userType'])
                    ->orWhere('users_archive.user_type', $filters['userType']);
            });
        }

        return $query;
    }

    private function getDailyStatistics($filters)
    {
        $query = $this->buildBaseQuery($filters);

        return $query->select(
            DB::raw('DATE(attendance.login_time) as date'),
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->toArray();
    }

    private function getMonthlyStatistics($filters)
    {
        $query = $this->buildBaseQuery($filters);

        return $query->select(
            DB::raw('DATE_FORMAT(attendance.login_time, "%Y-%m") as month'),
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->toArray();
    }

    private function getHourlyStatistics($filters)
    {
        $query = $this->buildBaseQuery($filters);

        return $query->select(
            DB::raw('HOUR(attendance.login_time) as hour'),
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('hour')
            ->orderBy('hour', 'asc')
            ->get()
            ->toArray();
    }

    private function getCourseStatistics($filters)
    {
        $query = $this->buildBaseQuery($filters);

        return $query->select(
            DB::raw('COALESCE(users.course, users_archive.course, "Unknown/Deleted User") as course'),
            DB::raw('COUNT(*) as total')
        )
            ->groupBy(DB::raw('COALESCE(users.course, users_archive.course, "Unknown/Deleted User")'))
            ->orderByDesc('total')
            ->get()
            ->toArray();
    }

    private function generateAttendanceExcels($tempDir, $filters)
    {
        $startDateTime = Carbon::parse($filters['startDate'] . ' ' . $filters['startTime']);
        $endDateTime = Carbon::parse($filters['endDate'] . ' ' . $filters['endTime']);
        $sectionName = $filters['sectionName'] ?? 'All Sections';

        // Group data by year
        $yearlyData = $this->getAttendanceDataByYear($startDateTime, $endDateTime, $filters);

        $excelFiles = [];

        foreach ($yearlyData as $year => $monthsData) {
            $fileName = "Attendance_Report_{$year}.xlsx";
            $filePath = $tempDir . '/' . $fileName;

            // Generate the Excel file and save directly to temp path
            $export = new AttendanceReportExport($monthsData, $year, $sectionName);

            // Use raw() to get the file content and save it directly
            $content = Excel::raw($export, \Maatwebsite\Excel\Excel::XLSX);
            file_put_contents($filePath, $content);

            \Log::info('Excel file created', [
                'fileName' => $fileName,
                'exists' => file_exists($filePath),
                'size' => file_exists($filePath) ? filesize($filePath) : 0
            ]);

            $excelFiles[] = [
                'name' => $fileName,
                'path' => $filePath
            ];
        }

        return $excelFiles;
    }

    private function getAttendanceDataByYear($startDateTime, $endDateTime, $filters)
    {
        $query = $this->buildBaseQuery($filters);

        // Get raw attendance data WITHOUT grouping - we need individual records
        $attendanceRecords = $query->select(
            DB::raw('DATE(attendance.login_time) as date'),
            DB::raw('COALESCE(users.user_type, users_archive.user_type) as user_type'),
            DB::raw('COALESCE(users.sex, users_archive.sex) as sex')
        )
            ->orderBy('date', 'asc')
            ->get();

        \Log::info('Attendance records fetched for Excel', [
            'total_records' => $attendanceRecords->count(),
            'query' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

        // Organize data by year -> month -> day
        $yearlyData = [];

        foreach ($attendanceRecords as $record) {
            $date = Carbon::parse($record->date);
            $year = $date->year;
            $month = $date->month;
            $day = $date->day;

            if (!isset($yearlyData[$year])) {
                $yearlyData[$year] = [];
            }

            if (!isset($yearlyData[$year][$month])) {
                $yearlyData[$year][$month] = [];
            }

            if (!isset($yearlyData[$year][$month][$day])) {
                $yearlyData[$year][$month][$day] = [
                    'students' => ['M' => 0, 'F' => 0],
                    'faculty' => ['M' => 0, 'F' => 0],
                    'staff' => ['M' => 0, 'F' => 0],
                    'visitors' => ['M' => 0, 'F' => 0],
                ];
            }

            // Map database user_type to grouping for Excel
            // Handle null user_type/sex for deleted users
            $userType = strtolower($record->user_type ?? 'visitor');
            if ($userType === 'student') {
                $userType = 'students';
            } elseif ($userType === 'visitor' || empty($record->user_type)) {
                $userType = 'visitors';
            } elseif ($userType === 'staff') {
                $userType = 'staff';
            }
            // faculty stays as 'faculty'

            $sex = strtoupper(substr($record->sex ?? 'M', 0, 1));

            // Increment count for each individual record
            if (isset($yearlyData[$year][$month][$day][$userType][$sex])) {
                $yearlyData[$year][$month][$day][$userType][$sex]++;
            }
        }

        return $yearlyData;
    }

    private function deleteTempDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteTempDirectory($path) : unlink($path);
        }

        rmdir($dir);
    }
}
