<?php

namespace App\Modules\Reports\Export;

use Maatwebsite\Excel\Concerns\{Exportable,WithEvents,FromCollection,ShouldAutoSize,WithHeadings};
use Maatwebsite\Excel\Events\AfterSheet;

class MissingCommitteeScoreErrorExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data['data'];
        $this->headings = $data['headings'];
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        $headings = [];
        foreach ($this->headings as $value) {
            $headings[] = ucwords(str_replace('_', ' ', $value));
        }
        if (!in_array('Error', $headings)) {
            $headings[] = 'Error';
        }
        return $headings;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
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
                $sheet->getStyle('A1:'.$to.'1')->applyFromArray($styleArray);
            }
        ];
    }

}
