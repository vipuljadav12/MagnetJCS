<?php

namespace App\Modules\Reports\Export;

use Maatwebsite\Excel\Concerns\{Exportable, WithEvents, FromCollection, ShouldAutoSize, WithHeadings};
use Maatwebsite\Excel\Events\AfterSheet;

class MissingRecommendationExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $headings = [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(25);
                $styleArray = [
                    'font' => [
                        'family' => 'Open Sans',
                        'size' =>  12,
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ];
                $styleArray1 = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ]
                ];
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:Z1')->applyFromArray($styleArray);

                $styleArray = [
                    'font' => [
                        'family' => 'Open Sans',
                        'size' =>  12,
                        'bold' => false,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ];


                $to = $event->sheet->getDelegate()->getHighestColumn();
                $toC = $event->sheet->getDelegate()->getHighestRow();

                $event->sheet->getDelegate()->getStyle('A2:' . $to . $toC)->applyFromArray($styleArray);
            }
        ];
    }
}
