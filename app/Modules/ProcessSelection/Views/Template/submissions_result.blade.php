<div class="tab-pane fade show active" id="preview04" role="tabpanel" aria-labelledby="preview04-tab">
    <div class=" @if($display_outcome > 0) d-none @endif" style="height: 704px; overflow-y: auto;">
        <div class="row col-md-12" id="submission_filters" style="padding: 10px 0 !important"></div>
        <div class="table-responsive" style="overflow-y: scroll; height: 100%;">
                    

            <table class="table" id="tbl_submission_results">
                <thead>
                    <tr>
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Submission ID</th>
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Student Name</th>
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Next Grade</th>
                        <th class="notexport" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Program</th>
                        <th class="notexport" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Outcome</th>
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Race</th>
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">School</th>
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Program</th>
                        <th class="text-center" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Choice</th>
                        <th class="text-center" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Outcome</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($final_data as $key=>$value)
                        <tr>
                            <td class="">{{$value['id']}}</td>
                            <td class="">{{$value['name']}}</td>
                            <td class="text-center">{{$value['grade']}}</td>
                            <td class="">{{$value['program_name']}}</td>
                            <td class="">{{$value['offered_status']}}</td>
                            <td class="">{{$value['race']}}</td>
                            <td class="">{{$value['school']}}</td>
                            <td class="">{{$value['program']}}</td>
                            <td class="text-center">{{$value['choice']}}</td>
                            <td class="">{!! $value['outcome'] !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
    </div>
    <div class="d-flex flex-wrap justify-content-between pt-20"><a href="javascript:void(0);" class="btn btn-secondary" title="" id="ExportReporttoExcel">Download Submissions Result</a>@if($display_outcome == 0) <a href="javascript:void(0);" class="btn btn-success" title="" onclick="updateFinalStatus()">Accept Outcome and Commit Result</a> @else <a href="javascript:void(0);" class="btn btn-danger" title="" onclick="alert('Already Outcome Commited')">Accept Outcome and Commit Result</a>  @endif</div>
</div>