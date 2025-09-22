@extends('layouts.admin.app')


@section('styles')
    <style type="text/css">
        .error{
            color: #e33d2d;
        }
    </style>
@stop

@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Zoned School Search</div>
        <div>
            <a href="{{url('/admin/ZonedSchool/import')}}" title="Import Zoned School Addresses" class="btn btn-info"><i class="fa fa-upload"></i> Import  Addresses</a>
            <a href="javascript::void(0)" onclick="export_zone_address()" title="Export Zoned School Addresses" class="btn btn-info"><i class="fa fa-download"></i> Export Addresses</a>
        </div>
    </div>
</div>

    <div class="raw">
        <div class="card shadow">
            <div class="card-body">
                <div class="form-group">
                    <label class="control-label">Street Address : </label>
                    <div class="">
                        <input type="text" name="address" value="" id="address" class="form-control" value="">
                    </div>
                </div>
                 <div class="form-group">
                    <label class="control-label">City : </label>
                    <div class="">
                        <input type="text" name="city" value="" id="city" class="form-control" value="">
                    </div>
                </div>
                 <div class="form-group">
                    <label class="control-label">Zip : </label>
                    <div class="">
                        <input type="text" name="zip" value="" id="zip" class="form-control" value="">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">Select Grade : </label>
                    <div class="">
                        <select name="grade" class="form-control" id="grade">
                            <option value="PreK">PreK</option>
                            <option value="K">K</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label"></label>
                    <div class="">
                        {{-- <input type="button" name="Fetch Schools" class="btn btn-success" value="Fetch Schools" onclick="fetch_zoned_school();"> --}}
                        <input type="button" name="Fetch Schools" class="btn btn-success" value="Fetch Schools">
                    </div>
                </div>
            </div>
        </div>
        <div id="response">
            
        </div>
    </div>
</form>
@stop

@section('scripts')
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script>

        var bldg_num=street_name=street_type=city=zip="";
        var dtbl_submission_list = $("#datatable").DataTable({
            "aaSorting": [],
            'serverSide': true,
                'ajax': {
                    url: "{{url('admin/ZonedSchool/getzonedschool')}}",
                    "data": function ( d ) {
                        d.bldg_num = bldg_num;
                        d.street_name = street_name;
                        d.street_type = street_type;
                        d.city = city;
                        d.zip = zip;
                    }
                    //type: 'POST'
                },
            dom: 'Bfrtip'
            });

        function fetch_zoned_school()
        {
            $.ajax({
                url:'{{url('/admin/ZonedSchool/search')}}',
                dataSrc: "Data",
                type:"post",
                data:{_token:'{{csrf_token()}}',address:$("#address").val(),grade: $("#grade").val(),zip: $("#zip").val(),city: $("#city").val()},
                success:function(response){
                    $("#response").html(response);
                }

            }).done( function(data) {
                $('#zoneList').dataTable({
                    "searching": false,
                    'columnDefs': [
                    {
                        "targets": "_all", // your case first column
                        "className": "text-center",
                    }],
                    // dom: 'Bfrtip',
                    // buttons: [
                    //     {
                    //         extend: 'excelHtml5',
                    //         title: 'Excel',
                    //         text:'Export to excel'
                    //     }
                    // ]
                });
            });
        }
        /*-- Description field validation end --*/

        function export_zone_address(){
            location.href = '{{url('/admin/ZonedSchool/export')}}?address='+$("#address").val()+'&grade='+$("#grade").val()+'&zip='+$("#zip").val()+'&city='+$("#city").val();
        }
    </script>
@stop

