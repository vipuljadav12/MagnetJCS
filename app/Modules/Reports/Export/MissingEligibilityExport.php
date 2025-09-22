<?php

namespace App\Modules\Reports\Export;

use Maatwebsite\Excel\Concerns\{Exportable, WithEvents, FromView, ShouldAutoSize};
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;

class MissingEligibilityExport implements FromView, ShouldAutoSize, WithEvents
{
    protected $data = [];
    protected $ts_name_fields = [];
    protected $fields = [];
    // protected $ts_count = 0;

    public function __construct($data)
    {
        $this->data = $data['data'];
        $this->fields = $data['fields'];
        // dd($data);
        $this->ts_name_fields = $data['ts_name_fields'];
    }

    public function view(): View
    {
        $data = $this->data;
        $fields = $this->fields;
        $ts_name_fields = $this->ts_name_fields;
        return view('Reports::export/eligibility', compact('data', 'fields', 'ts_name_fields'));
    }

    public function registerEvents(): array
    {
        $tsCnt = count($this->ts_name_fields);
        if ($tsCnt > 0) {
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
