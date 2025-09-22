@php
    $rsAcademicData = getSubmissionAcademicGradeData($submission);
@endphp

@if(!empty($rsAcademicData))
    <table class="table table-bordered">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Academic Year</th>
          <th scope="col">Academic Term</th>
          <th scope="col">Grade Level</th>
          <th scope="col">School Name</th>
          <th scope="col">Teacher Name</th>
          <th scope="col">Student Absences</th>
          <th scope="col">Earned Credit<br>Hrs</th>
          <th scope="col">Added GPA<br>Value</th>
          <th scope="col">Credit Type</th>
          <th scope="col">Course Name</th>
          <th scope="col">Course Number</th>
          <th scope="col">Percent</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rsAcademicData as $avalue)
            <tr>
              <th scope="row">{{$loop->iteration}}</th>
              <td>{{$avalue->academicYear}}</td>
              <td>{{$avalue->GradeName}}</td>
              <td>{{$avalue->grade_level}}</td>
              <td>{{$avalue->school_name}}</td>
              <td>{{$avalue->teacher_name}}</td>
              <td class="text-center">{{$avalue->student_absense}}</td>
              <td class="text-center">{{$avalue->earned_cr_hrs}}</td>
              <td class="text-center">{{$avalue->added_gpa}}</td>
              <td>{{$avalue->courseType}}</td>
              <td>{{$avalue->courseName}}</td>
              <td>{{$avalue->sectionNumber}}</td>
              <td class="text-center">{{$avalue->percent}}</td>
            </tr>
        @endforeach
      </tbody>
    </table>
@endif
