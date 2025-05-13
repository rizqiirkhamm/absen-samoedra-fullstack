<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Carbon\Carbon;

class AttendanceExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithEvents
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        $query = Attendance::query()
            ->orderBy('tanggal', 'desc')
            ->orderBy('check_in', 'desc');

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tanggal', [$this->startDate, $this->endDate]);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Tanggal',
            'Check In',
            'Check Out',
            'Lokasi',
            'Hadir Untuk',
            'Durasi Kerja',
            'Persentase Kehadiran',
            'Alasan Pulang Awal'
        ];
    }

    public function map($attendance): array
    {
        $duration = $this->formatDuration(abs($attendance->work_hours));
        
        return [
            $attendance->nama,
            $attendance->tanggal->format('Y-m-d'),
            $attendance->check_in ? date('H:i:s', strtotime($attendance->check_in)) : '-',
            $attendance->check_out ? date('H:i:s', strtotime($attendance->check_out)) : '-',
            $attendance->lokasi,
            $attendance->hadir_untuk,
            $duration,
            abs($attendance->attendance_percentage) . '%',
            $attendance->early_leave_reason ?? '-'
        ];
    }

    private function formatDuration($minutes)
    {
        if ($minutes < 60) {
            return $minutes . ' menit';
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        if ($remainingMinutes == 0) {
            return $hours . ' jam';
        }
        
        return $hours . ' jam ' . $remainingMinutes . ' menit';
    }

    private function calculateAttendanceStats($attendances, $targetHours)
    {
        if ($attendances->isEmpty()) {
            return [
                'percentage' => 0,
                'count' => 0,
                'total' => $targetHours
            ];
        }

        $totalMinutesWorked = 0;
        $count = 0;

        foreach ($attendances as $attendance) {
            if ($attendance->check_out) { // Only count completed attendances
                $totalMinutesWorked += min($attendance->work_hours, 480); // Cap at 8 hours (480 minutes)
                $count++;
            }
        }

        $targetMinutes = $targetHours * 8 * 60; // Convert target days to minutes (8 hours per day)
        $percentage = $targetMinutes > 0 ? ($totalMinutesWorked / $targetMinutes) * 100 : 0;

        return [
            'percentage' => min(100, round($percentage, 1)),
            'count' => $count,
            'total' => $targetHours
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();

                // Style the main table
                $tableRange = 'A1:' . $lastColumn . $lastRow;
                $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                
                // Center align specific columns
                $sheet->getStyle('B1:D' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G1:H' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Word wrap for text columns
                $sheet->getStyle('E1:F' . $lastRow)->getAlignment()->setWrapText(true);
                $sheet->getStyle('I1:I' . $lastRow)->getAlignment()->setWrapText(true);
                
                // Alternate row colors
                for ($row = 2; $row <= $lastRow; $row++) {
                    $fillColor = $row % 2 == 0 ? 'F3F4F6' : 'FFFFFF';
                    $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB($fillColor);
                }

                // Set column widths for main table
                $sheet->getColumnDimension('A')->setWidth(25); // Nama
                $sheet->getColumnDimension('B')->setWidth(15); // Tanggal
                $sheet->getColumnDimension('C')->setWidth(12); // Check In
                $sheet->getColumnDimension('D')->setWidth(12); // Check Out
                $sheet->getColumnDimension('E')->setWidth(40); // Lokasi
                $sheet->getColumnDimension('F')->setWidth(30); // Hadir Untuk
                $sheet->getColumnDimension('G')->setWidth(20); // Durasi Kerja
                $sheet->getColumnDimension('H')->setWidth(20); // Persentase Kehadiran
                $sheet->getColumnDimension('I')->setWidth(35); // Alasan Pulang Awal

                // Get employee statistics
                $employees = Employee::all();
                $now = Carbon::now();
                $startOfWeek = $now->copy()->startOfWeek();
                $endOfWeek = $now->copy()->endOfWeek();
                $startOfMonth = $now->copy()->startOfMonth();
                $endOfMonth = $now->copy()->endOfMonth();

                // Calculate working days (excluding weekends)
                $workingDaysThisWeek = now()->startOfWeek()->diffInDaysFiltered(function(Carbon $date) {
                    return !$date->isWeekend();
                }, now()->endOfWeek());

                $workingDaysThisMonth = now()->startOfMonth()->diffInDaysFiltered(function(Carbon $date) {
                    return !$date->isWeekend();
                }, now()->endOfMonth());

                // Add statistics section with new design
                $statsRow = $lastRow + 3;
                
                // Add title with new design
                $sheet->setCellValue('A' . $statsRow, 'STATISTIK KEHADIRAN KARYAWAN');
                $sheet->mergeCells('A' . $statsRow . ':D' . $statsRow);
                $sheet->getStyle('A' . $statsRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4F46E5']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);
                $sheet->getRowDimension($statsRow)->setRowHeight(30);

                // Add headers with new design
                $headerRow = $statsRow + 1;
                $sheet->setCellValue('A' . $headerRow, 'Nama Karyawan');
                $sheet->setCellValue('B' . $headerRow, 'Hadir Untuk');
                $sheet->setCellValue('C' . $headerRow, 'Kehadiran Minggu Ini');
                $sheet->setCellValue('D' . $headerRow, 'Kehadiran Bulan Ini');

                // Style headers
                $headerRange = 'A' . $headerRow . ':D' . $headerRow;
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => [
                        'bold' => true
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E5E7EB']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);
                $sheet->getRowDimension($headerRow)->setRowHeight(25);

                // Set column widths for statistics
                $sheet->getColumnDimension('A')->setWidth(30);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(25);

                // Add employee statistics
                $currentRow = $headerRow + 1;
                foreach ($employees as $employee) {
                    // Weekly attendance
                    $weeklyAttendances = Attendance::where('nama', $employee->nama)
                        ->whereBetween('tanggal', [$startOfWeek, $endOfWeek])
                        ->get();
                    
                    $weeklyPercentage = $weeklyAttendances->isEmpty() ? 0 : round($weeklyAttendances->avg('attendance_percentage'), 1);
                    
                    // Monthly attendance
                    $monthlyAttendances = Attendance::where('nama', $employee->nama)
                        ->whereMonth('tanggal', now()->month)
                        ->whereYear('tanggal', now()->year)
                        ->get();
                    
                    $monthlyPercentage = $monthlyAttendances->isEmpty() ? 0 : round($monthlyAttendances->avg('attendance_percentage'), 1);

                    // Set values
                    $sheet->setCellValue('A' . $currentRow, $employee->nama);
                    $sheet->setCellValue('B' . $currentRow, $employee->hadir_untuk);
                    $sheet->setCellValue('C' . $currentRow, abs($weeklyPercentage) . '%');
                    $sheet->setCellValue('D' . $currentRow, abs($monthlyPercentage) . '%');

                    // Style the row
                    $rowRange = 'A' . $currentRow . ':D' . $currentRow;
                    $fillColor = $currentRow % 2 == 0 ? 'F3F4F6' : 'FFFFFF';
                    $sheet->getStyle($rowRange)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $fillColor]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER
                        ]
                    ]);
                    $sheet->getRowDimension($currentRow)->setRowHeight(22);

                    $currentRow++;
                }

                // Add borders to statistics table
                $statsRange = 'A' . $statsRow . ':D' . ($currentRow - 1);
                $sheet->getStyle($statsRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
} 