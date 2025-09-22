<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Writing Sample</title>
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
  <body>
     <header>
        <div class="header">
          <table class="w-100">
              <tbody>
                  <tr>
                      <td class="w-100 text-center">
                       <div class="text-center font-20 b-600 mb-40">
                      <b><h3>Writing Sample</h3></b>
                    </div>
                       </td>
                  </tr>
              </tbody>
          </table>
      </div>
    </header> 
    <footer>
      <hr>
        {{-- <div class="text-center">1 Magnum Pass Mobile, AL 36618 | Phone: 251-221-4000</div> --}}
    </footer>

    <div class="container page">
      <div class="wrapper">
        <div class="section section-1">
          <div class="">
                <div class="p-20">
                    <
                    <div class="">
                        <div class="row">
                            <div class="col-12 col-lg-5 mb-10">
                              Student Name : <strong>{{$submission->first_name ." ".$submission->last_name}}</strong>
                              <span class="col-lg-4 text-lg-right">Submission #: <strong>{{$submission->confirmation_no}}</strong></span></div>
                              <span class="col-lg-4 text-lg-right">Grade : <strong>{{$submission->next_grade}}</strong></span></div>
                            <div class="col-12 col-lg-5 mb-10">Program Name : <strong>{{$submission['program_name']}}</strong></div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="">
              @if(isset($wp_detail))
                @foreach($wp_detail as $value)
                  <div class="row col-12"><b>Question:<br></b>{{$value->writing_prompt or ''}}</div><br>
                  <div class="row col-12"><b>Ans:</b></div><br>
                  <div>{{$value->writing_sample or ''}}</div><br><br>
                @endforeach
              @endif
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