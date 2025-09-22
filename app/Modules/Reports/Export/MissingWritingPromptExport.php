<?php

namespace App\Modules\Reports\Export;

use Maatwebsite\Excel\Concerns\{Exportable, WithEvents, FromCollection, ShouldAutoSize, WithHeadings};
use Maatwebsite\Excel\Events\AfterSheet;

class MissingWritingPromptExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
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
        return $headings = [
            "Submission ID",
            "Created At",
            "Submission Status",
            "State ID",
            "Last Name",
            "First Name",
            "Next Grade",
            "Current School",
            "Email Address",
            "Missing First Choice Writing Sample",
            "Missing Second Choice Writing Sample"
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(25);
                $to = $event->sheet->getDelegate()->getHighestColumn();
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
                $sheet->getStyle('A1:' . $to . '1')->applyFromArray($styleArray);
            }
        ];
    }
}
