<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AttendanceReportExport implements WithMultipleSheets
{
    protected $monthlyData;
    protected $year;
    protected $sectionName;

    public function __construct($monthlyData, $year, $sectionName = 'All Sections')
    {
        $this->monthlyData = $monthlyData;
        $this->year = $year;
        $this->sectionName = $sectionName;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->monthlyData as $month => $data) {
            $sheets[] = new AttendanceMonthSheet($data, $month, $this->year, $this->sectionName);
        }

        return $sheets;
    }
}
