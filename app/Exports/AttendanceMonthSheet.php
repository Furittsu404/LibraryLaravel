<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AttendanceMonthSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected $data;
    protected $month;
    protected $year;
    protected $sectionName;

    public function __construct($data, $month, $year, $sectionName = 'All Sections')
    {
        $this->data = $data;
        $this->month = $month;
        $this->year = $year;
        $this->sectionName = $sectionName;
    }

    public function array(): array
    {
        $rows = [];

        // Title row - Month and Year
        $monthName = date('F', mktime(0, 0, 0, $this->month, 1));
        $rows[] = ["{$monthName} {$this->year}"];
        $rows[] = []; // Empty row

        // Section label - now with actual section name
        $rows[] = ["Section: {$this->sectionName}"];
        $rows[] = []; // Empty row

        // Header rows (Days | Students M/F | Faculty M/F | Staff M/F | Visitors M/F | Total M/F)
        $rows[] = [
            'Days',
            'Students',
            '',
            'Faculty',
            '',
            'Staff',
            '',
            'Visitors',
            '',
            'Total',
            ''
        ];

        $rows[] = [
            '',
            'M',
            'F',
            'M',
            'F',
            'M',
            'F',
            'M',
            'F',
            'M',
            'F'
        ];

        // Calculate totals
        $totals = [
            'students_m' => 0,
            'students_f' => 0,
            'faculty_m' => 0,
            'faculty_f' => 0,
            'staff_m' => 0,
            'staff_f' => 0,
            'visitors_m' => 0,
            'visitors_f' => 0,
            'total_m' => 0,
            'total_f' => 0,
        ];

        // Data rows (Days 1-31)
        for ($day = 1; $day <= 31; $day++) {
            $dayData = $this->data[$day] ?? null;

            if (!$dayData || $this->isAllZero($dayData)) {
                // Empty row for days with no data
                $rows[] = [$day, '', '', '', '', '', '', '', ''];
            } else {
                $students_m = $dayData['students']['M'] ?? 0;
                $students_f = $dayData['students']['F'] ?? 0;
                $faculty_m = $dayData['faculty']['M'] ?? 0;
                $faculty_f = $dayData['faculty']['F'] ?? 0;
                $staff_m = $dayData['staff']['M'] ?? 0;
                $staff_f = $dayData['staff']['F'] ?? 0;
                $visitors_m = $dayData['visitors']['M'] ?? 0;
                $visitors_f = $dayData['visitors']['F'] ?? 0;

                $total_m = $students_m + $faculty_m + $staff_m + $visitors_m;
                $total_f = $students_f + $faculty_f + $staff_f + $visitors_f;

                // Update totals
                $totals['students_m'] += $students_m;
                $totals['students_f'] += $students_f;
                $totals['faculty_m'] += $faculty_m;
                $totals['faculty_f'] += $faculty_f;
                $totals['staff_m'] += $staff_m;
                $totals['staff_f'] += $staff_f;
                $totals['visitors_m'] += $visitors_m;
                $totals['visitors_f'] += $visitors_f;
                $totals['total_m'] += $total_m;
                $totals['total_f'] += $total_f;

                $rows[] = [
                    $day,
                    $students_m ?: '',
                    $students_f ?: '',
                    $faculty_m ?: '',
                    $faculty_f ?: '',
                    $staff_m ?: '',
                    $staff_f ?: '',
                    $visitors_m ?: '',
                    $visitors_f ?: '',
                    $total_m ?: '',
                    $total_f ?: ''
                ];
            }
        }

        // Total row
        $rows[] = [
            'Total',
            $totals['students_m'] ?: '',
            $totals['students_f'] ?: '',
            $totals['faculty_m'] ?: '',
            $totals['faculty_f'] ?: '',
            $totals['staff_m'] ?: '',
            $totals['staff_f'] ?: '',
            $totals['visitors_m'] ?: '',
            $totals['visitors_f'] ?: '',
            $totals['total_m'] ?: '',
            $totals['total_f'] ?: ''
        ];

        // Empty row before grand total
        $rows[] = [];

        // Grand Total row (combining M and F)
        $grandTotal = $totals['total_m'] + $totals['total_f'];
        $rows[] = [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'Grand Total',
            $grandTotal ?: ''
        ];

        return $rows;
    }

    protected function isAllZero($dayData): bool
    {
        $total = 0;
        foreach (['students', 'faculty', 'staff', 'visitors'] as $type) {
            $total += ($dayData[$type]['M'] ?? 0) + ($dayData[$type]['F'] ?? 0);
        }
        return $total === 0;
    }

    public function title(): string
    {
        return date('M', mktime(0, 0, 0, $this->month, 1)) . ' ' . $this->year;
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells for title
        $sheet->mergeCells('A1:K1');

        // Merge cells for header row (Students, Faculty, Staff, Visitors, Total)
        $sheet->mergeCells('B3:C3'); // Students
        $sheet->mergeCells('D3:E3'); // Faculty
        $sheet->mergeCells('F3:G3'); // Staff
        $sheet->mergeCells('H3:I3'); // Visitors
        $sheet->mergeCells('J3:K3'); // Total

        // Title styling
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Section label styling
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'size' => 10,
            ],
        ]);

        // Header rows styling
        $sheet->getStyle('A3:K4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8E8E8'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Data rows styling (including total row)
        $lastRow = 38; // Row 1 (title) + Row 2 (empty) + Row 3 (section) + Row 4 (empty) + Row 5 (header) + Row 6 (subheader) + 31 days + 1 total = row 38
        $sheet->getStyle("A5:K{$lastRow}")->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Total row styling (bold)
        $sheet->getStyle("A{$lastRow}:K{$lastRow}")->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F0F0F0'],
            ],
        ]);

        // Grand Total row styling (row 40: row 38 + 1 empty + 1 grand total)
        $grandTotalRow = $lastRow + 2;
        $sheet->getStyle("J{$grandTotalRow}:K{$grandTotalRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D0D0D0'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(5)->setRowHeight(20);
        $sheet->getRowDimension(6)->setRowHeight(20);
        $sheet->getRowDimension($grandTotalRow)->setRowHeight(25);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // Days
            'B' => 10,  // Students M
            'C' => 10,  // Students F
            'D' => 10,  // Faculty M
            'E' => 10,  // Faculty F
            'F' => 10,  // Staff M
            'G' => 10,  // Staff F
            'H' => 10,  // Visitors M
            'I' => 10,  // Visitors F
            'J' => 10,  // Total M
            'K' => 10,  // Total F
        ];
    }
}
