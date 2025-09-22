<?php
namespace App\Modules\EditCommunication\Excel;

use App\Modules\Fees\Models\{Fees,FeesPayment,FeesCartItem};
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Http\Request;
use Session;

class MailCommunicationExcel implements FromCollection, WithHeadings, ShouldAutoSize,WithEvents
{

    protected $data;
    public function __construct($data){
        $this->data = $data;
    }

    public function collection()
    {
        // dd(($this->data));
           $count=1;
        foreach($this->data as $key=>$value)
        {
           $count=$count+1;
           $data_single []=[
                           'District'  =>get_district_name($value[0]),
                           'Parent Name'  =>$value[1],
                           'Parent Email'  =>$value[2],
                           'Status'  =>$value[3]
                         ];
         
        }
       
        if(isset($data_single))
        $data_collection=collect($data_single);
        else
        $data_collection=collect([]);   
        
        return ($data_collection);

    }

    public function headings(): array
    {
        return $fees_array[] = array('District','Parent Name','Parent Email','Status');
    }
     public function registerEvents(): array
    {
       
        
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(20);
                $event->sheet->getParent()->getDefaultStyle()->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                    ]
                ]);
                $styleArray = [
                    'font' => [
                        'family' => 'Open Sans',
                        'size' =>  13,
                        'bold' => true,
                        ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    // 'borders' => [
                    //     'top' => [
                    //         'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    //     ],
                    // ],
                    'borders' => [
                        'outline' => [
                              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                              'color' => ['argb' => '#DCDCDC'],
                           ],
                     ],
                    'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startColor' => [
                        'argb' => 'dedede',
                    ],
                    'endColor' => [
                        'argb' => 'dedede',
                    ],
                ],
                ];

                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:J1')->applyFromArray($styleArray);
           
              
            }
        ];
    }
}