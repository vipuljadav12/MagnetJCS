$(document).ready(function()
	{
		$('.determination_method').on('change input', function () {
                changeIcon($(this), 0);
            });
		$('.assigned_eigibility_name').on('change input', function () {
                changeIcon($(this), 0);
            });
		$('.weight').on('change input', function () {
                changeIcon($(this), 0);
            });
		$('.eligibility_status').on('change input', function () {
                changeIcon($(this), 0);
            });

		// For late submission
		$('.determination_method_ls').on('change input', function () {
                changeIcon($(this), 0, 1);
            });
		$('.assigned_eigibility_name_ls').on('change input', function () {
                changeIcon($(this), 0, 1);
            });
		$('.weight_ls').on('change input', function () {
                changeIcon($(this), 0, 1);
            });
		$('.eligibility_status_ls').on('change input', function () {
                changeIcon($(this), 0, 1);
            });

		$(".gradeClass").change(function(){

			var elName = $(this).attr('name');
			elName = elName.replace("eligibility_grade_lavel", "");
			elName = elName.replace("[]", "");
			elName = elName.replace("[", "");
			elName = elName.replace("]", "");

			var strGrade = "";
			$("input[name='"+$(this).attr('name')+"']").each(function() {
				if($(this).prop("checked") == true)
				{
					strGrade += $(this).val()+"-";
					console.log("initiate strGrade = "+ strGrade);

				}
			})

			// For late submission
			var extra_val = '';
			var option_val = 0;
			if ($(this).hasClass('option_1')) { 
				option_val=1; 
				extra_val='_ls'; 
			}

			$("#grade"+extra_val+elName).val(strGrade);

			var currentId = $("#grade"+extra_val+elName);
			changeIcon($("#grade"+extra_val+elName), 0, option_val);
		})

});



function changeIcon(obj, val, option_val=0)
{
	if(val==0)
		var trObj = $(obj).closest('tr');
	else
		var trObj = $(obj);
	 
	// For late submission
	extra_val = '';
	if (option_val === 1) { extra_val="_ls"; }

	var dMthod = $(trObj).find(".determination_method"+extra_val).val();
	var dElName = $(trObj).find(".assigned_eigibility_name"+extra_val).val();
	var dWeight = $(trObj).find(".weight"+extra_val).val();
	var dStatus = $(trObj).find(".eligibility_status"+extra_val).prop('checked');
	var dGrade = $(trObj).find(".gradeval"+extra_val).val();

	var status = "Pending";
	if(dMthod == "" && dElName == "" && dStatus == false && dGrade == "")
	{
		$(trObj).find(".statusimg").attr('src',BASE_URL+"/resources/assets/admin/images/close.png");
		$(trObj).find(".tooltiptext").html("Not Applicable");
	}
	
	if(dMthod != "" || dElName != "" || dStatus == true || dWeight != "" || dGrade != "")
	{
		$(trObj).find(".statusimg").attr('src',BASE_URL+"/resources/assets/admin/images/alert.png");
		$(trObj).find(".tooltiptext").html("Awaiting Assignment");	
	}

	if(dMthod != "" && dElName != "" && dStatus == true && dGrade != "")
	{
		if(dMthod == "Combined" && dWeight != "")
			$(trObj).find(".statusimg").attr('src',BASE_URL+"/resources/assets/admin/images/right.png");
		else if(dMthod == "Basic")
			$(trObj).find(".statusimg").attr('src',BASE_URL + "/resources/assets/admin/images/right.png");
		$(trObj).find(".tooltiptext").html("Assignment Completed");
	}

//	 console.log($i);
}

function showGradePopup(eligibility_id, name, option_val=0)
{
	//For late submission
	var extra_val = '';
	if (option_val === 1) { 
		extra_val = "_ls"; 
		$(".gradeClass").addClass('option_1');
	}else {
		$(".gradeClass").removeClass('option_1');
	}


	$('#modal_1').modal('show');
	var values = $("#grade"+extra_val+eligibility_id).val();
	// console.log(values);
	var gradeArr = values.split("-");
	$(".eligibility_type_name").html(name);
	$(".gradeClass").each(function(){
		$(this).attr("name", "eligibility_grade_lavel["+eligibility_id+"][]");

		if($.inArray($(this).val(), gradeArr) >= 0)
			$(this).prop("checked", true);
		else	
			$(this).prop("checked", false);
	})	

}