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
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\School\Models\School;

use App\Modules\Program\Models\Program;
use Illuminate\Support\Facades\Session;

class AvailabilityImport implements ToCollection, WithBatchInserts, WithHeadingRow, SkipsOnFailure
{
  use Importable, SkipsFailures;

  protected $allErrors = [];

  public function collection(Collection $rows)
  {
    foreach ($rows as $row) {
      $errors = [];

      $program = Program::where('id', $row['program_id'])->where('district_id', Session::get("district_id"))->where('enrollment_id', Session::get('enrollment_id'))->first();

      if (!isset($program) && empty($program)) {
        $errors[] = "Entered Program ID is not valid.";
        $row['errors'] = $errors;
        $this->allErrors[] = $row;
      } else {

        $black_seat = $white_seat = $other_seat = $capacity = $available_seats = 0;

        // if($row['available_seats'] != '' && $row['white'] != '' && $row['other'] != '' && $row['capacity'] != '') 
        if ($row['available_seats'] != '') {
          // if(is_numeric($row['black'])){
          //   $black_seat = $row['black'];
          // }else {
          //   $errors[] = "Enter numeric value for field Black.";
          // }

          // if(is_numeric($row['white'])){
          //   $white_seat = $row['white'];
          // }else {
          //   $errors[] = "Enter numeric value for field White.";
          // }

          // if(is_numeric($row['other'])){
          //   $other_seat = $row['other'];
          // }else {
          //   $errors[] = "Enter numeric value for field Other.";
          // }

          // if(is_numeric($row['capacity'])){
          //   $capacity = $row['capacity'];
          // }else {
          //   $errors[] = "Enter numeric value for field Capacity.";
          // }

          if (!empty($errors)) {
            $row['errors'] = $errors;
            $this->allErrors[] = $row;
          } else {
            $available_seats = $row['available_seats'];

            $update_or_data = [
              'program_id' => $row['program_id'],
              'enrollment_id' => Session::get('enrollment_id'),
              'district_id' => Session::get('district_id'),
              //'grade' => $row['grade'],
              'available_seats' => $available_seats,
              // 'total_seats' => $capacity,
              // 'white_seats' => $black_seat,
              // 'black_seats' => $white_seat,
              // 'other_seats' => $other_seat
            ];

            $key_data = [
              'program_id' => $row['program_id'],
              'district_id' => Session::get("district_id"),
              'enrollment_id' => Session::get('enrollment_id'),
              //'grade'=> $row['grade']
            ];

            Availability::updateOrCreate($key_data, $update_or_data);
          }
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
