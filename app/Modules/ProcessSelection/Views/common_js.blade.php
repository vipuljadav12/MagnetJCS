<script type="text/javascript">
$('#process_selection').submit(function(event) {
        event.preventDefault();
            if($("#last_date_online_acceptance").val() == "")
            {
                alert("Please select Last date of online acceptance");
                return false;
            }

            if($("#last_date_offline_acceptance").val() == "")
            {
                alert("Please select Last date of offline acceptance");
                return false;
            }

            $("#wrapperloading").show();
            var data = $(this).serialize();
            $.ajax({
                url:'{{ url('admin/Process/Selection/store')}}',
                type:"POST",
                data: data,
                success:function(response){
                    $("#wrapperloading").hide();
                    document.location.href = "{{url('/admin/Process/Selection/Population/Application/')}}/{{$application_id}}";
                    
                }
            })

 });
 $("#last_date_online_acceptance").datetimepicker({
    numberOfMonths: 1,
    autoclose: true,
     startDate:new Date(),
    dateFormat: 'mm/dd/yy hh:ii'
})

$("#last_date_offline_acceptance").datetimepicker({
    numberOfMonths: 1,
    autoclose: true,
     startDate:new Date(),
    dateFormat: 'mm/dd/yy hh:ii'
})
</script>