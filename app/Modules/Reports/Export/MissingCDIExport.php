<?php

namespace App\Modules\Reports\Export;

use Maatwebsite\Excel\Concerns\{Exportable, WithEvents, FromCollection, ShouldAutoSize, WithHeadings};
use Maatwebsite\Excel\Events\AfterSheet;

class MissingCDIExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
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
        /*	        "Submission ID",
			"Submission Status",
			"Race",
			"State ID",
			"Last Name",
			"First Name",
			"Current School",
			"Zoned School",
			"First Choice",
			"Second Choice",
			"Sibling ID",
            "Lottery Number",
			// "Device Name / SIM Number / IMEI Number / MSISDN",
		];*/
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
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,

                    ],
                ];
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:Q1')->applyFromArray($styleArray);

                $styleArray = [
                    'font' => [
                        'family' => 'Open Sans',
                        'size' =>  13,
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

                $styleArray = [
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => "ffff15"]
                    ]
                ];
                $event->sheet->getDelegate()->getStyle('J1:O1')->applyFromArray($styleArray);

                //                $sheet->getStyle('A2:Q100')->applyFromArray($styleArray);
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
