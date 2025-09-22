<?php

namespace App\Modules\Form\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class INowController extends Controller
{
    public $api_url;
    public $api_username;
    public $api_password;
    public $application_key;

    public function __construct()
    {
        $this->api_url = "https://inow.tusc.k12.al.us/API/";
        $this->api_username = "LeanFrog_API";
        $this->api_password = "qs4p2CNu4H4N9g7ETKzF";
        $this->application_key = "leanfrog B/1F8Y/ToQlRufi/0DgoaKLOBcrd3PpT+wFJL6Sdwy2Z8vZP6GamF7KDmU2nb+Cn/ayElMuxwrWreWae06oNhrCE29gnEizIdFuS3bICs3eFOe7bnRsVyPbPE+4CmOc9QzI5pTbUv9aH/7TrSVVSYcL5WaLzeEwnl2+hlj9c2dw=";
    }

    public function fetch_single_student() {}
}
