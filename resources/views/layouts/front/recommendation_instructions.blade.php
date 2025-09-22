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

    body {padding:10px; margin:0; font-family: 'Open Sans', sans-serif; font-size:15px;}
    .container {max-width:720px; margin:0 auto; padding-top: 80px;}
    img {max-width:100%;}
    .w-50 {width:50%;}
    .w-80 {width:80%;}
    .w-100 {width:100%;}
    .logo-box {margin-bottom:20px;}
    .logo-box.text-right {margin-left:auto;}
    .text-center {text-align:center;}
    .table {width:100%; border:1px solid #ccc; border-collapse:collapse;}
    .table tr {padding:0; margin:0; border-bottom:1px solid #ccc;}
    .table tr th {padding:10px 5px;margin:0; border-top:1px solid #ccc; border-right:1px solid #ccc; font-size: 13px;}
    .table tr td {padding:10px 5px;margin:0; border-top:1px solid #ccc; border-right:1px solid #ccc;}
    .small-text{font-size:11px; line-height: 14px;}
    .section {margin-bottom:30px;}
    .section-title {padding:10px; text-align:center;background:#666;color:#fff;font-size:14px;text-transform:uppercase;}
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

                /** Extra personal styles **/
                
                
            }



    </style>
    
  </head>
  <body>
    @php $config_subjects = Config::get('variables.subjects') @endphp
    <header>
        <div class="header">
          <table class="w-100">
              <tbody>
                  <tr>
                      <td class="w-100" style="text-align:center;">
                        <center>
                         @php $logo = (isset($application_data) ? getDistrictLogo($application_data->display_logo) : getDistrictLogo()) @endphp
                        <div class="logo-box" style="padding-left: 10px;"><img src="{{str_replace('https://', 'http://', $logo)}}" title="" alt="" style="max-width: 300px" align="center"></div>
                          </center>
                      </td>
                      
                  </tr>
              </tbody>
          </table>
      </div>
    </header>
    <footer>
        </footer>
   <div class="container page">
          <div class="wrapper">
              <div class="section section-1">
                 <div class="mb-10 text-justify f-12">
                {!! $instructions !!}
              </div>
                 
                  
              </div>


              
          </div>
        </div>
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