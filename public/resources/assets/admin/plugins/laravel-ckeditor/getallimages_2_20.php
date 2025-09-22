<?php 
//print_r($_REQUEST);exit;
	
	if($_POST['action'] == 'getfiles') {
		$settings = $_POST['settings'];
		$sortby = $_POST['sortby'];
		$orderby = $_POST['orderby'];
		$class = new getallimages;
		$class->getfiles($_POST['path'],$settings,$sortby,$orderby);
	}

	if($_POST['action'] == 'getdirectories') {
		$class = new getallimages;
		$class->getdirectories($_POST['path']);
	}

	if($_POST['action'] == 'getpopupdirectories') {
		$class = new getallimages;
		$class->getpopupdirectories($_POST['path']);
	}

	if($_POST['action'] == 'copyfile') {
		$file_path = $_POST['file'];
		$directory_path = $_POST['directory'];
		$class = new getallimages;
		$class->copyfile($file_path,$directory_path);
	}

	if($_POST['action'] == 'movefile') {
		$file_path = $_POST['file'];
		$directory_path = $_POST['directory'];
		$class = new getallimages;
		$class->movefile($file_path,$directory_path);
	}

	if($_POST['action'] == 'deletefile') {
		$file_path = $_POST['file'];
		$directory_path = $_POST['directory'];
		$class = new getallimages;
		$class->deletefile($file_path,$directory_path);
	}

	if($_POST['action'] == 'renamefile') {
		$file_name = $_POST['file'];
		$old_file = $_POST['old_file'];
		$class = new getallimages;
		$class->renamefile($file_name,$old_file);
	}

	if($_POST['action'] == 'createFolder') {
		$folder_name = $_POST['folder_name'];
		$directory_path = $_POST['directory'];
		$class = new getallimages;
		$class->createNewFolder($folder_name,$directory_path);
	}

	if($_POST['action'] == 'uploadFile') {
		//print_r($_FILES);exit;
		$file_data = $_FILES;
		$directory_path = $_POST['path'];
		$class = new getallimages;
		$class->uploadFile($file_data,$directory_path);
	}

	if($_POST['action'] == 'saveCropImage') {
		//print_r($_FILES);exit;
		$class = new getallimages;
		extract($_POST);
		$class->editFile($_POST);
	}

	if($_POST['action'] == 'resizeImage') {
		$class = new getallimages;
		$class->resizeImage();
	}

	if($_POST['action'] == 'deleteResizeImage') {
		$class = new getallimages;
		$class->deleteResizeImage();
	}


	/**
	* 
	*/
	class getallimages 
	{
		
		function __construct()
		{
			# code...
			$this->ajaxurl = $_POST['ajaxurl'];
		}

		function getfiles($category,$settings = "",$sortby="",$orderby="") {
			if($category != "") {
				$url = $this->ajaxurl.'/resources/filebrowser/'.$category;
				$mainDir = __DIR__.'/../../../filebrowser/'.$category;
				$mDir = 'resources/filebrowser/'.$category;
			} else {
				$url = $this->ajaxurl.'/resources/filebrowser';
				$mainDir = __DIR__.'/../../../filebrowser';
				$mDir = 'resources/filebrowser';
			}
			// if($category != "") {
			// 	$mainDir   = $category;
			// 	$url = $category;
			// } else {
			// 	$mainDir   = __DIR__.'/../../../filebrowser';
			// 	$url = __DIR__.'/../../../filebrowser';
			// }

			//$url = __DIR__.'/../../../filebrowser'.$category;
			//$url = $this->ajaxurl.'/resources/filebrowser/'.$category;
			//print_r($settings);exit;
	        $getallCat = scandir($mainDir);
	        unset($getallCat[0]);
	        unset($getallCat[1]);
	        //print_r($getallCat);exit;
	        $return = [];
	        $extentions = ['jpg','jpeg','gif','png','pdf','docs','doc','xls','xlsx'];
	        $filefold = [];
	        $folder = $this->ajaxurl.'/resources/front/assets/images'.'/folder.jpg';
	        //$directories = glob($mainDir . '/*' , GLOB_ONLYDIR);
	        $folders = array();
	        $file_folder = "";
	        //echo __DIR__;exit;
	        
	        foreach ($getallCat as $key => $value) {
	            $file = explode('.', $value);
	            if (isset($file[1])) {
	                $dir = str_replace('resources/filebrowser', '', $mDir);
	                $size = filesize($mainDir.'/'.$value);
                	$file_size = $this->format_size($size);
	                if (in_array($file[1], $extentions)) {
	                    if (in_array($file[1], ['docs','doc'])) {                    
	                        $filefold[] = ['name'=>$value,'path'=>$url.'/'.$value,'imgpath'=>$this->ajaxurl.'/resources/plugins/Imgbrowse/docs.png','type'=>'file','filetype'=>'other','access'=>str_replace("/", "", $dir),'size'=>$file_size,'time'=>date('F d y h:i a',filemtime($mainDir.'/'.$value)),'filesize'=>$size];
	                    }elseif ($file[1]=='pdf') {
	                        $filefold[] = ['name'=>$value,'path'=>$url.'/'.$value,'imgpath'=>$this->ajaxurl.'/resources/plugins/Imgbrowse/pdf.png','type'=>'file','filetype'=>'other','access'=>str_replace("/", "", $dir),'size'=>$file_size,'time'=>date('F d y h:i a',filemtime($mainDir.'/'.$value)),'filesize'=>$size];
	                    }elseif (in_array($file[1], ['xls','xlsx'])) {
	                        $filefold[] = ['name'=>$value,'path'=>$url.'/'.$value,'imgpath'=>$this->ajaxurl.'/resources/plugins/Imgbrowse/xls.png','type'=>'file','filetype'=>'other','access'=>str_replace("/", "", $dir),'size'=>$file_size,'time'=>date('F d y h:i a',filemtime($mainDir.'/'.$value)),'filesize'=>$size];
	                    }else{
	                        $filefold[] = ['name'=>$value,'path'=>$url.'/'.$value,'imgpath'=>$mainDir.'/'.$value,'type'=>'file','filetype'=>'image','access'=>str_replace("/", "", $dir),'size'=>$file_size,'time'=>date('F d y h:i a',filemtime($mainDir.'/'.$value)),'filesize'=>$size];
	                    }
	                }else{
	                    $filefold[] = ['name'=>$value,'path'=>$url.'/'.$value,'imgpath'=>$this->ajaxurl.'/resources/plugins/Imgbrowse/file.png','type'=>'file','filetype'=>'other','access'=>str_replace("/", "", $dir),'size'=>$file_size,'time'=>date('F d y h:i a',filemtime($mainDir.'/'.$value)),'filesize'=>$size];
	                }
	                
	            }
        	}
	        
	        //print_r($filefold);exit;

        	if($sortby == "filename" && $orderby == "ascending") {
        		$this->array_sort_by_column($filefold, 'name',$orderby);
        	}
        	if($sortby == "filename" && $orderby == "descending") {
        		$this->array_sort_by_column($filefold, 'name',$orderby);
        	}
	        if($sortby == "date" && $orderby == "ascending") {
        		//sort($filefold['size']);
        		$this->array_sort_by_column($filefold, 'time',$orderby);
        	} 
        	if($sortby == "date" && $orderby == "descending") {
        		$this->array_sort_by_column($filefold, 'time',$orderby);
        	}

        	if($sortby == "filesize" && $orderby == "ascending") {
        		$this->array_sort_by_column($filefold, 'filesize',$orderby);
        	}

        	if($sortby == "filesize" && $orderby == "descending") {
        		$this->array_sort_by_column($filefold, 'filesize',$orderby);
        	}
	        //print_r($filefold);exit;
	        // exit;
	        //$file_folder .= '</ul></div>';
	        $data = $file_folder;
	        $data .= '<div class="content-wrapper ml-auto">
            			<ul class="file-list">';
            	foreach ($filefold as $key => $value) {
            		if($key == 0) {
            			$active = "active";
            		} else {
            			$active = "";
            		}
            		if($value['type'] == 'folder') {
            		} else {
            			$data .= '<li class="file-item '.$active.'">
	                    <figure class="figure"> <a href="javascript:void(0);">
	                        <div class="figure-img">';
	                        if($value['filetype'] == "other") {
	                        	$data .= '<img src="'.$value["imgpath"].'" class="img-fluid" alt="image" filepath="'.$value["path"].'" filetype="'.$value["filetype"].'" access="'.$value["access"].'" data-size="1200x800" data-index="'.$key.'" />';
	                        } else {
	                        	$data .= '<img src="'.$value["path"].'" class="img-fluid" alt="image" filepath="'.$value["imgpath"].'" filetype="'.$value["filetype"].'" access="'.$value["access"].'" data-size="1200x800" data-index="'.$key.'" />';
	                        }
	                        $data .= '</div>
	                        <figcaption class="figure-caption">';
	                        	if(is_array($settings)) {
		                        	if(in_array("filename", $settings)) {
		                        		$data .= '<h6>'.$value["name"].'</h6>';	
		                        	}
		                            if(in_array("date", $settings)) {
		                            	$data .= '<p><small>'.$value["time"].'</small></p>';
		                            }
		                            if(in_array("filesize", $settings)) {
		                            	$data .= '<p><small>'.$value["size"].'</small></p>';
		                            }
	                        	}
	                        $data .= '</figcaption>
	                        </a> 
                        </figure>
	                </li>';
            		}
            		
            	}
            $data .= "<ul></div>";    
            
	        echo $data;
		}

		function format_size($size) {
	      	$sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
	      	if ($size == 0) { 
	      		return('n/a'); 
	      	} else {
	    	  	return (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i]); 
	    	}
		}

		function getdirectories($dir) {
			// if($dir != "") {
			// 	$mainDir = $dir;
			// } else {
			// 	$mainDir = __DIR__.'/../../../filebrowser';
			// }
			//$mainDir = __DIR__.'/../../../filebrowser';

			$url = $this->ajaxurl.'/resources/filebrowser';
			$mainDir = 'resources/filebrowser/'.$dir;
			//$dir = "filebrowser";
			//echo $mainDir;
	        // $getallCat = scandir($mainDir);
	        // unset($getallCat[0]);
	        // unset($getallCat[1]);

	        $directories  = $this->listFolderFiles($dir);
	        //print_r($directories);exit;
	        $file_folder = '';
	        $file_folder .= '<div class="left-side-nav border-right" id="left-nav"><ul class="left-navigation list-unstyled">';
	        if(count($directories) > 0) {
	        	$file_folder .= '<li class="img-browser"><a href="javascript:void(0);" class="folder active" getpath="">filebrowser</a> <span class="submenu-icon"><i class="fas fa-sort-down"></i><i class="fas fa-caret-right"></i></span><ul class="sub-menu">';
        	}

	        $file_folder .= $this->showDirectories($directories);

	        if(count($directories) > 0) {
	        	$file_folder .= '</ul></li>';
        	}

	        $file_folder .= "</ul></div>";

	        echo $file_folder;
		}

		function getpopupdirectories($dir) {
			// if($dir != "") {
			// 	$mainDir = $dir;
			// } else {
			// 	$mainDir = __DIR__.'/../../../filebrowser';
			// }
			$mainDir = __DIR__.'/../../../filebrowser/'.$dir;
			//$dir = "filebrowser";
			//echo $mainDir;
	        // $getallCat = scandir($mainDir);
	        // unset($getallCat[0]);
	        // unset($getallCat[1]);

	        $directories  = $this->listFolderFiles($dir);
	        //print_r($directories);exit;
	        $file_folder = '';
	        $file_folder .= '<div class="popup-nav border-right" id="left-nav"><ul class="left-navigation list-unstyled">';

	        if(count($directories) > 0) {
	        	$file_folder .= '<li class="popup-img-browser"><a href="javascript:void(0);" class="folder" getpath="">filebrowser</a> <span class="submenu-icon"><i class="fas fa-sort-down"></i><i class="fas fa-caret-right"></i></span><ul class="sub-menu">';
        	}

	        $file_folder .= $this->showpopupDirectories($directories);

	        if(count($directories) > 0) {
	        	$file_folder .= "</ul></li>";
	        }
	        $file_folder .= "</ul></div>";

	        echo $file_folder;
		}

		function listFolderFiles($base_dir,$level = 0) {
			
			if($base_dir != "") {
				$url = $this->ajaxurl.'/resources/filebrowser/'.$base_dir;
				$mainDir = __DIR__.'/../../../filebrowser/'.$base_dir;
				$mDir = 'resources/filebrowser/'.$base_dir;
			} else {
				$url = $this->ajaxurl.'/resources/filebrowser';
				$mainDir = __DIR__.'/../../../filebrowser/';
				$mDir = 'resources/filebrowser';
			}
			//$dirs = scandir($url);
	        $directories = array();
	        foreach(scandir($mainDir) as $file) {
	            if($file == '.' || $file == '..') continue;
	            //$dir = $base_dir.DIRECTORY_SEPARATOR.$file;
	            //echo $file;
	            $dir = str_replace('resources/filebrowser/', '', $mDir.'/'.$file);
	            if(is_dir($mainDir.'/'.$file)) {
	                $directories[]= array(
	                        'level' => $level,
	                        'name' => $file,
	                        'path' => $dir,
	                        'children' => $this->listFolderFiles($dir, $level +1)
	                );
	            }
	        }
	        return $directories;
	    }

		function showDirectories($list, $parent = array())
	    {
	        static $str = '';
	        //print_r($list);
	        foreach ($list as $directory){
	            $parent_name = count($parent) ? " parent: ({$parent['name']}" : '';

	            //echo count($parent);

	            $prefix = str_repeat('-', $directory['level']);

	            //$str .= "$prefix {$directory['name']} $parent_name <br/>";  // <-----------
	            if(count($parent) == 0){
	                $str .= '<li class="img-browser"> <a href="javascript:void(0);" class="folder" getpath="'.$directory['path'].'">'.$directory['name'];
	            }
	            else{
	                //echo $directory['level'] .'---'. count($directory['children']);
	                if($directory['level'] == count($directory['children'])-1){

	                    //$str .= '-1111-';
	                    $str .= '<li class="img-browser"><a href="javascript:void(0);" class="folder" getpath="'.$directory['path'].'">'.$directory['name'].'</a></li></ul>';
	                }
	                else{
	                    //$str .= '-2222-';
	                    $str .= '<li class="img-browser"><a href="javascript:void(0);" class="folder" getpath="'.$directory['path'].'">'.$directory['name'];
	                    if(count($directory['children'])){
	                    } else {
	                    	$str .= '</a></li>';	
	                    }
	                    
	                }
	            }
	            //echo count($directory['children']).'------';
	            if(count($directory['children'])){
	                // list the children directories
	            	//$str .= '-children-';
	                $str .= '</a><span class="submenu-icon"><i class="fas fa-sort-down"></i><i class="fas fa-caret-right"></i></span><ul class="sub-menu">';
	                
	                $this->showDirectories($directory['children'], $directory);

	                $str .= "</ul>";
	            }
	            else {
	                $str .= "</a>"; 
	            }                           
	            $str .= '</li>'; 
	        }
	        //print_r($str);exit;
	        return $str;
	    }

	    function showpopupDirectories($list, $parent = array())
	    {
	        static $str = '';
	        //print_r($list);
	        foreach ($list as $directory){
	            $parent_name = count($parent) ? " parent: ({$parent['name']}" : '';

	            //echo count($parent);

	            $prefix = str_repeat('-', $directory['level']);

	            //$str .= "$prefix {$directory['name']} $parent_name <br/>";  // <-----------
	            if(count($parent) == 0){
	                $str .= '<li class="img-browser popup-img-browser"> <a href="javascript:void(0);" getpath="'.$directory['path'].'">'.$directory['name'];
	            }
	            else{
	                //echo $directory['level'] .'---'. count($directory['children']);
	                if($directory['level'] == count($directory['children'])-1){

	                    //$str .= '-1111-';
	                    $str .= '<li class="img-browser popup-img-browser"><a href="javascript:void(0);" getpath="'.$directory['path'].'">'.$directory['name'].'</a></li></ul>';
	                }
	                else{
	                    //$str .= '-2222-';
	                    $str .= '<li class="img-browser popup-img-browser"><a href="javascript:void(0);" getpath="'.$directory['path'].'">'.$directory['name'];
	                    if(count($directory['children'])){
	                    } else {
	                    	$str .= '</a></li>';	
	                    }
	                    
	                }
	            }
	            //echo count($directory['children']).'------';
	            if(count($directory['children'])){
	                // list the children directories
	            	//$str .= '-children-';
	                $str .= '</a><span class="submenu-icon"><i class="fas fa-sort-down"></i><i class="fas fa-caret-right"></i></span><ul class="sub-menu">';
	                
	                $this->showpopupDirectories($directory['children'], $directory);

	                $str .= "</ul>";
	            }
	            else {
	                $str .= "</a>"; 
	            }                           
	            $str .= '</li>'; 
	        }
	        //print_r($str);exit;
	        return $str;
	    }

	    function copyfile($filepath,$directorypath) {
	    	$link_array = explode('/',$filepath);
    		$file = end($link_array);
    		$directory = __DIR__.'/../../../filebrowser/'.$directorypath;
	    	copy($filepath,$directory.'/'.$file);
	    }

	    function movefile($filepath,$directorypath) {
	    	$link_array = explode('/',$filepath);
    		$file = end($link_array);
    		$directory = __DIR__.'/../../../filebrowser/'.$directorypath;
    		// echo $filepath.'------';
    		// echo $directory;exit;
	    	rename($filepath,$directory.'/'.$file);
	    }

	    function deletefile($filepath) {
    		unlink($filepath);
	    	//rename($filepath,$directorypath.'/'.$file);
	    }

	    function renamefile($file_name,$old_file) {
	    	$url = explode('/', $old_file);
			array_pop($url);
			$file_path = implode('/', $url);
	    	rename($old_file,$file_path.'/'.$file_name);
	    }

		function createNewFolder($folder_name,$directorypath) {
			if($directorypath == "") {
				$directory = __DIR__.'/../../../filebrowser';
			} else {
				$directory = __DIR__.'/../../../filebrowser/'.$directorypath;
			}
	    	if (!file_exists($directory.'/'.$folder_name)) {
			    mkdir($directory.'/'.$folder_name, 0777, true);
			    echo "success";
			} else {
				echo "";
			}
	    }

	    function uploadFile($file_data,$directorypath) {
	    	move_uploaded_file($file_data['uploadFile']['tmp_name'],__DIR__.'/../../../filebrowser/'.$directorypath.'/'.$file_data['uploadFile']['name']);
	    }	    

	    function editFile($data) {
	    	$t_width = 300; // Maximum thumbnail width
        	$t_height = 300;
        	//$post = isset($_POST) ? $_POST: array();
        	extract($data);
        	//$imagePath = $imgpath;
        	$fname = explode('/', $imgpath);
        	$file_name = end($fname);
        	//$ext = pathinfo($file_name, PATHINFO_EXTENSION);
        	//$fnames = explode('.', $file_name);
        	list($fname, $ext) = explode(".", $file_name);
        	if($path == "") {
        		$file_path = $_SERVER['DOCUMENT_ROOT'].'/SAH/resources/filebrowser/'.$file_name;	
        	} else {
        		$file_path = $_SERVER['DOCUMENT_ROOT'].'/SAH/resources/filebrowser/'.$path.'/'.$file_name;
        	}
        	
        	//$new_image = $file_path.$fnames[0].'_thumb_'.time().'.'.$ext;
        	//echo $new_image;exit;
            $ratio = ($t_width/$w1);
            $nw = ceil($w1 * $ratio);
            $nh = ceil($h1 * $ratio);
            $nimg = imagecreatetruecolor($w1,$h1);
            switch ($ext) {
	            case 'jpg':
	            case 'jpeg':
	            $source = imagecreatefromjpeg($file_path);
	            break;
	            case 'gif':
	            $source = imagecreatefromgif($file_path);
	            break;
	            case 'png':
	            $source = imagecreatefrompng($file_path);
	            break;
	            default:
	            $source = false;
	            break;
	        }
            //$im_src = imagecreatefromjpeg($file_path);
            imagecopyresampled($nimg,$source,0,0,$x1,$y1,$nw,$nh,$w1,$h1);
            switch ($ext) {
	            case 'jpg':
	            case 'jpeg':
	            imagejpeg($nimg,$file_path,90);
	            break;
	            case 'gif':
	            imagegif($nimg,$file_path,90);
	            break;
	            case 'png':
	            imagepng($nimg,$file_path);
	            break;
	            default:
	            $source = false;
	            break;
	        }
            //imagejpeg($nimg,$file_path,95);

   //          $width = $this->getWidth($imagePath);
			// $height = $this->getHeight($imagePath);
			
			// //Scale the image if it is greater than the width set above
			// if ($width > $max_width){
			// 	$scale = $max_width/$width;
			// }else{
			// 	$scale = 1;
			// }

			// $nw = ceil($w1 * $scale);
	  //       $nh = ceil($h1 * $scale);
	  //       $nimg = imagecreatetruecolor($nw,$nh);
	  //       $im_src = imagecreatefromjpeg($imagePath);
	  //       imagecopyresampled($nimg,$im_src,0,0,$x1,$y1,$nw,$nh,$width,$height);
	  //       imagejpeg($nimg,$imagePath,90);
	    }

	    function array_sort_by_column(&$filefold, $col,$order) {
	    	$sort_col = array();
	    	foreach ($filefold as $key=> $row) {
	    		if($col == "time") {
	    			$sort_col[$key] = strtotime($row[$col]);	
	    		}
	    		if($col == "name") {
	    			$sort_col[$key] = $row[$col];
	    		}
	    		if($col == "filesize") {
	    			$sort_col[$key] = $row[$col];	
	    		}
		    }
		    if($order == "ascending") {
		    	array_multisort($sort_col, SORT_ASC, $filefold);	
		    } else {
		    	array_multisort($sort_col, SORT_DESC, $filefold);
		    }
		    
	    }

	    function resizeImage() {
	    	$post = isset($_POST) ? $_POST: array();
	        $max_width = "500"; 
	        $path = $post['path'];

	        $valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
	        $filename = explode('/', $post['imgpath']);
	        $name = end($filename);
	        $tmp = $post['imgpath'];
	        if(strlen($name))
	        {
		        list($txt, $ext) = explode(".", $name);
		        if(in_array($ext,$valid_formats))
		        {
			        $actual_image_name = $txt.'_'.time().'.'.$ext;
			        //echo $_SERVER['DOCUMENT_ROOT'];exit;
			        if($path == "") {
		        		$filePath = $_SERVER['DOCUMENT_ROOT'].'/iws/resources/filebrowser/'.$actual_image_name;
		        	} else {
		        		$filePath = $_SERVER['DOCUMENT_ROOT'].'/iws/resources/filebrowser/'.$path.'/'.$actual_image_name;
		        	}
			        //$tmp = $_FILES['photoimg']['tmp_name'];
			        if(copy($tmp, $filePath))
			        {
			        	$width = getWidth($filePath);
			            $height = getHeight($filePath);
			            //Scale the image if it is greater than the width set above
			            if ($width > $max_width){
			                $scale = $max_width/$width;
			                $uploaded = resizeImage($filePath,$width,$height,$scale,$ext);
			            }else{
			                $scale = 1;
			                $uploaded = resizeImage($filePath,$width,$height,$scale,$ext);
			            }
			            if($path == "") {
			            	echo json_encode(array('url' => $post['ajaxurl'].'/resources/filebrowser/'.$actual_image_name,'path'=>$filePath));
			            } else {
			            	echo json_encode(array('url' => $post['ajaxurl'].'/resources/filebrowser/'.$path.'/'.$actual_image_name,'path' => $filePath));
			            	//return $post['ajaxurl'].'/resources/filebrowser/'.$path.'/'.$actual_image_name;
			            }
			            
			        }
		    	} else {
		    		echo "notsuccess";
		    	}

	    	}
		}

		function deleteResizeImage() {
			unlink($_POST['imgpath']);
			echo "success";
		}

		

	}

	function getHeight($image) {
	    $sizes = getimagesize($image);
	    $height = $sizes[1];
	    return $height;
	}

	/*********************************************************************
	     Purpose            : get image width.
	     Parameters         : null
	     Returns            : width
	     ***********************************************************************/
	function getWidth($image) {
	    $sizes = getimagesize($image);
	    $width = $sizes[0];
	    return $width;
	}

	function resizeImage($image,$width,$height,$scale,$ext) {
	    $newImageWidth = ceil($width * $scale);
	    $newImageHeight = ceil($height * $scale);
	    $newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
	    switch ($ext) {
            case 'jpg':
            case 'jpeg':
            $source = imagecreatefromjpeg($image);
            break;
            case 'gif':
            $source = imagecreatefromgif($image);
            break;
            case 'png':
            $source = imagecreatefrompng($image);
            break;
            default:
            $source = false;
            break;
        }
	    //$source = imagecreatefromjpeg($image);
	    imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
	    switch ($ext) {
            case 'jpg':
            case 'jpeg':
            imagejpeg($newImage,$image,90);
            break;
            case 'gif':
            imagegif($newImage,$image,90);
            break;
            case 'png':
            imagepng($newImage,$image);
            break;
            default:
            $source = false;
            break;
        }
	    //imagejpeg($newImage,$image,90);
	    chmod($image, 0777);
	    return $image;
	}