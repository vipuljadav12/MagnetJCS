            <table>
                <tr>
                        <td class="text-center align-middle" style="font-size: 10px;">Report Owner:  <strong>Magnet Programs Coordinator</strong><br><br>Data Source:  <strong>MyPick Magnet System/ Student<br>Informations System</strong></td>
                        <td class="text-center align-middle" style="font-size: 10px;" colspan="4">Consent Order Reference: <strong>II.F.1</strong></td>
                        <td></td>
                        <td class="text-center align-middle" style="font-size: 10px;" colspan="6"><strong>Revision Date:</strong> </td>

                          

                    </tr>
                <thead>
                    
               
                        <tr>
                            <th class="text-center align-middle" style="font-size: 10px;" rowspan="2">Name of Magnet Program/School</th>
                            <th class="text-center align-middle" colspan="3">Number of Applicants</th>
                            <th class="text-center align-middle" colspan="3">Number of Students<br>Offered</th>
                            <th class="text-center align-middle" colspan="3">Number of Students Denied Due to <br>Committee Review</th>
                            <th class="text-center align-middle" colspan="3">Number of Students Denied Due to <br>Space</th>
                            <th class="text-center align-middle" colspan="3">Total Number of Students <br>Withdrew/Transferred (include reasons)</th>
                            <th class="text-center align-middle" colspan="3">Total Number of Applicants<br> Enrolled</th>
                            <th class="text-center" colspan="3">Total School Enrollment <br> October 1</th>
                        </tr>
                        <tr>
                            @foreach($race_ary as $rk=>$rv)
                                <th class="align-middle">{{$rv}}</th>
                            @endforeach
                            @foreach($race_ary as $rk=>$rv)
                                <th class="align-middle">{{$rv}}</th>
                            @endforeach
                            @foreach($race_ary as $rk=>$rv)
                                <th class="align-middle">{{$rv}}</th>
                            @endforeach
                            @foreach($race_ary as $rk=>$rv)
                                <th class="align-middle">{{$rv}}</th>
                            @endforeach
                            @foreach($race_ary as $rk=>$rv)
                                <th class="align-middle">{{$rv}}</th>
                            @endforeach
                            @foreach($race_ary as $rk=>$rv)
                                <th class="align-middle">{{$rv}}</th>
                            @endforeach
                            @foreach($race_ary as $rk=>$rv)
                                <th class="align-middle">{{$rv}}</th>
                            @endforeach

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($court_data as $key=>$value)
                            <tr>
                                <td>{{$value['name']}}</td>
                                <!-- Application Count -->
                                <td class="text-center">{{$value['applications']['Black']}}</td>
                                <td class="text-center">{{$value['applications']['White']}}</td>
                                <td class="text-center">{{$value['applications']['Other']}}</td>

                                <!-- Offered Count -->
                                <td class="text-center">{{$value['offered']['Black']}}</td>
                                <td class="text-center">{{$value['offered']['White']}}</td>
                                <td class="text-center">{{$value['offered']['Other']}}</td>

                                <!-- Ineligible Count -->
                                <td class="text-center">{{$value['ineligible']['Black']}}</td>
                                <td class="text-center">{{$value['ineligible']['White']}}</td>
                                <td class="text-center">{{$value['ineligible']['Other']}}</td>

                                <!-- Denied Space Count -->
                                <td class="text-center">{{$value['denied_space']['Black']}}</td>
                                <td class="text-center">{{$value['denied_space']['White']}}</td>
                                <td class="text-center">{{$value['denied_space']['Other']}}</td>

                                <!-- Withdrawn Count -->
                                <td class="text-center">{{$value['withdrawn']['Black']}}</td>
                                <td class="text-center">{{$value['withdrawn']['White']}}</td>
                                <td class="text-center">{{$value['withdrawn']['Other']}}</td>

                                <!-- Enrolled Count -->
                                <td class="text-center">{{$value['enrolled_data']['Black']}}</td>
                                <td class="text-center">{{$value['enrolled_data']['White']}}</td>
                                <td class="text-center">{{$value['enrolled_data']['Other']}}</td>

                                <!-- 1st Oc Enrolled Count -->
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>

                            </tr>
                        @endforeach

                    </tbody>
                    <tbody>
                        

                    </tbody>
                </table>

<table>
    <tr><th colspan="4" align="center" style="text-align: center !important;">Total Number of Applicants</th></tr>
    <tr>
        <th>Applicants</th>
        <th>#<br>Black</th>
        <th>#<br>White</th>
        <th>#<br>Other</th>
    </tr>
    <tr>
        <th rowspan="2">Totals</th>
        <th>{{$Black}}</th>
        <th>{{$White}}</th>
        <th>{{$Other}}</th>
    </tr>
    <tr>
        <td colspan="3">{{$Black+$White+$Other}}</td>
    </tr>
    <tr><td colspan="4"></td></tr>
    <tr><td colspan="4"></td></tr>

    <tr><td colspan="4">MyPick System Report Date: <strong>{{getDateTimeFormat(date("Y-m-d H:i:s"))}}</strong></td></tr>
</table>