<?php

namespace App\Modules\Reports\Export;

use Maatwebsite\Excel\Concerns\{Exportable, WithEvents, FromView, ShouldAutoSize};
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;

class SubmissionExport implements FromView, ShouldAutoSize, WithEvents
{
    protected $data;
    protected $fields = [];
    protected $ts_field_ary = [];

    public function __construct($data, $fields, $ts_field_ary)
    {
        $this->data = $data;
        $this->fields = $fields;
        $this->ts_field_ary = $ts_field_ary;
    }

    public function view(): View
    {
        $submissions = $this->data;
        $fields = $this->fields;
        $ts_field_ary = $this->ts_field_ary;
        return view('Reports::submission_export_view', compact('submissions', 'fields', 'ts_field_ary'));
    }

    public function registerEvents(): array
    {
        if (count($this->ts_field_ary) > 0) {
            $head_count = '2';
        } else {
            $head_count = '1';
        }
        return [
            AfterSheet::class    => function (AfterSheet $event) use ($head_count) {
                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(25);
                $styleArray = [
                    'font' => [
                        'family' => 'Open Sans',
                        'size' =>  13,
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ];
                $to = $event->sheet->getDelegate()->getHighestColumn();
                // $toC = $event->sheet->getDelegate()->getHighestRow();
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:' . $to . $head_count)->applyFromArray($styleArray);
            }
        ];
    }
}
