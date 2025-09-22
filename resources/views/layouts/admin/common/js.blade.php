<script type="text/javascript" src="{{url('/resources/assets/admin')}}/js/jquery/jquery-3.4.1.min.js"></script> 
<script type="text/javascript" src="{{url('/resources/assets/admin')}}/js/menu/metisMenu.min.js"></script> 
<script type="text/javascript" src="{{url('/resources/assets/admin')}}/js/popper.min.js"></script> 
<script type="text/javascript" src="{{url('/resources/assets/admin')}}/js/bootstrap/bootstrap.min.js"></script> 
<script type="text/javascript" src="{{url('/resources/assets/admin')}}/js/jquery/jquery.slimscroll.js"></script>  
<script type="text/javascript" src="{{url('/resources/assets/admin')}}/js/switchery.min.js"></script>

<script type="text/javascript" src="{{url('/resources/assets/admin')}}/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- InstanceBeginEditable name="javascritp-row" --> <!-- InstanceEndEditable --> 
<script type="text/javascript" src="{{url('/resources/assets/admin')}}/js/custom.js"></script>
<script type="text/javascript" src="{{url('/resources/assets/admin')}}/js/jquery.mCustomScrollbar.concat.min.js"></script>  

<script type="text/javascript">
     (function($){
$(window).on("load",function(){     
    $("#remove-scroll").mCustomScrollbar({
        setWidth : false,
        setHeight : false,
        theme : "dark",
        alwaysShowScrollbar : 2,
//        mouseWheel:{ scrollAmount: 200 },
        setTop: 0,
        autoHideScrollbar : false,
        
    });
});
})(jQuery);
</script>
<!-- InstanceBeginEditable name="Footer Scripts" -->
<script type="text/javascript" src="{{url('/resources/assets/admin')}}/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="{{url('/resources/assets/admin')}}/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="{{url('/resources/assets/admin')}}/plugins/bootstrap-datepicker/js/bs-date-picker-init.js"></script>
<script type="text/javascript" src="{{url('/resources/assets/admin')}}/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="{{url('/resources/assets/admin')}}/plugins/bootstrap-datepicker/js/bs-date-picker-init.js"></script>
<script type="text/javascript" src="{{url('/resources/assets/admin')}}/plugins/bootstrap-datetimepicker/js/bs-datetime-picker-init.js"></script>
<script type="text/javascript" src="{{url('/resources/assets/admin/plugins/moment/moment.min.js')}}"></script>  
<script type="text/javascript" src="{{url('/resources/assets/admin/plugins/bootstrap-daterangepicker/js/daterangepicker.js')}}"></script>
<script type="text/javascript" src="{{url('/resources/assets/admin/plugins/bootstrap-daterangepicker/js/bs-daterange-picker-init.js')}}"></script>



<script type="text/javascript" src="{{url('/resources/assets/admin')}}/plugins/DataTables/datatables.min.js"></script>
<script type="text/javascript" src="{{url('/resources/assets/admin')}}/js/bootstrap/dataTables.rowReordering.js"></script>

<script type="text/javascript" src="{{url('/resources/assets/admin')}}/plugins/sweet-alert2/sweetalert2.min.js"></script>


<script src="{{asset('resources/assets/admin/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('resources/assets/admin/js/additional-methods.min.js')}}"></script>
<script src="{{asset('resources/assets/admin/js/Chart.min.js')}}"></script>
<script type="text/javascript">
    $(document).on("click", ".add-new" , function(){
        var cc = $("#first").clone().addClass('list').removeAttr("id");
        $("#inowtable tbody").append(cc);
    });
    
    function del(id){
        $(id).parents(".list").remove();
    }
    
    $(function () {
        $('#cp2').colorpicker().on('changeColor', function (e) {
            $('#chgcolor')[0].style.backgroundColor = e.color.toHex();
        });
        $(document).find('input[data-plugin="switchery"]').each(function (idx, obj) {
            if($(this).css('display') != 'none'){
                new Switchery($(this)[0], $(this).data());
            }
        });
    });


    setTimeout(function()
    {
        $(document).find(".alert").fadeOut();
    }, 3000);

    $(document).on('keydown','.numbersOnly',function(e){
        if (e.keyCode === 109  && this.value.split('-').length === 2) {
            return false;
        }else if (e.keyCode === 189  && this.value.split('-').length === 2) {
            return false;
        }else{
            // console.log(e.keyCode);
            if ($.inArray(e.keyCode, [8, 9, 27, 13, 189, 110 ,109]) !== -1 ||
                (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
                (e.keyCode >= 35 && e.keyCode <= 40))  {
                return;
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                // alert("sd");
                e.preventDefault();
            }
        }

    });

</script>