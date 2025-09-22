<?php

function CheckExcelHeader($excelHeader='',$fixheader='') {
	  
	  $result=array_diff_assoc($excelHeader,$fixheader);
	  // dd($result, $excelHeader,$fixheader);
	  if(empty($result))
	  return true;
	  else 
	  return false;
} 