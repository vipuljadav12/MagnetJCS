<?php
    set_time_limit(0);
    ini_set('max_execution_time', 0);
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

    $SQL = "SELECT * FROM student_cron WHERE txt = 'cron_started'";
    $rs = $objDB->select($SQL);
    if(count($rs) > 0)
    {
        exit;
    }

    $SQL = "INSERT INTO student_cron SET txt = 'cron_started'";
    $rs = $objDB->insert($SQL);

    /* Fetch Ethnicities of district */
    $ethnicities = fetch_inow_details( 'ethnicities' );
    foreach( $ethnicities as $ethnicity ){
        $race_hash[ $ethnicity->Id ] = $ethnicity->Name;
    }

    // Get all student ethnicities
    $student_race_hash = array();
    $ethnicities = fetch_inow_details( 'persons/ethnicities' );
    foreach( $ethnicities as $ethnicity ){
        if( $ethnicity->IsPrimary ) {
            $student_race_hash[$ethnicity->PersonId] = $race_hash[ $ethnicity->EthnicityId ];
        }
    }

    //print_r($student_race_hash);exit;

    // Get all student addresses
    $student_address_hash = array();
    /*$addresses = fetch_inow_details( 'persons/addresses' );
    foreach( $addresses as $address ){

        if( $address->IsPhysical ) {
            $student_address_hash[ $address->PersonId ] = [
                'address' => implode( ' ', [ $address->AddressLine1, $address->AddressLine2 ] ),
                'city' => $address->City,
                'state' => ( isset( $states[ $address->StateId ] ) ) ? $states[ $address->StateId ] : 'AL',
                'zip' => $address->PostalCode
            ];
        }
    }*/

    // Get all student email addresses
    $student_email_hash = array();
   /* $emails = fetch_inow_details('persons/emailaddresses' );
    foreach( $emails as $email ){

        if( $email->IsPrimary ) {
            $student_email_hash[ $email->PersonId ] = $email->EmailAddress;
        }
    }*/

    // Get all schools
    $schools_hash = array();
    $schools = fetch_inow_details('schools');
    foreach( $schools as $school ){
        $schools_hash[ $school->SchoolNumber ] = $school->Name;
    }

    // Get all Grade Levels
    $grade_level_hash = array();
    $grade_levels = fetch_inow_details( 'gradelevels' );
    
    foreach( $grade_levels as $grade_level ){
        $grade_level_hash[ $grade_level->Id ] = intval( $grade_level->Name );
    }
//print_r($grade_level_hash);exit;
     // Get all Genders
    $gender_hash = array();
    $genders = fetch_inow_details( 'genders' );
    foreach( $genders as $gender ){
        $gender_hash[ $gender->Id ] = $gender->Name;
    }

    // Get all Academic Sessions
    $sessions_hash = [
        'current' => [],
        'late' => []
    ];
    $acad_sessions = fetch_inow_details( 'acadsessions' );

    foreach($acad_sessions as $acad_session){
            if( $acad_session->AcadYear == date('Y')+1 ){
                $sessions_hash['current'][ $acad_session->Id ] = $acad_session;
            } else if( $acad_session->AcadYear == date('Y')  ){
                $sessions_hash['last'][ $acad_session->Id ] = $acad_session;
            }
        }

        uasort( $sessions_hash['current'], function ($a, $b)
        {
            if ($a->StartDate == $b->StartDate) {
                return 0;
            }
            return ($a->StartDate < $b->StartDate) ? 1 : -1;
        });

        uasort( $sessions_hash['last'], function ($a, $b)
        {
            if ($a->StartDate == $b->StartDate) {
                return 0;
            }
            return ($a->StartDate < $b->StartDate) ? 1 : -1;
        });

        $new = 0;
        $student_objects = array();
        $duplicates = [];
         $enrollment_statuses = [
            'Enrolled',
            'Registered'
        ];

        $find_students = array();
         foreach ($sessions_hash as $sessions) {

            foreach( $enrollment_statuses as $status ) {

                foreach ($sessions as $session_id => $acad_session) {
                     $students = fetch_inow_details($session_id . '/students?status=' . $status);

                     if (is_object($students)) {
                        $students = array();
                    }
                    

                    // $find_students = [];
                    foreach ($students as $index => $student) {

                        if (!is_object($student)) {
                            unset($students[$index]);
                        } else {

                            // write here to code about duplicate student entry
                            //$find_students[] = $student->StateIdNumber;
                        }
                    }
                    
                    foreach ($students as $student) {


                        if($student->StateIdNumber != '' && !in_array($student->StateIdNumber, $find_students))
                        {
                            $SQL = "INSERT INTO acad_sessions SET session_id = '".$session_id."'";
                            //$rsSession = $objDB->insert($SQL);
                            $SQL = "SELECT stateID FROM student_copy WHERE stateID = ".$student->StateIdNumber."";
                            
                            $rs2 = $objDB->select($SQL);
                            echo count($rs2)."<BR>";
                            if(count($rs2) > 0)
                            	continue; 
                            
                                $find_students[] = $student->StateIdNumber;
                              
                                $data = array();
                                $data['IsHispanic'] = ($student->IsHispanic != '' ? 1 : 0);
                                $data['studentFileNumber'] = $student->StudentNumber;
                                $data['stateID'] = $student->StateIdNumber;
                                $data['student_id'] = $student->Id;
                                
                                $data['race'] = isset($student_race_hash[$student->Id]) ? $student_race_hash[$student->Id] : '';
                                
                                $data['birthday'] = $student->DateOfBirth;

                                $endpoint = "students/details?studentNumber=".$student->StudentNumber;
                                $dataNew = fetch_inow_details($endpoint);

                                //echo "<pre>";
                                //print_r($dataNew);exit;

                                $data['first_name'] = $student->FirstName;
                                $data['last_name'] = $student->LastName;
                                $data['middle_name'] = $student->MiddleName;
                                $data['address'] = $dataNew[0]->MailingAddressLine1;
                                $data['city'] = $dataNew[0]->MailingCity;
                                $data['state'] = $dataNew[0]->MailingState;
                                $data['zip'] = $dataNew[0]->MailingZip;
                                $data['gender'] = ($dataNew[0]->Sex == "M" ? "Male" : "Female");
                                $data['parent_first_name'] = $dataNew[0]->GuardianFirstName;
                                $data['parent_last_name'] = $dataNew[0]->GuardianLastName;
                                $current_grade = intval($dataNew[0]->Grade);
                                if($current_grade == 97)
                                {
                                    $data['current_grade'] = "ASK-97";
                                }
                                elseif($current_grade == 98)
                                {
                                    $data['current_grade'] = "ASK-98";
                                }
                                elseif($current_grade == 99)
                                {
                                    $data['current_grade'] = "PreK";
                                }
                                elseif($current_grade == 0)
                                {
                                    $data['current_grade'] = "K";
                                }
                                else
                                {
                                    $data['current_grade'] = $current_grade;
                                }

                                $data['email'] = $dataNew[0]->GuardianEmail;
                                $data['mcps_email'] = $dataNew[0]->Username;

                                $data['phone'] = $dataNew[0]->HomePhoneNumber;
                                $data['work_phone'] = $dataNew[0]->WorkPhoneNumber;
                                $data['current_school'] = (isset($schools_hash[$dataNew[0]->SchoolNumber])) ? $schools_hash[$dataNew[0]->SchoolNumber] : '';
                               
                               
                                if(count($rs2) <= 0) 
                                {
                                    $SQL = "INSERT INTO student_copy SET ";
                                }
                                else
                                {
                                    $SQL = "UPDATE student_copy SET ";
                                }
                                foreach($data as $key=>$value)
                                {
                                    $SQL .= $key.' = "'.addslashes($value).'",';
                                }
                                $SQL = trim($SQL, ",");
                                if (count($rs2) > 0) 
                                {
                                    $SQL .= ", updated_at='".date("Y-m-d H:i:s")."' WHERE stateID = '".$data['stateID']."'";
                                    $rs = $objDB->sql_query($SQL);      

                                }
                                else
                                {
                                    $SQL .= ", created_at='".date("Y-m-d H:i:s")."'";
                                    $rs = $objDB->insert($SQL);      

                                }  
                                //$student_objects[] = $student->StateIdNumber;   
                                                            
                        }
                    }
                }
            }
        }
        $SQL = "DELETE FROM student_cron WHERE txt = 'cron_started'";
        $rs = $objDB->sql_query($SQL);

//    print_r($student_address_hash);exit;

?>