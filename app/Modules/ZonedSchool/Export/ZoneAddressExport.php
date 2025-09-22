<?php

namespace App\Modules\ZonedSchool\Export;

use Maatwebsite\Excel\Concerns\{Exportable, WithEvents, FromCollection, ShouldAutoSize, WithHeadings};
use Maatwebsite\Excel\Events\AfterSheet;

class ZoneAddressExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
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
                        'size' =>  13,
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ];
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:Z1')->applyFromArray($styleArray);

                $styleArray = [
                    'font' => [
                        'family' => 'Open Sans',
                        'size' =>  13,
                        'bold' => false,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ];
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('J2:Q1300')->applyFromArray($styleArray);

                /*$conditional1 = new Conditional();
                $conditional1->setConditionType(Conditional::CONDITION_CONTAINSTEXT);
                $conditional1->setOperatorType(Conditional::OPERATOR_EQUAL );
                $conditional1->setText("NA");
                $conditional1->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_RED);
                $conditional1->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getEndColor()->setARGB(Color::COLOR_RED);
                $conditionalStyles[] = $conditional1;
                $sheet->getStyle('J2:Z130000')->setConditionalStyles($conditional1);*/
            }
        ];
    }
}
