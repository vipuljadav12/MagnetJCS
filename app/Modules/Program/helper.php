<?php

use App\Modules\Priority\Models\Priority;

function getPriority($id)
{
    $priority_name=Priority::where('id',$id)->first();
    if(!empty($priority_name))
	    return $priority_name->name;
	else
		return "";
}


function getRankingMethod($data)
{
	$tmp = array();	
	/*if($data->committee_score != '')
	    $tmp['committee_score'] = $data->committee_score;
    if($data->audition_score != '')
		$tmp['audition_score'] = $data->audition_score;
    if($data->rating_priority != '')
	    $tmp['rating_priority'] = $data->rating_priority;
    if($data->combine_score != '')
		$tmp['combine_score'] = $data->combine_score;
    if($data->lottery_number != '')
		$tmp['lottery_number'] = $data->lottery_number;
    if($data->final_score != '')
		$tmp['final_score'] = $data->final_score;*/

	if($data->committee_score != '')
	    $tmp[] = array("key"=>'committee_score', "label"=>"Committee Score", "value"=>$data->committee_score);
    if($data->audition_score != '')
		$tmp[] = array("key"=>'audition_score', "label"=> "Audition Score", "value"=>$data->audition_score);
    if($data->rating_priority != '')
	    $tmp[] = array("key"=>'rating_priority', "label"=> "Priority", "value"=>$data->rating_priority);
    if($data->combine_score != '')
		$tmp[] = array("key"=>'combine_score', "label"=>"Combined Score", "value"=>$data->combine_score);
    if($data->lottery_number != '')
		$tmp[] = array("key"=>'lottery_number', "label"=>"Lotter Number", "value"=>$data->lottery_number);
    if($data->final_score != '')
		$tmp[] = array("key"=>'final_score', "label"=>"Final Score", "value"=>$data->final_score);


	usort($tmp, function ($item1, $item2) {
	    return $item1['value'] <=> $item2['value'];
	});
	$selection = "";
	for($i=0; $i < count($tmp); $i++)
	{
		$selection .= $tmp[$i]["label"]."<br>";
	}
	$selection = trim($selection, ", ");
	echo $selection;

}