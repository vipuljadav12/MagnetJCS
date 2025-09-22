<?php

namespace App\Modules\Reports\Export;

use Maatwebsite\Excel\Concerns\{Exportable, WithEvents, FromView, ShouldAutoSize};
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;

class CourtReportExport implements FromView, ShouldAutoSize, WithEvents
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

        $court_data = $this->data['court_data'];
        $Black = $this->data['Black'];
        $White = $this->data['White'];
        $Other = $this->data['Other'];
        $race_ary = $this->data['race_ary'];

        return view('Reports::court_report', compact("court_data", "Black", "White", "Other", "race_ary"));
    }

    public function registerEvents(): array
    {

        $head_count = '2';
        $total_row = count($this->data['court_data']) + 5;
        return [
            AfterSheet::class    => function (AfterSheet $event) use ($head_count, $total_row) {
                //$event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(25);
                $styleArray1 = [
                    'alignment' => [
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wraptext' => true
                    ],
                ];

                $total_application_row = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,

                    ],
                    'font' => [
                        'family' => 'Open Sans',
                        'size' =>  11,
                        'bold' => true,
                    ],
                    'background' => [
                        'color' => '#CCCCCC'
                    ]
                ];

                $aligh_right = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'font' => [
                        'family' => 'Open Sans',
                        'size' =>  11,
                        'bold' => true,
                    ]
                ];

                $align_center = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wraptext' => true
                    ],

                ];


                $styleArray = [
                    'font' => [
                        'family' => 'Open Sans',
                        'size' =>  11,
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wraptext' => true
                    ],
                ];
                $to = $event->sheet->getDelegate()->getHighestColumn();
                // $toC = $event->sheet->getDelegate()->getHighestRow();
                $sheet = $event->sheet->getDelegate();
                // $sheet->setAutoSize(true);              
                $sheet->getStyle('A2:' . $to . $head_count)->applyFromArray($styleArray);
                $sheet->getStyle('A1:' . $to . $head_count)->applyFromArray($styleArray1);
                $sheet->getStyle('A' . $total_row . ':D' . ($total_row + 1))->applyFromArray($total_application_row);
                $sheet->getStyle('A' . ($total_row + 2) . ':A' . ($total_row + 2))->applyFromArray($aligh_right);
                $sheet->getStyle('B' . ($total_row + 2) . ':D' . ($total_row + 2))->applyFromArray($align_center);
                $sheet->getRowDimension(2)->setRowHeight(60);
                $sheet->getStyle('B' . ($total_row + 3) . ':D' . ($total_row + 3))->applyFromArray($align_center);
                $sheet->getStyle('B4:V' . ($total_row + 3))->applyFromArray($align_center);
            }
        ];
    }
}
