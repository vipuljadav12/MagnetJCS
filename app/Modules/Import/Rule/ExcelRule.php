<?php 
namespace App\Modules\Import\Rule;

use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Validation\Rule;

class ExcelRule implements Rule
{
    private $file;

    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
    }

    public function passes($attribute, $value)
    {
        $extension = strtolower($this->file->getClientOriginalExtension());
        return in_array($extension, ['xls', 'xlsx']);
    }

    public function message()
    {
        return 'Invalid file format | File format must be xlsx or xls.';
    }
}