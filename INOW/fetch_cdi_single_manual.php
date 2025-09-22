`
<?php
    set_time_limit(0);
	ini_set("error_reporting", "1");
    ini_set('memory_limit','2048M');
    
    include("common_functions.php");
    
    
    include("dbClass.php");
    $objDB = new MySQLCN;
    

    $stateID = $_REQUEST['id'];

    $SQL = "SELECT stateID, student_id FROM student WHERE stateID = '".$stateID."'";
    $rs = $objDB->select($SQL);

    $SQL = "SELECT * FROM submission_conduct_discplinary_info WHERE stateID = '".$stateID."'";
    $rs1 = $objDB->select($SQL);

    if(count($rs1) > 0)
    {
        echo "Student CDI already exists";
        exit;
    }
    else
    {

        $arr = $arr1 = array();
        for($i=0; $i < count($rs); $i++)
        {
            $arr[] = $rs[$i]['student_id'];
            $arr1[] = $rs[$i]['stateID'];
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

    	$infraction_codeArr = array();
        foreach($arr as $key=>$value)
        {

            $endpoint = "students/".$value."/disciplinaryOccurrences";
            $dispData = fetch_inow_details($endpoint);

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
    			$SQL = "SELECT * FROM student_cdi_details WHERE student_id = '".$value."' AND date(datetime) >= '2019-08-01' AND date(datetime) <= '2020-07-31'";//" AND ".date("Y-m-d", mktime(0,0,0, 9, 1, (date("Y")-1))) ."' AND (year(datetime) = '".date("Y")."' OR year(datetime) = '".(date("Y")-1)."')";
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

    		$SQL = "UPDATE student SET cdi_fetched = 'Y' WHERE stateID = '".$arr1[$key]."'";
            $rs1 = $objDB->sql_query($SQL);

            $SQL = "UPDATE submissions SET cdi_exists = 'Y' WHERE student_id = '".$arr1[$key]."'";
            $rs1 = $objDB->sql_query($SQL);

        }
        
        echo "Student CDI fetched successfully";
        exit;
    }

?>
