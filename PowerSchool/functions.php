<?php
function getAccessToken($url, $clientID, $clientSecret) {

    $curl = curl_init();
    $authData = http_build_query(array(
        'client_id' => $clientID,
        'client_secret' => $clientSecret,
        'grant_type' => 'client_credentials'
    ));
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded'));
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $authData);
    curl_setopt($curl, CURLOPT_URL, $url . '/oauth/access_token');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    // $request = curl_getinfo($curl);
    
    $result = curl_exec($curl);
    print_r($result);exit;

    if (!$result) {
        echo 'Curl Error: ' . curl_error($curl) . '<br />';
        die("Connection Failure");
    }
    curl_close($curl);
    return $result;
}

function getPowerSchoolRecords($type, $accessTokenKey, $url, $sdata) {
    global $objDB;

    $race_aray = ['W'=>"White", "P"=>"Native Hawaiian/Pacific Islander", "I"=>"Asian", "M"=>"Two or More Races", "A"=>"American Indian/Alaskan Native", "B"=>"Black/African American"];

    $response = false;
    $queryName = '';
    switch ($type) {
        case 'enrolled_state_id_students':
            $queryName = 'org.magnet_jefferson.enrolled_state_id_students.hs_enrolled_state_id_students';
        break;
        case 'schools':
            $queryName = 'org.magnet_jefferson.schools.hs_schools';
        break;
        case 'students':
            $queryName = 'org.magnet_jefferson.students.hs_students';
        break;
        case 'studentrace':
            $queryName = 'org.magnet_jefferson.studentrace.hs_studentrace';
        break;
        case 'gen':
            $queryName = 'org.magnet_jefferson.gen.hs_gen';
        break;
        case 'cc':
            $queryName = 'org.magnet_jefferson.cc.hs_cc';
        break;
        case 'courses':
            $queryName = 'org.magnet_jefferson.courses.hs_courses';
        break;
        case 'storedgrades':
            $queryName = 'org.magnet_jefferson.storedgrades.hs_storedgrades';
        break;
        case 'student_storedgrades':
            $queryName = 'org.magnet_jefferson.student_storedgrades.hs_student_storedgrades';
        break;
        case 'pgfinalgrades':
            $queryName = 'org.magnet_jefferson.finalgrades.hs_finalgrades';
        break;
        case 'sections':
            $queryName = 'org.magnet_jefferson.sections.hs_sections';
        break;
        case 'terms':
            $queryName = 'org.magnet_jefferson.terms.hs_terms';
        break;
        case 'schoolstaff':
            $queryName = 'org.magnet_jefferson.schoolstaff.hs_schoolstaff';
        break;
        case 'users':
            $queryName = 'org.magnet_jefferson.teachers.hs_teachers';
        break;
        case 'fee':
            $queryName = 'org.magnet_jefferson.fee.hs_fee';
        break;
        case 'fees':
            $queryName = 'org.magnet_jefferson.fees.hs_fees';
        break;
        case 'feetype':
            $queryName = 'org.magnet_jefferson.feetype.hs_feetype';
        break;
        case 'schoolfee':
            $queryName = 'org.magnet_jefferson.schoolfee.hs_schoolfee';
        break;
        case 'coursefee':
            $queryName = 'org.magnet_jefferson.coursefee.hs_coursefee';
        break;
    }
    if (!empty($type) && $type == 'enrolled_state_id_students') {
        $payload = '{"Student_ID" : ' . $_GET['student_id'] . '}';
        $resource = $url . '/ws/schema/query/' . $queryName.'?pagesize=0';
        
        
        //Set options for POST call
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n".
                    "Authorization: Bearer $accessTokenKey\r\n",
                'content' => $payload
            )
        );

        $result = file_get_contents($resource, false, stream_context_create($opts));
        //Get the JSON data
        $jsonData = json_decode($result, true);


        if(!isset($jsonData['record']))
        {
            echo "No";
            exit;
        }
        $data = [];
        $item = $jsonData['record'][0];

        

        $next_school = $item['tables']['students']['next_school'];
        $SQL = "SELECT * FROM ps_schools WHERE school_id = '".$next_school."'";
        $rs = $objDB->sql_query($SQL);

        if(count($rs) > 0)
        {
            $data['next_school'] = $rs[0]['name'];
        }

        $schoo_id = $item['tables']['students']['schoolid'];
      // echo $schoo_id;exit;
        $SQL = "SELECT * FROM ps_schools WHERE school_id = '".$schoo_id."'";
        $rs = $objDB->sql_query($SQL);

        if(count($rs) > 0)
        {
            $data['current_school'] = $rs[0]['name'];
        }

        $sid = $item['tables']['students']['id'];

        //$race = $item['tables']['students']['ethnicity'];
        

        $rsRce = "SELECT * FROM ps_student_race WHERE studentid='".$item['tables']['students']['id']."'";
        $rsRceV = $objDB->sql_query($rsRce);
        $race = '';

        if(count($rsRceV) > 0)
        {
            if($rsRceV[0]['racecd'] != '')
            {
                $race = $race_aray[$rsRceV[0]['racecd']];
            }
        }
        else
        {
            $race = '';
        }



        
        // $SQL = "SELECT * FROM ps_general WHERE value = '".$race."'";
        // $rs = $objDB->sql_query($SQL);

        // if(count($rs) > 0)
          if($race != '')
         {
            if($item['tables']['students']['fedethnicity'] == '1')
            {
                $hispanic = " - Hispanic";
                $data['fedethnicity'] = 1;
            }
            else
            {
                $hispanic = " - Non-Hispanic";
                $data['fedethnicity'] = 0;
            }
            $data['race'] = $rs[0]['name'].$hispanic;
        }

        //$data['current_school'] = $item['tables']['students']['schoolid'];

        $data['birthday'] = $item['tables']['students']['dob'];
        $data['enroll_status'] = $item['tables']['students']['enroll_status'];
        $data['first_name'] = $item['tables']['students']['first_name'];
        $data['last_name'] = $item['tables']['students']['last_name'];
        $data['gender'] = $item['tables']['students']['gender'];
        $data['stateID'] = $item['tables']['students']['student_number'];
        $data['address'] = $item['tables']['students']['street'];
        $data['city'] = $item['tables']['students']['city'];
        $data['state'] = $item['tables']['students']['state'];
        $data['phone'] = $item['tables']['students']['home_phone'];
         $data['zip'] = $item['tables']['students']['zip'];

        $grade_level = $item['tables']['students']['grade_level'];
        if(in_array($grade_level, array("-4", "-3", "-2", "-1", "99")))
            $current_grade = "PreK";
        elseif(in_array($grade_level, array("0")))
            $current_grade = "K";
        else
            $current_grade = $grade_level;

        
        $data['current_grade'] = $current_grade;
        $data['middle_name'] = $item['tables']['students']['middle_name'];
        $data['student_id'] = $item['tables']['students']['id'];
        $data['dcid'] = $item['tables']['students']['dcid'];

        $SQL = "SELECT * FROM student WHERE stateID = '".$data['stateID']."'";
        $rs = $objDB->select($SQL);

        if(count($rs) > 0)
        {
            $SQL = "UPDATE student SET ";
        }
        else
        {
            $SQL = "INSERT INTO student SET ";
        }

//                $SQL = "INSERT INTO student SET ";
        foreach($data as $k=>$v)
                {
                    if($k != "id")
                    $SQL .= $k.' = "'.$v.'",';
                }
        $SQL = trim($SQL, ",");
        if(count($rs) > 0)
        {
            $SQL .= ", updated_at = '".date("Y-m-d H:i:s")."'";
            $SQL .= " WHERE stateID = '".$data['stateID']."'";
        }
        else
        {
        $SQL .= ", created_at = '".date("Y-m-d H:i:s")."'";
        }

        
        //$SQL .= ", created_at = '".date("Y-m-d H:i:s")."'";

        $rs = $objDB->sql_query($SQL);
        echo "Yes";
        exit;
    }
    if(!empty($type) && $type == 'studentrace')
    {
        $resource = $url . '/ws/schema/query/' . $queryName.'?pagesize=0';
        
        $payload = '{}';
        
        
        //Set options for POST call
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n".
                    "Authorization: Bearer $accessTokenKey\r\n",
                'content' => $payload
            )
        );
        //echo "<pre>"; print_r($opts); exit;
        //Call the server's oauth gateway
        $result = file_get_contents($resource, false, stream_context_create($opts));
        //Get the JSON data
        $jsonData = json_decode($result, true);
               

               foreach ($jsonData['record'] as $item) {
                $gdata = [];
                $gdata['studentid'] = $item['tables']['studentrace']['studentid'];
                $gdata['racecd'] = $item['tables']['studentrace']['racecd'];
                $gdata['id'] = $item['tables']['studentrace']['id'];
                $gdata['dcid'] = $item['tables']['studentrace']['dcid'];
               

                $SQL = "INSERT INTO ps_student_race SET ";
                foreach($gdata as $k=>$v)
                {
                    $SQL .= $k.' = "'.$v.'",';
                }
                echo $SQL;exit;
                $SQL = trim($SQL, ",");
                $rs = $objDB->sql_query($SQL);
            }
    }
    elseif (!empty($type) && $type == "schools") {
        $resource = $url . '/ws/schema/query/' . $queryName.'?pagesize=0';
        $payload = '{}';
        

        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n".
                    "Authorization: Bearer $accessTokenKey\r\n",
                'content' => $payload
            )
        );
        //echo "<pre>"; print_r($opts); exit;
        //Call the server's oauth gateway
        $result = file_get_contents($resource, false, stream_context_create($opts));
        //Get the JSON data
        $jsonData = json_decode($result, true);
       // echo "<pre>"; print_r($jsonData); exit;
        
        //Collapse the array a bit if there is data
        $hsRecords = array();
        $exist = [];
        if (isset($jsonData['record'])) {
            foreach ($jsonData['record'] as $item) {
                $gdata = [];
                $gdata['name'] = $item['tables']['schools']['name'];
                $gdata['dcid'] = $item['tables']['schools']['dcid'];
                // $gdata['id'] = 
                $gdata['school_id'] = $item['tables']['schools']['school_number'];
                $gdata['schoolzip'] = $item['tables']['schools']['schoolzip'];
                $gdata['low_grade'] = $item['tables']['schools']['low_grade'];
                $gdata['high_grade'] = $item['tables']['schools']['high_grade'];

                $SQL = "INSERT INTO ps_schools SET ";
                foreach($gdata as $k=>$v)
                {
                    $SQL .= $k.' = "'.$v.'",';
                }
                $SQL = trim($SQL, ",");
                $rs = $objDB->sql_query($SQL);
            }
        }

    }
    else if (!empty($type) && $type == "gen") {
        $resource = $url . '/ws/schema/query/' . $queryName.'?pagesize=0';
        $payload = '{}';
        

        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n".
                    "Authorization: Bearer $accessTokenKey\r\n",
                'content' => $payload
            )
        );
        //echo "<pre>"; print_r($opts); exit;
        //Call the server's oauth gateway
        $result = file_get_contents($resource, false, stream_context_create($opts));
        //Get the JSON data
        $jsonData = json_decode($result, true);
              //  echo "<pre>"; print_r($jsonData); exit;
        
        //Collapse the array a bit if there is data
        $hsRecords = array();
        $exist = [];
        if (isset($jsonData['record'])) {
            foreach ($jsonData['record'] as $item) {
                $gdata = [];
                $gdata['value2'] = $item['tables']['gen']['value2'];
                $gdata['dcid'] = $item['tables']['gen']['dcid'];
                $gdata['valuet'] = $item['tables']['gen']['valuet'];
                $gdata['cat'] = $item['tables']['gen']['cat'];
                $gdata['name'] = $item['tables']['gen']['name'];
                $gdata['id'] = $item['tables']['gen']['id'];
                $gdata['value'] = $item['tables']['gen']['value'];

                if($gdata['cat'] == "ethnicity")
                {

                            $SQL = "INSERT INTO ps_general SET ";
                            foreach($gdata as $k=>$v)
                            {
                                $SQL .= $k.' = "'.$v.'",';
                            }
                            $SQL = trim($SQL, ",");
                            $rs = $objDB->sql_query($SQL);
                    
                }
            }
        }

    }
    else if (!empty($type) && $type == "student_storedgrades") {

        $resource = $url . '/ws/schema/query/' . $queryName.'?pagesize=0';
        $payload = '{}';
        if ($type == 'student_storedgrades') {
            $payload = '{"StudentID" : '.$sdata['student_id'].'}';
        }
        
        
        //Set options for POST call
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n".
                    "Authorization: Bearer $accessTokenKey\r\n",
                'content' => $payload
            )
        );
        //echo "<pre>"; print_r($opts); exit;
        //Call the server's oauth gateway
        $result = file_get_contents($resource, false, stream_context_create($opts));
        //Get the JSON data
        $jsonData = json_decode($result, true);
        echo "<pre>"; print_r($jsonData); exit;


        //Collapse the array a bit if there is data
        $hsRecords = array();
        $exist = [];
        if (isset($jsonData['record'])) {
            foreach ($jsonData['record'] as $item) {

                $term = $item['tables']['storedgrades']['termid'];

                $academicYear = ($term/100) + 1990;
                $yrid = $academicYear;// ."-".($academicYear+1);
                 

                    //$yrid = (1990 + $term_data[0]['yearid'];
                $term = $yrid . "-".($yrid+1);
                $gdata = [];
                $gdata['course_number'] = $item['tables']['storedgrades']['course_number'];
                $gdata['credit_type'] = $item['tables']['storedgrades']['credit_type'];
                $gdata['grade_level'] = $item['tables']['storedgrades']['grade_level'];
                $gdata['studentid'] = $sdata['student_id'];
                $gdata['course_name'] = $item['tables']['storedgrades']['course_name'];
                $gdata['academicYear'] = $term;
                $gdata['numericGrade'] = $item['tables']['storedgrades']['percent'];
                $gdata['teacher_name'] = $item['tables']['storedgrades']['teacher_name'];
                $gdata['gpa_points'] = $item['tables']['storedgrades']['gpa_points'];
                $gdata['gradescale_name'] = $item['tables']['storedgrades']['gradescale_name'];
                $gdata['grade'] = $item['tables']['storedgrades']['grade'];
                $gdata['sectionid'] = $item['tables']['storedgrades']['sectionid'];
                $gdata['schoolname'] = $item['tables']['storedgrades']['schoolname'];
                $gdata['storecode'] = $item['tables']['storedgrades']['storecode'];

                foreach($gdata as $k=>$v)
                {
                    echo $v."^";
                }
                echo "<br>";




            }
        }
    }
    else if (!empty($type) && $type == "students") 
    {
        $resource = $url . '/ws/schema/query/' . $queryName.'?pagesize=0';
        
        $payload = '{}';
        
        
        //Set options for POST call
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n".
                    "Authorization: Bearer $accessTokenKey\r\n",
                'content' => $payload
            )
        );
        //echo "<pre>"; print_r($opts); exit;
        //Call the server's oauth gateway
        $result = file_get_contents($resource, false, stream_context_create($opts));
        // $curl = curl_init();
        // curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        //         "Content-Type: application/json",
        //                     "Authorization: Bearer $accessTokenKey"
        //     ));
        // curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        // curl_setopt($curl, CURLOPT_URL, $resource);
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //     curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        //     $result = curl_exec($curl);
                    

        //Get the JSON data
        $jsonData = json_decode($result, true);

        // $fields = array_keys($jsonData['record'][0]['tables']['students']);
        // foreach($fields as $val)
        // {
        //      echo $val."^";
        // }
        // echo "<br>";
        // foreach ($jsonData['record'] as $item) {
        //      $tmp = $item['tables']['students'];
        //      foreach($fields as $val)
        //      {
        //          echo $tmp[$val]."^";
        //      }
        //      echo "<br>";
        //  }
        //  exit;
  
        //Collapse the array a bit if there is data
        $hsRecords = array();
        $exist = [];

            foreach ($jsonData['record'] as $item) {

                if($item['tables']['students']['enroll_status'] == "0")
                {
                    $next_school = $item['tables']['students']['next_school'];
                    $SQL = "SELECT * FROM ps_schools WHERE school_id = '".$next_school."'";
                    $rs = $objDB->sql_query($SQL);

                    if(count($rs) > 0)
                    {
                        $data['next_school'] = $rs[0]['name'];
                    }

                    $schoo_id = $item['tables']['students']['schoolid'];
                  // echo $schoo_id;exit;
                    $SQL = "SELECT * FROM ps_schools WHERE school_id = '".$schoo_id."'";
                    $rs = $objDB->sql_query($SQL);



                    if(count($rs) > 0)
                    {
                        $data['current_school'] = $rs[0]['name'];
                    }

                    $sid = $item['tables']['students']['id'];
                           // $race = $item['tables']['students']['ethnicity'];


                    $rsRce = "SELECT * FROM ps_student_race WHERE studentid='".$item['tables']['students']['id']."'";
                    $rsRceV = $objDB->sql_query($rsRce);
                    $race = '';

                    if(count($rsRceV) > 0)
                    {
                        if($rsRceV[0]['racecd'] != '')
                        {
                            $race = $race_aray[$rsRceV[0]['racecd']];
                        }
                    }
                    else
                    {
                        $race = '';
                    }



                    
                    // $SQL = "SELECT * FROM ps_general WHERE value = '".$race."'";
                    // $rs = $objDB->sql_query($SQL);

                    // if(count($rs) > 0)
                      if($race != '')
                     {
                        if($item['tables']['students']['fedethnicity'] == '1')
                        {
                            $hispanic = " - Hispanic";
                            $data['fedethnicity'] = 1;
                        }
                        else
                        {
                            $hispanic = " - Non-Hispanic";
                            $data['fedethnicity'] = 0;
                        }
                        $data['race'] = $race.$hispanic;
                        $data['race_char'] = $race;
                    }


                    
                    // $SQL = "SELECT * FROM ps_general WHERE value = '".$race."'";
                    // $rs = $objDB->sql_query($SQL);

                    // if(count($rs) > 0)
                    // {
                    //     if($item['tables']['students']['fedethnicity'] == '1')
                    //     {
                    //         $hispanic = " - Hispanic";
                    //         $data['fedethnicity'] = 1;
                    //     }
                    //     else
                    //     {
                    //         $hispanic = " - Non-Hispanic";
                    //         $data['fedethnicity'] = 0;
                    //     }
                    //     $data['race'] = $rs[0]['name'].$hispanic;
                    // }


                    // if(count($rs) > 0)
                    // {
                    //     if($item['tables']['students']['fedethnicity'] == '1')
                    //         $hispanic = " - Hispanic";
                    //     else
                    //         $hispanic = " - Non-Hispanic";
                    //     $data['race'] = $rs[0]['name'].$hispanic;
                    // }

                    //$data['current_school'] = $item['tables']['students']['schoolid'];

                    $data['birthday'] = $item['tables']['students']['dob'];
                    $data['enroll_status'] = $item['tables']['students']['enroll_status'];
                    $data['first_name'] = $item['tables']['students']['first_name'];
                    $data['last_name'] = $item['tables']['students']['last_name'];
                    $data['gender'] = $item['tables']['students']['gender'];
                    $data['stateID'] = $item['tables']['students']['student_number'];
                    $data['address'] = $item['tables']['students']['street'];
                    $data['city'] = $item['tables']['students']['city'];
                    $data['state'] = $item['tables']['students']['state'];
                    $data['phone'] = $item['tables']['students']['home_phone'];
                     $data['zip'] = $item['tables']['students']['zip'];

                    $grade_level = $item['tables']['students']['grade_level'];
                    if(in_array($grade_level, array("-4", "-3", "-2", "-1", "99")))
                        $current_grade = "PreK";
                    elseif(in_array($grade_level, array("0")))
                        $current_grade = "K";
                    else
                        $current_grade = $grade_level;

                    
                    $data['current_grade'] = $current_grade;
                    $data['middle_name'] = $item['tables']['students']['middle_name'];
                    $data['student_id'] = $item['tables']['students']['id'];
                    $data['dcid'] = $item['tables']['students']['dcid'];

              

                
              

                $SQL = "SELECT * FROM student WHERE stateID = '".$data['stateID']."'";
                $rs = $objDB->select($SQL);

                if(count($rs) > 0)
                {
                    $SQL = "UPDATE student SET ";
                }
                else
                {
                    $SQL = "INSERT INTO student SET ";
                }

//                $SQL = "INSERT INTO student SET ";
                foreach($data as $k=>$v)
                        {
                            if($k != "id")
                            $SQL .= $k.' = "'.addslashes($v).'",';
                        }
                $SQL = trim($SQL, ",");
                if(count($rs) > 0)
                {
                    $SQL .= ", updated_at = '".date("Y-m-d H:i:s")."'";
                    $SQL .= " WHERE stateID = '".$data['stateID']."'";
                }
                else
                {
                $SQL .= ", created_at = '".date("Y-m-d H:i:s")."'";
                }
                //$SQL .= ", created_at = '".date("Y-m-d H:i:s")."'";

                $rs = $objDB->sql_query($SQL);
                }




            } ///s
    }

//    return $response;
}

