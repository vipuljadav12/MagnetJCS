<?php
ini_set("error_reporting","1");
    set_time_limit(0);
    ini_set('memory_limit','2048M');
    
    include("common_functions.php");     
    include("dbClass.php");
    $objDB = new MySQLCN;

    $submission_id = $_REQUEST['id'];



    $SQL = "SELECT * FROM submissions WHERE id = '".$submission_id."'";
    $rs = $objDB->select($SQL);

    if(count($rs) > 0)
    {
        $SQL = "SELECT student_id FROM student WHERE stateID = '".$rs[0]['student_id']."'";
        $rsS = $objDB->select($SQL);

        $stateID = $rs[0]['student_id'];
        $arr[] = $rsS[0]['student_id'];
        $arr1[] = $stateID;
    

        $courseType = array("31" => "Academic","36" => "Art","26" => "ArtA","16" => "Business","27" => "Career/technical","43" => "Career\Technical Education","39" => "Computer","10" => "Computer Application","42" => "Cooperative Career\Tech Ed","41" => "Coordinated Studies","2" => "Elective","3" => "English","17" => "Fam/Cons Science","11" => "Fine Arts","12" => "Foreign Language","14" => "Health","34" => "Hearing Impaired","22" => "KeyboardingKB","21" => "Learning Strategies","4" => "Math","37" => "Music","1" => "NA","23" => "No Valid Code","32" => "Non Credit","5" => "Physical Education","24" => "Planning Period","40" => "Reading","6" => "READINGRDG","18" => "ROTC","7" => "Science","8" => "Social Skills","9" => "Social Studies","28" => "Special Ed Math","29" => "Special Ed Science","30" => "Special Ed Soc Stds","33" => "Special Ed. Health","20" => "Special Education","19" => "Special EducationSPE","38" => "Spelling","35" => "Teacher Only","15" => "Teacher Planning","25" => "Technical");
    
        $acad_sessions = fetch_inow_details( 'acadsessions' );
        $sess_arr = $year_arr = array();
        foreach($acad_sessions as $acad_session){
                if(in_array($acad_session->Name, array("2020-2021", "2020-21", "2019-2020", "2019-20", "2018-2019", "2018-19")))//array("2018-2019", "2019-2020")))
                {
                    $sess_arr[] = $acad_session->Id;
                    $year_arr[$acad_session->Id] = $acad_session->Name;

                }
            }
        $sections = $graded_items_hash = $grading_periods_hash = array();

        

        $teacherArr = $gradeItemIdArr = $gradePeriodArr = $sectionArr = array();
        foreach($arr as $ak=>$av)
        {
            $endpoint = "students/".$av."/acadSessions";
            $academic_sessions = fetch_inow_details($endpoint);
            $acd = $academic_sessions;
            
            foreach($acd as $key=>$value)
            {
                if(in_array($value->AcadSessionId, $sess_arr))
                {
                        $endpoint = $value->AcadSessionId."/students/".$av."/grades";
                        $grade_data = fetch_inow_details($endpoint);

                        foreach($grade_data as $gkey=>$gvalue)
                        {
                            $data = array();
                            $ins = array();
                            $data['stateID'] = $arr1[$ak]; //0
                            $data['academic_session'] = $value->AcadSessionId;
                            $data['NumericGrade'] = $gvalue->NumericGrade;

                            $sectionId = $gvalue->SectionId;
                            if(isset($sectionArr[$sectionId]))
                            {
                                $tmp = $sectionArr[$sectionId];
                                $data['CourseId'] = $tmp['CourseId'];
                                $data['CourseNumber'] = $tmp['CourseNumber'];
                                $data['CourseTypeId'] = $tmp['CourseTypeId'];
                                $data['FullName'] = $tmp['FullName'];
                                $data['FullSectionNumber'] = $tmp['FullSectionNumber'];
                                $data['MaxGradeLevelId'] = $tmp['MaxGradeLevelId'];
                                $data['StateCourseNumber'] = $tmp['StateCourseNumber'];
                                $data['ShortName'] = $tmp['ShortName'];
                            }
                            else
                            {
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
                            }

                            $teacher_id = $section_data->PrimaryTeacherId;
                            if(isset($teacherArr[$teacher_id]))
                            {
                                $data['TeacherName']  = $teacherArr[$teacher_id];
                            }
                            else
                            {
                                $endpoint = "persons/".$teacher_id;
                                $teacher_data = fetch_inow_details($endpoint);
                                $data['TeacherName'] = addslashes($teacher_data->DisplayName);
                                $teacherArr[$teacher_id] = $data['TeacherName'];
                            }

                            $grade_item_id = $gvalue->GradedItemId;
                            if(isset($gradeItemIdArr[$grade_item_id]))
                            {
                                $tmp = $gradeItemIdArr[$grade_item_id];
                                $data['GradeDescription'] = $tmp['GradeDescription'];
                                $data['GradeName'] = $tmp['GradeName'];
                                $data['Sequence'] = $tmp['Sequence'];
                                $grade_period_id = $tmp['GradePeriodId'];

                            }
                            else
                            {
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
             
                            }                

                           
                            if(isset($gradePeriodArr[$grade_period_id]))
                            {
                                $tmp = $gradePeriodArr[$grade_period_id];
                                $data['GradeTerm'] = $tmp['GradeTerm'];
                                $data['academicYear'] = $tmp['academicYear'];
                                $data['GradePeriodName'] = $tmp['GradePeriodName'];
                                $data['academicTerm'] = $tmp['academicTerm'];
                                $data['SchoolAnnouncement'] = $tmp['SchoolAnnouncement'];
                            }
                            else
                            {
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
             
                            } 

                            


                            $ins['stateID'] = $data['stateID'];
                            $ins['academicYear'] = $year_arr[$value->AcadSessionId];
                            $ins['academicTerm'] = $data['GradeName'];
                            $ins['GradeName'] = $data['academicTerm'];
                            $ins['courseTypeID'] = $data['CourseTypeId'];
                            $ins['sequence'] = $data['Sequence'];
                            $ins['courseType'] = (isset($courseType[$data['CourseTypeId']]) ? $courseType[$data['CourseTypeId']] : 0);
                            $ins['courseFullName'] = $data['FullName'];
                            $ins['courseName'] = $data['ShortName'];
                            $ins['sectionNumber'] = $data['StateCourseNumber'];
                            $ins['fullsection_number'] = $data['FullSectionNumber'];
                            $ins['numericGrade'] = $data['NumericGrade'];

                            
                            if(in_array($data['CourseTypeId'], array(3,4,9,7)))
                            {
                                $SQL = "DELETE FROM studentgrade WHERE stateID = '".$ins['stateID']."' AND  academicYear='".$ins['academicYear']."' AND GradeName = '".$ins['GradeName']."' AND courseType = '".$ins['courseType']."'";
                                $rsDel = $objDB->sql_query($SQL);


                                $SQL = "INSERT INTO studentgrade SET ";
                                foreach($ins as $mkey=>$mvalue)
                                {
                                    $SQL .= $mkey."='".$mvalue."',";
                                }
                                $SQL = trim($SQL,",");
                                //echo $SQL;exit;
                                $rs = $objDB->insert($SQL);


                                $SQL = "DELETE FROM submission_grade WHERE  submission_id = '".$submission_id."' AND stateID = '".$ins['stateID']."' AND academicYear='".$ins['academicYear']."' AND GradeName = '".$ins['GradeName']."' AND courseType = '".$ins['courseType']."'";
                                $rsDel = $objDB->sql_query($SQL);

                               

                                $SQL = "INSERT INTO submission_grade SET ";
                                foreach($ins as $mkey=>$mvalue)
                                {
                                    $SQL .= $mkey."='".$mvalue."',";
                                }
                                $SQL .= " submission_id = ".$submission_id;
                                $SQL = trim($SQL,",");
                                $rs = $objDB->insert($SQL);                                
                            }


                            

                        }
                }

            }
            $SQL = "UPDATE student SET grade_fetched = 'Y' WHERE stateID = '".$arr1[$ak]."'";
            $rs1 = $objDB->sql_query($SQL);

            $SQL = "UPDATE submissions SET grade_exists = 'Y' WHERE student_id = '".$arr1[$ak]."'";
            $rs1 = $objDB->sql_query($SQL);

        }
        echo "Student Grade fetched successfully.";
}
?>