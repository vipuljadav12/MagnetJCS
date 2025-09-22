<?php

    $funcNum = $_GET['CKEditorFuncNum'] ;
    // Optional: instance name (might be used to load a specific configuration file or anything else).
    $CKEditor = $_GET['CKEditor'] ;
    // Optional: might be used to provide localized messages.
    $langCode = $_GET['langCode'] ;

    $StoreID = 'editor';

	$allowed =  array('png','jpg','jpeg','gif','bmp','tiff','doc','docx','xls','xlsx','csv','txt','xml','zip','rar','7z','pdf');
	$filename = $_FILES['upload']['name'];
	$ext = pathinfo($filename, PATHINFO_EXTENSION);
	if(in_array(strtolower($ext),$allowed) ) {
		$filen 	= rand(1000,9999).'_'.$_FILES["upload"]['name']; //file name

		// $url 	= "../../../resources/assets/ck-editor";
		$url 	= 'images/'.$filen;

		move_uploaded_file($_FILES["upload"]['tmp_name'],$url); 
		$stat = @stat( dirname($url) );
		$dir_perms = $stat['mode'] & 0007777;  // Get the permission bits.
		@chmod($url, $dir_perms );
		$message = 'file uploaded';
		// Check the $_FILES array and save the file. Assign the correct path to a variable ($url).
		$url = 'images/'.$filen;
		// $url = "http://10.0.10.48/tfs18/iwebsquare2018/resources/assets/Store-".$StoreID.'-image/'.$filen;
		//$url = SERVER_ROOT."/theme/".strtolower(fetch_store_name($_REQUEST['StoreID']))."/".SITE_THEME."/page-images/".$filen;
		// Usually you will only assign something here if the file could not be uploaded.
	}else{
		$message = 'file not supported';
		$url = '';
	}

    echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";
?>