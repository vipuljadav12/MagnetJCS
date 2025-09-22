<?php
    set_time_limit(0);
    $states = [
        '',
        'AL',
        'AK',
        'AZ',
        'AR',
        'CA',
        'CO',
        'CT',
        'DE',
        'DC',
        'FL',
        'GA',
        'HI',
        'ID',
        'IL',
        'IN',
        'IA',
        'KS',
        'KY',
        'LA',
        'ME',
        'MD',
        'MA',
        'MI',
        'MN',
        'MS',
        'MO',
        'MT',
        'NE',
        'NV',
        'NH',
        'NJ',
        'NM',
        'NY',
        'NC',
        'ND',
        'OH',
        'OK',
        'OR',
        'PA',
        'PR',
        'RI',
        'SC',
        'SD',
        'TN',
        'TX',
        'UT',
        'VT',
        'VA',
        'WA',
        'WV',
        'WI',
        'WY',
    ];
    ini_set('memory_limit','1G');
    include("common_functions.php");
    
    include("dbClass.php");
    $objDB = new MySQLCN;

    $SQL = "INSERT INTO student_cron SET txt='cron_started'";
    $rs = $objDB->sql_query($SQL);

    $endpoint = "students/details?requestDate=".date("Y-m-d");
    $data = fetch_inow_details($endpoint);

    /* Fetch Ethnicities of district */
    $ethnicities = fetch_inow_details( 'ethnicities' );
    foreach( $ethnicities as $ethnicity ){
        $race_hash[ $ethnicity->Id ] = $ethnicity->Name;
    }

    $student_race_hash = array();
    $ethnicities = fetch_inow_details( 'persons/ethnicities' );
    foreach( $ethnicities as $ethnicity ){
        if( $ethnicity->IsPrimary ) {
            $student_race_hash[$ethnicity->PersonId] = $race_hash[ $ethnicity->EthnicityId ];
        }
    }

    $schools_hash = array();
    $schools = fetch_inow_details('schools');
    foreach( $schools as $school ){
        $schools_hash[ $school->SchoolNumber ] = $school->Name;
    }

    $grade_level_hash = array();
    $grade_levels = fetch_inow_details( 'gradelevels' );
    
    foreach( $grade_levels as $grade_level ){
        $grade_level_hash[ $grade_level->Id ] = intval( $grade_level->Name );
    }

    $endpoint = "students/details?requestDate=".date("Y-m-d");
    $data = fetch_inow_details($endpoint);
    foreach($data as $key=>$student)
    {
        $SQL = "SELECT stateID FROM student WHERE stateID = ".$student->StateIdNumber."";
        $rs2 = $objDB->select($SQL);
        
            $endpoint = "students/".$student->StudentNumber;
            $data = fetch_inow_details($endpoint);

            $endpoint = "students/".$student->StudentId;
            $dataInd = fetch_inow_details($endpoint);
            $dataNew = array();
            $dataNew['first_name'] = $student->FirstName;
            $dataNew['last_name'] = $student->LastName;
            $dataNew['middle_name'] = $student->MiddleName;
            $dataNew['IsHispanic'] = ($dataInd->IsHispanic != '' ? 1 : 0);
            $dataNew['studentFileNumber'] = $student->StudentNumber;
            $dataNew['stateID'] = $student->StateIdNumber;
            $dataNew['student_id'] = $student->StudentId;
            $dataNew['race'] = isset($student_race_hash[$student->StudentId]) ? $student_race_hash[$student->StudentId] : '';
            $dataNew['birthday'] = date("Y-m-d", strtotime($student->DateofBirth));
            $dataNew['address'] = $student->AddressLine1;
            $dataNew['city'] = $student->City;
            $dataNew['state'] = $student->State;
            $dataNew['zip'] = $student->Zip;
            $dataNew['gender'] = ($student->Sex == "M" ? "Male" : "Female");
            $dataNew['parent_first_name'] = $student->GuardianFirstName;
            $dataNew['parent_last_name'] = $student->GuardianLastName;


            $current_grade = intval($student->Grade);
            if($current_grade == 97)
            {
                $dataNew['current_grade'] = "ASK-97";
            }
            elseif($current_grade == 98)
            {
                $dataNew['current_grade'] = "ASK-98";
            }
            elseif($current_grade == 99)
            {
                $dataNew['current_grade'] = "PreK";
            }
            elseif($current_grade == 0)
            {
                $dataNew['current_grade'] = "K";
            }
            else
                $dataNew['current_grade'] = $current_grade;

            $dataNew['email'] = $student->GuardianEmail;
            $dataNew['phone'] = $student->HomePhoneNumber;
            $dataNew['work_phone'] = $student->WorkPhoneNumber;
            $dataNew['current_school'] = (isset($schools_hash[$student->SchoolNumber])) ? $schools_hash[$student->SchoolNumber] : '';

            $dataNew['mcps_email'] = $student->Username;
        if(count($rs2) <= 0)
        {
            $SQL = "INSERT INTO student SET ";
            foreach($dataNew as $dnkey=>$dnvalue)
            {
                $SQL .= $dnkey.' = "'.addslashes($dnvalue).'",';
            }
            $SQL = trim($SQL, ",");
            $SQL .= ", created_at='".date("Y-m-d H:i:s")."'";
            $rs = $objDB->insert($SQL);      

        }
        else{
            $SQL = "UPDATE student SET ";
            foreach($dataNew as $dnkey=>$dnvalue)
            {
                $SQL .= $dnkey.' = "'.addslashes($dnvalue).'",';
            }
            $SQL = trim($SQL, ",");
            $SQL .= ", updated_at='".date("Y-m-d H:i:s")."'";
            $SQL .= " WHERE stateID = ".$student->StateIdNumber."";
            
            $rs = $objDB->sql_query($SQL);      

        }        

    }
    echo "Done";
    exit;
    
?>