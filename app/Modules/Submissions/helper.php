<?php

use App\Modules\School\Models\Grade;
use App\Modules\Submissions\Models\SubmissionAudition;
use App\Modules\Submissions\Models\SubmissionWritingPrompt;
use App\Modules\Submissions\Models\SubmissionInterviewScore;
use App\Modules\Submissions\Models\SubmissionCommitteeScore;
use App\Modules\Submissions\Models\SubmissionConductDisciplinaryInfo;
use App\Modules\Submissions\Models\SubmissionStandardizedTesting;
use App\Modules\Submissions\Models\SubmissionAcademicGradeCalculation;


function getGradeName($id)
{
    return Grade::where('id',$id)->first()->name;
}

function getSubmissionAudition($id)
{
	$data = SubmissionAudition::where("submission_id",$id)->first();
	if(isset($data->id))
	{
		return $data;
	}	
	else
	{
		return null;
	}
}
function getSubmissionWritingPrompt($id)
{
	$data = SubmissionWritingPrompt::where("submission_id",$id)->first();
	if(isset($data->id))
	{
		return $data;
	}
	else
	{
		return null;
	}
}
function getSubmissionInterviewScore($id)
{
	$data = SubmissionInterviewScore::where("submission_id",$id)->first();
	if(isset($data->id))
	{
		return $data;
	}
	else
	{
		return null;
	}
}
function getSubmissionCommitteeScore($submission_id, $program_id)
{
    $rs = SubmissionCommitteeScore::where("submission_id", $submission_id)->where("program_id", $program_id)->first();
    if(!empty($rs))
        return $rs->data;
    else
        return "";
}
function getConductDisciplinaryInfo($id)
{
	$data = SubmissionConductDisciplinaryInfo::where("submission_id",$id)->first();
	if(isset($data->id))
	{
		return $data;
	}
	else
	{
		return null;
	}
}
function getSubmissionAcademicGradeCalculation($id)
{
	$data = SubmissionAcademicGradeCalculation::where("submission_id",$id)->first();
	if(isset($data->id))
	{
		return $data;
	}
	else
	{
		return null;
	}
}

function getSubmissionStandardizedTesting($id)
{
	$data = SubmissionStandardizedTesting::where("submission_id",$id)->get()->keyBy("subject")->all();
	if(isset($data) && !empty($data))
	{
		return $data;
	}
	else
	{
		return null;
	}
}
