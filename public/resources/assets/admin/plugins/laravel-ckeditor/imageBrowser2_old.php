<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head id="Head1">
<title>Image Browser</title>
<link rel="stylesheet" type="text/css" href="<?php echo $_GET['ajaxurl'];?>/resources/assets/admin/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo $_GET['ajaxurl'];?>/resources/assets/admin/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo $_GET['ajaxurl'];?>/resources/assets/admin/css/style.css">
<link rel="stylesheet" type="text/css" href="<?php echo $_GET['ajaxurl'];?>/resources/plugins/Imgbrowse/browseimg.css">
<style type="text/css">
body {
	margin: 0px;
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-size:12px;
	background-color: #E3E3C7;

}
form {
	width:550px;
	background-color: #E3E3C7;
}
h1 {
	padding: 15px;
	margin: 0px;
	padding-bottom: 0px;
	font-family:Arial;
	font-size: 14pt;
	color: #737357;
}
.submit { padding: 5px; background-color:#4a4a4a; color:#FFF; border: 1px solid #6e6d6d;}
#imgbrowse .modal-dialog{
	max-width: 1000px !important;
}
#imgbrowse .modal-content {
    /*height: auto !important;*/
}
#imgbrowse .ajax-files .img-browser {
    height: 300px !important;
}
</style>
<?php
	ob_start();
	session_start();

	$StoreID = 'editor';
	
	/*require_once("../include/config.php");
	require_once("../include/dbClass.php");
	$objDB = new MySQLCN;

	$SQL = "Select * from appconfig";
	$appconfigdata = $objDB->select($SQL);
	for($i=0; $i<count($appconfigdata);$i++)
	{
		define($appconfigdata[$i]['ConfigName'], $appconfigdata[$i]['ConfigValue']);
	}*/
	
	$funcNum = $_REQUEST['CKEditorFuncNum'] ;
	$CKEditor = $_REQUEST['CKEditor'] ;
	$langCode = $_REQUEST['langCode'] ;
	$defaultDir = "../../../assets/".$StoreID.'/images/';
	// $defaultDir = "../../../assets/".$StoreID.'/images/';
	// $defaultDir = "../../resources/assets/Store-".$StoreID.'-image/';
	$rootDirectory = "";
	$dirArray = array();
	
?>
<script>
   function changeScreenSize(w,h)
     {
       window.resizeTo( w,h )
     }
</script></head>
<body onload="changeScreenSize(1250,800)">
<center>
  	<div>
    	<h1 style="text-align: left; margin-left:15px;">Image Browser</h1>
    	<div id="UpdatePanel1">
	     	<label for="browse">
	        	<button class="btn btn-default br-0 btn-sm browseImage" inpname="profile_image" type="button">Browse</button>
	        	<input name="profile_image" value="" type="hidden">
	        	<input type="hidden" name="CKEditorFuncNum" value="<?php echo $_GET['CKEditorFuncNum'];?>" />
	    	</label>
    	</div>
  	</div>
</center>
<script type="text/javascript" src="<?php echo $_GET['ajaxurl'];?>/resources/assets/admin/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $_GET['ajaxurl'];?>/resources/assets/admin/js/bootstrap.min.js"></script> 
<script type="text/javascript" src="<?php echo $_GET['ajaxurl'];?>/resources/plugins/Imgbrowse/browseimg.js" ajaxurl="<?php echo $_GET['ajaxurl'];?>" token="<?php echo $_GET['token'];?>"></script>
<script type="text/javascript">
	$(document).on('change','input[name="profile_image"]',function(){
		var urlval = $(this).val();
		var funcNum = $('input[name="CKEditorFuncNum"]').val();
		window.opener.CKEDITOR.tools.callFunction(funcNum, urlval, '');
		window.close();
	});
	$(document).ready(function(){
		$('.browseImage').trigger('click')
		// $.when($('.browseImage').trigger('click')).then($('#imgbrowse .modal-dialog').addClass('maxwidthreset'));
	})
</script>
</body>
</html>
