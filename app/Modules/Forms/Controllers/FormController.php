<?php

namespace App\Modules\Form\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Form\Models\Form;
use App\Modules\Form\Models\Form_field;
use App\Modules\Form\Models\Field;
use App\Modules\Form\Models\PageForm;
use Illuminate\Support\Facades\Session;

class FormController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("Form::admin.index");
    }

    /**
     * Show the FormTest for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $data = PageForm::where('page_content_id', $id)->first();
        $page = Form::where('page_id', $data->page_id)->first();
        $page_form = PageForm::join('forms', 'forms.form_id', 'page_form.form_id')->where('page_form.page_content_id', $id)->get();
        foreach ($page_form as $key => $value) {
            $value['child'] = Form_field::where('form_field.form_id', $value->form_id)->orderBy('sort')->get();
            foreach ($value['child'] as $keys => $values) {
                $values['child'] = Field::where('form_field_id', $values->form_field_id)->get();
            }
        }
        // print_r($data);exit;
        $next = PageForm::where('page_content_id', ">", $id)->where('page_id', $data->page_id)->min('page_content_id');
        $previous = PageForm::where('page_content_id', "<", $id)->where('page_id', $data->page_id)->max('page_content_id');

        return view("Form::admin.create", ['data' => $data, 'next' => $next, 'previous' => $previous, 'page_form' => $page_form, 'page_content_id' => $id, 'page_id' => $data->page_id, 'page' => $page]);
    }


    public function setFormStyle(Request $request)
    {
        $data = $request->all();
        $page_id = $data['page_id'];
        $returnid = $data['pageContentId'];
        unset($data['_token']);
        unset($data['page_id']);
        if (isset($page_id)) {

            $formstyle = "<style>";
            for ($i = 0; $i < count($data['fieldName']); $i++) {
                $style = $data['fieldName'][$i] . "_font_style";
                $size = $data['fieldName'][$i] . "_font_size";
                $bold = $data['fieldName'][$i] . "_isBold";
                $italic = $data['fieldName'][$i] . "_isItalic";
                $underline = $data['fieldName'][$i] . "_isUnderline";

                $formstyle .= ($data['fieldName'][$i] == 'text') ? "\n.form-show" . $data['fieldName'][$i] : "\n.form-" . $data['fieldName'][$i];

                $formstyle .=   "{
                                    font-family:" . (isset($data[$style]) ? $data[$style] : 'unset') . ";
                                    font-size:" . (isset($data[$size]) ? $data[$size] : '') . ";
                                    font-weight:" . (isset($data[$bold]) ? 'bold' : 'normal') . ";
                                    font-style:" . (isset($data[$italic]) ? 'italic' : 'normal') . ";"
                    . (isset($data[$underline]) ? "text-decoration:underline" : '') . "                                    
                                }";
            }

            $formstyle .= "</style>";

            $fieldStyle = json_encode($data);

            $pageContentId = PageForm::where('page_id', $page_id)->pluck('page_content_id')->toArray();

            if (isset($pageContentId)) {
                PageForm::whereIn('page_content_id', $pageContentId)->update(['form_style' => $formstyle, 'form_fields_style' => $fieldStyle]);
            }

            return redirect('admin/form/' . $returnid);
        }
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $content_id = $data['page_content_id'];
        if (isset($data['formValue'])) {
            $pageform_data = PageForm::where('page_content_id', $content_id)->get();
            if (count($pageform_data) > 0) {
                $form_field_array = array();
                foreach ($data['formValue'] as $key => $value) {
                    $form_id = $value['form_id'];
                    unset($value['form_id']);
                    $form_insert['form_title'] = "";

                    Form::where('form_id', $form_id)->update($form_insert);

                    $layout = explode("_", $key);
                    $page_form['form_id'] = $form_id;
                    $page_form['page_content_id'] = $layout[0];
                    $page_form['layout'] = $layout[1];

                    PageForm::where('form_id', $form_id)->update($page_form);

                    $form_field_data = Form_field::where('form_id', $form_id)->get();

                    foreach ($form_field_data as $fields) {
                        Field::where('form_field_id', $fields->form_field_id)->delete();
                    }
                    //Form_field::where('form_id',$form_id)->delete();
                    foreach ($value as $keys => $values) {
                        $form_field['form_id'] = $form_id;
                        $form_field['type_id'] = $values['type_id'];
                        $form_field['sort'] = $values['sort'];
                        unset($values['sort']);
                        if (isset($values['form_field_id'])) {
                            $form_field_id = $values['form_field_id'];
                            Form_field::where('form_field_id', $form_field_id)->update($form_field);
                            unset($values['form_field_id']);
                        } else {
                            $form_field_id = Form_field::create($form_field)->form_field_id;
                        }

                        $form_field_array[] = $form_field_id;
                        foreach ($values as $property => $property_value) {
                            $field['field_property'] = $property;
                            $field['field_value'] = $property_value;
                            $field['form_field_id'] = $form_field_id;
                            Field::create($field);
                        }
                    }

                    Form_field::whereNotIn('form_field_id', $form_field_array)->where('form_id', $form_id)->delete();
                }
            } else {
                foreach ($data['formValue'] as $key => $value) {
                    $form_insert['form_title'] = "";
                    $form_id = Form::create($form_insert)->form_id;
                    $layout = explode("_", $key);
                    $page_form['form_id'] = $form_id;
                    $page_form['page_content_id'] = $layout[0];
                    $page_form['layout'] = $layout[1];
                    PageForm::create($page_form);
                    foreach ($value as $keys => $values) {
                        $form_field['form_id'] = $form_id;
                        $form_field['type_id'] = $values['type_id'];
                        $form_field['sort'] = $values['sort'];
                        unset($values['sort']);
                        $form_field_id = Form_field::create($form_field)->form_field_id;
                        foreach ($values as $property => $property_value) {
                            $field['field_property'] = $property;
                            $field['field_value'] = $property_value;
                            $field['form_field_id'] = $form_field_id;
                            Field::create($field);
                        }
                    }
                }
            }
        } else {
            $page_content_data = PageForm::where('page_content_id', $content_id)->first();
            $layout = explode('-', $page_content_data->page_layout);
            foreach ($layout as $key => $value) {
                $form_insert['form_title'] = "";
                $form_id = Form::create($form_insert)->form_id;
                $page_form['form_id'] = $form_id;
                $page_form['page_content_id'] = $content_id;
                $page_form['layout'] = $value;
                PageForm::create($page_form);
            }
        }
        $submit_data = $data['submit_data'];
        $page_data = PageForm::where('page_content_id', $content_id)->first();
        $page_id = $page_data->page_id;
        if ($submit_data == "next") {
            $next = PageForm::where('page_content_id', ">", $content_id)->where('page_id', $page_id)->min('page_content_id');
            $page_content_id = $next;
        } elseif ($submit_data == "previous") {
            $previous = PageForm::where('page_content_id', "<", $content_id)->where('page_id', $page_id)->max('page_content_id');
            $page_content_id = $previous;
        } else {
            $page_content_id = $content_id;
        }

        Session::flash('success', 'Form save successfully.');

        return $page_content_id;
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Form::leftjoin('form_field', 'form_field.form_id', 'forms.form_id')->where('forms.form_id', $id)->get();
        foreach ($data as $key => $value) {
            $value['child'] = Field::where('field.form_field_id', $value->form_field_id)->get();
        }

        return view("Form::admin.edit", ['data' => $data]);
    }
    /*Get Form View from */
    public function getFormView($type_id, $data)
    {
        switch ($type_id) {
            case 1:
                return view("Form::field_type.textbox", ['data' => $data]);
                break;
            case 3:
                return view("Form::field_type.email", ['data' => $data]);
                break;
            case 4:
                return view("Form::field_type.radio", ['data' => $data]);
                break;
            case 5:
                return view("Form::field_type.checkbox", ['data' => $data]);
                break;
            case 6:
                return view("Form::field_type.ckeditor", ['data' => $data]);
                break;
            case 7:
                return view("Form::field_type.dropdown", ['data' => $data]);
                break;
            case 9:
                return view("Form::field_type.textarea", ['data' => $data]);
                break;
            case 10:
                return view("Form::field_type.datepicker", ['data' => $data]);
                break;
            case 11:
                return view("Form::field_type.uploadfile", ['data' => $data]);
                break;
            case 12:
                return view("Form::field_type.toggle", ['data' => $data]);
                break;
                /*404*/
        }
    }

    public function preview($page_id)
    {
        $page = Form::where('page_id', $page_id)->first();
        $content = PageForm::where('page_id', $page_id)->get();
        if (!empty($content)) {
            foreach ($content as $key => $value) {
                $value->PageForm = PageForm::join('forms', 'forms.form_id', 'page_form.form_id')->where('page_content_id', $value->page_content_id)->orderBy('page_form_id')->get();
            }
        }
        $length = count($content);

        $data = PageForm::where('page_id', $page_id)->orderBy('page_id', 'ASC')->first();

        if (!empty($data)) {
            $page_content_id = $data->page_content_id;
        } else {
            $page_content_id = 0;
        }

        return view('Form::admin.preview', ['page' => $page, 'content' => $content, 'length' => $length, 'page_content_id' => $page_content_id]);
    }
}
