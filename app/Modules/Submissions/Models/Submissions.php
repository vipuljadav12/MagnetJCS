<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Session;
use App\Modules\Form\Models\Form;
use App\Modules\Program\Models\Program;
use App\Modules\Submissions\Models\{SubmissionGrade};
use DB;

class Submissions extends Model {

    //
    protected $table='submissions';
    public $additional = ['enrollment_id', 'application_id'];
    public $date_fields = ['birthday'];
    public $primaryKey='id';
    public $createField  = ['student_id', 'first_name', 'last_name', 'current_school','birthday', 'current_grade', 'next_grade', 'first_choice', 'second_choice', 'third_choice'];
    
    public $fillable=[
    	'student_id',
        'district_id',
        'application_id',
    	'state_id',
    	'application_id',
        'enrollment_id',
        'form_id',
    	'first_name',
    	'last_name',
    	'race',
        'calculated_race',
    	'birthday',
    	'address',
    	'city',
    	'state',
    	'zip',
    	'current_school',
    	'current_grade',
    	'next_grade',
    	'non_hsv_student',
    	'special_accommodations',
    	'parent_first_name',
    	'parent_last_name',
        'writing_prompt_email',
        'audition_email',
    	'parent_email',
    	'emergency_contact',
    	'emergency_contact_phone',
    	'emergency_contact_relationship',
    	'phone_number',
    	'alternate_number',
    	'zoned_school',
        'awarded_school',
    	'lottery_number',
    	'first_choice',
        'second_choice',
    	'third_choice',
    	'open_enrollment',
    	'submission_status',
        "confirmation_no",
        "employee_id",
        "work_location",
        "employee_first_name",
        "employee_last_name",
        "mcp_employee",
        "gifted_student",
        'first_choice_program_id',
        'second_choice_program_id',
        'third_choice_program_id',
        'grade_upload_confirmed',
        'grade_upload_confirmed_by',
        'grade_upload_confirmed_at',
        'cdi_upload_confirmed',
        'cdi_upload_confirmed_by',
        'cdi_upload_confirmed_at',
        'gifted_verification_status',
        'gifted_verification_status_by',
        'gifted_verification_status_at',
        'first_sibling',
        'second_sibling',
        'third_sibling',
        'override_student',
        'grade_exists',
        'cdi_override',
        'additional_questions',
        'grade_override',
        'late_submission',
        'submitted_by',
        'email_body',
        'email_subject',
        'mcpss_verification_status',
        'magnet_program_employee',
        'magnet_program_employee_by',
        'magnet_program_employee_at',
        'mcpss_verification_status_by',
        'mcpss_verification_status_at',
        'manual_grade_change',
        'gender',
        'letter_body',
        'sp_exception',
        'is_504_eligible'


    ];


    public function scopegetSearhData(){
        $ids = array('"PreK"', '"K"', '"1"', '"2"', '"3"', '"4"', '"5"', '"6"', '"7"', '"8"', '"9"', '"10"', '"11"', '"12"');

        $programs = Program::where("district_id", Session::get('district_id'))->where('enrollment_id', Session::get('enrollment_id'))->where('status', 'Y')->get();

        $send_data = array();
       $first_programs=Submissions::
        join('program','program.id','submissions.first_choice_program_id')
        ->where('submissions.district_id', Session::get('district_id'))
        ->where('submissions.enrollment_id', Session::get('enrollment_id'));
        $first_programs = $first_programs->select('program.name', 'program.id');
        $first_programs = $first_programs->distinct('program.name');
        $first_programs = $first_programs->get();


        $second_programs=Submissions::
        join('program','program.id','submissions.second_choice_program_id')
        ->where('submissions.district_id', Session::get('district_id'))
        ->where('submissions.enrollment_id', Session::get('enrollment_id'));
        $second_programs = $second_programs->select('program.name', 'program.id');
        $second_programs = $second_programs->distinct('program.name');
        $second_programs = $second_programs->get();

        $third_programs=Submissions::
        join('program','program.id','submissions.third_choice_program_id')
        ->where('submissions.district_id', Session::get('district_id'))
        ->where('submissions.enrollment_id', Session::get('enrollment_id'));
        $third_programs = $third_programs->select('program.name', 'program.id');
        $third_programs = $third_programs->distinct('program.name');
        $third_programs = $third_programs->get();


        $enroll_year=Submissions::
        join('application','application.id','submissions.application_id')
        ->join('enrollments','enrollments.id','application.enrollment_id')
        ->where('submissions.district_id', Session::get('district_id'))
        ->where('submissions.enrollment_id', Session::get('enrollment_id'));
        $enroll_year = $enroll_year->select('enrollments.school_year');
        $enroll_year = $enroll_year->distinct('enrollments.school_year');
        $enroll_year = $enroll_year->get();

        $race=Submissions::
        join('application','application.id','submissions.application_id')
        ->join('enrollments','enrollments.id','application.enrollment_id')
        ->where('submissions.district_id', Session::get('district_id'))
        ->where('submissions.enrollment_id', Session::get('enrollment_id'));
        $race = $race->select('submissions.race');
        $race = $race->distinct('submissions.race');
        $race = $race->get();


        $forms=Submissions::
        join('form','form.id','submissions.form_id')
        ->where('submissions.district_id', Session::get('district_id'))
        ->where('submissions.enrollment_id', Session::get('enrollment_id'));
        
        $forms = $forms->select('submissions.form_id');
        $forms = $forms->distinct('submissions.form_id');
        $forms = $forms->get();

        $gender=Submissions::where('submissions.district_id', Session::get('district_id'))
        ->where('submissions.enrollment_id', Session::get('enrollment_id'));
        
        $gender = $gender->select('submissions.gender');
        $gender = $gender->distinct('submissions.gender');
        $gender = $gender->get();

        $current_school=Submissions::
        join('application','application.id','submissions.application_id')
        ->join('enrollments','enrollments.id','application.enrollment_id')
        ->where('submissions.district_id', Session::get('district_id'))
        ->where('submissions.enrollment_id', Session::get('enrollment_id'));
        $current_school = $current_school->select('submissions.current_school');
        $current_school = $current_school->distinct('submissions.current_school');
        $current_school = $current_school->get();

        $current_grade=Submissions::
        join('application','application.id','submissions.application_id')
        ->join('enrollments','enrollments.id','application.enrollment_id')
        ->where('submissions.district_id', Session::get('district_id'))
        ->where('submissions.enrollment_id', Session::get('enrollment_id'));
        $current_grade = $current_grade->select('submissions.current_grade');
        $current_grade = $current_grade->distinct('submissions.current_grade');
        $current_grade = $current_grade->orderByRaw('FIELD(current_grade,'.implode(",",$ids).')')->get();

        $next_grade=Submissions::
        join('application','application.id','submissions.application_id')
        ->join('enrollments','enrollments.id','application.enrollment_id')
        ->where('submissions.district_id', Session::get('district_id'))
        ->where('submissions.enrollment_id', Session::get('enrollment_id'));
        $next_grade = $next_grade->select('submissions.next_grade');
        $next_grade = $next_grade->distinct('submissions.next_grade');
        $next_grade = $next_grade->orderByRaw('FIELD(next_grade,'.implode(",",$ids).')')->get();

        $submission_status=Submissions::
        join('application','application.id','submissions.application_id')
        ->join('enrollments','enrollments.id','application.enrollment_id')
        ->where('submissions.district_id', Session::get('district_id'))
        ->where('submissions.enrollment_id', Session::get('enrollment_id'));
        $submission_status = $submission_status->select('submissions.submission_status');
        $submission_status = $submission_status->distinct('submissions.submission_status');
        $submission_status = $submission_status->get();

        $zoned_school=Submissions::
        join('application','application.id','submissions.application_id')
        ->join('enrollments','enrollments.id','application.enrollment_id')
        ->where('submissions.district_id', Session::get('district_id'))
        ->where('submissions.enrollment_id', Session::get('enrollment_id'));
        $zoned_school = $zoned_school->select('submissions.zoned_school');
        $zoned_school = $zoned_school->distinct('submissions.zoned_school');
        $zoned_school = $zoned_school->orderBy('zoned_school')->get();  

        $send_data['enroll_year'] = $enroll_year;
        $send_data['race'] = $race;
        $send_data['current_school'] = $current_school;
        $send_data['current_grade'] = $current_grade;
        $send_data['next_grade'] = $next_grade;
        $send_data['submission_status'] = $submission_status;
        $send_data['zoned_school'] = $zoned_school;
        $send_data['forms'] = $forms;
        $send_data['first_programs'] = $first_programs;
        $send_data['second_programs'] = $second_programs;
        $send_data['third_programs'] = $third_programs;
        $send_data['awarded_school'] = $programs;
        $send_data['gender'] = $gender;

        //echo "<pre>===";print_r($zoned_school->toArray());exit;
        return $send_data;
    }

    public function scopegetSubmissionList($query,$all,$flag){
        
        
        
        $query->join('application','application.id','submissions.application_id')
                ->join('enrollments','enrollments.id','application.enrollment_id')
                ->where('submissions.district_id', Session::get('district_id'))
                ->where('submissions.enrollment_id', Session::get('enrollment_id'));
        if($all['enroll_yr']!=''){
            $query->where('enrollments.school_year',$all['enroll_yr'] );
        }
         if($all['form']!=''){
            $query->where('submissions.form_id',$all['form'] );
        }
        if($all['first_choice_program']!=''){
            $query->where('first_choice_program_id',$all['first_choice_program'] );
        }
        if($all['second_choice_program']!=''){
            $query->where('second_choice_program_id',$all['second_choice_program'] );
        }
        if($all['third_choice_program']!=''){
            $query->where('third_choice_program_id',$all['third_choice_program'] );
        }
        if($all['gender']!=''){
            $query->where('gender',$all['gender'] );
        }

        if($all['sel_race']!=''){
            $query->where('submissions.race',$all['sel_race'] );
        }
        if($all['curr_school']!=''){
            $query->where('submissions.current_school',$all['curr_school'] );
        }
        if($all['curr_grade']!=''){
            $query->where('submissions.current_grade',$all['curr_grade']);
        }
        if($all['next_grade']!=''){
            $query->where('submissions.next_grade',$all['next_grade']);
        }
        if($all['app_status']!=''){
            $query->where('submissions.submission_status',$all['app_status']);
        }
        if($all['zoned_school']!=''){
            $query->where('submissions.zoned_school',$all['zoned_school']);
        }
        if($all['awarded_school']!=''){
            $query->where('submissions.awarded_school',$all['awarded_school']);
        }
        if($all['late_submission']!=''){
            $query->where('submissions.late_submission',$all['late_submission']);
        }
        if($all['contract_status']!=''){
            $ids1 = DB::table("submissions_final_status")->where(function($q){
                $q->where("submissions_final_status.first_offer_status", 'Accepted')->orWhere("submissions_final_status.second_offer_status", 'Accepted');
            })->where('contract_status',$all['contract_status'])->select("submission_id")->get()->toArray();
            $ids2 = DB::table("submissions_waitlist_final_status")->where(function($q){
                $q->where("submissions_waitlist_final_status.first_offer_status", 'Accepted')->orWhere("submissions_waitlist_final_status.second_offer_status", 'Accepted');
            })->where('contract_status',$all['contract_status'])->select("submission_id")->get()->toArray();

            $ids = array();
            foreach($ids1 as $key=>$value)
            {
                $ids[] = $value->submission_id;
            }
            foreach($ids2 as $key=>$value)
            {
                $ids[] = $value->submission_id;
            }

            $query->whereIn("submissions.id", $ids)->where("submission_status", "Offered and Accepted");
            
            /*$query->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")->where(function($q){
                $q->where("submissions_final_status.first_offer_status", 'Accepted')->orWhere("submissions_final_status.second_offer_status", 'Accepted');
            })->where('contract_status',$all['contract_status']);
            echo $query->toSql();exit;*/
        }
        if($all['stu_type']!=''){
            if($all['stu_type']=='current'){
                $query->whereNotNull('submissions.student_id');
            }else{
                $query->whereNull('submissions.student_id');
            }
            
        }

        if($all['search']['value']!=''){
            $search = $all['search']['value'];
            $query->Where('submissions.submission_status','like',$search.'%')->orWhere('enrollments.school_year','like',$search.'%')->orWhere('submissions.first_name','like',$search.'%')->orWhere('submissions.last_name','like',$search.'%')->orWhere('submissions.current_school','like',$search.'%')->orWhere('submissions.current_grade','like',$search.'%')->orWhere('submissions.next_grade','like',$search.'%')->orWhere('submissions.student_id','like',$search.'%')->orWhere('submissions.id',$search);
        }
        $query->select('submissions.*','enrollments.school_year');

        if($flag==1){
            
                return $query->orderBy('created_at','desc')->skip($all['start'])->take($all['length'])->get();
            

        }else{
             return $query->orderBy('created_at','asc')->count();
        }
    }

    public function getSubmissionGrade()
    {
        return $this->hasMany(SubmissionGrade::class, 'submission_id');
    }
}
