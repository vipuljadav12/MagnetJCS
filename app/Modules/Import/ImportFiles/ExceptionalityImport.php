<?php

namespace App\Modules\Import\ImportFiles;

use App\Traits\AuditTrail;
use Maatwebsite\Excel\Row;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;

use App\Modules\Import\Models\GiftedStudents;
use App\Modules\Import\Models\AgtToNch;

use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Modules\Submissions\Models\Submissions;

class ExceptionalityImport implements ToModel, WithValidation, WithBatchInserts, WithHeadingRow, SkipsOnFailure
{
    use SkipsFailures, Importable, AuditTrail;
    public $invalidArr = array();
    public $program_name = "";

    public function __construct() {}

    public function rules(): array
    {
        return [];
    }
    public function customValidationMessages()
    {
        return [];
    }
    public function model(array $row)
    {
        $insert = array();

        if (isset($row['ssid']) && isset($row['confirmation_number'])) {

            $check_student = [
                'id' => $row['confirmation_number'],
                'student_id' => $row['ssid'],
            ];

            $submission_data = Submissions::where($check_student)->first();

            if (isset($submission_data) && !empty($submission_data)) {

                $udpate_arr = [
                    'sp_exception' => $row['sp_exception'],
                    'is_504_eligible' => $row['504_eligible'],
                ];

                Submissions::where($check_student)->update($udpate_arr);
            }
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
