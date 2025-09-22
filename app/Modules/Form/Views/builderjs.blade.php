<script type="text/javascript">
	var inputJson = new Array();
	$("#draggable1 > li").each(function(){
        $(this).draggable({
            connectToSortable: '.input_container_builder',
            containment: ".tab-input_container_builder bordered",
            cursor: 'move',
            helper: '.input_container_builder',
            zIndex: 100,
            stop : function(event, ui){
            	var obj = event.target;
            	if($(obj).parent().attr("id") == "input_container_builder")
            	{
            		$("#wrapperloading").fadeIn();
                	box_control(this);
                }
            },
            revert: "invalid",
            scroll: false,
        });
    });

    function box_control(obj)
    {
    	var fieldType = $(obj).attr("data-type");
		var fieldID = $(obj).attr("data-type-id");
		var form_id = $(obj).attr("data-form-id");
		var page_id = {{$page_id}};
		// alert(fieldType);
		$.ajax({
			url: '{{url("admin/Form/insertField")}}',
			type: 'GET',
			data: {
				type:fieldType,
				field_id:fieldID,
				form_id:form_id,
				page_id:page_id
			},
			success: function(data) {
				// $(document).find(".input_container_builder").append(data);
    			getFormContent();
    			$("#wrapperloading").fadeOut();

			},
		});
    }

    $(function()
    {
    	getFormContent();
    	sortElements();
    });
    function sortElements()
    {
    	$(document).find("#input_container_builder").sortable(
		{
			cursor: "move",
			scroll: true,
			stop: function( event, ui ) {
				// alert();
				// console.log($( "#input_container_builder" ).sortable( "toArray" , {attribute: 'data-build-id'}));
				registerSort();
			}
		});
    }
    function registerSort()
    {
		// console.log($( "#input_container_builder" ).sortable( "toArray" , {attribute: 'data-build-id'}));
		var list = new Array();
		let i = 1;
    	$(document).find("#input_container_builder").find(".form-group-input").each(function(key)
    	{
    		id = $(this).attr("data-build-id");
    		console.log(id);
    		if(id != undefined)
    		{
    			// list[key] = id;
    			// list[id] = i;
    			list[i] = id;
    			i++;
    		}
    	});
  //   	var filteredList = list.filter(function (el) {
		//   return el != null;
		// });
    	$.ajax({
			url: '{{url("admin/Form/registerSort/")}}',
			type: 'POST',
			data:
			{
				_token:"{{csrf_token()}}",
				data:list,
				form_id:'{{$form->id}}'
			},
			success: function(data) {
    			console.log(data);
			},
		});
    	console.log(list);
    }

    function getFormContent()
    {
    	// var form_id = $(document).find("form_id").val();
    	var form_id = "{{$form->id}}";
    	var page_id = "{{$page_id}}";
    	// alert(form_id);
    	$.ajax({
			url: '{{url("admin/Form/getFormContent")}}',
			type: 'GET',
			data: {
				form_id:form_id,
				page_id:page_id
			},
			success: function(data) {
				$(document).find(".input_container_builder").html(data);
				sortElements();
			},
		});
    }
    /*$(document).on("mouseover",".form-group-input",function()
    {
    	$(this).addClass();
    });*/
	// console.log(inputJson);
	$(document).on("dblclick",".field-type-menu-item",function()
	{
		var fieldType = $(this).attr("data-type");
		var fieldID = $(this).attr("data-type-id");
		var form_id = $(this).attr("data-form-id");
		var page_id = {{$page_id}};
		// alert(fieldType);
		$.ajax({
			url: '{{url("admin/Form/insertField")}}',
			type: 'GET',
			data: {
				type:fieldType,
				field_id:fieldID,
				form_id:form_id,
				page_id:page_id
			},
			success: function(data) {
				// $(document).find(".input_container_builder").append(data);
    			getFormContent();

			},
		});
	});
	$(document).on("click",".removeField",function()
	{
		 var build_id = $(this).parent().parent().attr("data-build-id");
		//var build_id = $(this).parents(".form-group-input").attr("data-build-id");
		$(document).find("#fieldEditor").html();
		$(document).find("#fieldEditor").addClass("d-none");
		$.ajax({
			url: '{{url("admin/Form/removeField/")}}'+"/"+build_id,
			type: 'GET',
			success: function(data) {
    			getFormContent();
			},
		});
	});
	

	/* Select Box */
	$(document).on("click",".addMoreOption",function()
	{
		var object = $(this).parent().parent().clone();
		object.find(".removeMoreOption").attr("content-id","");
		object.find("input").val("");
		$(this).parent().parent().after(object);
		$(document).find(".optionBox").find("input").each(function(key)
		{
			$(this).attr("name","option_"+(key+1));
			$(this).attr("data-for","option_"+(key+1));
		});
		arrangeRemoveButton();
	});
	$(document).on("click",".removeMoreOption",function()
	{
		var build_id = $(this).parent().prev().find(".optionList").attr("build-id");

		var object = $(this).parent().parent().remove();
		var content_id = $(this).attr("content-id");
		if(content_id != "")
		{
			$.ajax({
				url: '{{url("admin/Form/removeOption/")}}'+"/"+content_id,
				type: 'GET',
				success: function(data) {
					
	    			// getFormContent();
				},
			});
		}
		$(document).find(".optionBox").find("input").each(function(key)
		{
			$(this).attr("name","option_"+(key+1));
			$(this).attr("data-for","option_"+(key+1));
		});

		var html = "";
		$(document).find(".optionList").each(function()
		{

			html += "<option>"+$(this).val()+"</option>";
		});
		$(document).find("#input"+build_id).html(html);
		arrangeRemoveButton();

	});
	function arrangeRemoveButton()
	{
		let optionBoxLength = $(document).find('.optionList').length;
		// alert(optionBoxLength);
		if(optionBoxLength > 1)
		{
			$(document).find(".removeMoreOption").removeClass("d-none");
		}
		if(optionBoxLength == 1)
		{
			$(document).find(".removeMoreOption").addClass("d-none");
		}

		let checkBoxLength = $(document).find('.checkboxList').length;
		if(checkBoxLength > 1)
		{
			$(document).find(".removeMoreCheckbox").removeClass("d-none");
		}
		if(checkBoxLength == 1)
		{
			$(document).find(".removeMoreCheckbox").addClass("d-none");
		}
		// removeMoreCheckbox
		/*$(document).find(".").each(fuction()
		{

		});*/
	}
	$(document).on("focusout blur",".optionList",function()
	{
		var build_id = $(this).attr("build-id");
		var html = "";
		$(document).find(".optionList").each(function()
		{

			html += "<option>"+$(this).val()+"</option>";
		});
		$(document).find("#input"+build_id).html(html)

	});

	/* Checkbox */
	$(document).on("click",".addMoreCheckbox",function()
	{
		var object = $(this).parent().parent().clone();
		object.find(".removeMoreCheckbox").attr("content-id","");
		object.find("input").val("");
		$(this).parent().parent().after(object);

		$(document).find(".optionBox").find("input").each(function(key)
		{
			$(this).attr("name","checkbox_"+(key+1));
			$(this).attr("data-for","checkbox_"+(key+1));
		});
		arrangeRemoveButton();

	});
	$(document).on("click",".removeMoreCheckbox",function()
	{
		var build_id = $(this).parent().prev().find(".checkboxList").attr("build-id");

		var object = $(this).parent().parent().remove();
		var content_id = $(this).attr("content-id");
		if(content_id != "")
		{
			$.ajax({
				url: '{{url("admin/Form/removeOption/")}}'+"/"+content_id,
				type: 'GET',
				success: function(data) {
					
	    			// getFormContent();
				},
			});
		}
		$(document).find(".optionBox").find("input").each(function(key)
		{
			$(this).attr("name","checkbox_"+(key+1));
			$(this).attr("data-for","checkbox_"+(key+1));
		});

		var html = "";
		$(document).find(".checkboxList").each(function()
		{

			html += "<input type='checkbox' name='input"+content_id+"'>&nbsp;"+$(this).val()+" ";
		});
		$(document).find("#input"+build_id).html(html);
		arrangeRemoveButton();
	});
	$(document).on("focusout blur",".checkboxList",function()
	{
		var build_id = $(this).attr("build-id");
		var html = "";
		$(document).find(".checkboxList").each(function()
		{
			html += "<input type='checkbox' name='input"+build_id+"'> "+$(this).val()+" ";
		});
		// alert(html);
		$(document).find("#input"+build_id).html(html)

	});
	/* Checkbox Ends */


	/* Radio Button */
	$(document).on("click",".addMoreRadio",function()
	{
		var object = $(this).parent().parent().clone();
		object.find(".removeMoreRadio").attr("content-id","");
		object.find("input").val("");
		$(this).parent().parent().after(object);

		$(document).find(".optionBox").find("input").each(function(key)
		{
			$(this).attr("name","radio_"+(key+1));
			$(this).attr("data-for","radio_"+(key+1));
		});
		arrangeRemoveButton();

	});
	$(document).on("click",".removeMoreRadio",function()
	{
		var build_id = $(this).parent().prev().find(".radioboxList").attr("build-id");

		var object = $(this).parent().parent().remove();
		var content_id = $(this).attr("content-id");
		if(content_id != "")
		{
			$.ajax({
				url: '{{url("admin/Form/removeOption/")}}'+"/"+content_id,
				type: 'GET',
				success: function(data) {
					
	    			// getFormContent();
				},
			});
		}
		$(document).find(".optionBox").find("input").each(function(key)
		{
			$(this).attr("name","radio_"+(key+1));
			$(this).attr("data-for","radio_"+(key+1));
		});

		var html = "";
		$(document).find(".checkboxList").each(function()
		{

			html += "<input type='radio' name='input"+content_id+"'>&nbsp;"+$(this).val()+" ";
		});
		$(document).find("#input"+build_id).html(html);
		arrangeRemoveButton();
	});
	$(document).on("focusout blur",".radioboxList",function()
	{
		var build_id = $(this).attr("build-id");
		var html = "";
		$(document).find(".radioboxList").each(function()
		{
			html += "<input type='radio' name='input"+build_id+"'> "+$(this).val()+" ";
		});
		// alert(html);
		$(document).find("#input"+build_id).html(html)

	});
	/* Checkbox Ends */



	/* Terms Text */
	$(document).on("focusout blur",".termtext",function()
	{
		var build_id = $(this).attr("build-id");
		$(document).find("#input"+build_id).html($(this).val());

	});

	/* Terms Text */

	
	$(document).on("click",".form-group-input",function()
	{
		//$(document).find("#fieldEditor").fadeOut();
		$(this).siblings().removeClass("selectedForChange").addClass("form-group-input");
		$(this).addClass("selectedForChange").removeClass("form-group-input");
		$(document).find("#fieldEditor").html();
		$(document).find("#fieldEditor").addClass("d-none");
		var build_id = $(this).attr("data-build-id");
		$.ajax({
			url: '{{url("admin/Form/fieldEditor/")}}'+"/"+build_id,
			type: 'GET',
			success: function(data) {
    			// getFormContent();
    			$(document).find("#fieldEditor").html(data);
				$(document).find("#fieldEditor").removeClass("d-none");

			},
		});

	});
	$(document).on("change input",".editorInput",function()
	{
		var value = $(this).val();
		var dataFor = $(this).attr("data-for");
		var build_id = $(this).attr("build-id");
		if(dataFor == "label")
		{
			$(document).find("#label"+build_id).html(value);
		}
		if(dataFor == "placeholder")
		{
			$(document).find("#input"+build_id).attr("placeholder",value);
		}
	});


	$(document).on("blur",".editorInput",function()
	{
		/*var value = $(this).val();
		var dataFor = $(this).attr("data-for");
		var build_id = $(this).attr("build-id");
		$.ajax({
			url: '{{url("admin/Form/saveSingle/")}}',
			type: 'POST',
			data:
			{
				_token:"{{csrf_token()}}",
				field_property:dataFor,
				field_value:value,
				build_id:build_id,
				form_id:'{{$form->id}}'

			},
			success: function(data) {
    			console.log(data);
			},
		});*/
		
	});
	$(document).on("click",".saveFormCreate",function()
	{
		var allInputs = $(document).find(".inputToSave").attr("data-name");
		var allInputsValues = new Array();
		$(document).find(".form-input-group").each(function(key)
		{
			if($(this).attr("data-type") == "text")
			{
				var currentInput = new Array();
				currentInput["label"] = $(this).find(".inputLabel").html();
				currentInput["placeholder"] = $(this).find(".placeholderInput").text();
				currentInput["type"] = $(this).find(".typeChangeInput").val();
				checkRequired= $(this).find(".inputCheckBoxRequired").prop("checked");
				currentInput["required"]  = checkRequired == true ? "y" : "n";
				
			}
			// allInputsValues.push(currentInput);
			allInputsValues[key] = currentInput;
			// allInputsValues[input_name] = $(this).html();
		});
		console.log(allInputsValues);
		var form_id = $(document).find("#form_id").val();
		$.ajax({
		  url: '{{url("admin/Form/saveBuild")}}',
		  type: 'post',
		  data: {
		 	_token:"{{csrf_token()}}",
		 	data:allInputsValues,
		 	form_id:form_id
		  },
		  success: function(data) {
		    $(document).find(".input_container_builder").append(data);
		  },
		});
		/*$(document).find(".inputToSave").each(function()
		{
			var input_name = $(this).attr("data-name");
			allVal[input_name] = $(this).html();
		});*/
		// console.log(allInputs);
	});

	function saveFieldInfo()
	{
		$('#wrapperloading').fadeIn();
		var sort = 0;
		$(".property_section").find("*").each(function(){
			if($(this).attr("build-id") != undefined)
			{
				var value = $(this).val();
				var dataFor = $(this).attr("data-for");
				var build_id = $(this).attr("build-id");
				$.ajax({
					url: '{{url("admin/Form/saveSingle/")}}',
					type: 'POST',
					data:
					{
						_token:"{{csrf_token()}}",
						field_property:dataFor,
						field_value:value,
						build_id:build_id,
						form_id:'{{$form->id}}',
						sort_option: sort

					},
					success: function(data) {
		    			
					},
				});
			}
			sort++;
		});
		$('#wrapperloading').fadeOut();
	}

	$(document).on("input change",".placeholderInput",function()
	{
		$(this).parent().find(".inputText").attr("placeholder",$(this).html());
	});
	$(document).on("change",".typeChangeInput",function()
	{
		$(this).parent().find("input").attr("type",$(this).val());
	});
	
	$(document).on("focus",".inputToSave",function()
	{
		$(this).addClass("input-border");
	});
	$(document).on("focusout",".inputToSave",function()
	{
		$(this).removeClass("input-border");
	});
	$(document).on("keyup focusout",".FormPageTitle",function()
	{
		let page_id = $(this).attr("data-page-id");
		let form_id = $(this).attr("data-form-id");
		let page_title = $(this).html();
		$.ajax(
		{
			url: '{{url("admin/Form/changeTitle/")}}'+"/"+form_id+"/"+page_id,
			type: 'GET',
			data:{
				page_title:page_title
			},
			success: function(data) {
								
    			// getFormContent();
			},
		});
		// console.log(page_title);
		// alert(page_id);
	});
</script>