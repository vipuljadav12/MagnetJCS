<html>
    <head>
        <style type="text/css">
            td {padding: 3px;}
        </style>
    </head>
    <body>
<?php
    $date = $_REQUEST['date'];
    set_time_limit(0);


    ini_set('memory_limit','2048M');
    include("common_functions.php");
    
    include("dbClass.php");
    $objDB = new MySQLCN;

    $endpoint = "students/details?requestDate=".$date."&studentNumber=S76528";
    $acad_sessions = fetch_inow_details($endpoint);

    $tmp = array();

    $tmp['AddressLine1'] = $acad_sessions[0]->AddressLine1;
    $tmp['AddressLine2'] = $acad_sessions[0]->AddressLine2;
    $tmp['AltStudentNumber'] = $acad_sessions[0]->AltStudentNumber;
    $tmp['City'] = $acad_sessions[0]->City;
    $tmp['Classification'] = $acad_sessions[0]->Classification;
    $tmp['DateofBirth'] = $acad_sessions[0]->DateofBirth;
    $tmp['Email'] = $acad_sessions[0]->Email;
    $tmp['FirstName'] = $acad_sessions[0]->FirstName;
    $tmp['Grade'] = $acad_sessions[0]->Grade;
    $tmp['GuardianFirstName'] = $acad_sessions[0]->GuardianFirstName;
    $tmp['GuardianLastName'] = $acad_sessions[0]->GuardianLastName;
    $tmp['GuardianPhoneNumber'] = $acad_sessions[0]->GuardianPhoneNumber;
    $tmp['GuardianEmail'] = $acad_sessions[0]->GuardianEmail;
    $tmp['HomePhoneNumber'] = $acad_sessions[0]->HomePhoneNumber;
    $tmp['Homeroom'] = $acad_sessions[0]->Homeroom;
    $tmp['LastName'] = $acad_sessions[0]->LastName;
    $tmp['MiddleName'] = $acad_sessions[0]->MiddleName;
    $tmp['MailingAddressLine1'] = $acad_sessions[0]->MailingAddressLine1;
    $tmp['MailingAddressLine2'] = $acad_sessions[0]->MailingAddressLine2;
    $tmp['MailingCity'] = $acad_sessions[0]->MailingCity;
    $tmp['MailingState'] = $acad_sessions[0]->MailingState;
    $tmp['MailingZip'] = $acad_sessions[0]->MailingZip;
    $tmp['SchoolNumber'] = $acad_sessions[0]->SchoolNumber;
    $tmp['Sex'] = $acad_sessions[0]->Sex;
    $tmp['SocialSecurityNumber'] = $acad_sessions[0]->SocialSecurityNumber;
    $tmp['State'] = $acad_sessions[0]->State;
    $tmp['StateIdNumber'] = $acad_sessions[0]->StateIdNumber;
    $tmp['StudentId'] = $acad_sessions[0]->StudentId;
    $tmp['StudentNumber'] = $acad_sessions[0]->StudentNumber;
    $tmp['Username'] = $acad_sessions[0]->Username;
    $tmp['WorkPhoneNumber'] = $acad_sessions[0]->WorkPhoneNumber;
    $tmp['Zip'] = $acad_sessions[0]->Zip;

    



    ?>
    <table cellpadding="0" cellspacing="0">
            <?php
                foreach($tmp as $key=>$value)
                {
                    ?>
                    <tr>
                    <td><?=$key?></td>
                    <td> &nbsp;</td>
                    <td><?=$value?></td>
                </tr>

                    <?php
                }
            ?>
</table>
</body>
</html>