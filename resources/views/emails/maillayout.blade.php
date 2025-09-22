<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">
@media only screen and (max-width: 600px) {
.inner-body {
    width: 100% !important;
}
.footer {
    width: 100% !important;
}
}

@media only screen and (max-width: 500px) {
.button {
    width: 100% !important;
}
}
td {color:#000 !important;}

</style>
<table width="600" class="inner-body" align="center" style="width:600px; border: none; margin: 0 auto;font-family: Avenir, Helvetica, sans-serif;" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="3" style="height: 5px;"><div class="header" style="background: #236cb9;color: #bbbfc3;"></div></td>
    </tr>
    <tr>
        <td colspan="3" align="center">
            {{-- @php
                if (isset($message)) {
                    $logo_src = $message->embed($data['logo']);
                } else {
                    $logo_src = $data['logo'];
                }
            @endphp --}}
            <div class="header" style="background: #ffffff;color: #bbbfc3;"><img style="padding: 15px; max-height: 100px !important;" src="{{$data['logo']}}" height="100">
        </td>
    </tr>
    
    <tr>
        <td colspan="3" style="height: 5px;"><div class="header" style="background: #236cb9;color: #bbbfc3;"></div></td>
    </tr>
    <tr><td colspan="3" style="height: 10px;"></td></tr>
    <tr>
       {{--  <td style="width: 20px;"></td> --}}
        <td colspan="3">
            @yield('content')

        </td>
        {{-- <td style="width: 20px;">&nbsp;</td> --}}
    </tr>
    <tr><td colspan="3" style="height: 10px;"></td></tr>
    <tr><td colspan="3" style="height:1px;"><div class="footer" style="text-align: center;background: #236cb9;padding:1px ;line-height:30px;color: #AEAEAE;"></div></td></tr>
    <tr><td colspan="3"><div class="footer" style="text-align: center;background: #236cb9;padding:1px;color: #AEAEAE;"><p style="color:#fff; font-size:13px;">© Copyrights {{date('Y')}} Lean Frog Digital, LLC. All rights Reserved.</p></div></td></tr>
    <tr><td colspan="3" style="height:1px;"><div class="footer" style="text-align: center;background: #236cb9;padding:1px ;line-height:30px;color: #AEAEAE;"></div></td></tr>
</table>