<?php

namespace App\Exports;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class RegistrationsExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping,
    WithStyles,
    WithColumnWidths
{   
    protected $registrations;

    public function __construct($registrations)
    {
        $this->registrations = $registrations;
    }

    /* ================= DATA ================= */
    public function collection()
    {
        return $this->registrations; // ✅ Use filtered data
    }

    /* ================= HEADINGS ================= */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Registration Date',
            'Expiry Date',
            'Status'
        ];
    }

    /* ================= MAP DATA ================= */
    public function map($registration): array
    {
        if ($registration->status == 1) {
            $statusText = 'Approved';
        } elseif ($registration->status == 0) {
            $statusText = 'Pending';
        } else {
            $statusText = 'Rejected';
        }

        return [
            $registration->id,
            trim(($registration->first_name ?? '') . ' ' . ($registration->last_name ?? '')) ?: 'N/A',
            $registration->email ?? '',
            $registration->registration_date,
            Carbon::parse($registration->registration_date)->addYear(1)->format('Y-m-d'),
            $statusText,
        ];
    }

    /* ================= HEADER STYLE ================= */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [ // Header row
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical'   => 'center',
                ],
            ],
        ];
    }

    /* ================= COLUMN WIDTH ================= */
    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 30,
            'C' => 30,
            'D' => 18,
            'E' => 18,
            'F' => 15,
        ];
    }
}