<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Excel;

class VehicleTimelineExport implements WithMultipleSheets
{
    protected $vehicles;

    public function __construct($vehicles)
    {
        $this->vehicles = $vehicles;
    }

    /**
     * Return an array of sheet objects (one per vehicle)
     */
    public function sheets(): array
    {
        $sheets = [];

        // Collect all payload keys from all assignments' bookings so we can add them as columns
        $payloadKeys = [];
        foreach ($this->vehicles as $v) {
            foreach ($v['assignments'] as $a) {
                if (!empty($a['booking']) && !empty($a['booking']['payload_data']) && is_array($a['booking']['payload_data'])) {
                    $payloadKeys = array_merge($payloadKeys, array_keys($a['booking']['payload_data']));
                }
            }
        }
        $payloadKeys = array_values(array_unique($payloadKeys));

        // Build headings: base + payload keys
        $baseHeadings = ['Assignment ID','Booking ID','Booking Order','Booking User','Start At','End At','Notes','Status'];
        $payloadHeadings = array_map(function($k){ return ucwords(str_replace('_',' ',$k)); }, $payloadKeys);
        $headings = array_merge($baseHeadings, $payloadHeadings);

        foreach ($this->vehicles as $v) {
            $rows = [];
            foreach ($v['assignments'] as $a) {
                $base = [
                    $a['id'] ?? '',
                    $a['booking_id'] ?? '',
                    $a['booking_order'] ?? '',
                    $a['booking_user'] ?? '',
                    $a['start_at'] ?? '',
                    $a['end_at'] ?? '',
                    $a['notes'] ?? '',
                    $a['status'] ?? '',
                ];

                // append payload values in consistent order
                $payloadValues = [];
                $bookingPayload = !empty($a['booking']) && !empty($a['booking']['payload_data']) && is_array($a['booking']['payload_data']) ? $a['booking']['payload_data'] : [];
                foreach ($payloadKeys as $key) {
                    $val = $bookingPayload[$key] ?? '';
                    if (is_array($val)) {
                        $val = implode(', ', array_map(function($it){ return is_string($it)?$it:json_encode($it, JSON_UNESCAPED_UNICODE); }, $val));
                    } elseif (is_object($val)) {
                        $val = json_encode($val, JSON_UNESCAPED_UNICODE);
                    }
                    $payloadValues[] = $val;
                }

                $rows[] = array_merge($base, $payloadValues);
            }

            $sheets[] = new VehicleSheet($v, $rows, $headings);
        }

        return $sheets;
    }
}
