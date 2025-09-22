<?php

namespace App\Modules\Reports\Export;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;
use App\Modules\Reports\Export\MissingGradesExport;
use App\Modules\Submissions\Models\{Submissions, SubmissionCommitteeScore};
use App\Modules\Program\Models\Program;
use App\Traits\AuditTrail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CommitteeScoreImport implements ToCollection, WithBatchInserts, WithHeadingRow
{
  use Importable, AuditTrail;
  public $invalidArr = array();

  public function getProgramID($name)
  {
    $program = Program::where('name', $name)->first();
    if (isset($program)) {
      return $program->id;
    }
    return 0;
  }

  public function getProgramChoiceID($id, $choice)
  {
    $str = $choice . "_choice_program_id";
    $program = Submissions::where("id", $id)->first();
    if (isset($program)) {
      return $program->{$str};
    }
    return 0;
  }

  public function collection(Collection $rows)
  {
    if (count($rows) > 0) {
      foreach ($rows as $key => $row) {
        $err = [];
        // submission_id
        if ($row['submission_id'] != '') {
          $submission = Submissions::where('id', $row['submission_id'])->first();
          if (!isset($submission)) {
            $err[] = 'Invalid Submission ID';
          }
        } else {
          $err[] = 'Submission ID is required';
        }
        // program_name
        if ($row['program_name'] != '') {
          $program_id = $this->getProgramChoiceID($row['submission_id'], strtolower($row['program_name']));
          if ($program_id == 0) {
            $err[] = 'Invalid Program Name';
          }
        } else {
          $err[] = 'Program Name is required';
        }

        // score
        if ($row['score'] != '' || $row['score'] == '0') {
          if (!is_numeric($row['score']) || ($row['score'] > 100 || $row['score'] < 0)) {
            $err[] = 'Score value allowed between 0-100';
          }
        } else {
          $err[] = 'Score value is required';
        }

        $err_count = count($err);
        if ($err_count > 0) {
          $err_str = '';
          foreach ($err as $key => $value) {
            $err_str .= $value;
            if (($err_count - 1) != $key) {
              $err_str .= ' | ';
            }
          }
          $row['error'] = $err_str;
          $this->invalidArr[] = $row;
        } else {
          $oldObj = SubmissionCommitteeScore::where("submission_id", $row['submission_id'])->join("submissions", "submissions.id", "submission_committee_score.submission_id")->join("application", "application.id", "submissions.application_id")->where("submission_committee_score.program_id", $program_id)->select("submission_committee_score.*", "submissions.application_id", "application.enrollment_id")->first();

          SubmissionCommitteeScore::updateOrCreate(
            [
              'submission_id' => $row['submission_id'],
              'program_id' => $program_id
            ],
            ['data' => $row['score']]
          );

          $newObj = SubmissionCommitteeScore::where("submission_id", $row['submission_id'])->join("submissions", "submissions.id", "submission_committee_score.submission_id")->join("application", "application.id", "submissions.application_id")->where("submission_committee_score.program_id", $program_id)->select("submission_committee_score.*", "submissions.application_id", "application.enrollment_id")->first();
          // dd($newObj);
          if (!empty($oldObj))
            $this->modelChanges($oldObj, $newObj, "Submission - Committee Score");
          else
            $this->modelCreate($newObj, "Submission - Committee Score");
        }
      }
    } else {
      Session::flash('warning', 'No rows present.');
    }
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
