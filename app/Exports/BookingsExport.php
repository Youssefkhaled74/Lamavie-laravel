<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

class BookingsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $request;
    protected $payloadKeys = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
        
        // Collect all unique payload keys from all bookings
        $this->collectPayloadKeys();
    }
    
    /**
     * Collect all unique keys from payload_data across all bookings
     */
    protected function collectPayloadKeys()
    {
        $query = $this->query();
        $bookings = $query->get();
        
        $allKeys = [];
        foreach ($bookings as $booking) {
            if ($booking->payload_data && is_array($booking->payload_data)) {
                $allKeys = array_merge($allKeys, array_keys($booking->payload_data));
            }
        }
        
        $this->payloadKeys = array_unique($allKeys);
    }

    /**
     * Query for the export with filters
     */
    public function query()
    {
        $query = Booking::query()
            ->with(['user', 'service', 'serviceCategory', 'serviceType', 'paymentMethod', 'driver', 'lab'])
            ->orderByDesc('created_at');

        // Filter by service
        if ($this->request->filled('service_id')) {
            $query->where('service_id', $this->request->service_id);
        }

        // Filter by service category
        if ($this->request->filled('service_category_id')) {
            $query->where('service_category_id', $this->request->service_category_id);
        }

        // Filter by service type
        if ($this->request->filled('service_type_id')) {
            $query->where('service_type_id', $this->request->service_type_id);
        }

        // Filter by user
        if ($this->request->filled('user_id')) {
            $query->where('user_id', $this->request->user_id);
        }

        // Filter by status
        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        // Filter by payment method
        if ($this->request->filled('payment_method_id')) {
            $query->where('payment_method_id', $this->request->payment_method_id);
        }

        // Filter by user area
        if ($this->request->filled('area_id')) {
            $query->whereHas('user', function ($q) {
                $q->where('area_id', $this->request->area_id);
            });
        }

        // Filter by date range
        if ($this->request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $this->request->date_from);
        }

        if ($this->request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $this->request->date_to);
        }

        // Filter by single date
        if ($this->request->filled('date') && !$this->request->filled('date_from')) {
            $query->whereDate('created_at', $this->request->date);
        }

        // Search by user name, phone, or email
        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        return $query;
    }

    /**
     * Map data for each row
     */
    public function map($booking): array
    {
        $row = [
            $booking->id,
            $booking->order_number,
            $booking->user ? $booking->user->name : 'N/A',
            $booking->user ? $booking->user->phone : 'N/A',
            $booking->user ? $booking->user->email : 'N/A',
            $booking->service ? ($booking->service->name['en'] ?? $booking->service->name['ar'] ?? 'N/A') : 'N/A',
            $booking->serviceCategory ? ($booking->serviceCategory->name['en'] ?? $booking->serviceCategory->name['ar'] ?? 'N/A') : 'N/A',
            $booking->serviceType ? ($booking->serviceType->name['en'] ?? $booking->serviceType->name['ar'] ?? 'N/A') : 'N/A',
            ucfirst($booking->status),
            $booking->total,
            $booking->paymentMethod ? ($booking->paymentMethod->name['en'] ?? $booking->paymentMethod->name['ar'] ?? 'N/A') : 'N/A',
            $booking->driver ? $booking->driver->name : 'N/A',
            $booking->lab ? $booking->lab->name : 'N/A',
            $booking->lab_assigned_at ? $booking->lab_assigned_at->format('Y-m-d H:i:s') : 'N/A',
            $booking->lab_arrived_at ? $booking->lab_arrived_at->format('Y-m-d H:i:s') : 'N/A',
            $booking->lab_picked_at ? $booking->lab_picked_at->format('Y-m-d H:i:s') : 'N/A',
            $booking->driver_collected_at ? $booking->driver_collected_at->format('Y-m-d H:i:s') : 'N/A',
            $booking->created_at->format('Y-m-d H:i:s'),
            $booking->updated_at->format('Y-m-d H:i:s'),
        ];
        
        // Add payload data columns dynamically
        foreach ($this->payloadKeys as $key) {
            $value = $booking->payload_data[$key] ?? '';
            
            // Handle arrays/objects in payload - preserve Arabic text
            if (is_array($value)) {
                // If it's an array, join with commas
                $value = implode(', ', array_map(function($item) {
                    return is_string($item) ? $item : json_encode($item, JSON_UNESCAPED_UNICODE);
                }, $value));
            } elseif (is_object($value)) {
                // If it's an object, convert to JSON with unescaped Unicode
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }
            
            $row[] = $value;
        }
        
        return $row;
    }

    /**
     * Column headings
     */
    public function headings(): array
    {
        $baseHeadings = [
            'ID',
            'Order Number',
            'User Name',
            'User Phone',
            'User Email',
            'Service',
            'Category',
            'Type',
            'Status',
            'Total',
            'Payment Method',
            'Driver',
            'Lab',
            'Lab Assigned At',
            'Lab Arrived At',
            'Lab Picked At',
            'Driver Collected At',
            'Created At',
            'Updated At',
        ];
        
        // Add payload keys as column headers
        foreach ($this->payloadKeys as $key) {
            // Convert snake_case to Title Case for better readability
            $heading = ucwords(str_replace('_', ' ', $key));
            $baseHeadings[] = $heading;
        }
        
        return $baseHeadings;
    }

    /**
     * Style the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        // Get the last column letter dynamically
        $lastColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();
        $headerRange = 'A1:' . $lastColumn . '1';
        
        // ===== HEADER ROW STYLING =====
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 13,
                'color' => ['rgb' => 'FFFFFF'], // White text
                'name' => 'Segoe UI'
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => ['rgb' => '1E40AF'], // Deep blue
                'endColor' => ['rgb' => '3B82F6'],   // Lighter blue
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '1E3A8A'],
                ],
            ],
        ]);
        
        // Set header row height
        $sheet->getRowDimension(1)->setRowHeight(30);
        
        // Freeze the header row
        $sheet->freezePane('A2');
        
        // Add filters to header row
        $sheet->setAutoFilter($headerRange);
        
        // ===== DATA ROWS STYLING =====
        for ($row = 2; $row <= $lastRow; $row++) {
            $rowRange = 'A' . $row . ':' . $lastColumn . $row;
            
            // Zebra striping with subtle colors
            $fillColor = ($row % 2 == 0) ? 'F0F9FF' : 'FFFFFF'; // Very light blue / white
            
            $sheet->getStyle($rowRange)->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $fillColor]
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'DBEAFE'],
                    ],
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText' => false,
                ],
                'font' => [
                    'name' => 'Segoe UI',
                    'size' => 11,
                ],
            ]);
            
            // Set minimum row height for readability
            $sheet->getRowDimension($row)->setRowHeight(22);
        }
        
        // ===== COLUMN-SPECIFIC STYLING =====
        
        // ID column - center aligned, bold
        $sheet->getStyle('A2:A' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'font' => ['bold' => true, 'color' => ['rgb' => '1F2937']],
        ]);
        
        // Order Number column - bold, slight background
        $sheet->getStyle('B2:B' . $lastRow)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '1E40AF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EFF6FF']
            ],
        ]);
        
        // Status column - center aligned
        $statusCol = 'I'; // Status is the 9th column
        $sheet->getStyle($statusCol . '2:' . $statusCol . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'font' => ['bold' => true],
        ]);
        
        // Apply conditional styling for Status column
        for ($row = 2; $row <= $lastRow; $row++) {
            $cellValue = $sheet->getCell($statusCol . $row)->getValue();
            $statusColors = [
                'Pending' => ['bg' => 'FEF3C7', 'text' => '92400E'],  // Yellow
                'Pickup' => ['bg' => 'DBEAFE', 'text' => '1E40AF'],   // Blue
                'Delivered' => ['bg' => 'D1FAE5', 'text' => '065F46'], // Green
                'Canceled' => ['bg' => 'FEE2E2', 'text' => '991B1B'],  // Red
            ];
            
            foreach ($statusColors as $status => $colors) {
                if (stripos($cellValue, $status) !== false) {
                    $sheet->getStyle($statusCol . $row)->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $colors['bg']]
                        ],
                        'font' => ['color' => ['rgb' => $colors['text']], 'bold' => true],
                    ]);
                    break;
                }
            }
        }
        
        // Total column - right aligned, bold, currency style
        $totalCol = 'J'; // Total is the 10th column
        $sheet->getStyle($totalCol . '2:' . $totalCol . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            ],
            'font' => ['bold' => true, 'color' => ['rgb' => '059669']],
            'numberFormat' => [
                'formatCode' => '#,##0.00'
            ],
        ]);
        
        // Date columns - center aligned with date format
        $dateColumns = ['R', 'S']; // Created At, Updated At
        foreach ($dateColumns as $col) {
            $sheet->getStyle($col . '2:' . $col . $lastRow)->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
                'font' => ['size' => 10, 'color' => ['rgb' => '6B7280']],
            ]);
        }
        
        // ===== COLUMN WIDTHS =====
        $columnWidths = [
            'A' => 8,   // ID
            'B' => 18,  // Order Number
            'C' => 20,  // User Name
            'D' => 15,  // User Phone
            'E' => 25,  // User Email
            'F' => 18,  // Service
            'G' => 18,  // Category
            'H' => 18,  // Type
            'I' => 12,  // Status
            'J' => 12,  // Total
            'K' => 18,  // Payment Method
            'L' => 18,  // Driver
            'M' => 18,  // Lab
            'N' => 20,  // Lab Assigned At
            'O' => 20,  // Lab Arrived At
            'P' => 20,  // Lab Picked At
            'Q' => 22,  // Driver Collected At
            'R' => 20,  // Created At
            'S' => 20,  // Updated At
        ];
        
        foreach ($columnWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }
        
        // Auto-size payload columns
        $currentCol = 'T';
        foreach ($this->payloadKeys as $key) {
            $sheet->getColumnDimension($currentCol)->setAutoSize(true);
            $currentCol++;
        }
        
        return [];
    }
}
