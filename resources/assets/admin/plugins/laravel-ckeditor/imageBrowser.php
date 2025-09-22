<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head id="Head1">
<title>Image Browser</title>
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
	$defaultDir = "../../../assets/admin/upload/";
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
<body onload="changeScreenSize(560,250)">
<center>
<form name="form1" method="post" action="imageUploader.php?funcNum=<?=$funcNum?>&CKEditor=<?=$CKEditor?>&langCode=<?=$langCode?>" id="form1" enctype="multipart/form-data">
    <input type="hidden" name="path" value="<?=$_REQUEST['path']?>">

  <div>
    <h1 style="text-align: left; margin-left:15px;">Image Browser</h1>
    <div id="UpdatePanel1">
      <table width="500" cellpadding="10" cellspacing="0" border="0" style="background-color:#F1F1E3; margin:15px; border:1px dotted #000">
        <tr>
          <td style="width: 480px;" valign="top"> Upload Image: (10 MB max)
            <input type="file" name="UploadedImageFile" id="UploadedImageFile" />
            <input type="submit" name="UploadButton" value="Upload" id="UploadButton" />
          </td>
        </tr>
      </table>
      <input type="hidden" name="StoreID" value="<?=$_GET['StoreID']?>" />
      <input type="hidden" name="CKEditor" value="<?=$_GET['CKEditor']?>" />
      <input type="hidden" name="CKEditorFuncNum" value="<?=$_GET['CKEditorFuncNum']?>" />
      <input type="hidden" name="langCode" value="<?=$_GET['langCode']?>" />
      <input type="submit" name="OkButton" value="Submit" id="OkButton"  />
      <input type="submit" name="CancelButton" value="Cancel" onClick="window.top.close(); window.top.opener.focus();" id="CancelButton" />
    </div>
  </div>
</form>
</center>

</body>
</html>