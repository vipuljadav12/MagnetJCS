<?php

namespace App\Traits;

use App\Modules\AuditTrailData\Models\AuditTrailData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

trait AuditTrail
{

    private $ignoreFields = [
        "password",
        "submission_id",
        "application_id",
        "program_id",
        "created_at",
        "updated_at",
        "special_accommodations",
        "non_hsv_student",
        "open_enrollment",
        "first_choice_program_id",
        "second_choice_program_id"
    ];
    public function catchChanges($any)
    {
        print_r("Trait called");
        print_r($any);
        exit;
    }

    public function modelCreate($base, $module)
    {
        $fields = $base->fillable;
        //$fields[] = isset($base->primaryKey) ? $base->primaryKey : "id";

        if (isset($base->primaryKey)) {
            $old_values[(isset($base->traitField) ? $base->traitField : $base->primaryKey)] = $new_values[(isset($base->traitField) ? $base->traitField : $base->primaryKey)] = $base->{(isset($base->traitField) ? $base->traitField : $base->primaryKey)};

            if (isset($base->additional)) {
                foreach ($base->additional as $key => $value) {
                    if (isset($base->{$value})) {
                        ${$value} = $base->{$value};
                    }
                }
            }

            $enrollmentstr = "enrollment_id";
            $applicationstr = "application_id";
        }
        if (isset($base->date_fields))
            $date_fields = $base->date_fields;
        else
            $date_fields = array();

        foreach ($fields as $f => $field) {
            if (!in_array($field, $this->ignoreFields)) {
                if ($module == "Submission By Admin") {
                    if (in_array($field, $base->createField)) {
                        if (in_array($field, $date_fields)) {
                            $old_values[$field] = getDateFormat($base->$field);
                        } elseif ($field == "first_choice" || $field == "second_choice" || $field == "third_choice") {
                            $old_values[$field] = getApplicationProgramName($base->$field);
                        } else {
                            if ($module == "Submission - Committee Score")
                                $old_values["committee_score"] = $base->$field;
                            elseif ($module == "Submission - Interview Score")
                                $old_values["interview_score"] = $base->$field;
                            else
                                $old_values[$field] = $base->$field;
                        }
                    }
                } else {
                    if (in_array($field, $date_fields)) {
                        $old_values[$field] = getDateFormat($base->$field);
                    } elseif ($field == "first_choice" || $field == "second_choice" || $field == "third_choice") {
                        $old_values[$field] = getApplicationProgramName($base->$field);
                    } else {
                        if ($module == "Submission - Committee Score")
                            $old_values["committee_score"] = $base->$field;
                        elseif ($module == "Submission - Interview Score")
                            $old_values["interview_score"] = $base->$field;
                        else
                            $old_values[$field] = $base->$field;
                    }
                }

                //$old_values[$field] = $base->$field;
            }
        }
        if ($module == "Submission - Committee Score") {
            $old_values["program"] = getProgramName($base->program_id);
        }
        if ($module == "Submission By Admin") {
            $old_values['submitted_by'] = "HCS User";
        }
        $insert_data = array(
            'user_id' => Auth::user()->id,
            "old_values" => json_encode($old_values, 1),
            "module" => $module,
            "enrollment_id" => (isset(${$enrollmentstr}) ? ${$enrollmentstr} : 0),
            "application_id" => (isset(${$applicationstr}) ? ${$applicationstr} : 0),

        );
        AuditTrailData::create($insert_data);
    }

    public function modelChanges($base, $result, $module)
    {
        // echo "done";
        $fields = $base->fillable;
        $changed_fields = $old_values = $new_values = [];

        $old_values[(isset($base->traitField) ? $base->traitField : $base->primaryKey)] = $new_values[(isset($base->traitField) ? $base->traitField : $base->primaryKey)] = $result->{(isset($base->traitField) ? $base->traitField : $base->primaryKey)};

        if (isset($base->date_fields))
            $date_fields = $base->date_fields;
        else
            $date_fields = array();
        if (isset($base->additional)) {
            foreach ($base->additional as $key => $value) {
                if (isset($base->{$value})) {
                    ${$value} = $base->{$value};
                }
            }
        }

        $enrollmentstr = "enrollment_id";
        $applicationstr = "application_id";

        foreach ($fields as $f => $field) {
            if (!in_array($field, $this->ignoreFields)) {
                if (in_array($field, $date_fields)) {
                    $old_values[$field] = getDateFormat($base->$field);
                    $new_values[$field] = getDateFormat($result->$field);
                } elseif ($field == "first_choice" || $field == "second_choice" || $field == "third_choice") {
                    $old_values[$field] = getApplicationProgramName($base->$field);
                    $new_values[$field] = getApplicationProgramName($result->$field);
                } else {
                    if ($module == "Submission - Committee Score") {
                        $old_values["committee_score"] = $base->$field;
                        $new_values["committee_score"] = $result->$field;

                        if (!is_array($base->$field)) {
                            if (strtolower($base->$field)  != strtolower($result->$field)) {
                                $changed_fields[] = "committee_score";
                                // print_r($field);
                            } else {
                                unset($old_values[$field]);
                                unset($new_values[$field]);
                            }
                        }
                    } elseif ($module == "Submission - Interview Score") {
                        $old_values["interview_score"] = $base->$field;
                        $new_values["interview_score"] = $result->$field;

                        if (!is_array($base->$field)) {
                            if (strtolower($base->$field)  != strtolower($result->$field)) {
                                $changed_fields[] = "interview_score";
                                // print_r($field);
                            } else {
                                unset($old_values[$field]);
                                unset($new_values[$field]);
                            }
                        }
                    } else {
                        $old_values[$field] = $base->$field;
                        $new_values[$field] = $result->$field;
                    }
                }

                if (!is_array($base->$field)) {
                    if (strtolower($base->$field)  != strtolower($result->$field)) {
                        $changed_fields[] = $field;
                        // print_r($field);
                    } else {
                        unset($old_values[$field]);
                        unset($new_values[$field]);
                    }
                }
            }
            // print_r($base->$field  != $result->$field);
            // print_r($base->$field."---".$result->$field);
            // print_r("<br>");
        }

        if ($module == "Submission - Committee Score") {
            $old_values["program"] = getProgramName($result->program_id);
            $new_values["program"] = getProgramName($result->program_id);
        }
        // exit;
        //dd($changed_fields);
        if (count($changed_fields) > 0) {
            $insert_data = array(
                'user_id' => Auth::user()->id,
                "changed_fields" => json_encode($changed_fields, 1),
                "old_values" => json_encode($old_values, 1),
                "new_values" => json_encode($new_values, 1),
                "module" => ucwords($module),
                "enrollment_id" => (isset(${$enrollmentstr}) ? ${$enrollmentstr} : 0),
                "application_id" => (isset(${$applicationstr}) ? ${$applicationstr} : 0),
            );
            // print_r($insert_data);exit;
            AuditTrailData::create($insert_data);
        }
    }

    public function modelGradeChanges($base, $result, $module)
    {

        $courseType = Config::get('variables.courseType');
        $changed_fields = $old_values = $new_values = [];
        $submission_id = $application_id = $enrollment_id = 0;
        foreach ($result as $key => $value) {
            $numericGrade = $value['numericGrade'];
            $academicYear = $value['academicYear'];
            $academicTerm = $value['academicTerm'];
            $courseTypeID = $value['courseTypeID'];

            $submission_id = $value['submission_id'];
            $application_id = $value['application_id'];
            $enrollment_id = $value['enrollment_id'];

            $changed = true;
            $new_values['submission_id'] = $old_values['submission_id'] = $submission_id;
            foreach ($base as $bkey => $bvalue) {
                if ($bvalue['academicYear'] == $academicYear && $bvalue['academicTerm'] == $academicTerm && $bvalue['courseTypeID'] == $courseTypeID) {

                    if ($bvalue['numericGrade'] != $numericGrade) {
                        $changed_fields[] = $courseType[$courseTypeID] . " - " . $academicTerm . "(" . $academicYear . ")";
                        $new_values[$courseType[$courseTypeID] . " - " . $academicTerm . "(" . $academicYear . ")"] = $numericGrade;
                        $old_values[$courseType[$courseTypeID] . " - " . $academicTerm . "(" . $academicYear . ")"] = $bvalue['numericGrade'];
                    }
                    $changed = false;
                }
            }
            if ($changed == true) {
                $new_values[$courseType[$courseTypeID] . " - " . $academicTerm . "(" . $academicYear . ")"] = $numericGrade;
                $old_values[$courseType[$courseTypeID] . " - " . $academicTerm . "(" . $academicYear . ")"] = 0;
                $changed_fields[] = $courseType[$courseTypeID] . " - " . $academicTerm . "(" . $academicYear . ")";
            }
        }

        if (count($changed_fields) > 0) {
            $insert_data = array(
                'user_id' => Auth::user()->id,
                "changed_fields" => json_encode($changed_fields, 1),
                "old_values" => json_encode($old_values, 1),
                "new_values" => json_encode($new_values, 1),
                "module" => ucwords($module),
                "enrollment_id" => $enrollment_id,
                "application_id" => $application_id,
            );
            // print_r($insert_data);exit;
            AuditTrailData::create($insert_data);
        }
    }


    public function modelCDICreate($base, $module)
    {
        $changed_fields = $old_values = $new_values = [];
        $fields = $base->fillable;
        $fields[] = isset($base->primaryKey) ? $base->primaryKey : "id";

        if (isset($base->primaryKey)) {
            $old_values[(isset($base->traitField) ? $base->traitField : $base->primaryKey)] = $new_values[(isset($base->traitField) ? $base->traitField : $base->primaryKey)] = $base->{(isset($base->traitField) ? $base->traitField : $base->primaryKey)};

            if (isset($base->additional)) {
                foreach ($base->additional as $key => $value) {
                    if (isset($base->{$value})) {
                        ${$value} = $base->{$value};
                    }
                }
            }

            $enrollmentstr = "enrollment_id";
            $applicationstr = "application_id";
        }
        foreach ($fields as $f => $field) {
            if (!in_array($field, $this->ignoreFields) && $field != "submission_id" && $field != "stateID" && $field != "id") {
                $old_values[$field] = 0;
                $new_values[$field] = $base->$field;
                $changed_fields[] = $field;
            }
        }
        $insert_data = array(
            'user_id' => Auth::user()->id,
            "old_values" => json_encode($old_values, 1),
            "new_values" => json_encode($new_values, 1),
            "changed_fields" => json_encode($changed_fields, 1),
            "module" => $module,
            "enrollment_id" => (isset(${$enrollmentstr}) ? ${$enrollmentstr} : 0),
            "application_id" => (isset(${$applicationstr}) ? ${$applicationstr} : 0),

        );
        AuditTrailData::create($insert_data);
    }

    public function modelTestScoreAudit($base = [], $result = [], $module)
    {
        $changed_fields = [];
        $application_id = $base['application_id'];
        $enrollment_id = $base['enrollment_id'];

        if (!empty($result)) {
            foreach ($result['ts_data'] as $ts_name => $ts_rank) {
                if (!isset($base['ts_data'][$ts_name]) || $ts_rank != $base['ts_data'][$ts_name]) {
                    if ($ts_rank != '')
                        $changed_fields[] = $ts_name;
                }
            }
        }
        unset($base['application_id']);
        unset($base['enrollment_id']);
        unset($base['program_id']);

        if (count($changed_fields) > 0 || empty($result)) {
            $insert_data = array(
                'user_id' => Auth::user()->id,
                "old_values" => json_encode($base, 1),
                "module" => ucwords($module),
                "enrollment_id" => $enrollment_id,
                "application_id" => $application_id,
            );
            if (!empty($result)) {
                unset($result['application_id']);
                unset($result['enrollment_id']);
                unset($result['program_id']);

                $insert_data["changed_fields"] = json_encode($changed_fields, 1);
                $insert_data["new_values"] = json_encode($result, 1);
            }
            AuditTrailData::create($insert_data);
        }
    }
}
