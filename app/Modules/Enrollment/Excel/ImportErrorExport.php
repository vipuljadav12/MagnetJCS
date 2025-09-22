<?php


namespace App\Modules\Enrollment\Excel;


use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ImportErrorExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data['data'];
    }

    public function headings(): array
    {
        // dd($this->data['type'], 22);
        // $headings = [];
        // if (isset($this->data['data']) && (count($this->data['data']) > 0)) {
        //     $row = $this->data['data']->first();
        //     foreach($row as $key => $val) {
        //         $headings[] = ucwords(str_replace('_', ' ', $key));
        //     }
        // } else {
        $headings = ['School ID', 'School Name', 'Black', 'Non_Black'];
        // }
        return $headings;
    }
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }
}
