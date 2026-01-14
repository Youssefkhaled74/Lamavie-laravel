<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VehicleSheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $vehicle;
    protected $rows;
    protected $headings;

    public function __construct($vehicle, $rows = [], $headings = [])
    {
        $this->vehicle = $vehicle;
        $this->rows = $rows;
        $this->headings = $headings;
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return $this->headings ?: ['Assignment ID','Booking ID','Booking Order','Booking User','Start At','End At','Notes','Status'];
    }

    public function title(): string
    {
        $plate = $this->vehicle['plate_number'] ?? 'vehicle';
        // keep sheet name short and safe for Excel
        $name = preg_replace('/[^A-Za-z0-9\-_]/', '-', $plate);
        return substr($name, 0, 31);
    }

    public function styles(Worksheet $sheet)
    {
        // header styling
        $lastColumn = $sheet->getHighestColumn();
        $headerRange = 'A1:' . $lastColumn . '1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Segoe UI'],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => ['rgb' => '05668D'],
                'endColor' => ['rgb' => '2AA7DF'],
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
        ]);

        // freeze header
        $sheet->freezePane('A2');

        // zebra rows & general styling
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++) {
            $range = 'A' . $row . ':' . $lastColumn . $row;
            $fill = ($row % 2 == 0) ? 'FFFFFF' : 'F7FEFF';
            $sheet->getStyle($range)->applyFromArray([
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $fill]],
                'font' => ['name' => 'Segoe UI', 'size' => 11],
                'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(20);
        }

        // set column widths minimally (auto-size handles rest)
        foreach (range('A', $lastColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
