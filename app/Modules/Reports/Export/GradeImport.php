<?php

namespace App\Modules\Reports\Export;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Validation\Rule;
use App\Modules\Reports\Export\MissingGradesExport;
use App\Modules\Submissions\Models\{Submissions, SubmissionGrade, SubmissionConductDisciplinaryInfo};
use App\Traits\AuditTrail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class GradeImport implements ToModel, WithValidation, WithBatchInserts, WithHeadingRow, SkipsOnFailure
{
  use SkipsFailures, Importable, AuditTrail;
  public $invalidArr = array();
  public $termArr = array("11" => "1.1", "12" => "1.2", "13" => "1.3", "14" => "1.4", "21" => "2.1", "22" => "2.2", "23" => "2.3", "24" => "2.4", "31" => "3.1", "32" => "3.2", "33" => "3.3", "34" => "3.4", "41" => "4.1", "42" => "4.2", "43" => "4.3", "44" => "4.4");
  public function __construct() {}

  public function rules(): array
  {
    return [
      '*.submission_id' => 'required',
    ];
  }
  public function customValidationMessages()
  {
    return [];
  }
  public function model(array $row)
  {
    $courseType = Config::get('variables.courseType');
    $academic_year = Config::get("variables.import_academic_year");
    $data = array();
    $data['submission_id'] = $row['submission_id'];
    $data['stateID'] = $row['state_id'];

    $import = true;
    $excludeArr = array("submission_id", "academic_year", "state_id", "submission_status", "last_name", "first_name", "next_grade", "current_school", "first_choice", "second_choice", "current_grade");

    $submission_grade = SubmissionGrade::where("submission_id", $data['submission_id'])->join("submissions", "submissions.id", "submission_grade.submission_id")->join("application", "application.id", "submissions.application_id")->select("submission_grade.*", "submissions.application_id", "application.enrollment_id")->get();
    $current_grade = array();
    $new_grade = array();
    if (isset($submission_grade)) {
      foreach ($submission_grade as $key => $value) {
        $tmp = array();
        $tmp['submission_id'] = $value->submission_id;
        $tmp['application_id'] = $value->application_id;
        $tmp['enrollment_id'] = $value->enrollment_id;
        $tmp['academicYear'] = $value->academicYear;
        $tmp['academicTerm'] = $value->academicTerm;
        $tmp['GradeName'] = $value->GradeName;
        $tmp['courseTypeID'] = $value->courseTypeID;
        $tmp['numericGrade'] = $value->numericGrade;
        $tmp['courseName'] = $value->courseName;
        $current_grade[] = $tmp;
      }
    }
    $valid = true;
    foreach ($row as $key => $value) {

      if (!in_array($key, $excludeArr)) {
        if (is_numeric($value) && $value >= 0) {
          $tmp = explode("_", $key);
          $tmpkey = $key;
          $tmp1 = $tmp[0];
          if ($tmp1 == "social") {
            $tmp1 = "Social Studies";
            $tmpkey = str_replace("social_studies_", "", $tmpkey);
          } else {
            $tmpkey = str_replace($tmp1 . "_", "", $tmpkey);
          }
          $tmpkey = ucwords(str_replace("_", " ", $tmpkey));
          $tmpkey = str_replace(array_keys($this->termArr), array_values($this->termArr), $tmpkey);
          $data['GradeName'] = $tmpkey;
          $data['academicTerm'] = $tmpkey;
          $data['courseTypeID'] = findArrayKey($courseType, ucwords($tmp1));
          $data['courseType'] = ucwords($tmp1);
          $data['courseName'] = ucwords($tmp1);
          $data['numericGrade'] = $value;
          $data['academicYear'] = $academic_year;
          //print_r($data);exit;
          if ($data['numericGrade'] != "NA") {

            $rs = DB::table("submission_grade")->where("submission_id", $data['submission_id'])->where('GradeName', $data['GradeName'])->where('courseTypeID', $data['courseTypeID'])->where('academicYear', $data['academicYear'])->first();
            if (!empty($rs)) {
              DB::table("submission_grade")->where('id', $rs->id)->update(["numericGrade" => $data['numericGrade']]);
            } else {
              DB::table("submission_grade")->insert($data);
            }

            $initSubmission = Submissions::where('submissions.id', $data['submission_id'])->join("application", "application.id", "submissions.application_id")->select("submissions.*", "application.enrollment_id")->first();
            $data['enrollment_id'] = $initSubmission->enrollment_id;
            $data['application_id'] = $initSubmission->application_id;
            $data['academicTerm'] = $tmpkey;
            $new_grade[] = $data;


            unset($data['enrollment_id']);
            unset($data['application_id']);
            //unset($data['academicTerm']);
          }
        } elseif ($value != "NA") {
          $valid = false;
        }
      }
    }


    if (!$valid) {
      $this->invalidArr[] = $row;
    }
    $this->modelGradeChanges($current_grade, $new_grade, "Submission Academic Grade Import");
  }
  /**
   * @inheritDoc
   */
  public function batchSize(): int
  {
    // TODO: Implement batchSize() method.
    return  1;
  }

  /**
   * @inheritDoc
   */
  /*public function onFailure(Failure ...$failures)
    {
    }*/
  public function headingRow(): int
  {

    return 1;
  }
}
