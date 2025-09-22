<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="" rel="stylesheet">
    <style type="text/css">
       
    @font-face {
        font-family: 'Open Sans';
        src: url('fonts/OpenSans-Regular.ttf') format('truetype');
        font-weight: normal;
        font-style: normal;
    }
   footer {
                position: fixed; 
                bottom: -30px; 
                left: 0px; 
                right: 0px;
                height: 50px; 

                /** Extra personal styles **/
                border-top: 1px solid #000;
                text-align: center;
                line-height: 35px;
            }

.header {
    top: 0px;
}
.footer {
    bottom: 0px;
}

    body {padding: 10px; margin: 0; font-family: 'Open Sans', sans-serif;}
    .container {max-width: 700px; margin: 0 auto;}
    img {max-width:100%;}
    .w-50 {width:50%;}
    .w-100 {width:100%;}
    .logo-box {width:120px; margin-bottom: 20px;}
    .logo-box.text-right {margin-left: auto;}
    .text-center {text-align:center;}
    .table {width:100%; border: 1px solid #ccc; border-collapse: collapse;}
    .table tr {padding: 0; margin: 0; border-bottom: 1px solid #ccc;}
    .table tr th, .table tr td {padding: 10px 5px;margin: 0; border-top: 1px solid #ccc; border-right: 1px solid #ccc;}
    .section {margin-bottom: 30px;}
    .section-title {margin-bottom: 10px; }
    .section-title1 {margin-bottom: 10px; font-weight: 600; font-family: 'Open Sans', sans-serif;}

    .section-1 {}  
    .text-right {text-align: right;}
    .text-center {text-align: center;}




    </style>
    
  </head>
  <body>
    @php $config_subjects = Config::get('variables.subjects') @endphp
    <div class="header">
            
        </div>
    <footer>
            Jefferson County School | Phone: 205-379-2000
        </footer>
    @foreach($student_data as $value)

        <div class="container page">
          <div class="header1">
            <table class="w-100">
                <tbody>
                    <tr>
                        <td class="w-100"><div class="logo-box"><img src="{{(isset($application_data) ? getDistrictLogo($application_data->display_logo) : getDistrictLogo())}}" title="" alt="" style="max-width: 100px !important;"></div></td>
                        <td class="w-100"><div class="logo-box text-right"><img src="{{url('/resources/assets/admin/images/login.png')}}" title="" alt="" style="max-width: 130px !important;"></div></td>
                    </tr>
                </tbody>
            </table>
            
        </div>
         <div class="wrapper">
            <div class="section section-1">
              <table class="table">
                    <tbody>
                        <tr>
                            <td class="w-50">Student Name: {{$value['name']}}</td>
                            <td class="">Submission ID : {{$value['id']}}</td>
                        </tr>
                        <tr>
                            <td class="">Confirmation No: {{$value['confirmation_no']}}</td>
                            <td class="">Date of Birth: {{$value['birth_date']}} </td>
                        </tr>
                        <tr>
                            <td class="">First Choice : {{$value['first_choice']}}</td>
                            <td class="">Second Choice : {{($value['second_choice'] != "" ? $value['second_choice'] : "NA")}}</td>
                        </tr>
                        <tr>
                            <td class="">Student ID : {{($value['student_id'] != "" ? $value['student_id'] : "")}}</td>
                            <td class="">Submission Date : {{$value['created_at']}}</td>
                        </tr>

                    </tbody>
                </table>
              </div>
              <div class="section section-1">

                <div class="section-title text-center"><strong>Student Application Data Sheet</strong></div>
              </div>              
              <div class="section section-2">
                  <div class="section-title"><strong>Academic Grades (2019-2020)</strong></div>
                  <table class="table text-center">
                      <tr>
                        @foreach($subjects as $sbjct)
                            @foreach($terms as $term)
                                <th class="align-middle">{{$config_subjects[$sbjct]}} {{$term}}</th>
                            @endforeach
                        @endforeach
                      </tr>
                      <tr>
                        @foreach($value['score'] as $skey=>$sbjct)
                                @foreach($terms as $term)
                                    <td class="align-middle text-center">
                                        @if(isset($sbjct[$term]))
                                            {{$sbjct[$term]}}
                                        @else
                                            {{"NA"}}
                                        @endif
                                    </td>
                                @endforeach
                            @endforeach

                      </tr>
                    </table>
              </div>
              <div class="section section-2">
                  <div class="section-title"><strong>Conduct Disciplinary Information (2019 - 2020)</strong></div>
                  <table class="table">
                    <tr>
                        <th class="align-middle">B Info</th>
                        <th class="align-middle">C Info</th>
                        <th class="align-middle">D Info</th>
                        <th class="align-middle">E Info</th>
                        <th class="align-middle">#Susp</th>
                        <th class="align-middle">Susp Days</th>
                    </tr>
                    <tr>
                       <td class="text-center">{{$value['cdi']['b_info']}}</td>
                        <td class="text-center">{{$value['cdi']['c_info']}}</td>
                        <td class="text-center">{{$value['cdi']['d_info']}}</td>
                        <td class="text-center">{{$value['cdi']['e_info']}}</td>
                        <td class="text-center">{{$value['cdi']['susp']}}</td>
                        <td class="text-center">{{$value['cdi']['susp_days']}}</td>
                    </tr>
                  </table>
              </div>
         </div>
         
          
         
          
        </div>

   
    @endforeach
  </body>

  <style>
    .page {
       page-break-after: always;
    }
    .page:last-child {
       page-break-after: unset;
    }
  </style>
</html>