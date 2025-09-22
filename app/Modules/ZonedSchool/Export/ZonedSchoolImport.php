<?php

namespace App\Modules\ZonedSchool\Export;

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
use App\Modules\ZonedSchool\Models\ZonedSchool;
use App\Modules\ZonedSchool\Models\ZonedAddressMaster;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Validation\Rule;

class ZonedSchoolImport implements ToModel, WithBatchInserts, WithHeadingRow, SkipsOnFailure
{
  use SkipsFailures, Importable;
  public $invalidArr = array();
  public $addedArr = array();
  protected $master_id = 1;
  
  public function __construct($zone_master_id = 1)
  {
    $this->master_id = $zone_master_id;
  }

  // public function rules(): array
  // {
  //   return [
  //                 '*.submission_id' => 'required',
  //              ];
  // }
  // public function customValidationMessages()
  // {
  //  return [];
  //  }
  public function model(array $row)
  {
    $data = array();
    $suffixArr = ['S' => 'South', 'E' => 'East', 'N' => 'North', 'W' => 'West'];

    if ($row) {

      if (isset($row['bldg_num']) && isset($row['street_name']) && isset($row['city']) && isset($row['zip']) && (isset($row['elementary_school']) || isset($row['intermediate_school']) || isset($row['middle_school']) || isset($row['high_school']))) {

        $data = [
          'bldg_num'       => isset($row['bldg_num']) ? $row['bldg_num'] : '',
          'zone_master_id' => $this->master_id,
          'prefix_dir'     => isset($row['prefix_dir']) ? $row['prefix_dir'] : '',
          'prefix_type'     => isset($row['prefix_type']) ? $row['prefix_type'] : '',
          'street_name'     => isset($row['street_name']) ? $row['street_name'] : '',
          'street_type'     => isset($row['street_type']) ? $row['street_type'] : '',
          'suffix_dir'     => isset($row['suffix_dir']) ? $row['suffix_dir'] : '',
          'suffix_dir_full'    => (isset($row['suffix_dir']) && isset($suffixArr[$row['suffix_dir']])) ? $suffixArr[$row['suffix_dir']] : '',
          'unit_info'     => isset($row['unit_info']) ? $row['unit_info'] : '',
          'city'         => isset($row['city']) ? $row['city'] : '',
          'state'       => isset($row['state']) ? $row['state'] : '',
          'zip'         => isset($row['zip']) ? $row['zip'] : '',
          'elementary_school' => isset($row['elementary_school']) ? $row['elementary_school'] : '',
          'intermediate_school' => isset($row['intermediate_school']) ? $row['intermediate_school'] : '',
          'middle_school'   => isset($row['middle_school']) ? $row['middle_school'] : '',
          'high_school'     => isset($row['high_school']) ? $row['high_school'] : '',
          'added_by'        => 'import'
        ];
        $str = strtolower($data['bldg_num'] . " " . $data['street_name'] . " " . $data['street_type'] . " " . $data['suffix_dir'] . " " . $data['unit_info'] . " " . $data['city'] . " " . $data['zip']);

        $rsData = DB::table("zone_address")->where(DB::raw("LOWER(concat(bldg_num, ' ', street_name, ' ', street_type, ' ', suffix_dir, ' ', unit_info, ' ', city, ' ', zip))"), $str)->first();
        if (!empty($rsData)) {
          ZonedSchool::where("id", $rsData->id)->update($data);
        } else {
          ZonedSchool::create($data);
        }

        $this->addedArr[] = $row;
      } else {
        $this->invalidArr[] = $row;
      }
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
