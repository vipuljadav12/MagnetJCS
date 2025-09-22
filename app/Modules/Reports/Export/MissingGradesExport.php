<?php

namespace App\Modules\Reports\Export;

use Maatwebsite\Excel\Concerns\{Exportable, WithEvents, FromView, ShouldAutoSize};
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;

class MissingGradesExport implements FromView, ShouldAutoSize, WithEvents
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        $firstdata = $this->data['firstdata'];
        $subjects = $this->data['subjects'];
        $terms = $this->data['terms'];

        return view('Reports::export.missing_grade', compact('firstdata', 'subjects', 'terms'));
    }

    // public function collection()
    // {
    //     return $this->data;
    // }

    // public function headings(): array
    // {
    //     return $headings = [];
    // }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(25);

                $to = $event->sheet->getDelegate()->getHighestColumn();
                $toC = $event->sheet->getDelegate()->getHighestRow();

                // Bold style
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
                // dd($to);
                $sheet->getStyle('A1:' . $to . '3')->applyFromArray($styleArray);

                // Center element
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
                $event->sheet->getDelegate()->getStyle('A4:' . $to . $toC)->applyFromArray($styleArray);
            }
        ];
    }
}
