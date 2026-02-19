<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;

class EventsRegisteredDetailsExport implements
    FromCollection,
    WithHeadings,
    WithColumnWidths,
    WithTitle,
    ShouldAutoSize,
    WithStyles
{
    protected $key;

    public function __construct($key = null)
    {
        $this->key = $key;
    }

    public function title(): string
    {
        return 'Events Registered Details';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical'   => 'center',
                ],
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Sl No',
            'Event Name',
            'First Name',
            'Last Name',
            'Email',
            'Mobile No',
            'QID',
            'No.of Child Below 5',
            'No.of Child Above 10',
            'No.of Child 5 to 10',
            'No.of Adult',
            'Workplace',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // Sl No
            'B' => 30,  // Event Name
            'C' => 18,  // First Name
            'D' => 18,  // Last Name
            'E' => 30,  // Email
            'F' => 15,  // Mobile No
            'G' => 15,  // QID
            'H' => 20,  // Children Below 5
            'I' => 20,  // Children Above 10
            'J' => 20,  // Children 5 to 10
            'K' => 12,  // Adults
            'L' => 25,  // Workplace
        ];
    }

    public function collection()
    {
        $data = DB::table('registrations as r')
            ->join('eventregisters as er', 'r.user_id', '=', 'er.user_id')
            ->join('events as e', 'er.event_id', '=', 'e.id')
            ->select(
                'e.tittle as event_name',
                'r.first_name',
                'r.last_name',
                'r.email',
                'r.mob_no',
                'r.qid',
                'er.no_child_below_5',
                'er.no_child_above_10',
                'er.no_child_5_to_10',
                'er.number_adult',
                'er.workplace'
            )
            ->when($this->key, function ($query) {
                $query->where('e.tittle', 'like', '%' . $this->key . '%');
            })
            ->orderBy('r.id', 'desc')
            ->get();

        return $data->map(function ($row, $index) {
            return [
                $index + 1, // Sl No
                $row->event_name ?? 'N/A',
                $row->first_name ?? 'N/A',
                $row->last_name ?? 'N/A',
                $row->email ?? 'N/A',
                $row->mob_no ?? 'N/A',
                $row->qid ?? 'N/A',
                $row->no_child_below_5 ?? '0',
                $row->no_child_above_10 ?? '0',
                $row->no_child_5_to_10 ?? '0',
                $row->number_adult ?? '0',
                $row->workplace ?? 'N/A',
            ];
        });
    }
}