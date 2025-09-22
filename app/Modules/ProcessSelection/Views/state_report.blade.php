@extends('layouts.admin.app')
@section('title')Process Selection | {{config('app.name',env("APP_NAME"))}} @endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Current State Report</div>
        </div>
        <div>      
            <div class="table-responsive" id="table-wrap" style="overflow-y: scroll; height: 100%;">
                <table class="table table-bordered" >
                    <thead>
                        <tr>
                            <th class="">Program Name</th>
                            <th class="text-center">Initial<br>Available Seats</th>
                            <th class="text-center">Offered and<br>Accepted</th>
                            <th class="text-center">Final Available Seats</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($programAvailability as $pkey=>$pvalue)
                            <tr class="bg-info">
                                <td>{{getProgramName($pkey)}}</td>
                                <td class="text-center">{{$pvalue['availability']}}</td>
                                <td class="text-center">{{$pvalue['offered_accepted']}}</td>
                                <td class="text-center">{{$pvalue['availability']-$pvalue['offered_accepted']}}</td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                     <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" class="">School Name</th>
                                                <th class="text-center" colspan="4">Black</th>
                                                <th class="text-center" colspan="4">Non-Black</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center">Initial<br>RC</th>
                                                <th class="text-center">Withdrawns</th>
                                                <th class="text-center">Offered</th>
                                                <th class="text-center">ADM<br>Population<br>(%)</th>
                                                
                                                <th class="text-center">Initial<br>RC</th>
                                                <th class="text-center">Withdrawns</th>
                                                <th class="text-center">Offered</th>
                                                <th class="text-center">ADM<br>Population<br>(%)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pvalue as $key=>$value)
                                                @if(!in_array($key, ['availability', 'offered_accepted']))
                                                    <tr>
                                                        <td>{{$key}}</td>
                                                        <td class="text-center">{{$value->black ?? 0}}</td>
                                                        <td class="text-center">{{$value->black_added ?? 0}}</td>
                                                        <td class="text-center">{{$value->black_withdrawn ?? 0}}</td>
                                                        <td class="text-center">{{$schoolAdmData[$key]['black']['value'] ?? 0}}%</td>

                                                        <td class="text-center">{{$value->non_black ?? 0}}</td>
                                                        <td class="text-center">{{$value->non_black_added ?? 0}}</td>
                                                        <td class="text-center">{{$value->non_black_withdrawn ?? 0}}</td>
                                                        <td class="text-center">{{$schoolAdmData[$key]['non_black']['value'] ?? 0}}%</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                     </table>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    
       
       
@endsection