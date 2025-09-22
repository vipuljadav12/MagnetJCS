<?php

$StoreID = 'editor';

if ((($_FILES["UploadedImageFile"]["type"] == "image/gif") || ($_FILES["UploadedImageFile"]["type"] == "image/jpeg")
		|| ($_FILES["UploadedImageFile"]["type"] == "image/pjpeg")
		|| ($_FILES["UploadedImageFile"]["type"] == "image/png")
		|| ($_FILES["UploadedImageFile"]["type"] == "image/x-png")
		||($_FILES["UploadedImageFile"]["type"] == "image/bmp")))
		{
			
		$filen 	= rand(1000,9999).'_'.$_FILES["UploadedImageFile"]['name']; //file name
		
		$url = "../../upload/".$filen;

		move_uploaded_file($_FILES["UploadedImageFile"]['tmp_name'],$url);
		$stat = @stat( dirname($url) );
		$dir_perms = $stat['mode'] & 0007777;  // Get the permission bits.
		@chmod($url, $dir_perms );

	}

	$url = $_POST['path'].'/resources/assets/admin/upload/'.$filen;
	
//	$url = SERVER_ROOT."/theme/".strtolower(fetch_store_name($_REQUEST['StoreID']))."/".SITE_THEME."/page-images/".$filen;

	$funcNum = $_POST['CKEditorFuncNum']; 
	// Optional: instance name (might be used to load specific configuration file or anything else)
	$CKEditor = $_POST['CKEditor'] ;
	// Optional: might be used to provide localized messages
	$langCode = $_POST['langCode'] ;
	$message = '';
	echo "<script type='text/javascript'>window.opener.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";
	echo "<script>window.close();</script>";

?>