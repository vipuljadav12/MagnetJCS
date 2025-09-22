<?php
    set_time_limit(0);
	ini_set("error_reporting", "1");
    ini_set('memory_limit','2048M');
    
//    $start = strtotime("2015-09-14T00:00:00");
 //   $end = strtotime("2015-09-19T00:00:00");

//  $days_between = ceil(abs($end - $start) / 86400);
//  echo $dataArr['suspend_days'] = $days_between;
//  exit;

    
    include("common_functions.php");
    
    //$endpoint = "dispositions/1";
    //print_r(fetch_inow_details($endpoint));exit;
    
    include("dbClass.php");
    $objDB = new MySQLCN;
    
    
      $rs = $objDB->select("SELECT stateID FROM student_session WHERE stateID in (1971751324,1966147405,1967900570,1965909912,1968327856,1969273752,1968957991,1970605752,1973955956,1969663218,1973875139,1970453377,1970466767,1970465462,1970453252,1972850091,1971319650,1970453450,1970476634,1969349982,1972593972,1970453237,1971299761,1971752702,1971974736,1972551525,1972230690,1972485187,1973765462,1972597817,1974020909,1973196387,1974427724,1974559674,1975959899,1974454819,1973934167,1975142173,1975399138,1974632406,1976784080,1976755460,1976584241,1976584076,1976811693)");
    $arr = $arr1 = array();
     for($i=0; $i < count($rs); $i++)
    {
        $SQL = "SELECT student_id, stateID FROM student_session WHERE stateID = '".$rs[$i]['stateID']."'";
            $rs2 = $objDB->select($SQL);
            if(count($rs2) > 0)
            {
                $arr[] = $rs2[0]['student_id'];
                $arr1[] = $rs2[0]['stateID'];
            }
    }
    $locations = fetch_inow_details("disciplinaryLocations");
    $locations_hash = [];
    foreach( $locations as $location ){
        $locations_hash[ $location->Id ] = $location;
    }
    unset( $locations );
    /* Get Incidents */
    $incidents = fetch_inow_details("incidents");
    $incident_hash = [];
    foreach( $incidents as $incident ){
        $incident_hash[ $incident->Id ] = $incident;
    }
    unset( $incidents );

    $infractions = fetch_inow_details( 'infractions' );
    $infraction_hash = [];
    foreach( $infractions as $infraction ){
        $infraction_hash[ $infraction->Id ] = $infraction;
    }
    unset( $infractions );

    
   /* $disciplinary_actions = fetch_inow_details( 'disciplinaryactions' );
    $disciplinary_actions_hash = [];
    foreach( $disciplinary_actions as $disciplinary_action ){
        $disciplinary_actions_hash[ $disciplinary_action->Id ] = $disciplinary_action;
    }*/
    //$studentArr = array("244480");
    //echo "studentid^grade^incident_number^datetime^infraction_code^infraction_name^disposition^disposition_type^note^actioncode^actionname^suspend_days<br>";

	$infraction_codeArr = array();
    foreach($arr as $key=>$value)
    {

        $endpoint = "students/".$value."/disciplinaryOccurrences";
        $dispData = fetch_inow_details($endpoint);

        print_r($dispData);
        echo "<BR><BR>";
        
        if(count($dispData) > 0)
        {
            foreach($dispData as $key1=>$value1)
            {
                $dataArr = array();
                $aca_session_id = $value1->AcadSessionId;
                
                $endpoint = "students/".$value."/acadSessions/".$aca_session_id;
                $data = fetch_inow_details($endpoint);
                
                $grade_level_id = $data->GradeLevelId;
                $endpoint = "gradelevels/".$grade_level_id;
                $data = fetch_inow_details($endpoint);
                $dataArr['grade'] = $data->Description;

                if($locations_hash[$value1->DisciplinaryLocationId])
                    $studentArr['location'] = $locations_hash[$value1->DisciplinaryLocationId];
                else
                    $studentArr['location'] = 0;
                $dataArr['suspend_days'] = "0";
                $dataArr['datetime'] = $value1->DateTime;
                $dataArr['startdate'] = "";
                $dataArr['enddate'] = "";

                if(isset($value1->Dispositions[0]))
                {
                    $dispositionId = $value1->Dispositions[0]->DispositionId;
                    $endpoint = "dispositions/".$dispositionId;
                    $data = fetch_inow_details($endpoint);


                    $dataArr['disposition'] = $data->Description;
                    $dataArr['disposition_type'] = $data->Name;
                    
                    
                    
                    $dataArr['note'] = $value1->Dispositions[0]->Note;

                    if($data->Name=="Suspended/Out of School")
                    {
                        if(isset($value1->Dispositions[0]->StartDateTime) && isset($value1->Dispositions[0]->EndDateTime))
                        {
                            if($value1->Dispositions[0]->EndDateTime != "" && $value1->Dispositions[0]->StartDateTime != "")
                            {
                                $dataArr['suspend_days'] = daydiff($value1->Dispositions[0]->StartDateTime, $value1->Dispositions[0]->EndDateTime);
                                $dataArr['startdate'] = $value1->Dispositions[0]->StartDateTime;
                                $dataArr['enddate'] = $value1->Dispositions[0]->EndDateTime;
                            }
                        }
                    }
                }
                else{
                    $dataArr['note'] = "";
                    $dataArr['disposition'] = "";
                    $dataArr['disposition_type'] = "";
                    //$dataArr['startdate'] = "";
                    //  $dataArr['enddate'] = "";
                }
                //print_r($value1);exit;
                if(isset($value1->DisciplinaryActions[0]))
                {
                    $dispActionId = $value1->DisciplinaryActions[0]->DisciplinaryActionId;
                    $endpoint = "disciplinaryActions/".$dispActionId;
                    $data = fetch_inow_details($endpoint);
                    $dataArr['actioncode'] = $data->Code;
                    $dataArr['actionname'] = $data->Name;
                }
                else
                {
                    $dataArr['actioncode'] = "";
                    $dataArr['actionname'] = "";

                }
                
                if(isset($value1->Infractions))
                {
                    $infractionid = $value1->Infractions[0]->InfractionId;
                    if(isset($infraction_hash[$infractionid]))
                    {
                        $dataArr['infraction_code'] = $infraction_hash[$infractionid]->Code;
                        $dataArr['infraction_name'] = $infraction_hash[$infractionid]->Name;

                    }
                    else
                    {
                        $dataArr['infraction_code'] = "";
                        $dataArr['infraction_name'] = "";
                        
                    }
                }
                else{
                    $dataArr['infraction_code'] = "";
                        $dataArr['infraction_name'] = "";
                }

                if($value1->IncidentId != '')
                {
                    $endpoint = "incidents/".$value1->IncidentId;
                    $data = fetch_inow_details($endpoint);
                    if(!empty($data))
                    {
                        $dataArr['incident_number'] = $data->IncidentNumber;
                    }
                    else
                    {
                        $dataArr['incident_number'] = "";
                    }
                }
                else
                {
                    $dataArr['incident_number'] = "";
                }
                $dataArr['student_id'] = $value;
				$dataArr['stateID'] = $arr1[$key];
				
				$insertSql = "INSERT INTO student_cdi_details SET ";
                $exclude = array("note","actioncode");
                foreach($dataArr as $skey=>$svalue)
                {
                    if(!in_array($skey, $exclude))
                    {
                        $insertSql .= $skey." = '".$svalue."',";
                    }
                }
                $insertSql = trim($insertSql,",");
                //echo $insertSql;exit;

                $res = $objDB->insert($insertSql);

            }
			
			
			
			$stateID = $arr1[$key];
			$SQL = "SELECT * FROM student_cdi_details WHERE student_id = '".$value."' AND date(datetime) >= '2019-08-01' AND date(datetime) <= '2020-07-31'";
			//echo $SQL;exit;
			$rs1 = $objDB->select($SQL);
			$BInfo = $CInfo = $DInfo = $EInfo = $Susp = $Days = 0;
			for($j=0; $j < count($rs1); $j++)
			{
				if($rs1[$j]['disposition_type'] == "Suspended/Out of School")
				{
					$Days += $rs1[$j]['suspend_days'];
					$Susp++;
				}
				if(substr($rs1[$j]['infraction_name'], 0, 1)=="B" || substr($rs1[$j]['infraction_name'], 0, 3)=="S-B")
				{
					$BInfo++;
				}
				if(substr($rs1[$j]['infraction_name'], 0, 1)=="C" || substr($rs1[$j]['infraction_name'], 0, 3)=="S-C")
				{
					$CInfo++;
				}
				if(substr($rs1[$j]['infraction_name'], 0, 1)=="D" || substr($rs1[$j]['infraction_name'], 0, 3)=="S-D")
				{
					$CInfo++;
				}
			}
			$data = array();
			$data['student_id'] = $value;
			$data['stateID'] = $arr1[$key];
			$data['b_info'] = $BInfo;
			$data['c_info'] = $CInfo;
			$data['d_info'] = $DInfo;
			$data['e_info'] = $EInfo;
			$data['susp'] = $Susp;
			$data['susp_days'] = $Days;
			
			

			$SQL = "INSERT INTO student_conduct_disciplinary SET ";
			foreach($data as $dkey=>$dvalue)
			{
				$SQL .= $dkey." = '".$dvalue."',";
			}
			$SQL = trim($SQL, ",");
			$rs = $objDB->insert($SQL);

           

        }
        else
        {
            $SQL = "INSERT INTO student_conduct_disciplinary SET stateID='".$arr1[$key]."', student_id = '".$arr[$key]."', b_info = 0, c_info = 0, d_info = 0, e_info = 0, susp = 0, susp_days = 0";
            $rsins = $objDB->sql_query($SQL);
        }
		  $SQL = "UPDATE student_session SET cdi_fetched = 'Y' WHERE stateID = '".$arr1[$key]."'";
             $rs1 = $objDB->sql_query($SQL);


    }
    
echo "Done";

?>
