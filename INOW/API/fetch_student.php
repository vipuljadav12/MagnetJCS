<?php
    set_time_limit(0);
	ini_set("error_reporting", "1");
    ini_set('memory_limit','2048M');
    
    include("../common_functions.php");
    
    
    include("../dbClass.php");
    $objDB = new MySQLCN;

    $id = $_REQUEST['id'];

    if($id != "")
    {
        $SQL = "SELECT * FROM submissions WHERE student_id = '".$id."'";
        $rs = $objDB->select($SQL);
        if(count($rs) > 0)
        {
            echo "Student Application Already Subimtted";
            exit;
        }
        else
        {
            $SQL = "SELECT stateID, studentFileNumber FROM student WHERE stateID='".$id."'";
            $rs = $objDB->select($SQL);
            if(count($rs) > 0)
            {
                $schools_hash = array();
                $schools = fetch_inow_details('schools');
                foreach( $schools as $school ){
                    $schools_hash[ $school->SchoolNumber ] = $school->Name;
                }

                $endpoint = "students/details?studentNumber=".$rs[0]['studentFileNumber'];
                $dataNew = fetch_inow_details($endpoint);

                $student = $dataNew[0];

                $data = array();
                $data['first_name'] = $student->FirstName;
                $data['last_name'] = $student->LastName;
                $data['middle_name'] = $student->MiddleName;
                $data['address'] = $student->MailingAddressLine1;
                $data['city'] = $student->MailingCity;
                $data['state'] = $student->MailingState;
                $data['zip'] = $student->MailingZip;
                $data['gender'] = ($student->Sex == "M" ? "Male" : "Female");
                $data['parent_first_name'] = $student->GuardianFirstName;
                $data['parent_last_name'] = $student->GuardianLastName;
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
                    $data['current_grade'] = $current_grade;
                $data['email'] = $student->GuardianEmail;
                $data['mcps_email'] = $student->Username;

                $data['phone'] = $student->HomePhoneNumber;
                $data['work_phone'] = $student->WorkPhoneNumber;
                $data['current_school'] = (isset($schools_hash[$dataNew[0]->SchoolNumber])) ? $schools_hash[$dataNew[0]->SchoolNumber] : '';
                $data['birthday'] = date("Y-m-d", strtotime($student->DateofBirth));
                
                //$data['birthday'] = $date[0];

                $SQL = "UPDATE student_session SET ";
                foreach($data as $key=>$value)
                {
                    $SQL .= $key.' = "'.addslashes($value).'",';
                }
                $SQL = trim($SQL, ",");
                $SQL .= " WHERE stateID = '".$id."'";
                $rs = $objDB->sql_query($SQL);
                $data['birthday'] = date("m/d/Y", strtotime($data['birthday']));
                echo json_encode($data); 
                exit;
            }

        }
    }

    
echo "Done";

?>
