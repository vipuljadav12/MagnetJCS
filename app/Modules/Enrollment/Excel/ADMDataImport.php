<?php

namespace App\Modules\Enrollment\Excel;

use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Modules\Enrollment\Models\ADMData;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\School\Models\School;
use Illuminate\Support\Facades\Session;

class ADMDataImport implements ToCollection, WithBatchInserts, WithHeadingRow, SkipsOnFailure
{
  use Importable, SkipsFailures;

  protected $allErrors = [];

  public function collection(Collection $rows)
  {
    foreach ($rows as $key => $row) {
      $errors = [];
      $ins = array();

      if (isset($row['school_id']) && ($row['school_id'] != '')) {
        $school = School::where('id', $row['school_id'])
          ->where('district_id', Session::get("district_id"))
          ->first();
        if (!isset($school)) {
          $errors[] = "Entered School ID is not valid.";
        }
      } else {
        $errors[] = "School ID is required.";
      }
      $ins['black'] = $row['black'] ?? NULL;
      $ins['non_black'] = $row['non_black'] ?? NULL;

      // if($ins['black'] || $ins['non-black']) {
      if (!isset($ins['black'])) {
        $errors[] = "Enter value for Black race.";
      } elseif (!is_numeric($ins['black'])) {
        $errors[] = "Enter valid value for Black race.";
      }
      if (!isset($ins['non_black'])) {
        $errors[] = "Enter value for Non-Black race.";
      } elseif (!is_numeric($ins['non_black'])) {
        $errors[] = "Enter valid value for Non-Black race.";
      }
      if (empty($errors)) {
        $ins['majority_race'] = ($ins['black'] > 50) ? 'black' : (($ins['non_black'] > 50) ? 'non_black' : '');
        $ins['school_id'] = $school->id;
        $ins['enrollment_id'] = Session::get('upADM_Enroll_id');
        $key_data = [
          'school_id' => $school->id,
          'enrollment_id' => Session::get('upADM_Enroll_id')
        ];
        $result = ADMData::updateOrCreate($key_data, $ins);
      } else {
        unset($row['']);
        $row['errors'] = $errors;
        $this->allErrors[] = $row;
      }
      // }     
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
