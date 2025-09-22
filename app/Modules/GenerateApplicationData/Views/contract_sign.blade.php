<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="" rel="stylesheet">
    <style type="text/css">
        
        @font-face {
            font-family:'Open Sans';
            src:url('fonts/OpenSans-Regular.ttf') format('truetype');
            font-weight:normal;
            font-style:normal;
        }
        @font-face {
            font-family:'Open Sans SemiBold';
            src:url('storage/fonts/OpenSans-SemiBold.ttf') format('truetype');
            font-weight:normal;
            font-style:normal;
        }
        @font-face {
            font-family:'Open Sans bold';
            src:url('fonts/OpenSans-Bold.ttf') format('truetype');
            font-weight:normal;
            font-style:normal;
        }
        
        footer {
            position: fixed; 
            bottom: -30px; 
            left: 0px; 
            right: 0px;
            height: 50px; 
            
            /** Extra personal styles **/
            /*border-top: 1px solid #000;*/
            text-align: center;
            line-height: 35px;
        }
        
        body {padding:10px; margin:0; font-family: 'Open Sans', sans-serif; font-size:12px;}
        .container {max-width:700px; margin:0 auto; padding-top: 25px;}
        img {max-width:100%;}
        .w-50 {width:50%;}
        .w-80 {width:80%;}
        .w-100 {width:100%;}
        .logo-box {width:120px; margin-bottom:20px;}
        .logo-box.text-right {margin-left:auto;}
        .text-center {text-align:center;}
        .table {width:100%; border:1px solid #ccc; border-collapse:collapse;}
        .table tr {padding:0; margin:0; border-bottom:1px solid #ccc;}
        .table tr th {padding:10px 5px;margin:0; border-top:1px solid #ccc; border-right:1px solid #ccc; font-size: 12px;}
        .table tr td {padding:10px 5px;margin:0; border-top:1px solid #ccc; border-right:1px solid #ccc;}
        .small-text{font-size:11px; line-height: 14px;}
        .section {margin-bottom:30px;}
        .section-title {padding:10px; text-align:center;background:#666;color:#fff;font-size:16px;text-transform:uppercase;}
        .section-1 {}  
        .text-right {text-align:right;}
        .f-12{font-size:13px;}
        header {
            position: fixed;
            top: -20px;
            left: 0px;
            right: 0px;
            height: 50px;
            max-width: 700px;
        }
        .mb-10{margin-bottom: 8px;}
        .text-lg-right {
            text-align: right !important;
            float:right !important;
        }
        .d-flex {
            display: flex !important;
        }
        .col-lg-8 {
            flex: 0 0 66.6666666667%;
            max-width: 66.6666666667%;
        }
        .col-lg-4 {
            flex: 0 0 33.3333333333%;
            max-width: 33.3333333333%;
        }
        .mb-40 {margin-bottom: 30px !important}
    </style>
    
</head>
@if(isset($submissions))
@foreach($submissions as $key=>$submission)
<body>
    <header>
        <div class="header">
            <table class="w-100">
                <tbody>
                    <tr>
                        <td class="w-100 text-center">
                            @php 
                                $logo = (isset($application_data) ? getDistrictLogo($application_data->display_logo) : getDistrictLogo()) 
                            @endphp
                            <img src="{{str_replace('https://', 'http://', $logo)}}" title="" alt="" style="max-width: 100px !important;">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </header>
    <footer>
        <hr>
        <div class="text-center">1 Magnum Pass Mobile, AL 36618 | Phone: 251-221-4000</div>
    </footer>
    
    <div class="container page">
        <div class="wrapper">
            <div class="section section-1">
                
                <div class="container">
                    <div class="">
                        <div class="p-20">
                            <div class="text-center font-20 b-600 mb-40">MCPSS MAGNET SCHOOLS PARENT CONTRACT</div>
                            <div class="">
                                <div class="row">
                                    @php
                                        $program_name = "";
                                        if($submission->first_offer_status == "Accepted")
                                        {
                                            $program_name = getProgramName($submission->first_waitlist_for);
                                        }
                                        elseif($submission->second_offer_status == "Accepted")
                                        {
                                            $program_name = getProgramName($submission->second_waitlist_for);
                                        }    
                                    @endphp
                                    <div class="col-12 col-lg-5 mb-10">STUDENT NAME : <strong>{{$submission->first_name ." ".$submission->last_name}}</strong><span class="col-lg-4 text-lg-right">GRADE : <strong>{{$submission->next_grade}}</strong></span></div>
                                    <div class="col-12 col-lg-5 mb-10">SELECTED SCHOOL : <strong>{{$program_name ?? ''}}</strong></div>
                                </div>
                                <div class="mb-10 text-justify" style="text-align: justify;">Welcome to the 2020-2021 school year in Mobile County Public Schools where we are "Learning Today. Leading Tomorrow." Congratulations on your child's admittance into one of our stellar magnet programs! By choosing to send your child to an open-zoned school of choice, you are agreeing to adhere to the high expectations of the school of choice. Please carefully read and discuss the following commitment statements with your child, and initial each one to indicate you have read and agree with each item. A returned contract is necessary for continuation in the magnet program so that all parents and students are aware of magnet school expectations.</div>
                                <div class="mb-10"><strong>Check Each :</strong></div>    
                                <div class="text-center font-20 b-600 mb-10">MCPSS MAGNET SCHOOLS PARENT CONTRACT</div>
                                <div class="text-justify">
                                    <div class="mb-10" style="text-align: justify;"><input type="checkbox" class="mr-10"  checked style="padding-top: 7px !important; display: inline;"> I understand the school my child has been selected to attend is an open-zoned school of choice. <br><em class="font-14">This means my child has a zoned school of attendance for which he/she <strong>can</strong> attend, but I am <strong>choosing</strong> to place my child at the named magnet school which has a unique set of rules, policies, and procedures to which my child and I must adhere. Therefore, I will cooperate and work collaboratively with the school staff for the benefit and success of my child.</em></div>
                                    <div class="mb-10" style="text-align: justify;"><input type="checkbox" class="mr-10"  checked style="padding-top: 7px !important; display: inline;"> I understand that each magnet school has uniform and dress guidelines which are unique to magnet schools. <br><em class="font-14">We expect our students to "dress for success!" By choosing to send my child to a MCPSS magnet school, I am choosing to adhere to the dress-code of my school of choice.</em></div>
                                    <div class="mb-10" style="text-align: justify;"><input type="checkbox" class="mr-10"  checked style="padding-top: 7px !important; display: inline;"> I understand that magnet schools have grading and retention policies which differ from other MCPSS schools. <br><em class="font-14">Refer to the magnet grading scale: 90-100 = A, 80-89 = B, 70-79 = C, 69 &amp; Below = Does not meet magnet standards. Students who score less than a 70 on their final yearly average in any subject area will be required to repeat the grade at the magnet location or move to his or her zoned school of attendance for promotion opportunity.</em></div>
                                    <div class="mb-10" style="text-align: justify;"><input type="checkbox" class="mr-10"  checked style="padding-top: 7px !important; display: inline;"> I understand the importance of school attendance and its impact on academic success.<br><em class="font-14">Greater than five (5) unexcused absences and fifteen (15) unexcused check-in (tardies) or check-outs is considered excessive and may result in truancy violations and/or loss of privilege to return to the magnet program.</em></div>
                                    <div class="mb-10" style="text-align: justify;"><input type="checkbox" class="mr-10"  checked style="padding-top: 7px !important; display: inline;"> I understand that all students deserve to learn in a safe, caring, and orderly environment free from distractions.<br><em class="font-14"><strong>Discipline criteria:</strong> Students with 3 or more suspensions, one suspension for 5 or more days, and/or any C,  D, or E offense may be recommended for removal from the magnet program immediately. Students who incur five (5) or more Class "B" offenses within an academic period will be removed from the magnet program for at least one full academic year.</em></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-10" style="text-align: justify;"><input type="checkbox" class="mr-10" checked style="padding-top: 7px !important; display: inline;"> I understand that MCPSS Choice Schools are open-zoned schools of choice which means I am responsible for the transportation of my child to and from a school which may or may not be located near my home or work.<br><em class="font-14">I will abide by all rules and guidelines set forth by my child’s choice school regarding drop off and pick up including times, locations, carpool lines, walking, bus locations, etc. I will abide by the rules of my zoned school when dropping my student for magnet bus transportation (where applicable). I understand that violating these rules and guidelines can result in my child being removed from the school of choice.</em></div>
                    
                    <div class="mb-10" style="text-align: justify;"><input type="checkbox" class="mr-10"  checked style="padding-top: 7px !important; display: inline;"> I understand that I must complete the registration process within the time-lines provided by my school and district.<br><em class="font-14">On-line and on-site registration requirements must be met according to times provided for the school year and a re- commitment may be required.</em></div>
                    <div class="mb-10" style="text-align: justify;"><input type="checkbox" class="mr-10"  checked style="padding-top: 7px !important; display: inline;"> I understand that my child’s continued enrollment at the selected school is not final until his/her final report card has been reviewed, all entrance and discipline criteria have been met, and on-line and on-site registration have been completed. In addition, if I choose to remove my child from the magnet program my child will not be eligible to attend a magnet school for at least one academic school year.</div>
                    <div class="" style="padding-top: 20px !important">
                        <div class="col-12  mb-10">Parent Signature : {{$submission->contract_name}}
                            <span class="col-12 mb-10 text-lg-right">Date : <strong>{{date("M d, Y")}}</strong></span>
                        </div>
                        
                        <div class="mb-10">Parent Name - Printed : <strong>{{$submission->parent_first_name ." ".$submission->parent_last_name}}</strong></div>
                        <div class="mb-10">Student Name - Printed : <strong>{{$submission->first_name ." ".$submission->last_name}}</strong></div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</body>
@endforeach
@endif
<style>
    .page {
        page-break-after: always;
    }
    .page:last-child {
        page-break-after: unset;
    }
</style>
</html>