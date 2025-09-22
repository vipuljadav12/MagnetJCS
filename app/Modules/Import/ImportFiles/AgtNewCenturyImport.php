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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\ValidationException;

class AgtNewCenturyImport implements ToModel, WithValidation, WithBatchInserts, WithHeadingRow, SkipsOnFailure
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
        if (isset($row['student_id'])) {
            $insert['student_id'] = $row['student_id'];
            $insert['name'] = $row['name'];
            $insert['user_id'] = Auth::user()->id;
            $insert['grade_level'] = $row['grade_level'];
            $insert['program_name'] = $this->program_name;
            $insert['enrollment_id'] = Session::get('enrollment_id');
            AgtToNch::create($insert);
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
