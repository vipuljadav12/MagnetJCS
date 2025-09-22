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
    dd($rows);

    foreach ($rows as $key => $row) {
      $errors = [];
      $school = School::where('id', $row['school_id'])
        ->where('district_id', Session::get("district_id"))
        ->first();
      if (isset($school)) {
        $enrollment = Enrollment::where('status', 'Y')
          ->where("district_id", $school->district_id)
          ->get()
          ->last();
      } else {
        $errors[] = "Entered School ID is not valid.";
      }
      $ins = array();
      // $gs_total = 0;

      $insert = false;

      if ($row['black'] != '' && $row['white'] != '' && $row['other'] != '') {
        $insert = true;
        if ($row['black'] != '' && is_numeric($row['black'])) {
          $ins['black'] = $row['black'] ?? 0;
        } else {
          $errors[] = "Enter numeric value for field Black.";
        }
        if ($row['white'] != '' && is_numeric($row['white'])) {
          $ins['white'] = $row['white'] ?? 0;
        } else {
          $errors[] = "Enter numeric value for field White.";
        }
        if ($row['other'] != '' && is_numeric($row['other'])) {
          $ins['other'] = $row['other'] ?? 0;
        } else {
          $errors[] = "Enter numeric value for field Other.";
        }

        if (empty($errors) && $insert) {
          // $ins['available_seats'] = $gs_total;
          //$ins['year'] = $enrollment->school_year ?? (date("Y")-1)."-".date("Y");
          $key_data = [
            'school_id' => $school->id,
            'enrollment_id' => Session::get('upADM_Enroll_id')
          ];

          $result = ADMData::updateOrCreate($key_data, $ins);
        } else {
          $row['errors'] = $errors;
          $this->allErrors[] = $row;
        }
      }
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
