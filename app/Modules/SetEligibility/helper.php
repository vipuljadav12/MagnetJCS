<?php

/**
 *	SetEligibility Helper  
 */

function getEligibilityTypeById($id)
{
	$template = DB::table("eligibility_template")->where('id',$id)->first();
	return $template;
}
function getEligibilityContent($id,$value='')
{
	// return $id;
	if(isset($id)){
		$Eligibility = new  App\Modules\Eligibility\Models\Eligibility;
		$EligibilityContent = new  App\Modules\Eligibility\Models\EligibilityContent;
		$EligibilityTemplate = new  App\Modules\Eligibility\Models\EligibilityTemplate;
		$content = $EligibilityContent->where("eligibility_id",$id)->first();
		$html = "";
		if(isset($content->content))
		{
			$eligibility = $Eligibility->where("id",$content->eligibility_id)->first();
			$eligibilityTemplate = $EligibilityTemplate->where("id",$eligibility->template_id)->first();
			// return  $eligibilityTemplate->content_html;
			// return $eligibility;
			// return $content;
			$data = json_decode($content->content);
			if(isset($eligibilityTemplate->content_html) && ($eligibilityTemplate->content_html == "interview" ||$eligibilityTemplate->content_html == "writing_prompt" || $eligibilityTemplate->content_html ==  "committee_score" || $eligibilityTemplate->content_html ==  "audition"  ))
			{
				$type = $data->eligibility_type->type;
				// return  $type;
				// $options = $data->eligibility_type->$type;
				$options = isset($data->eligibility_type->$type) ? $data->eligibility_type->$type : array();
				if(empty($options))
				{
					$html .= '<option value="" selected>'."N/A".'</option>';

				}
				foreach($options as $o=>$option)
				{
					if(isset($value) && $value	== $option)
						$html .= '<option value="'.$option.'" selected>'.$option.'</option>';
					else
						$html .= '<option value="'.$option.'">'.$option.'</option>';
				}
				// dd($html);
				// return $data;
			}
			if(isset($eligibilityTemplate->content_html) && ($eligibilityTemplate->content_html == "academic_grade_calculation" || $eligibilityTemplate->content_html == "standardized_testing" || $eligibilityTemplate->content_html == "conduct_disciplinary"))
			{
				// return  $content->content;
				/*print_r("dd");
				dd('dd');
				return $data;*/
				// $html .= "deede";
				$type = $data->scoring->method;
				// return  $type;
				$options = isset($data->scoring->$type) ? $data->scoring->$type : array();
				if(empty($options))
				{
					$html .= '<option value="" selected>'."N/A".'</option>';

				}
				else 
				{
					// print_r($options);
					// print_r("expression");
					if(is_array($options))
					{
						foreach($options as $o=>$option)
						{
							if(isset($value) && $value	== $option)
								$html .= '<option value="'.$option.'" selected>'.$option.'</option>';
							else
								$html .= '<option value="'.$option.'">'.$option.'</option>';
						}
					}
				}
			}
			
		}
		return $html;
		// return $content->content;
	}
}
function getEligibilityContentType($id,$value='')
{
	// return $id;
	if(isset($id)){
		$Eligibility = new  App\Modules\Eligibility\Models\Eligibility;
		$EligibilityContent = new  App\Modules\Eligibility\Models\EligibilityContent;
		$EligibilityTemplate = new  App\Modules\Eligibility\Models\EligibilityTemplate;
		$content = $EligibilityContent->where("eligibility_id",$id)->first();
		$html = "";
		if(isset($content->content))
		{
			$eligibility = $Eligibility->where("id",$content->eligibility_id)->first();
			$eligibilityTemplate = $EligibilityTemplate->where("id",$eligibility->template_id)->first();
			// return  $eligibilityTemplate->content_html;
			// return $eligibility;
			// return $content;
			$data = json_decode($content->content);
			if(isset($eligibilityTemplate->content_html) && ($eligibilityTemplate->content_html == "interview" ||$eligibilityTemplate->content_html == "writing_prompt" || $eligibilityTemplate->content_html ==  "committee_score" || $eligibilityTemplate->content_html ==  "audition"  ))
			{
				$type = $data->eligibility_type->type;
				return  $type;
				// $options = $data->eligibility_type->$type;
				$options = isset($data->eligibility_type->$type) ? $data->eligibility_type->$type : array();
				if(empty($options))
				{
					$html .= '<option value="" selected>'."N/A".'</option>';

				}
				foreach($options as $o=>$option)
				{
					if(isset($value) && $value	== $option)
						$html .= '<option value="'.$option.'" selected>'.$option.'</option>';
					else
						$html .= '<option value="'.$option.'">'.$option.'</option>';
				}
				// dd($html);
				// return $data;
			}
			if(isset($eligibilityTemplate->content_html) && ($eligibilityTemplate->content_html == "academic_grade_calculation" || $eligibilityTemplate->content_html == "standardized_testing" || $eligibilityTemplate->content_html == "conduct_disciplinary"))
			{
				// return  $content->content;
				/*print_r("dd");
				dd('dd');
				return $data;*/
				// $html .= "deede";
				$type = $data->scoring->method;
				return  $type;
				$options = isset($data->scoring->$type) ? $data->scoring->$type : array();
				if(empty($options))
				{
					$html .= '<option value="" selected>'."N/A".'</option>';

				}
				else 
				{
					// print_r($options);
					// print_r("expression");
					if(is_array($options))
					{
						foreach($options as $o=>$option)
						{
							if(isset($value) && $value	== $option)
								$html .= '<option value="'.$option.'" selected>'.$option.'</option>';
							else
								$html .= '<option value="'.$option.'">'.$option.'</option>';
						}
					}
				}
			}
			
		}
		return $html;
		// return $content->content;
	}
}