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
use App\Modules\Submissions\Models\{Submissions, SubmissionGrade, SubmissionConductDisciplinaryInfo};
use App\Traits\AuditTrail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CDIImport implements ToModel, WithValidation, WithBatchInserts, WithHeadingRow, SkipsOnFailure
{
  use SkipsFailures, Importable, AuditTrail;
  public $invalidArr = array();

  public function __construct() {}

  public function rules(): array
  {
    return [
      '*.submission_id' => 'required',
      /*                  '*.b_info' => 'rquired|numeric';
                  '*.c_info' => 'rquired|numeric';
                  '*.d_info' => 'rquired|numeric';
                  '*.e_info' => 'rquired|numeric';
                  '*.days_of_suspension' => 'rquired|numeric';*/

    ];
  }
  public function customValidationMessages()
  {
    return [];
  }
  public function model(array $row)
  {
    $courseType = Config::get('variables.courseType');
    $data = array();
    $data['submission_id'] = $row['submission_id'];
    $data['stateID'] = $row['state_id'];

    $import = true;
    $excludeArr = array("submission_id", "academic_year", "state_id", "submission_status", "last_name", "first_name", "next_grade", "current_school", "first_choice", "second_choice", "current_grade");

    $valid = true;
    foreach ($row as $key => $value) {

      if (!in_array($key, $excludeArr) && $key != "") {
        if (is_numeric($value) && $value < 100 && $value >= 0) {
          if ($key == "days_of_suspension")
            $data['susp_days'] = $value;
          else
            $data[$key] = $value;
        } else {
          $valid = false;
        }
      }
    }

    if ($valid) {
      $rs = DB::table("submission_conduct_discplinary_info")->where("submission_id", $data['submission_id'])->first();
      if (!empty($rs)) {
        $conduct_discplinary_info = SubmissionConductDisciplinaryInfo::where("submission_id", $data['submission_id'])->join("submissions", "submissions.id", "submission_conduct_discplinary_info.submission_id")->join("application", "application.id", "submissions.application_id")->select("submission_conduct_discplinary_info.*", "submissions.application_id", "application.enrollment_id")->first();

        DB::table("submission_conduct_discplinary_info")->where('id', $rs->id)->update($data);

        $newconduct_discplinary_info = SubmissionConductDisciplinaryInfo::where("submission_id", $data['submission_id'])->join("submissions", "submissions.id", "submission_conduct_discplinary_info.submission_id")->join("application", "application.id", "submissions.application_id")->select("submission_conduct_discplinary_info.*", "submissions.application_id", "application.enrollment_id")->first();

        $this->modelChanges($conduct_discplinary_info, $newconduct_discplinary_info, "Submission Mission Report - CDI");
      } else {
        DB::table("submission_conduct_discplinary_info")->insert($data);
        $app_data = SubmissionConductDisciplinaryInfo::where("submission_id", $data['submission_id'])->join("submissions", "submissions.id", "submission_conduct_discplinary_info.submission_id")->join("application", "application.id", "submissions.application_id")->select("submission_conduct_discplinary_info.*", "submissions.application_id", "application.enrollment_id")->first();
        $this->modelCDICreate($app_data, "Submission Mission Report - CDI");
      }
    } else {
      $this->invalidArr[] = $row;
    }
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
