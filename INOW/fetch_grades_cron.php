<?php
ini_set("error_reporting","1");
    set_time_limit(0);
    ini_set('memory_limit','4196M');
    
    include("common_functions.php");     
    include("dbClass.php");
    $objDB = new MySQLCN;

    $stateID = $_REQUEST['id'];

    $courseType = array("31" => "Academic","36" => "Art","26" => "ArtA","16" => "Business","27" => "Career/technical","43" => "Career\Technical Education","39" => "Computer","10" => "Computer Application","42" => "Cooperative Career\Tech Ed","41" => "Coordinated Studies","2" => "Elective","3" => "English","17" => "Fam/Cons Science","11" => "Fine Arts","12" => "Foreign Language","14" => "Health","34" => "Hearing Impaired","22" => "KeyboardingKB","21" => "Learning Strategies","4" => "Math","37" => "Music","1" => "NA","23" => "No Valid Code","32" => "Non Credit","5" => "Physical Education","24" => "Planning Period","40" => "Reading","6" => "READINGRDG","18" => "ROTC","7" => "Science","8" => "Social Skills","9" => "Social Studies","28" => "Special Ed Math","29" => "Special Ed Science","30" => "Special Ed Soc Stds","33" => "Special Ed. Health","20" => "Special Education","19" => "Special EducationSPE","38" => "Spelling","35" => "Teacher Only","15" => "Teacher Planning","25" => "Technical");


    $acad_sessions = fetch_inow_details( 'acadsessions' );
    $sess_arr = $year_session = array();
    foreach($acad_sessions as $acad_session){
        if(in_array($acad_session->Name, array("2021-2022", "2021-22", "2020-2021", "2020-21", "2019-2020", "2019-20", "2018-2019", "2018-19")))//, "2017-2018", "2017-18")))       
        {
                $sess_arr[] = $acad_session->Id;
                $year_session[$acad_session->Id] = $acad_session->Name;
        }
    }


    $SQL = "SELECT stateID  FROM student WHERE current_grade NOT IN ('PreK', 'K', '1', '2', '3', '4', '5', '6', '7') ORDER BY id ASC";
    $rs = $objDB->select($SQL);

    foreach($rs as $stdkey=>$stdval)
    { //1
        $stateID = $stdval['stateID'];
        $SQL = "SELECT * FROM studentgrade_latest WHERE stateID = '".$stateID."'";
        $rs1 = $objDB->select($SQL);
        if(count($rs1) > 0)
        { //2

        } // 2-e
        else
        { // 3
            $sections = $graded_items_hash = $grading_periods_hash = array();

            $SQL = "SELECT stateID, student_id FROM student WHERE stateID = '".$stateID."'";
            $rs2 = $objDB->select($SQL);

            $arr = $arr1 = array();
            for($i=0; $i < count($rs2); $i++)
            {//4
                $arr[] = $rs2[$i]['student_id'];
                $arr1[] = $rs2[$i]['stateID'];
            }//4-e

            $teacherArr = $gradeItemIdArr = $gradePeriodArr = $sectionArr = array();
            foreach($arr as $ak=>$av)
            { //5
                $endpoint = "students/".$av."/acadSessions";
                $academic_sessions = fetch_inow_details($endpoint);
                $acd = $academic_sessions;
            
                foreach($acd as $key=>$value)
                { //6
                    if(in_array($value->AcadSessionId, $sess_arr))
                    { //7
                            $endpoint = $value->AcadSessionId."/students/".$av."/grades";
                            $grade_data = fetch_inow_details($endpoint);


                            if(isset($grade_data->Message))
                            {
                            	$SQL = "INSERT INTO studentgrade_error SET stateID = '".$av."', session_id = '".$value->AcadSessionId."', message = '".addslashes($grade_data->Message)."'";
                            	$rserror = $objDB->insert($SQL);
                            }
                            else
                            {
                            	foreach($grade_data as $gkey=>$gvalue)
	                            { //8
	                                $data = array();
	                                $ins = array();
	                                $data['stateID'] = $arr1[$ak]; //0
	                                $data['academic_session'] = $value->AcadSessionId;
	                                $data['NumericGrade'] = $gvalue->NumericGrade;

	                                $sectionId = $gvalue->SectionId;
	                                if(isset($sectionArr[$sectionId]))
	                                { //9
	                                    $tmp = $sectionArr[$sectionId];
	                                    $data['CourseId'] = $tmp['CourseId'];
	                                    $data['CourseNumber'] = $tmp['CourseNumber'];
	                                    $data['CourseTypeId'] = $tmp['CourseTypeId'];
	                                    $data['FullName'] = $tmp['FullName'];
	                                    $data['FullSectionNumber'] = $tmp['FullSectionNumber'];
	                                    $data['MaxGradeLevelId'] = $tmp['MaxGradeLevelId'];
	                                    $data['StateCourseNumber'] = $tmp['StateCourseNumber'];
	                                    $data['ShortName'] = $tmp['ShortName'];
	                                } //9-e
	                                else
	                                { //10
	                                    $endpoint = $value->AcadSessionId."/sections/".$sectionId;
	                                    $section_data = fetch_inow_details($endpoint);
	                                    $data['CourseId'] = addslashes($section_data->CourseId);
	                                    $data['CourseNumber'] = addslashes($section_data->CourseNumber);
	                                    $data['CourseTypeId'] = addslashes($section_data->CourseTypeId);
	                                    $data['FullName'] = addslashes($section_data->FullName);
	                                    $data['FullSectionNumber'] = addslashes($section_data->FullSectionNumber);
	                                    $data['MaxGradeLevelId'] = addslashes($section_data->MaxGradeLevelId);
	                                    $data['StateCourseNumber'] = addslashes($section_data->StateCourseNumber);
	                                    $data['ShortName'] = addslashes($section_data->ShortName);
	                                
	                                    $tmp = array();
	                                    $tmp['CourseId'] = addslashes($section_data->CourseId);
	                                    $tmp['CourseNumber'] = addslashes($section_data->CourseNumber);
	                                    $tmp['CourseTypeId'] = addslashes($section_data->CourseTypeId);
	                                    $tmp['FullName'] = addslashes($section_data->FullName);
	                                    $tmp['FullSectionNumber'] = addslashes($section_data->FullSectionNumber);
	                                    $tmp['MaxGradeLevelId'] = addslashes($section_data->MaxGradeLevelId);
	                                    $tmp['StateCourseNumber'] = addslashes($section_data->StateCourseNumber);
	                                    $tmp['ShortName'] = addslashes($section_data->ShortName);
	                                    $sectionArr[$sectionId] = $tmp;
	                                } //10-e

	                                $teacher_id = $section_data->PrimaryTeacherId;
	                                if(isset($teacherArr[$teacher_id]))
	                                { //11
	                                    $data['TeacherName']  = $teacherArr[$teacher_id];
	                                } //11-e
	                                else
	                                { //12
	                                    $endpoint = "persons/".$teacher_id;
	                                    $teacher_data = fetch_inow_details($endpoint);
	                                    $data['TeacherName'] = addslashes($teacher_data->DisplayName);
	                                    $teacherArr[$teacher_id] = $data['TeacherName'];
	                                } //12-e

	                                $grade_item_id = $gvalue->GradedItemId;
	                                if(isset($gradeItemIdArr[$grade_item_id]))
	                                { //13
	                                    $tmp = $gradeItemIdArr[$grade_item_id];
	                                    $data['GradeDescription'] = $tmp['GradeDescription'];
	                                    $data['GradeName'] = $tmp['GradeName'];
	                                    $data['Sequence'] = $tmp['Sequence'];
	                                    $grade_period_id = $tmp['GradePeriodId'];

	                                } //13-e
	                                else
	                                { //14
	                                    $endpoint = $value->AcadSessionId."/gradedItems/".$grade_item_id;
	                                    $grade_item_data = fetch_inow_details($endpoint);
	                                    $data['GradeDescription'] = addslashes($grade_item_data->Description);
	                                    $data['GradeName'] = addslashes($grade_item_data->Name);
	                                    $data['Sequence'] = addslashes($grade_item_data->Sequence);

	                                    $tmp = array();
	                                    $tmp['GradeDescription'] = $data['GradeDescription'];
	                                    $tmp['GradeName'] = $data['GradeName'];
	                                    $tmp['Sequence'] = $data['Sequence'];
	                                    $tmp['GradePeriodId'] = $grade_item_data->GradingPeriodId;
	                                    $gradeItemIdArr[$grade_item_id] = $tmp;
	                                    $grade_period_id = $grade_item_data->GradingPeriodId;
	                 
	                                }  //14-e           

	                               
	                                if(isset($gradePeriodArr[$grade_period_id]))
	                                { //15
	                                    $tmp = $gradePeriodArr[$grade_period_id];
	                                    $data['GradeTerm'] = $tmp['GradeTerm'];
	                                    $data['academicYear'] = $tmp['academicYear'];
	                                    $data['GradePeriodName'] = $tmp['GradePeriodName'];
	                                    $data['academicTerm'] = $tmp['academicTerm'];
	                                    $data['SchoolAnnouncement'] = $tmp['SchoolAnnouncement'];
	                                } //15-e
	                                else
	                                { //16
	                                    $endpoint = $value->AcadSessionId."/gradingPeriods/".$grade_period_id;
	                                    $grade_period_data = fetch_inow_details($endpoint);
	                                    $data['GradeTerm'] = addslashes($grade_period_data->Description);
	                                    $tmp = explode(" ", $data['GradeTerm']);
	                                    $data['academicYear'] = $tmp[0]; //1
	                                    $data['GradePeriodName'] = addslashes($grade_period_data->Name);
	                                    $data['academicTerm'] = $data['GradePeriodName'];
	                                    $data['SchoolAnnouncement'] = addslashes($grade_period_data->SchoolAnnouncement);


	                                    $tmp = array();
	                                    $tmp['GradeTerm'] = $data['GradeTerm'];
	                                    $tmp['academicYear'] = $data['academicYear'];
	                                    $tmp['GradePeriodName'] = $data['GradePeriodName'];
	                                    $tmp['academicTerm'] = $data['academicTerm'];
	                                    $tmp['SchoolAnnouncement'] = $data['SchoolAnnouncement'];
	                                    $gradePeriodArr[$grade_period_id] = $tmp;
	                 
	                                }  //16-e

	                                


	                                $ins['stateID'] = $data['stateID'];
	                                $ins['academicYear'] = $data['academicYear'];
	                                $ins['academicTerm'] = $data['academicTerm'];
	                                $ins['GradeName'] = $data['GradeName'];
	                                $ins['courseTypeID'] = $data['CourseTypeId'];
	                                $ins['sequence'] = $data['Sequence'];
	                                $ins['courseType'] = (isset($courseType[$data['CourseTypeId']]) ? $courseType[$data['CourseTypeId']] : 0);
	                                $ins['academicYear'] = $year_session[$value->AcadSessionId];
	                                $ins['courseFullName'] = $data['FullName'];
	                                $ins['courseName'] = $data['ShortName'];
	                                $ins['sectionNumber'] = $data['StateCourseNumber'];
	                                $ins['fullsection_number'] = $data['FullSectionNumber'];
	                                $ins['numericGrade'] = $data['NumericGrade'];

	                                
	                                $SQL = "DELETE FROM studentgrade_latest WHERE stateID = '".$ins['stateID']."' AND  academicYear='".$ins['academicYear']."' AND GradeName = '".$ins['GradeName']."' AND courseType = '".$ins['courseType']."'";
	                                $rsDel = $objDB->sql_query($SQL);


	                                $SQL = "INSERT INTO studentgrade_latest SET ";
	                                foreach($ins as $mkey=>$mvalue)
	                                {
	                                    $SQL .= $mkey."='".$mvalue."',";
	                                }
	                                $SQL = trim($SQL,",");
	                                echo $SQL."<BR><BR>";
	                                $rsr = $objDB->insert($SQL);


	                            } //8-e

                            }
                    } //7-e

                } // 6-e



        } //5-e

    } //3-e

        echo "Student Grade fetched successfully.";
} //1-e
?>