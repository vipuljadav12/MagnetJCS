<?php
namespace App\Modules\Import\ImportFiles;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use App\Modules\Program\Models\Program;
use App\Modules\Application\Models\ApplicationProgram;
use App\Modules\Submissions\Models\Submissions;
use App\Modules\Form\Models\Form;
use Illuminate\Support\Facades\DB;

class SubmissionsImport implements ToModel,WithValidation,WithBatchInserts,WithHeadingRow,SkipsOnFailure{
  use SkipsFailures,Importable;
  // use SkipsFailures,Importable, AuditTrail;
  public $invalidArr = array();
  public $fields = [];
  	public function __construct($fields_ary=[]){
        $this->fields = $fields_ary;
    }

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
    //  dd($row);
        $error = [];
        $fields_ary = $this->fields;
        $not_required = [
            'awarded_school',
            'student_state_id',
            'second_choice_program',
            'third_choice_program',
            'first_sibling',
            'second_sibling',
            'third_sibling',
            'sp_exception',
            'is_504_eligible',
        ];
        foreach ($fields_ary as $field => $fprops) {
            $db_field = (isset($fprops['db_field']) ? $fprops['db_field'] : $field);
            if (isset($row[$field]) && ($row[$field]!='')) {
                /*if (in_array($field, ['current_grade', 'next_grade'])) {
                    $data[$db_field] = ($grades->where('name', $row[$field])->first()->id ?? NULL);
                }*/ 
                if (in_array($field, ['first_choice_program', 'second_choice_program', 'third_choice_program'])) {
                    $program = Program::where('name', $row[$field])
                        ->where('enrollment_id', session('enrollment_id'))
                        ->where('district_id', session('district_id'))
                        ->first();
                    if (isset($program)) {
                        $data[$db_field] = $program->id;
                    } else {
                        $error[] = $fields_ary[$field]['title'].' is not valid.';
                    }
                } else if ($field == 'birthday') {
                    $row[$field] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[$field])->format('Y-m-d');
                    $data[$db_field] = date('Y-m-d', strtotime($row[$field]));
                } else {
                    $data[$db_field] = $row[$field];
                }
            } else if(!in_array($field, $not_required)) {
                $error[] = $fields_ary[$field]['title'].' is required.';
            }
        }
        $data['enrollment_id'] = session('enrollment_id');
        $data['district_id'] = session('district_id');
        $data['form_id'] = 1;
        $data['application_id'] = 1;
        //dd($data);
        // Check if already present
        if (isset($data['student_id']) && ($data['student_id']!='')) {
            $submission = Submissions::where('student_id', $data['student_id'])
                ->where('enrollment_id', $data['enrollment_id'])
                ->where('district_id', $data['district_id'])
                ->first();
        } else {
            $submission = Submissions::where('first_name', $row['first_name'])
                ->where('last_name', $row['last_name'])
                ->where('parent_email', $row['parent_email'])
                ->where('enrollment_id', $data['enrollment_id'])
                ->where('district_id', $data['district_id'])
                ->first();
        }
        //dd($submission);
        if (isset($submission)) {
            $error[] = 'Submission already present'; 
        }
        // Create if empty
        if (empty($error)) {
            if (isset($data['first_choice_program_id'])) {
                $data['first_choice'] = $this->getChoiceID($data, 'first');
            }
            if (isset($data['second_choice_program_id'])) {
                $data['second_choice'] = $this->getChoiceID($data, 'second');
            }
            if (isset($data['third_choice_program_id'])) {
                $data['third_choice'] = $this->getChoiceID($data, 'third');
            }
           // dd($data);
            $submission = Submissions::create($data);
            
            if (isset($submission)) {
                $form_details = Form::where("id", $data['form_id'])->first();
                if(!empty($form_details)) {
                    $confirmtion_style = $form_details->confirmation_style;
                } else {
                    $confirmtion_style = "MAGNET";
                }
                $confirmation_no = $confirmtion_style."-".getEnrolmentConfirmationStyle($data['application_id'])."-".str_pad($submission->id, 4, "0", STR_PAD_LEFT);
                Submissions::where('id', $submission->id)->update(['confirmation_no'=>$confirmation_no, "lottery_number"=>generate_lottery_number()]);
            }
        } else {
            unset($row['']);
            $row['error'] = implode('|', $error);
            $this->invalidArr[] = $row;
        }
    }

    public function getChoiceID($data, $choice='') {
        $next_grade = (DB::table('grade')->where('name', $data['next_grade'])->first()->id ?? NULL);
        $ap = ApplicationProgram::where('application_id', $data['application_id'])
            ->where('grade_id', $next_grade)
            ->where('program_id', $data[$choice.'_choice_program_id'])
            ->first();
        return ($ap->id ?? NULL);
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