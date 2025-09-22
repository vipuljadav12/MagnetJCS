<?php

/**
 *	Configuration Helper  
 */
function getConfigNameArray()
{
	return array(
		"welcome_text"=>"Welcome Text",
		"welcome_message"=>"Welcome Message",
		"new_student"=>"New Student",
		"existing_student"=>"Existing Student",
	);
}
/*function getConfigNameArray()
{
	$all = array(
		"welcome_text"=>"Welcome Text",
		"welcome_message"=>"Welcome Message",
		"new_student"=>"New Student",
		"existing_student"=>"Existing Student",
	);
}*/
// function 
function getConfigValues($name='')
{
	$config_value = DB::table("district_config")->where("district_id",Session::get("district_id"))->where("config_name",$name)->first();
	return $config_value;
}
