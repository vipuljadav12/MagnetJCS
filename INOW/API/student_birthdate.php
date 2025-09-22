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
                $data['birthday'] = date("Y-m-d", strtotime($student->DateofBirth));
                $SQL = "UPDATE student_session SET ";
                foreach($data as $key=>$value)
                {
                    $SQL .= $key.' = "'.addslashes($value).'",';
                }
                $SQL = trim($SQL, ",");
                $SQL .= " WHERE stateID = '".$id."'";
               // $rs = $objDB->sql_query($SQL);
                echo json_encode($data); 
                exit;
            }

        }
    }

    
echo "Done";

?>
