<div class="card shadow">
<div class="card-header">{{$eligibility->name}}</div>
<div class="card-body">
    <div class="table-responsive">
        <table class="table table-striped mb-0" id="grade-table">
            <thead>
                <tr>
                    <th class="align-middle">Academic year</th>
                    <th class="align-middle">Academic Term</th>
                    <th class="align-middle">Course Type</th>
                    <th class="align-middle w-120">Section Number</th>
                    <th class="align-middle">Class Name</th>
                    <th class="align-middle w-120">Grade(0-100)</th>
                    <th class="align-middle w-120">Used to Calculate GPA</th>
                </tr>
@php
    if(isset($eligibility))
    {
        $content = json_decode($eligibility->content) ?? null;
        // print_r($eligibility->content);
        $needRow = 0;
        // $subjectsLength = count($eligibility->subjects);

        /*if($content->academic_term == "SEM")
        {
            $needRow = $subjectsLength * 2;
        }*/
        $terms = array();
        if($content->academic_term == "SEM")
        {
            $terms = array(
                "s1"=>"Semester 1",
                "s2"=>"Semester 2"
            );
        }
        if($content->academic_term == "9W")
        {
            $terms = array(
                "1st" =>"1st 9 weeks",
                "2nd" =>"2nd 9 weeks",
                "3rd" =>"3rd 9 weeks",
                "4th" =>"4th 9 weeks"
            );
        }
        if($content->academic_term == "YE")
        {
            $terms = array(
                "YE" =>"Year End"
            );

        }
        // print_r($terms);
        // print_r($content->subjects);

    }
    $subjects = array("re"=>"Reading","eng"=>"English","math"=>"Math","sci"=>"Science","ss"=>"Social Studies","o"=>"other");
@endphp
            </thead>
            <tbody>
                @if(isset($terms) && count($terms) != 0)
                    @foreach($terms as $o=>$outer)
                        @foreach($content->subjects as $i=>$inner)
                            <tr>
                                <td class="">
                                    <select class="form-control custom-select form-control-sm">
                                        <option value="">2019-2020</option>
                                        <option value="">2020-2021</option>
                                    </select>
                                </td>
                                <td class="">
                                    <select class="form-control custom-select form-control-sm">
                                        @foreach($terms as $t=>$term)
                                            <option value="{{$t}}">{{$term ?? ""}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="">
                                    <select class="form-control custom-select form-control-sm">
                                        @foreach($subjects as $s=>$subject)
                                            @if(in_array($s, $content->subjects))
                                                <option>{{$subject}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </td>
                                <td class=""><input type="text" class="form-control form-control-sm" value=""></td>
                                <td class=""><input type="text" class="form-control form-control-sm" value=""></td>
                                <td class=""><input type="text" class="form-control form-control-sm" value=""></td>
                                <td class=""><input type="text" class="form-control form-control-sm" value=""></td>
                            </tr>
                        @endforeach
                    @endforeach
                @else
                    <tr class="text-center">
                        <td colspan="9">
                            <label>no data found</label>
                        </td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr class="text-center">
                    {{-- <td colspan="9"><a href="javascript:void(0);" class="btn btn-secondary add-grade" title="">Add New</a></td> --}}
                </tr>
            </tfoot>
        </table>
    </div>
</div>
</div>