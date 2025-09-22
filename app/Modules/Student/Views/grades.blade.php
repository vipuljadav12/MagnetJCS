@extends('layouts.admin.app')
@section('title')
	Student Grades
@endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">
                Student Grades For "{{$id}} {{getStudentName($id)}}" 
            </div>
        </div>
    </div>
    <div class="card shadow">
        <div class="card-body">
            <div class="pt-20 pb-20">
                <div class="table-responsive">
                    <table id="tbl_student_grades" class="table table-striped mb-0">
                        <thead>
                            <tr> 
                                <th class="align-middle">Academic year</th>
                                <th class="align-middle">Academic Term</th>
                                <th class="align-middle">Grade Name</th>
                                <th class="align-middle">Course Type ID</th>
                                <th class="align-middle">Course Type</th>
                                <th class="align-middle">Course Name</th>
                                <th class="align-middle">Course Full Name</th>
                                <th class="align-middle">Section Number</th>
                                <th class="align-middle">Full Section Number</th>
                                <th class="align-middle">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @isset($grades)
                            @foreach($grades as $value)
                            <tr>
                                <td class="">
                                    {{$value->academicYear}}
                                </td>
                                <td class="">
                                    {{$value->academicTerm}}
                                </td>
                                <td class="">
                                    {{$value->GradeName}}
                                </td>
                                <td class="">
                                    {{$value->courseTypeID}}
                                </td>
                                <td class="">
                                    {{$value->courseType}}
                                </td>
                                <td class="">
                                    {{$value->courseName}}
                                </td>
                                <td class="">
                                    {{$value->courseFullName}}
                                </td>
                                <td class="">
                                    {{$value->sectionNumber}}
                                </td>
                                <td class="">
                                    {{$value->fullsection_number}}
                                </td>
                                <td class="">
                                    {{$value->numericGrade}}
                                </td>
                            </tr>
                            @endforeach
                            @endisset
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script type="text/javascript">
    $("#tbl_student_grades").DataTable({});
</script>
@endsection