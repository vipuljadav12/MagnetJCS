<?php

namespace App\Modules\CustomCommunication\Export;

use Maatwebsite\Excel\Concerns\{Exportable, WithEvents, FromCollection, ShouldAutoSize, WithHeadings};
use Maatwebsite\Excel\Events\AfterSheet;

class CustomCommunicationEmails implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
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
            "Student Name",
            "Parent Name",
            "Parent Email",
            "Grade"
        ];
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
            }
        ];
    }
}
