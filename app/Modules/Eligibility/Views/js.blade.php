 <script type="text/javascript">
    
    $(document).on("change",".template-select",function()
    {
        $(document).find("#optionContent").html("");
        var template_id = $(this).val();
        $.ajax({
            url:"{{$module_url}}/getTemplateHtml/"+template_id,
            type: 'GET',  // http method
            // data: { _token: '{{csrf_token()}}' }, 
            success: function (result) {
                // console.log(result.content_html);
                console.log(result);
                $(document).find("#optionContent").html(result);
                if(template_id == "3" || template_id == "8")
                {
                    $("#override").removeClass("d-none");
                }
                else
                {
                    $("#override").addClass("d-none");
                }
            },
            error: function (jqXhr, textStatus, errorMessage) {
                    $('p').append('Error' + errorMessage);
            }
        });
    });
    $(document).on("change",".template-type",function(){
        var a = $(this).val();
        if(a == "YN"){
            $(".template-type-1").removeClass("d-none");
            $(".template-type-2").addClass("d-none");
        }
        else if(a == "NR"){
            $(".template-type-1").addClass("d-none");
            $(".template-type-2").removeClass("d-none");
        }
        else {
            $(".template-type-1").addClass("d-none");
            $(".template-type-2").addClass("d-none");
        }
    });
    $(document).on("click", ".add-ranking-13" , function(){
        var i = $(this).parents(".template-type-2").find(".form-group").length + 1;
        var a = '<div class="form-group">'+
            '<label class="">Numeric Ranking '+i+' : </label>'+
            '<div class=""><input type="text" name="extra[eligibility_type][NR][]" class="form-control"></div>'+
            '</div>';
        var cc = $(this).parents(".template-type-2").find(".mb-20");
        $(a).insertBefore(cc);
    });
    $(document).on("click",".add-more-numeric-ranking-st",function()
    {
        var i = $(this).parents(".scoreTypeDiv").find(".form-group").length + 1;
        var a = '<div class="form-group">'+
            '<label class="">Numeric Ranking '+i+' : </label>'+
            '<div class=""><input type="text" class="form-control"  name="extra[scoring][NR][]"></div>'+
            '</div>';
        var cc = $(this).parents(".scoreTypeDiv").find(".mb-20");
        $(a).insertBefore(cc); 
    });
    $(document).on("click", ".add-question" , function(){
        var i = $(this).parent().parent(".card-body").find(".question-list").children(".form-group").length + 1;
        // var headerInput = $(this).parent().parent().parent((".card-header").find(".headerInput").attr("id");
        var headerId = $(this).attr("data-header");
        var question =  '<div class="form-group border p-15">'+
                            '<label class="control-label d-flex flex-wrap justify-content-between"><span><a href="javascript:void(0);" class="mr-10 handle1" title=""><i class="fas fa-arrows-alt"></i></a>Question '+i+' : </span>'+
                            '</label>'+
                            '<div class=""><input type="text" class="form-control" value="" name="extra[header]['+headerId+'][questions]['+i+']"></div>'+
                        '</div>';
        // console.log(question);

        $(this).parent().parent(".card-body").find(".question-list").append(question);
        custsort1();
    });
    $(document).on("click", ".add-header" , function(){
        var i = $(".form-list").children(".card").length + 1;
        var header =    '<div class="card shadow">'+
                            '<div class="card-header">'+
                                '<div class="form-group">'+
                                    '<label class="control-label"><a href="javascript:void(0);" class="mr-10 handle" title=""><i class="fas fa-arrows-alt"></i></a> Header Name '+i+': </label>'+
                                    '<div class=""><input type="text" class="form-control headerInput" name="extra[header]['+i+'][name]" id="header_'+i+'"></div>'+
                                '</div>'+
                            '</div>'+
                            '<div class="card-body">'+
                                '<div class="form-group text-right"><a href="javascript:void(0);" class="btn btn-secondary btn-sm add-option" title="" data-header="'+i+'">Add Option</a></div>'+
                                '<div class="option-list mt-10"></div>'+
                            // '</div>'+
                                '<div class="form-group text-right"><a href="javascript:void(0);" class="btn btn-secondary btn-sm add-question" title="" data-header="'+i+'">Add Question</a></div>'+
                                '<div class="question-list mt-10"></div>'+
                            '</div>'+
                        '</div>';
        console.log("Header : "+i);
        $(this).parents(".card-body").find(".form-list").append(header);
        custsort();
    });
    $(document).on("click", ".add-option" , function(){
        var i = $(this).parent().parent().children(".option-list").children(".form-group").length + 1;

        var headerId = $(this).attr("data-header");
        // var questionId = $(this).attr("data-question");

        var option =    '<div class="form-group border p-10">'+
                            '<div class="row">'+
                                '<div class="col-12 col-md-7 d-flex flex-wrap align-items-center">'+
                                    '<a href="javascript:void(0);" class="mr-10 handle2" title=""><i class="fas fa-arrows-alt"></i></a>'+
                                    '<label for="" class="mr-10">Option '+i+' : </label>'+
                                    '<div class="flex-grow-1"><input type="text" class="form-control" name="extra[header]['+headerId+'][options]['+i+']"></div>'+
                                '</div>'+
                                '<div class="col-10 col-md-5 d-flex flex-wrap align-items-center">'+
                                    '<label for="" class="mr-10">Point : </label>'+
                                    '<div class="flex-grow-1"><input type="text" class="form-control" name="extra[header]['+headerId+'][points]['+i+']"></div>'+
                                '</div>'+
                            '</div>'+
                        '</div>';
        console.log("option : "+i);
        // console.log( $(this).parent().parent(".form-group"));
        $(this).parent().parent().children(".option-list").append(option);
        custsort2();
    });

    $(document).on("click", ".add-description" , function(){
        // var i = $(".form-list").children(".card").length + 1;
        var description =   '<div class="form-group border p-10">'+
                            '<div class="">'+
                            '<textarea class="form-control" name="extra[description][]"></textarea>'+
                            '</div>'+
                            '</div>';

        $(document).find('.description-list').append(description);
    });
    
    function custsort() {
        $(".form-list").sortable({
            handle: ".handle"
        });
        $(".form-list").disableSelection();
    };
    function custsort1() {
        $(".question-list").sortable({
            handle: ".handle1"
        });
        $(".question-list").disableSelection();
    };
    function custsort2() {
        $(".option-list").sortable({
            handle: ".handle2"
        });
        $(".option-list").disableSelection();
    };


    $(document).on("change","#selectDataType",function()
    {
        var selected = $(this).val();
        console.log(selected);
        $(document).find("#selectScoreMethod").parent().parent().removeClass("d-none");
        if(selected != "DD")
        {
            $(document).find("#selectScoreMethod").find("option").remove();
            $(document).find(".scoreTypeDiv").attr("disable",false);
            $(document).find("#selectScoreMethod").append('<option value="">Select Option</option><option value="YN">Yes/No</option><option value="NR">Numeric Ranking</option><option value="CO">Conversion</option>');
        }
        else
        {
            $(document).find("#selectScoreMethod").find("option").remove();
            $(document).find(".scoreTypeDiv").attr("disable",true).addClass("d-none");
            $(document).find("#selectScoreMethod").append('<option value="NA">N/A (display only)</option>');
        }
    });
    $(document).on("change","#selectDataTypeConduct",function()
    {
        var selected = $(this).val();
        console.log(selected);
        $(document).find("#selectScoreMethod").parent().parent().removeClass("d-none");
        if(selected != "DD")
        {
            $(document).find("#selectScoreMethod").find("option").remove();
            $(document).find(".scoreTypeDiv").attr("disable",false);
            $(document).find("#selectScoreMethod").append('<option value="">Select Option</option><option value="YN">Yes/No</option><option value="NR">Numeric Ranking</option>');
        }
        else
        {
            $(document).find("#selectScoreMethod").find("option").remove();
            $(document).find(".scoreTypeDiv").attr("disable",true).addClass("d-none");
            $(document).find("#selectScoreMethod").append('<option value="NA">N/A (display only)</option>');
        }
    });
    $(document).on("change","#selectScoreMethod",function()
    {
        var selected = $(this).val();
        console.log(selected);
        var currentObj = $(document).find(".scoreType"+selected);
        currentObj.removeClass("d-none");
        currentObj.siblings(".scoreTypeDiv").addClass("d-none");
    });
    $(document).on("change","#selectScoreMethodAGC",function()
    {
        var selected = $(this).val();
        console.log(selected);
        var currentObj = $(document).find(".scoreType"+selected);
        currentObj.removeClass("d-none");
        currentObj.siblings(".scoreTypeDiv").addClass("d-none");
    });
    $(document).on("change","#selectScoreTypeAGC",function()
    {
        var selected = $(this).val();
        if(selected == "GPA")
        {
            $(document).find(".ifGPA").removeClass("d-none");
            $(document).find(".ifDD").addClass("d-none");
            $(document).find(".ifNotDD").removeClass("d-none");

        }
        else if(selected == "DD" || selected == "GA")
        {
            $(document).find(".ifDD").removeClass("d-none");
            $(document).find(".ifGPA").addClass("d-none");
            $(document).find(".ifNotDD").addClass("d-none");
        }
        else
        {
            $(document).find(".ifNotDD").removeClass("d-none");
            $(document).find(".ifGPA").addClass("d-none");
            $(document).find(".ifDD").addClass("d-none");
        }
    });

    $(document).on('click', '.academic_year_checkbox_calc', function(){
        var academic_year = $(this).attr('id');

        if($(this).is(':checked')){
            $(document).find('.'+academic_year).show();
        }else{
            $(document).find('.'+academic_year).hide();

        }
    });
</script>