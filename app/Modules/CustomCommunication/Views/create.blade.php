@extends('layouts.admin.app')
@section('title')
    Create Custom Communication
@endsection
@section('content')
    <style type="text/css">
        .alert1 {
            position: relative;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-top-color: transparent;
            border-right-color: transparent;
            border-bottom-color: transparent;
            border-left-color: transparent;
            border-radius: 0.25rem;
        }
    </style>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Custom Communication</div>
            <div class=""><a href="{{ url('admin/CustomCommunication') }}" class="btn btn-sm btn-secondary"
                    title="Go Bck">Go Back</a></div>
        </div>
    </div>
    @include('layouts.admin.common.alerts')

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item"><a class="nav-link active" id="new-tab" data-toggle="tab" href="#new" role="tab"
                aria-controls="new" aria-selected="false">Custom Communication</a></li>

    </ul>
    <div class="tab-content bordered" id="myTabContent">
        <div class="tab-pane active" id="new">
            <form class="" action="{{ url('/admin/CustomCommunication/store') }}" method="post" id="generateform"
                name="generateform" onsubmit="return validateAll()">
                {{ csrf_field() }}
                <div class="card shadow">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="">Template Name : </label>
                            <div class="">
                                <input type="text" class="form-control" name="template_name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Select Enrollment Year : </label>
                            <div class="">
                                <select class="form-control custom-select" id="enrollment_id" name="enrollment_id">
                                    <option value="">Select</option>
                                    @foreach ($enrollment as $key => $value)
                                        @if ($value->id == Session::get('enrollment_id'))
                                            <option value="{{ $value->id }}">{{ $value->school_year }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Select Program : </label>
                            <div class="">
                                <select class="form-control custom-select" id="program" name="program"
                                    onchange="changeGrade(this.value)">
                                    <option value="">Select</option>
                                    <option value="0">All Programs</option>
                                    @foreach ($programs as $key => $value)
                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Select Specific Grade : </label>
                            <div class="">
                                <select class="form-control custom-select" id="grade" name="grade">
                                    <option value="">Select</option>
                                    <option value="All">All Grades</option>
                                    @foreach ($grades as $value)
                                        <option value="{{ $value->next_grade }}">{{ $value->next_grade }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Select Status : </label>
                            <div class="">
                                <select class="form-control custom-select" id="submission_status" name="submission_status">
                                    <option value="">Select</option>
                                    <option value="All">All</option>
                                    @foreach ($submission_status as $key => $value)
                                        <option value="{{ $value->submission_status }}">{{ $value->submission_status }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                        </div>


                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-body">
                        <ul class="nav nav-tabs custom-nav-tabs" id="myTab2">
                            <li class="nav-item"><a class="nav-link active" id="active-screen-tab" data-toggle="tab"
                                    href="#active-screen">Letters</a></li>
                            <li class="nav-item"><a class="nav-link" id="active-email-tab" data-toggle="tab"
                                    href="#active-email">Emails</a></li>
                        </ul>
                        <div class="tab-content  tab-validate bordered" id="myTab2Content">
                            <div class="tab-pane active" id="active-screen">
                                <div class="form-group d-none">
                                    <label for="">Letter Subject</label>
                                    <div class="">
                                        <input type="text" class="form-control" name="letter_subject">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Letter Body : </label>

                                    <div class="editor-height-210">
                                        <textarea class="form-control" id="letter_body" name="letter_body">
                                            
                                            </textarea>
                                    </div>
                                </div>
                                <div class="form-group"> <input type="submit" name="save_letter"
                                        class="btn btn-secondary" value="Save Changes"> <input type="submit"
                                        name="generate_letter_now" class="btn btn-success" value="Generate Letters Now">
                                </div>


                            </div>
                            <div class="tab-pane" id="active-email">
                                <div class="form-group">
                                    <label for="">Mail Subject</label>
                                    <div class="">
                                        <input type="text" class="form-control" name="mail_subject">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Mail Body : </label>
                                    <div class="editor-height-210">
                                        <textarea class="form-control" id="mail_body" name="mail_body">
                                           
                                            </textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>SMS Text : </label>
                                    <div class="">
                                        <textarea class="form-control" id="sms_text" name="sms_text" rows="4"
                                            placeholder="Enter SMS text message..."></textarea>
                                    </div>
                                </div>
                                <div class="form-group d-none" id="emaillist">

                                </div>
                                <div class="form-group"> <input type="submit" name="save_email"
                                        class="btn btn-secondary" value="Save Changes"> <a href="javascript:void(0)"
                                        class="btn btn-success" onclick="fetchEmails()" id="generate_email_link">Send
                                        Emails Now</a> <input type="submit" name="send_email_now"
                                        value="Generate Email Now" class="btn btn-primary d-none"
                                        id="generate_email_submit"></div>

                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>

    </div>
@endsection
@section('scripts')
    <script type="text/javascript" src="{{ url('/') }}/resources/assets/admin/plugins/laravel-ckeditor/ckeditor.js">
    </script>
    <script type="text/javascript" src="{{ url('/resources/assets/admin/plugins/laravel-ckeditor/adapters/jquery.js') }}">
    </script>
    <script type="text/javascript">
        CKEDITOR.replace('letter_body', {
            toolbar: 'Basic',
            toolbarGroups: [{
                    name: 'document',
                    groups: ['mode', 'document']
                }, // Displays document group with its two subgroups.
                {
                    name: 'clipboard',
                    groups: ['clipboard', 'undo']
                }, // Group's name will be used to create voice label.
                {
                    name: 'basicstyles',
                    groups: ['cleanup', 'basicstyles']
                },

                '/', // Line break - next group will be placed in new line.
                {
                    name: 'links'
                }
            ],
            on: {
                pluginsLoaded: function() {
                    var editor = this,
                        config = editor.config;

                    editor.ui.addRichCombo('my-combo', {
                        label: 'Insert Short Code',
                        title: 'Insert Short Code',
                        toolbar: 'basicstyles',

                        panel: {
                            css: [CKEDITOR.skin.getPath('editor')].concat(config.contentsCss),
                            multiSelect: false,
                            attributes: {
                                'aria-label': 'Insert Short Code'
                            }
                        },

                        init: function() {
                            var chk = [];
                            $.ajax({
                                url: '{{ url('/admin/shortCode/list') }}',
                                type: "get",
                                async: false,
                                success: function(response) {
                                    chk = response;
                                }
                            })
                            for (var i = 0; i < chk.length; i++) {
                                this.add(chk[i], chk[i]);
                            }
                        },

                        onClick: function(value) {
                            editor.focus();
                            editor.fire('saveSnapshot');

                            editor.insertHtml(value);

                            editor.fire('saveSnapshot');
                        }
                    });
                }
            }
        });

        CKEDITOR.replace('mail_body', {
            toolbar: 'Basic',
            toolbarGroups: [{
                    name: 'document',
                    groups: ['mode', 'document']
                }, // Displays document group with its two subgroups.
                {
                    name: 'clipboard',
                    groups: ['clipboard', 'undo']
                }, // Group's name will be used to create voice label.
                {
                    name: 'basicstyles',
                    groups: ['cleanup', 'basicstyles']
                },

                '/', // Line break - next group will be placed in new line.
                {
                    name: 'links'
                }
            ],
            on: {
                pluginsLoaded: function() {
                    var editor = this,
                        config = editor.config;

                    editor.ui.addRichCombo('my-combo', {
                        label: 'Insert Short Code',
                        title: 'Insert Short Code',
                        toolbar: 'basicstyles',

                        panel: {
                            css: [CKEDITOR.skin.getPath('editor')].concat(config.contentsCss),
                            multiSelect: false,
                            attributes: {
                                'aria-label': 'Insert Short Code'
                            }
                        },

                        init: function() {
                            var chk = [];
                            $.ajax({
                                url: '{{ url('/admin/shortCode/list') }}',
                                type: "get",
                                async: false,
                                success: function(response) {
                                    chk = response;
                                }
                            })
                            for (var i = 0; i < chk.length; i++) {
                                this.add(chk[i], chk[i]);
                            }
                        },

                        onClick: function(value) {
                            editor.focus();
                            editor.fire('saveSnapshot');

                            editor.insertHtml(value);

                            editor.fire('saveSnapshot');
                        }
                    });
                }
            }
        });


        $("form[name='generateform']").validate({
            rules: {
                template_name: {
                    required: true,
                    maxlength: 255
                },
                enrollment_id: {
                    required: true,
                },
                program: {
                    required: true,
                },
                grade: {
                    required: true
                },
                submission_status: {
                    required: true
                },
                mail_subject: {
                    required: true,
                    maxlength: 255,
                },
                mail_body: {
                    required: function() {
                        CKEDITOR.instances.mail_body.updateElement();
                    },

                    minlength: 10
                },
                sms_text: {
                    maxlength: 1000
                },
                /* letter_subject:{
                     required: true
                 },*/
                letter_body: {
                    required: function() {
                        CKEDITOR.instances.letter_body.updateElement();
                    },

                    minlength: 10
                },

            },
            errorPlacement: function(error, element) {
                error.appendTo(element.parents('.form-group'));
                error.css('color', 'red');
            },
            invalidHandler: function() {
                setTimeout(function() {
                    $('.custom-nav-tabs a small.required').remove();
                    var validatePane = $('.tab-content.tab-validate .tab-pane:has(input.error)').each(
                        function() {
                            var id = $(this).attr('id');
                            $('.custom-nav-tabs').find('a[href^="#' + id + '"]').append(
                                ' <small class="required">***</small>');
                        });
                });
            },
            submitHandler: function(form) {
                form.submit();
            }
        });

        function validateAll() {
            var valid1 = true;

            $("#myTab2").tabs({
                active: 0
            });
            $("#myTab2").find(".check:not(:hidden,:button)").each(function() {
                if (!validator.element(this) && valid1)
                    valid1 = false;
            });
        }

        function changeGrade(value) {
            $.ajax({
                url: '{{ url('/admin/Submissions/get/grades/program/') }}/' + value,
                type: "get",
                async: false,
                success: function(response) {
                    $('#grade').children('option').remove();
                    var data = JSON.parse(response);
                    $("#grade").append('<option value="All">All</option>');
                    for (i = 0; i < data.length; i++) {

                        $("#grade").append('<option value="' + data[i].next_grade + '">' + data[i].next_grade +
                            '</option>');
                    }
                    chk = response;
                }
            })
        }

        function fetchEmails() {
            if ($("#enrollment_id").val() != "" && $("#program").val() != "" && $("#grade").val() != "" && $(
                    "#submission_status").val() != "") {
                $("#emaillist").removeClass("d-none");
                $("#emaillist").html("<p class='text-center'>Loading Emails</p>");
                var enrollment_id = $("#enrollment_id").val();
                var program = $("#program").val();
                var grade = $("#grade").val();
                var submission_status = $("#submission_status").val();

                $.ajax({
                    url: '{{ url('/admin/CustomCommunication/get/emails/') }}',
                    type: "post",
                    data: {
                        'enrollment_id': enrollment_id,
                        'program': program,
                        'grade': grade,
                        'submission_status': submission_status,
                        "_token": "{{ csrf_token() }}"
                    },
                    async: false,
                    success: function(response) {
                        var data = JSON.parse(response);
                        var html = '<table id="datatable" class="table table-striped mb-0">';
                        html += '<thead>';
                        html += '<tr>';
                        html += '<th class="align-middle w-120 text-center">Submission ID</th>';
                        html += '<th class="align-middle w-120 text-center">Student Name</th>';
                        html += '<th class="align-middle w-120 text-center">Parent Name</th>';
                        html += '<th class="align-middle w-120 text-center">Parent Email</th>';
                        html += '<th class="align-middle w-120 text-center">Grade</th>';
                        html += '</tr>';
                        html += '</thead>';
                        for (i = 0; i < data.length; i++) {
                            html += '<tr>';
                            html += '<td class="align-middle w-120 text-center">' + data[i]['id'] + '</td>';
                            html += '<td class="align-middle w-120 text-center">' + data[i]['student_name'] +
                                '</td>';
                            html += '<td class="align-middle w-120 text-center">' + data[i]['parent_name'] +
                                '</td>';
                            html += '<td class="align-middle w-120 text-center">' + data[i]['parent_email'] +
                                '</td>';
                            html += '<td class="align-middle w-120 text-center">' + data[i]['grade'] + '</td>';
                            html += '</tr>';
                        }
                        html += '</table>';

                        $("#emaillist").html(html);
                        $("#datatable").DataTable();
                        $("#generate_email_submit").removeClass('d-none');
                        $("#generate_email_link").addClass('d-none');

                    }
                })

            } else {
                alert("Select all fields");
            }
        }
    </script>
@endsection
