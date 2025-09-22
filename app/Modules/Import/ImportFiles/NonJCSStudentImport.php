<?php

namespace App\Modules\Import\ImportFiles;

use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
// use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Modules\Import\Models\NonJCSStudent;

class NonJCSStudentImport implements ToCollection,WithBatchInserts,WithHeadingRow,SkipsOnFailure
{
    use Importable, SkipsFailures;
    protected $allErrors=[];

    public function collection(Collection $collection)
    {
            $insdata = [];
            $district_id = session('district_id');
            $enrollment_id = session('enrollment_id');
            foreach ($collection as $value) {
                unset($value['']);
                $error = [];
                if ($value['ssid'] == '') {
                    $error[] = 'Student ID is requred.';
                } else {
                    /*$exist_student = NonJCSStudent::where('ssid', $value['ssid'])
                        ->where('district_id', $district_id)
                        ->where('enrollment_id', $enrollment_id)
                        ->first();
                    if (isset($exist_student)) {
                        $error[] = 'Student already present.';
                    }*/
                }
                if ($value['firstname'] == '') {
                    $error[] = 'First Name is requred.';
                }
                if ($value['lastname'] == '') {
                    $error[] = 'Last Name is requred.';
                }
                if ($value['date_of_birth'] == '') {
                    $error[] = 'Date of Birth is requred.';
                }
                if ($value['current_school'] == '') {
                    $error[] = 'Curent School is requred.';
                }
                if ($value['current_grade'] == '') {
                    $error[] = 'Current Grade is requred.';
                }
                if ($value['type_of_transfer'] == '') {
                    $error[] = 'Type of Transfer is requred.';
                }
                if ($value['approved_zoned_school'] == '') {
                    $error[] = 'Approved Zoned School is requred.';
                }
                if (empty($error)) {
                    $tmpdata['district_id'] = $district_id;
                    $tmpdata['enrollment_id'] = $enrollment_id;
                    $tmpdata['ssid'] = $value['ssid'];
                    $tmpdata['first_name'] = $value['firstname'];
                    $tmpdata['last_name'] = $value['lastname'];
                    $tmpdata['birthday'] = date('Y-m-d', strtotime($value['date_of_birth']));
                    $tmpdata['current_school'] = $value['current_school'];
                    $tmpdata['current_grade'] = $value['current_grade'];
                    $tmpdata['transfer_type'] = $value['type_of_transfer'];
                    $tmpdata['approved_zoned_school'] = $value['approved_zoned_school'];
                    $tmpdata['created_at'] = date('Y-m-d H:i:s');
                    $tmpdata['updated_at'] = date('Y-m-d H:i:s');
                    $rs = NonJCSStudent::updateOrCreate(["ssid"=>$value['ssid']], $tmpdata);
//                    $insdata[] = $tmpdata;
                } else {
                    $value['errors'] = implode(' | ', $error);
                    $this->allErrors[]=$value;    
                }
            }
            if (!empty($insdata)) {
               // NonJCSStudent::insert($insdata);
            }
    }

    public function batchSize(): int
    {
        return 1;
    }

    public function errors()
    {
        return $this->allErrors;
    }
}
