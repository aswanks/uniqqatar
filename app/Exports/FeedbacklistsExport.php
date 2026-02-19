<?php 

namespace App\Exports;

use DB;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class FeedbacklistsExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function collection()
    {
        $feedbacks = DB::table('event_feedback')
            ->join('events', 'events.id', '=', 'event_feedback.event_id')
            ->join('users', 'users.id', '=', 'event_feedback.user_id')
            ->select(
                'event_feedback.answer',
                'event_feedback.suggestion',
                'events.tittle as event_name',
                'users.firstname'
            )
            ->get();

        $questions = DB::table('event_feedback_questions')
            ->pluck('question', 'id');

        $rows = collect();

        foreach ($feedbacks as $feedback) {

            $answers = json_decode($feedback->answer, true);
            $qaText  = '';

            if (is_array($answers)) {
                $i = 1;
                foreach ($answers as $item) {
                    $question = $questions[$item['question_id']] ?? 'Unknown Question';
                    $qaText .= "Q{$i}. {$question}\n";
                    $qaText .= "*: {$item['answer']}\n\n";
                    $i++;
                }
            }

            $rows->push([
                'Event'      => $feedback->event_name,
                'User'       => $feedback->firstname,
                'Feedback'   => trim($qaText),
                'Suggestion' => $feedback->suggestion,
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Event',
            'User',
            'Feedback (Questions & Answers)',
            'Suggestion',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25, // Event
            'B' => 20, // User
            'C' => 70, // Feedback (Q&A)
            'D' => 30, // Suggestion
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical'   => 'center',
                ],
            ],

            'A' => [
                'alignment' => [
                    'vertical' => 'top',
                ],
            ],

            'B' => [
                'alignment' => [
                    'vertical' => 'top',
                ],
            ],

            'C' => [
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => 'top',
                ],
            ],

            'D' => [
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => 'top',
                ],
            ],
        ];
    }

}