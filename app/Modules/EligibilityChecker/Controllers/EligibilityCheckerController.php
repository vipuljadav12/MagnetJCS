<?php

namespace App\Modules\EligibilityChecker\Controllers;

use App\Modules\Eligibility\Models\Eligibility;
use App\Modules\Application\Models\{Application, ApplicationProgram};
use App\Modules\Eligibility\Models\EligibilityTemplate;
use App\Modules\Eligibility\Models\EligibilityContent;
use App\Modules\Program\Models\ProgramEligibility;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\School\Models\Grade;
use App\Modules\Eligibility\Models\SubjectManagement;
use App\Traits\AuditTrail;
use Illuminate\Support\Facades\Session;

class EligibilityCheckerController extends Controller
{
    use AuditTrail;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {}

    public function index()
    {
        $applications = Application::where("enrollment_id", Session::get("enrollment_id"))->get();
        return view("EligibilityChecker::index", compact("applications"));
    }

    public function application_data($application_id)
    {
        $programs = ApplicationProgram::where("application_id", $application_id)->join("program", "program.id", "application_programs.program_id")->where("district_id", Session::get("district_id"))->where("enrollment_id", Session::get("enrollment_id"))->select("program_id")->distinct()->get();

        $eligibility_templates = EligibilityTemplate::where("status", "Y")->get();
        $program_eligibilities = [];
        foreach ($programs as $pkey => $pvalue) {
            $program_id = $pvalue->program_id;
            foreach ($eligibility_templates as $key => $value) {
                $eligibility_type = $value->id;
                $eligibility = ProgramEligibility::where("program_id", $program_id)->where("eligibility_type", $eligibility_type)->join("eligibiility", "eligibiility.id", "program_eligibility.assigned_eigibility_name")->where("application_id", $application_id)->first();
                if (!empty($eligibility)) {
                    $program_eligibilities[$program_id][$eligibility_type] = $eligibility->name;
                } else {
                    $program_eligibilities[$program_id][$eligibility_type] = "";
                }
            }
        }
        return view("EligibilityChecker::eligibiility_data", compact("application_id", "programs", "program_eligibilities", "eligibility_templates"));
    }
}
