<?php

/**
 *	From Helper  
 */

function getValuesForms()
{
	return "here";
}
function getFieldBox($field_id)
{
	$field = DB::table("form_fields")->where("id",$field_id)->first();
	return $field;
}
function getContentValue($build_id,$field_property)
{
	$content = DB::table("form_content")->where("build_id",$build_id)->where("field_property",$field_property)->first();
	if(isset($content))
	{
		return $content->field_value;
	}
	else
	{
		return null;
	}
}
/*
function getFieldLabel($field)
{
	$content = DB::table("form_content")->where("field_value",$field)->first(['build_id']);
	if(isset($content)){
		return getContentValue($content->build_id, 'label');
	}
	else{
		return null;
	}
}*/

function getContentOptions($build_id)
{
	$content = DB::table("form_content")->where("build_id",$build_id)->where("field_property",'like','option_'.'%')->orderBy("sort_option",'asc')->get();
	
	if(isset($content))
	{
		return $content;
	}
	else
	{
		// return "";
	}
}

function getContentCheckbox($build_id)
{
	$content = DB::table("form_content")->where("build_id",$build_id)->where("field_property",'like','checkbox_'.'%')->orderBy("field_property")->get();
	if(isset($content))
	{
		return $content;
	}
	else
	{
		// return "";
	}
}

function getContentRadio($build_id)
{
	$content = DB::table("form_content")->where("build_id",$build_id)->where("field_property",'like','radio_'.'%')->orderBy("field_property")->get();
	if(isset($content))
	{
		return $content;
	}
	else
	{
		// return "";
	}
}

function getFormPageTitle($form_id,$page_id)
{
    $exist = DB::table("form_page")->where("form_id",$form_id)->where("page_id",$page_id)->first();
    if(isset($exist->page_title))
    {
    	return $exist->page_title;
    }
    else
    {
    	return null;
    }
}