<?php

namespace App\Modules\Import\ImportFiles;

use App\Traits\AuditTrail;
use Maatwebsite\Excel\Row;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use App\{StudentData, Staff};
use App\Modules\Import\Models\GiftedStudents;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\ValidationException;

class GiftedStudentsImport implements ToModel, WithValidation, WithBatchInserts, WithHeadingRow, SkipsOnFailure
{
    use SkipsFailures, Importable, AuditTrail;
    public $invalidArr = array();

    public function __construct() {}

    public function rules(): array
    {
        return [];
    }
    public function customValidationMessages()
    {
        return [];
    }
    public function model(array $row)
    {
        $specialeducationstatus = 'Inactive';
        $statusArray = ['Active', 'Inactive', 'Referred'];
        if (isset($row['specialeducationstatus']))
            $specialeducationstatus = in_array($row['specialeducationstatus'], $statusArray) ? $row['specialeducationstatus'] : 'Inactive';


        /*$name = explode(",", $row['casemanager']);
            $firstname = trim($name[0]);
            $lastname = trim($name[1]);

            $data = Staff::where(function ($q) use ($firstname, $lastname){
                $q->where("first_name", $firstname)->orWhere("last_name", $lastname);
            })->orWhere(function ($q) use ($firstname, $lastname){
                $q->where("first_name", $lastname)->orWhere("last_name", $firstname);
            })->first();
            //print_r($data);exit;
            if(!empty($data))
            {*/
        $insert = array();
        $insert['stateID'] = $row['stateidnumber'];
        $insert['field_name'] = 'gifted_student';
        $insert['field_value'] = 3;
        StudentData::create($insert);

        $insert['stateID'] = $row['stateidnumber'];
        $insert['field_name'] = 'gifted_teacher_name';
        $insert['field_value'] = ""; //$firstname." ".$lastname;
        StudentData::create($insert);


        $insert['stateID'] = $row['stateidnumber'];
        $insert['field_name'] = 'gifted_teacher_email';
        $insert['field_value'] = ""; //$data->email;
        StudentData::create($insert);

        /* }
            else
            {
                $insert = array();
                $insert['stateID'] = $row['stateidnumber'];
                $insert['field_name'] = 'gifted_student';
                $insert['field_value'] = 3;
                StudentData::create($insert);
            }*/
        /*$data['current_enrollment_status'] = $row['currentenrollmentstatus'];
	        $data['state_id_number']           = $row['stateidnumber'];
	        $data['last_name']                 = $row['lastname'];
	        $data['first_name']                = $row['firstname'];
	        $data['gr']                        = $row['gr'];
	        $data['school']                    = $row['school'];
	        $data['primary_exceptionality']    = $row['primaryexceptionality'];
	        $data['case_manager']              = $row['casemanager'];
	        $data['special_education_status']  = $specialeducationstatus;
            $data['enrichment_student']        = $row['enrichmentstudent'];
	        $data['district_id']               = Session::get("district_id");
	        return new GiftedStudents($data);*/
        // /	    }

    }
    public function batchSize(): int
    {
        return  1;
    }
    public function headingRow(): int
    {

        return 1;
    }
}
