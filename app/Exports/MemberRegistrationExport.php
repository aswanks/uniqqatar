<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class MemberRegistrationExport implements FromCollection, WithHeadings, WithColumnWidths, WithMapping, WithStyles
{
    protected $key;
    protected $regDate;
    protected $row = 0;

    public function __construct($key = null, $regDate = null)
    {
        $this->key = $key;
        $this->regDate = $regDate;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [ // Row 1 = heading row
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


    /* ================= HEADINGS ================= */
    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Gender',
            'Blood Group',
            'DOB',
            'Mobile',
            'E-mail',
            'Current Employer',
            'Designation',
            'UNIQ ID',
            'Status',
            'Registration at',
        ];
    }

    /* ================= COLUMN WIDTH ================= */
    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 25,
            'C' => 12,
            'D' => 15,
            'E' => 18,
            'F' => 30,
            'G' => 25,
            'H' => 20,
            'I' => 18,
            'J' => 14,
            'K' => 14,
            'L' => 14,
        ];
    }

    /* ================= ROW DATA ================= */
    public function map($member): array
    {
        $this->row++;

        // Status text (PHP 7.3 safe)
        if ($member->status == 1) {
            $statusText = 'Approved';
        } elseif ($member->status == 0) {
            $statusText = 'Pending';
        } else {
            $statusText = 'Rejected';
        }

        return [
            $this->row,
            (trim(($member->first_name ?? '') . ' ' . ($member->last_name ?? ''))) ?: 'N/A',
            $member->gender ?? 'N/A',
            $member->blood_grp ?? 'N/A',
            $member->dob ?? 'N/A',
            $member->mob_no ?? 'N/A',
            $member->email  ?? 'N/A',
            $member->employer_current ?? 'N/A',
            $member->destination ?? 'N/A',
            $member->uniq_id ?? 'N/A',
            $statusText ?? 'N/A',
            $member->registration_date ?? 'N/A',
        ];
    }

    /* ================= QUERY ================= */
    public function collection()
    {
        $query = DB::table('registrations as t1')
            ->leftJoin('registrations as t2', 't1.reffered_by', '=', 't2.id')
            ->select(
                't1.first_name',
                't1.last_name',
                't1.gender',
                't1.blood_grp',
                't1.dob',
                't1.mob_no',
                't1.email',
                't1.destination',
                't1.employer_current',
                't1.uniq_id',
                't1.status',
                't1.registration_date',
                DB::raw("CONCAT(t2.first_name,' ',t2.last_name) as referred_by")
            )
            ->orderBy('t1.id', 'desc');

        if (!empty($this->key)) {

            // Status search
            $status = null;
            $keyUpper = strtoupper($this->key);

            if ($keyUpper === 'PENDING') {
                $status = 0;
            } elseif ($keyUpper === 'APPROVED') {
                $status = 1;
            } elseif ($keyUpper === 'REJECTED') {
                $status = 2;
            }

            $query->where(function ($q) use ($status) {
                $q->where('t1.first_name', 'like', '%' . $this->key . '%')
                ->orWhere('t1.last_name', 'like', '%' . $this->key . '%')
                ->orWhere('t1.uniq_id', 'like', '%' . $this->key . '%')
                ->orWhere('t1.mob_no', 'like', '%' . $this->key . '%')
                ->orWhere('t1.dob', 'like', '%' . $this->key . '%')
                ->orWhere('t1.email', 'like', '%' . $this->key . '%')
                ->orWhere('t1.destination', 'like', '%' . $this->key . '%')
                ->orWhere('t1.employer_current', 'like', '%' . $this->key . '%');

                if ($status !== null) {
                    $q->orWhere('t1.status', $status);
                }
            });
        }
        
        if (!empty($this->regDate)) {
            $query->whereDate('t1.registration_date', $this->regDate);
        }

        return $query->get();
    }

}