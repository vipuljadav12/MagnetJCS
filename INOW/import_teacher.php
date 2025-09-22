<?php

    set_time_limit(0);
    ini_set('memory_limit','2048M');
    include("common_functions.php");
    
    include("dbClass.php");
    $objDB = new MySQLCN;


    /* Fetch all staff Details */
    $staff_hash = [];
    $staff = fetch_inow_details( 'staff' );
    foreach( $staff as $staff_member ){
        $staff_hash[ $staff_member->Id ] = $staff_member;
    }

    /* Fetch all teachers emails */
    $teacher_email_hash = array();
    $emails = fetch_inow_details( 'persons/emailaddresses' );
    foreach( $emails as $email ){

        if( $email->IsPrimary ) {
            $teacher_email_hash[ $email->PersonId ] = $email->EmailAddress;
        }
    }

    /* Fetch all Schools & staff details */
    $staff_flushed = [];
    $school_principal_hash = [];
    $schools = fetch_inow_details( 'schools' );
    foreach( $schools as $school ){
        $classifications = fetch_inow_details( 'schools/'.$school->Id.'/staffClassifications' );

        foreach( $classifications as $staffer ){
            if( isset( $teacher_email_hash[ $staffer->StaffId ] ) ){

                //Principal
                if( get_staff_position_from_id( $staffer->StaffClassificationId ) == 'principal'){

                    if( empty( $school_principal_hash[ $school->Id ] ) ){
                        $school_principal_hash[ $school->Id ] = [];
                    }
                    $school_principal_hash[ $school->Id ] = [
                        'email' => $teacher_email_hash[ $staffer->StaffId ],
                        'name' => $staff_hash[ $staffer->StaffId ]->FirstName .' '. $staff_hash[ $staffer->StaffId ]->LastName,
                    ];
                }
                if( !in_array($staffer->StaffId, $staff_flushed ) ){

                    $SQL = "INSERT INTO staff SET first_name = '".addslashes($staff_hash[ $staffer->StaffId ]->FirstName)."', last_name = '".addslashes($staff_hash[ $staffer->StaffId ]->LastName)."', email = '".$teacher_email_hash[ $staffer->StaffId ]."', staff_id = '".$staffer->StaffId."'";
                    $rs = $objDB->sql_query($SQL);
                    $staff_flushed[] = $staffer->StaffId;
                }
            }
        }
    }

    /* Get Current Academic Session */
    $year = intval( date('Y') );
    $year = ( intval( date( 'm' ) ) < 6 ) ? $year : $year + 1;

    $sessions_hash = [];
    $acad_sessions = fetch_inow_details( 'acadsessions' );
    foreach($acad_sessions as $acad_session){
        if( is_object( $acad_session ) ){

            if( $acad_session->AcadYear == $year ){
                $sessions_hash['current'][ $acad_session->Id ] = $acad_session;
            }
        }
    }

    uasort( $sessions_hash['current'], function ($a, $b)
    {
        if ($a->StartDate == $b->StartDate) {
            return 0;
        }
        return ($a->StartDate < $b->StartDate) ? 1 : -1;
    });

    $enrollment_statuses = [
            'Enrolled',
            'Registered'
        ];

    $student_hash =[];
    foreach ($sessions_hash as $sessions) {

            foreach( $enrollment_statuses as $status ) {

                foreach ($sessions as $session_id => $acad_session) {

                    $students = fetch_inow_details($session_id . '/students?status=' . $status);

                    if (is_object($students)) {
                        $students = array();
                    };

                    foreach ($students as $index => $student) {

                        if ( !is_object($student) || !$student->StateIdNumber ){
                            unset($students[$index]);
                        } else if( !isset( $students_hash[ $student->Id ] ) ){
                            $students_hash[ $student->Id ] = $student->StateIdNumber;
                        }
                    }
                }
            }
        }

         $sections_hash = [];
        $section_teachers = [];
        $section_year_hash = [];
        foreach ($sessions_hash as $sessions) {

            foreach ($sessions as $session_id => $session) {

                if( is_object( $session ) ){

                    $sections = fetch_inow_details( $session->Id.'/sections' );
                    foreach( $sections as $section ){
                        if( is_object( $section ) && !empty( $section->PrimaryTeacherId ) ){

                            $sections_hash[ $section->Id ] = $section;
                            $section_teachers[ $section->PrimaryTeacherId ] = $staff_hash[ $section->PrimaryTeacherId ];
                            $section_year_hash[ $section->Id ] = $session->AcadYear;
                        }
                    }
                }
            }
        }

        $student_section_hash = [];
        $student_principal_hash = [];
        foreach( $sessions_hash as $sessions ){
            foreach( $sessions as $session){
                $schedule = fetch_inow_details( $session->Id.'/students/schedule' );
                foreach( $schedule as $scheduled_section ){
                    if( is_object( $scheduled_section )
                        && isset( $sections_hash[ $scheduled_section->SectionId ] )
                        && isset( $students_hash[ $scheduled_section->StudentId ] )
                    ){

                        $section = $sections_hash[ $scheduled_section->SectionId ];

                        $student = $scheduled_section->StudentId;

                        $courseType = get_course_type_from_section( $section );

                        if( empty( $student_section_hash[$student][ $courseType ] )
                                || $session->AcadYear > $section_year_hash[ $section->Id ]
                        ){

                            if( $courseType
                                && in_array($courseType, ['english', 'math', 'science'] )
                            ){
                                $student_section_hash[$student][ $courseType ] = $section;
                            }
                            if( isset( $school_principal_hash[ $session->SchoolId ] ) ){
                                $student_principal_hash[$student] = $school_principal_hash[ $session->SchoolId ];
                            }
                        }
                    }
                }
            }
        }
        unset( $sessions_hash );

        foreach( $student_section_hash as $student_id => $courses ){
            foreach( $courses as $courseType => $section ){

                if( $courseType
                    && in_array($courseType, ['english', 'math', 'science'] )
                    && isset( $students_hash[ $student_id ] )
                ){

                    $student = $students_hash[ $student_id ];

                    if( isset( $teacher_email_hash[ $section->PrimaryTeacherId ] ) ){

                        $stateID = $student;
                        $field_name = $courseType .'_teacher_email';
                        $field_value = $teacher_email_hash[ $section->PrimaryTeacherId ];
                        $SQL = "INSERT INTO student_data SET stateID = '".$stateID."', field_name = '".$field_name."', field_value = '".$field_value."'";
                        $rs = $objDB->sql_query($SQL);
                    }

                    if( isset( $staff_hash[ $staffer->StaffId ] ) ){

                        $stateID = $student;
                        $field_name = $courseType .'_teacher_name';
                        $field_value = $teacher_email_hash[ $section->PrimaryTeacherId ];
                        $SQL = "INSERT INTO student_data SET stateID = '".$stateID."', field_name = '".$field_name."', field_value = '".$field_value."'";
                        $rs = $objDB->sql_query($SQL);
                    }
                }
            }
        }

        foreach( $student_principal_hash as $student_id => $principal ){

            $student = $students_hash[ $student_id ];

            $stateID = $student;
            $field_name = 'principal_email';
            $field_value = $principal['email'];
            $SQL = "INSERT INTO student_data SET stateID = '".$stateID."', field_name = '".$field_name."', field_value = '".$field_value."'";
            $rs = $objDB->sql_query($SQL);

            $field_name = 'principal_name';
            $field_value = $principal['name'];
            $SQL = "INSERT INTO student_data SET stateID = '".$stateID."', field_name = '".$field_name."', field_value = '".$field_value."'";
            $rs = $objDB->sql_query($SQL);
        }



    function get_staff_position_from_id( $position_id ){

        switch( $position_id ){
            case 1: //'Teacher'
                $position = 'teacher';
                break;
            case 44: //'Principal'
                $position = 'principal';
                break;
            case 90: //'LEAP Teacher'
                $position = 'gifted_teacher';
                break;

            case 94: //'Administrative Assistant'
                $position = 'Administrative Assistant';
                break;
            case 71: //'Advanced Placement Coordinator'
                $position = 'Advanced Placement Coordinator';
                break;
            case 101: //'ARI Literacy Specialist'
                $position = 'ARI Literacy Specialist';
                break;
            case 45: //'Assistant Principal'
                $position = 'Assistant Principal';
                break;
            case 49: //'Athletics Coordinator'
                $position = 'Athletics Coordinator';
                break;
            case 98: //'Behavioral Interventionist'
                $position = 'Behavioral Interventionist';
                break;
            case 53: //'Behavioral Learning'
                $position = 'Behavioral Learning';
                break;
            case 28: //'Campus Security Officer'
                $position = 'Campus Security Officer';
                break;
            case 75: //'Career Academy Director'
                $position = 'Career Academy Director';
                break;
            case 99: //'Chief Financial Officer'
                $position = 'Chief Financial Officer';
                break;
            case 77: //'CNP Department'
                $position = 'CNP Department';
                break;
            case 41: //'CNP Director'
                $position = 'CNP Director';
                break;
            case 58: //'Compliance Department'
                $position = 'Compliance Department';
                break;
            case 66: //'Contract - Clerical Assistant'
                $position = 'Contract - Clerical Assistant';
                break;
            case 4: //'Counselor'
                $position = 'Counselor';
                break;
            case 30: //'Curriculum Specialist'
                $position = 'Curriculum Specialist';
                break;
            case 68: //'Data Strategist'
                $position = 'Data Strategist';
                break;
            case 74: //'Dean of College Academy'
                $position = 'Dean of College Academy';
                break;
            case 73: //'Dean of Students'
                $position = 'Dean of Students';
                break;
            case 50: //'Deputy Superintendent of Instruction'
                $position = 'Deputy Superintendent of Instruction';
                break;
            case 60: //'Deputy Superintendent of Strategy & Innovation'
                $position = 'Deputy Superintendent of Strategy & Innovation';
                break;
            case 100: //'Director of Finance'
                $position = 'Director of Finance';
                break;
            case 54: //'Director of Instruction - Elementary & P-8'
                $position = 'Director of Instruction - Elementary & P-8';
                break;
            case 55: //'Director of Instruction - High School'
                $position = 'Director of Instruction - High School';
                break;
            case 70: //'Director of Magnet Programs'
                $position = 'Director of Magnet Programs';
                break;
            case 56: //'Director of School Readiness'
                $position = 'Director of School Readiness';
                break;
            case 24: //'ED Lead Teacher'
                $position = 'ED Lead Teacher';
                break;
            case 89: //'Entertainment Technology Academy Staff'
                $position = 'Entertainment Technology Academy Staff';
                break;
            case 97: //'ESOL Coordinator'
                $position = 'ESOL Coordinator';
                break;
            case 93: //'Executive Administrative Assistant'
                $position = 'Executive Administrative Assistant';
                break;
            case 61: //'Federal Programs'
                $position = 'Federal Programs';
                break;
            case 80: //'Graduation Coach'
                $position = 'Graduation Coach';
                break;
            case 14: //'HCS Clerical Assistant'
                $position = 'HCS Clerical Assistant';
                break;
            case 22: //'IB Coordinator'
                $position = 'IB Coordinator';
                break;
            case 8: //'INACTIVE'
                $position = 'INACTIVE';
                break;
            case 2: //'INow Administrator'
                $position = 'INow Administrator';
                break;
            case 15: //'Instructional Coach'
                $position = 'Instructional Coach';
                break;
            case 48: //'IT Department'
                $position = 'IT Department';
                break;
            case 85: //'Lead Student Data Generalist'
                $position = 'Lead Student Data Generalist';
                break;

            case 17: //'Librarian'
                $position = 'Librarian';
                break;
            case 57: //'Magnet Department'
                $position = 'Magnet Department';
                break;
            case 76: //'Military Student Transition'
                $position = 'Military Student Transition';
                break;
            case 83: //'Networked Learning Coach'
                $position = 'Networked Learning Coach';
                break;
            case 34: //'Networked Learning Coordinator'
                $position = 'Networked Learning Coordinator';
                break;
            case 35: //'Networked Learning Director'
                $position = 'Networked Learning Director';
                break;
            case 96: //'Non-Traditional Learning Coordinator'
                $position = 'Non-Traditional Learning Coordinator';
                break;
            case 11: //'Nurse'
                $position = 'Nurse';
                break;
            case 13: //'Office Manager / Administrative Assistant'
                $position = 'Office Manager / Administrative Assistant';
                break;
            case 5: //'Other'
                $position = 'Other';
                break;
            case 47: //'Payroll Clerk'
                $position = 'Payroll Clerk';
                break;

            case 84: //'Professional Development'
                $position = 'Professional Development';
                break;
            case 29: //'Psychologist'
                $position = 'Psychologist';
                break;
            case 23: //'Reading Coach'
                $position = 'Reading Coach';
                break;
            case 18: //'Registrar'
                $position = 'Registrar';
                break;
            case 9: //'Retired'
                $position = 'Retired';
                break;
            case 92: //'School Office Assistant'
                $position = 'School Office Assistant';
                break;
            case 26: //'School Resource Officers'
                $position = 'School Resource Officers';
                break;
            case 38: //'School Technician'
                $position = 'School Technician';
                break;
            case 32: //'Security Operations'
                $position = 'Security Operations';
                break;
            case 20: //'Senior Accounting Clerk'
                $position = 'Senior Accounting Clerk';
                break;
            case 7: //'SETS Staff'
                $position = 'SETS Staff';
                break;
            case 69: //'Student Assignment Coordinator'
                $position = 'Student Assignment Coordinator';
                break;
            case 16: //'Student Data Generalist'
                $position = 'Student Data Generalist';
                break;
            case 88: //'Student Outcomes Data Strategist'
                $position = 'Student Outcomes Data Strategist';
                break;
            case 21: //'Student Welfare and Social Services'
                $position = 'Student Welfare and Social Services';
                break;
            case 43: //'Substitute Teacher'
                $position = 'Substitute Teacher';
                break;
            case 52: //'Superintendent'
                $position = 'Superintendent';
                break;
            case 3: //'Support'
                $position = 'Support';
                break;
            case 19: //'System Tech'
                $position = 'System Tech';
                break;
            case 59: //'Talent Management'
                $position = 'Talent Management';
                break;

            case 95: //'Technology & Learning Coach'
                $position = 'Technology & Learning Coach';
                break;
            case 25: //'TEST I'
                $position = 'TEST I';
                break;
            case 46: //'TOSA'
                $position = 'TOSA';
                break;
            case 79: //'Transformation Specialist'
                $position = 'Transformation Specialist';
                break;
            case 31: //'Transportation Coordinator'
                $position = 'Transportation Coordinator';
                break;
            case 87: //'Truancy Specialist'
                $position = 'Truancy Specialist';
                break;
            default:
                $position = 'Other';
                break;
        }
        return $position;
    }


    function get_course_type_from_section( $section ){

        if( !is_object( $section ) ){
            var_dump( $section ); die;
        }

        switch( $section->CourseTypeId ){
            case 3:
                if( strpos( strtolower( $section->ShortName ), 'read') !== false ){
                    $course_type = 'reading';
                } else {
                    $course_type = 'english';
                }
                break;
            case 4:
                $course_type = 'math';
                break;
            case 7:
                $course_type = 'science';
                break;
            case 9:
                $course_type = 'social studies';
                break;
            default:
                $course_type = '??' . $section->CourseTypeId . $section->ShortName;
                break;
        }
        return $course_type;
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