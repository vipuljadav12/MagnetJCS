<?php

namespace App\Modules\Eligibility\Controllers;

use App\Modules\Eligibility\Models\Eligibility;
use App\Modules\Eligibility\Models\EligibilityTemplate;
use App\Modules\Eligibility\Models\EligibilityContent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class SetEligibiltyController extends Controller
{

    public $url;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->url = url("admin/Eligibility/set");
        View::share(["module_url" => $this->url]);
    }
    public function index()
    {
        return "here";
    }
}
