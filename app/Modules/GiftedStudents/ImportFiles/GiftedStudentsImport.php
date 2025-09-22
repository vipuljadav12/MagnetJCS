<?php

namespace App\Modules\GiftedStudents\ImportFiles;

use App\Traits\AuditTrail;
use Maatwebsite\Excel\Row;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\ValidationException;

use App\Modules\GiftedStudents\Models\GiftedStudents;
use Illuminate\Support\Facades\Session;

class GiftedStudentsImport implements ToModel, WithValidation, WithBatchInserts, WithHeadingRow, SkipsOnFailure
{
    use SkipsFailures, Importable, AuditTrail;
    public $invalidArr = array();
    public $district_id = '';
    public $enrollment_id = '';

    public function __construct()
    {
        $this->district_id = Session::get('district_id');
        $this->enrollment_id = Session::get('enrollment_id');
    }

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
        $insert['stateID'] = $row['ssid'];
        $insert['first_name'] = $row['firstname'];
        $insert['last_name'] = $row['lastname'];
        $insert['admin'] = $row['admin'];
        $insert['district_id'] = $this->district_id;
        $insert['enrollment_id'] = $this->enrollment_id;

        if ($insert['stateID'] != "") {
            GiftedStudents::updateOrCreate(["stateID" => $insert['stateID']], $insert);
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
