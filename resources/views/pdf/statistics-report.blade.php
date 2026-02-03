<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Library Statistics Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.4;
        }

        .page {
            page-break-after: always;
            padding: 20px;
        }

        .page:last-child {
            page-break-after: auto;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #000;
        }

        .header h1 {
            color: #000;
            font-size: 24pt;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .header h2 {
            color: #666;
            font-size: 14pt;
            font-weight: normal;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Filters Section */
        .filters-box {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #000;
        }

        .filters-box h3 {
            color: #000;
            font-size: 12pt;
            margin-bottom: 8px;
        }

        .filter-item {
            margin-bottom: 4px;
            font-size: 9pt;
        }

        .filter-label {
            font-weight: bold;
            color: #555;
        }

        /* Summary Cards */
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }

        .summary-card {
            display: table-cell;
            width: 33.33%;
            padding: 15px;
            text-align: center;
            border: 2px solid #000;
            background: #f8f9fa;
        }

        .summary-card+.summary-card {
            border-left: none;
        }

        .summary-card .label {
            font-size: 9pt;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-card .value {
            font-size: 20pt;
            font-weight: bold;
            color: #000;
        }

        /* Section Titles */
        .section-title {
            font-size: 14pt;
            font-weight: bold;
            color: #000;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #000;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th {
            background: #333;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
        }

        table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
            font-size: 9pt;
        }

        table tr:nth-child(even) {
            background: #f8f9fa;
        }

        /* Stats Grid */
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            background: #f8f9fa;
        }

        .stat-box .label {
            font-size: 8pt;
            color: #666;
            margin-bottom: 4px;
        }

        .stat-box .value {
            font-size: 14pt;
            font-weight: bold;
            color: #000;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 15px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 8pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }

        /* Two Column Layout */
        .two-column {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }

        .column:first-child {
            padding-left: 0;
        }

        .column:last-child {
            padding-right: 0;
        }
    </style>
</head>

<body>
    <!-- PAGE 1: Cover & Executive Summary -->
    <div class="page">
        <div class="logo-container">
            <img src="{{ asset('storage/images/LISO_LogoColored.png') }}" alt="CLSU Logo" width="100">
        </div>
        <div class="header">
            <h1>CLSU Library and Information Services Office</h1>
            <h2>Statistical Report</h2>
        </div>

        <!-- Filters Applied -->
        <div class="filters-box">
            <h3>Report Parameters</h3>
            <div class="filter-item">
                <span class="filter-label">Library Section:</span> {{ $sectionName ?? 'All Sections' }}
            </div>
            <div class="filter-item">
                <span class="filter-label">Date Range:</span> {{ $startDate }} - {{ $endDate }}
            </div>
            @if (!empty($filters['sex']))
                <div class="filter-item">
                    <span class="filter-label">Sex:</span> {{ ucfirst($filters['sex']) }}
                </div>
            @endif
            @if (!empty($filters['userType']))
                <div class="filter-item">
                    <span class="filter-label">User Type:</span> {{ ucfirst($filters['userType']) }}
                </div>
            @endif
            @if (!empty($filters['courses']) && count($filters['courses']) > 0)
                <div class="filter-item">
                    <span class="filter-label">Courses:</span> {{ implode(', ', $filters['courses']) }}
                </div>
            @endif
        </div>

        <!-- Executive Summary -->
        <div class="section-title">Executive Summary</div>

        <div class="summary-grid">
            <div class="summary-card">
                <div class="label">Total Logins</div>
                <div class="value">{{ number_format($totalStatistics['total_logins'] ?? 0) }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Unique Users</div>
                <div class="value">{{ number_format($totalStatistics['unique_logins'] ?? 0) }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Average per Day</div>
                <div class="value">
                    {{ number_format(count($dailyStats) > 0 ? ($totalStatistics['total_logins'] ?? 0) / count($dailyStats) : 0, 1) }}
                </div>
            </div>
        </div>

        <!-- Category Breakdown -->
        <div class="section-title">Category Breakdown</div>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="label">Students</div>
                <div class="value">{{ number_format($categoryStats['student']['total'] ?? 0) }}</div>
            </div>
            <div class="stat-box">
                <div class="label">Faculty</div>
                <div class="value">{{ number_format($categoryStats['faculty']['total'] ?? 0) }}</div>
            </div>
            <div class="stat-box">
                <div class="label">Staff</div>
                <div class="value">{{ number_format($categoryStats['staff']['total'] ?? 0) }}</div>
            </div>
            <div class="stat-box">
                <div class="label">Visitors</div>
                <div class="value">{{ number_format($categoryStats['visitor']['total'] ?? 0) }}</div>
            </div>
        </div>

        <!-- Sex Breakdown -->
        <div class="section-title">Sex Breakdown</div>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="label">Male</div>
                <div class="value">{{ number_format($totalStatistics['total_male_logins'] ?? 0) }}</div>
            </div>
            <div class="stat-box">
                <div class="label">Female</div>
                <div class="value">{{ number_format($totalStatistics['total_female_logins'] ?? 0) }}</div>
            </div>
            <div class="stat-box">
                <div class="label">Sex Ratio (M:F)</div>
                <div class="value">
                    {{ $totalStatistics['total_male_logins'] ?? 0 }}:{{ $totalStatistics['total_female_logins'] ?? 0 }}
                </div>
            </div>
            <div class="stat-box">
                <div class="label">Total</div>
                <div class="value">{{ number_format($totalStatistics['total_logins'] ?? 0) }}</div>
            </div>
        </div>
    </div>

    <!-- PAGE 2: Monthly/Daily & Hourly Statistics -->
    <div class="page">
        <div class="header">
            <h1>CLSU University Library</h1>
            <h2>Temporal Analysis</h2>
        </div>

        <!-- Monthly Statistics -->
        <div class="section-title">Monthly Statistics</div>
        @if (count($monthlyStats) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th style="text-align: right;">Total Logins</th>
                        <th style="text-align: right;">% of Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($monthlyStats as $stat)
                        @php
                            $percentage =
                                ($totalStatistics['total_logins'] ?? 0) > 0
                                    ? ($stat->total / $totalStatistics['total_logins']) * 100
                                    : 0;
                        @endphp
                        <tr>
                            <td>{{ date('F Y', strtotime($stat->month . '-01')) }}</td>
                            <td style="text-align: right;">{{ number_format($stat->total) }}</td>
                            <td style="text-align: right;">{{ number_format($percentage, 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; color: #999; padding: 20px;">No monthly data available</p>
        @endif

        <!-- Hourly Statistics -->
        <div class="section-title">Hourly Distribution</div>
        @if (count($hourlyStats) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Time</th>
                        <th style="text-align: right;">Logins</th>
                        <th style="text-align: right;">% of Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($hourlyStats as $stat)
                        @php
                            $percentage =
                                ($totalStatistics['total_logins'] ?? 0) > 0
                                    ? ($stat->total / $totalStatistics['total_logins']) * 100
                                    : 0;
                        @endphp
                        <tr>
                            <td>{{ str_pad($stat->hour, 2, '0', STR_PAD_LEFT) }}:00 -
                                {{ str_pad($stat->hour + 1, 2, '0', STR_PAD_LEFT) }}:00</td>
                            <td style="text-align: right;">{{ number_format($stat->total) }}</td>
                            <td style="text-align: right;">{{ number_format($percentage, 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; color: #999; padding: 20px;">No hourly data available</p>
        @endif
    </div>

    <!-- PAGE 3: Course Distribution & Detailed Statistics -->
    <div class="page">
        <div class="header">
            <h1>CLSU University Library</h1>
            <h2>Course & Category Analysis</h2>
        </div>

        <!-- All Courses -->
        <div class="section-title">All Courses by Attendance</div>
        @if (count($courseStats) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Course</th>
                        <th style="text-align: right;">Total Logins</th>
                        <th style="text-align: right;">% of Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($courseStats as $index => $stat)
                        @php
                            $percentage =
                                ($totalStatistics['total_logins'] ?? 0) > 0
                                    ? ($stat->total / $totalStatistics['total_logins']) * 100
                                    : 0;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $stat->course }}</td>
                            <td style="text-align: right;">{{ number_format($stat->total) }}</td>
                            <td style="text-align: right;">{{ number_format($percentage, 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; color: #999; padding: 20px;">No course data available</p>
        @endif

        <!-- User Type Detailed Breakdown -->
        <div class="section-title">Detailed Category Statistics</div>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th style="text-align: right;">Total Logins</th>
                    <th style="text-align: right;">Unique Users</th>
                    <th style="text-align: right;">Avg. Visits/User</th>
                    <th style="text-align: right;">% of Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach (['student' => 'Students', 'faculty' => 'Faculty', 'staff' => 'Staff', 'visitor' => 'Visitors'] as $key => $label)
                    @php
                        $total = $categoryStats[$key]['total'] ?? 0;
                        $unique = $categoryStats[$key]['unique'] ?? 0;
                        $avg = $unique > 0 ? $total / $unique : 0;
                        $percentage =
                            ($totalStatistics['total_logins'] ?? 0) > 0
                                ? ($total / $totalStatistics['total_logins']) * 100
                                : 0;
                    @endphp
                    <tr>
                        <td>{{ $label }}</td>
                        <td style="text-align: right;">{{ number_format($total) }}</td>
                        <td style="text-align: right;">{{ number_format($unique) }}</td>
                        <td style="text-align: right;">{{ number_format($avg, 1) }}</td>
                        <td style="text-align: right;">{{ number_format($percentage, 1) }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Sex Breakdown -->
        <div class="section-title">Sex Distribution</div>
        <div class="two-column">
            <div class="column">
                <table>
                    <thead>
                        <tr>
                            <th>Sex</th>
                            <th style="text-align: right;">Total Logins</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Male</td>
                            <td style="text-align: right;">
                                {{ number_format($totalStatistics['total_male_logins'] ?? 0) }}</td>
                        </tr>
                        <tr>
                            <td>Female</td>
                            <td style="text-align: right;">
                                {{ number_format($totalStatistics['total_female_logins'] ?? 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="column">
                <table>
                    <thead>
                        <tr>
                            <th>Sex</th>
                            <th style="text-align: right;">Unique Users</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Male</td>
                            <td style="text-align: right;">
                                {{ number_format($totalStatistics['unique_male_logins'] ?? 0) }}</td>
                        </tr>
                        <tr>
                            <td>Female</td>
                            <td style="text-align: right;">
                                {{ number_format($totalStatistics['unique_female_logins'] ?? 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="footer">
        Generated on {{ date('F d, Y h:i A') }} | CLSU University Library Information System
    </div>
</body>

</html>
