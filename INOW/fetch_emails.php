<?php
    set_time_limit(0);
    ini_set('memory_limit','2048M');
    include("common_functions.php");
    
    include("dbClass.php");
    $objDB = new MySQLCN;


    $ethnicities = fetch_inow_details( 'ethnicities' );
    foreach( $ethnicities as $ethnicity ){
        $race_hash[ $ethnicity->Id ] = $ethnicity->Name;
    }

    // Get all student ethnicities
    $student_race_hash = array();
    $ethnicities = fetch_inow_details( 'persons/ethnicities' );
    foreach( $ethnicities as $ethnicity ){
    	$SQL = "SELECT * FROM student_race WHERE studentId = '".$ethnicity->PersonId."' AND race = '".$race_hash[ $ethnicity->EthnicityId ]."'";
    	$rs = $objDB->select($SQL);

    	if(count($rs) > 0)
    	{
    		$SQL = "DELETE FROM student_race WHERE studentId = '".$ethnicity->PersonId."' AND race = '".$race_hash[ $ethnicity->EthnicityId ]."'";
    		$rs = $objDB->sql_query($SQL);
    	}
    		$SQL = "INSERT INTO student_race SET studentId = '".$ethnicity->PersonId."', race = '".$race_hash[ $ethnicity->EthnicityId ]."', is_primary = '".($ethnicity->IsPrimary == 1 ? "Y" : "N")."', percentage = '".$ethnicity->Percentage."'";
    		$rs = $objDB->sql_query($SQL);


    	
    }

/*    echo "<pre>";
    print_r($ethnicities);

    //print_r($student_race_hash);
  //exit;
    // Get all schools
    $schools_hash = array();
    $schools = fetch_inow_details('students/285487');//details?studentNumber=S34489');
    //$schools = fetch_inow_details('students/details?studentNumber=S34489');
    print_r($schools);exit;
*/
?>