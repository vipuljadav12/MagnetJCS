<?php

namespace App\Modules\ReturningStudent\ImportExport;

use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
// use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Modules\ReturningStudent\Models\ReturningStudent;
use Illuminate\Support\Facades\Validator;

class DataImport implements ToCollection, WithBatchInserts, WithHeadingRow, SkipsOnFailure
{
    use Importable, SkipsFailures;
    protected $allErrors = [];

    public function collection(Collection $collection)
    {
        $messages = [
            'required' => 'The :attribute Date Format is invalid',
            'date_format'    => 'The :attribute must be text format',

        ];
        // Validator::make($collection->toArray(), [
        //      '*.birth_date' => 'date_format:m/d/Y'

        //  ], $messages)->validate();

        $insdata = [];
        foreach ($collection as $value) {
            unset($value['']);
            $error = [];
            if (isset($value['ssid']) && ($value['ssid'] == '')) {
                $error[] = 'SSID is requred.';
            }
            if (isset($value['date_of_birth']) && ($value['date_of_birth'] == '')) {
                $error[] = 'Date of birth is requred.';
            }
            if (isset($value['student_name']) && ($value['student_name'] == '')) {
                $error[] = 'Student Name is requred.';
            }
            if (isset($value['next_grade']) && ($value['next_grade'] == '')) {
                $error[] = 'Next Grade is requred.';
            }
            if (isset($value['race']) && ($value['race'] == '')) {
                $error[] = 'Race is requred.';
            }
            if (isset($value['current_school']) && ($value['current_school'] == '')) {
                $error[] = 'Current School is requred.';
            }
            if (isset($value['current_signature_academy']) && ($value['current_signature_academy'] == '')) {
                $error[] = 'Current Signature Academy is requred.';
            }
            // dump($error);
            if (empty($error)) {
                //echo $value['date_of_birth']."<br>";
                $tmpdata['birthday'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_of_birth'])->format('Y-m-d');

                $tmpdata['student_name'] = $value['student_name'];
                $tmpdata['next_grade'] = $value['next_grade'];
                $tmpdata['race'] = $value['race'];
                $tmpdata['current_school'] = $value['current_school'];
                $tmpdata['current_signature_academy'] = $value['current_signature_academy'];

                $tmpdata['stateID'] = $value['ssid'];


                $rs = ReturningStudent::updateOrCreate(["stateID" => $value['ssid']], $tmpdata);
            } else {
                $value['errors'] = implode(' | ', $error);
                $this->allErrors[] = $value;
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
