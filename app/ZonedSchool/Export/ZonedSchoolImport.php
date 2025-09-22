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
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Validation\Rule;

class ZonedSchoolImport implements ToModel, WithBatchInserts, WithHeadingRow, SkipsOnFailure
{
    use SkipsFailures, Importable;
    public function __construct() {}

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

        if ($row) {

            $data = [
                'bldg_num'             => isset($row['bldg_num']) ? $row['bldg_num'] : '',
                'prefix_dir'         => isset($row['prefix_dir']) ? $row['prefix_dir'] : '',
                'prefix_type'         => isset($row['prefix_type']) ? $row['prefix_type'] : '',
                'street_name'         => isset($row['street_name']) ? $row['street_name'] : '',
                'street_type'         => isset($row['street_type']) ? $row['street_type'] : '',
                'suffix_dir'         => isset($row['suffix_dir']) ? $row['suffix_dir'] : '',
                'unit_info'         => isset($row['unit_info']) ? $row['unit_info'] : '',
                'city'                 => isset($row['city']) ? $row['city'] : '',
                'state'             => isset($row['state']) ? $row['state'] : '',
                'zip'                 => isset($row['zip']) ? $row['zip'] : '',
                'elementary_school' => isset($row['elementary_school']) ? $row['elementary_school'] : '',
                'intermediate_school' => isset($row['intermediate_school']) ? $row['intermediate_school'] : '',
                'middle_school'     => isset($row['middle_school']) ? $row['middle_school'] : '',
                'high_school'         => isset($row['high_school']) ? $row['high_school'] : '',
            ];

            ZonedSchool::create($data);
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
