<?php

namespace App\Modules\AuditTrailData\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\AuditTrailData\Models\AuditTrailData;
use App\Traits\AuditTrail;

class AuditTrailDataController extends Controller
{
    use AuditTrail;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function  __construct()
    {
        # code...
    }
    public function index()
    {
        // $audit_trails = AuditTrailData::orderByDesc('created_at')->get();
        $audit_trails["submission"] = AuditTrailData::where("module", "LIKE", "Submission%")->orderByDesc('created_at')->get();
        $audit_trails["general"] = AuditTrailData::where("module", "NOT LIKE", "Submission%")->orderByDesc('created_at')->get();
        // return $audit_trails;
        return view("AuditTrailData::admin.index", compact("audit_trails"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource    in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
