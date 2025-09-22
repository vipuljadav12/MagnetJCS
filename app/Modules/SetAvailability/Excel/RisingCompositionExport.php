<?php


namespace App\Modules\SetAvailability\Excel;


use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RisingCompositionExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
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
        $headings = ['Program ID', 'Program Name', 'School Name', 'Black', 'Non Black'];
        if (isset($this->data['error'])) {
            array_push($headings, 'Error');
        }
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
