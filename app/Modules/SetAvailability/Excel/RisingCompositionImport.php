<?php

namespace App\Modules\SetAvailability\Excel;

use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Modules\SetAvailability\Models\Availability;
use App\Modules\Program\Models\Program;
use Illuminate\Support\Facades\Session;

class RisingCompositionImport implements ToCollection, WithBatchInserts, WithHeadingRow, SkipsOnFailure
{
  use Importable, SkipsFailures;
  protected $allErrors = [];

  public function collection(Collection $rows)
  {
    foreach ($rows as $row) {
      $errors = [];
      // Program
      if (isset($row['program_id'])) {
        $program = Program::where('id', $row['program_id'])->first();
        if (isset($program)) {
          // School
          if (isset($row['school_name'])) {
            $school = $program->whereRaw('FIND_IN_SET(?, zoned_schools)', [$row['school_name']])->first();
            if (!isset($school)) {
              $errors[] = "School Name is invalid.";
            }
          } else {
            $errors[] = "School Name is required.";
          }
        } else {
          $errors[] = "Program ID is invalid.";
        }
      } else {
        $errors[] = "Program ID is required.";
      }
      // Seats
      $black = $non_black = 0;
      if (is_numeric($row['black'])) {
        $black = $row['black'];
      } else {
        $errors[] = "Enter numeric value for field Black.";
      }
      if (is_numeric($row['non_black'])) {
        $non_black = $row['non_black'];
      } else {
        $errors[] = "Enter numeric value for field Non Black.";
      }
      // Store
      if (empty($errors)) {
        $key_data = [
          'program_id' => $row['program_id'],
          'district_id' => Session::get("district_id"),
          'enrollment_id' => Session::get('enrollment_id')
        ];
        $rising_composition = [];
        $availability = Availability::where($key_data)->first();
        if (isset($availability)) {
          $rising_composition = isset($availability->rising_composition) ? json_decode($availability->rising_composition, true) : [];
        }
        $rising_composition[$row['school_name']] = [
          'black' => $black,
          'non_black' => $non_black
        ];
        $data = [
          'rising_composition' => json_encode($rising_composition)
        ];
        Availability::updateOrCreate($key_data, $data);
      } else {
        unset($row['']);
        unset($row['error']);
        $row['error'] = $errors;
        $this->allErrors[] = $row;
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
