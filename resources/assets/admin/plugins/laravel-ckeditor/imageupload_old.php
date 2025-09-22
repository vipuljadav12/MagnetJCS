<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="">
<meta name="author" content="">
<!--<link rel="icon" href="" />-->
<title>CKFinder</title>
<link href="<?php echo $_GET['ajaxurl'];?>/resources/assets/imageupload/css/all.min.css" rel="stylesheet">
<link href="<?php echo $_GET['ajaxurl'];?>/resources/assets/imageupload/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo $_GET['ajaxurl'];?>/resources/assets/imageupload/css/imageupload.css" rel="stylesheet">
<link href="<?php echo $_GET['ajaxurl'];?>/resources/assets/imageupload/css/magnific-popup.css" rel="stylesheet">
<link href="<?php echo $_GET['ajaxurl'];?>/resources/assets/imageupload/css/imgareaselect.css" rel="stylesheet">
<style type="text/css">
#divLargerImage
{
    display: none;
    width: 250px;
    height: 250px;
    position: absolute;
    top: 35%;
    left: 35%;
    z-index: 99;
}
#divOverlay
{
    display: none;
    position: absolute;
    top: 0;
    left: 0;
    background-color: #CCC;
    opacity: 0.5;
    width: 100%;
    height: 100%;
    z-index: 98;
}

</style>
</head>
<body>
<div class="wrapper"> 
    <!-- Header  -->
    <div class="header bg-light border-bottom d-flex align-items-center">
        <div class="mr-auto btn-wrapper-head"> 
            <a href="javascript:void(0);" class="btn btn-primary d-inline-block d-md-none menu-btn" title=""> <i class="fas fa-bars"></i> <span class="btn-text">Menu</span> </a> 
            <a href="javascript:void(0);" class="btn btn-primary choose" title="Choose"> <i class="fas fa-check-circle"></i> <span class="btn-text">Choose</span> </a> 
            <a href="javascript:void(0);" class="btn btn-primary d-none d-md-inline-block upload" title="Upload"> <i class="fas fa-upload"></i> <span class="btn-text">Upload</span> </a> 
            <a href="javascript:void(0);" class="btn btn-primary d-none d-md-inline-block view" title="View"> <i class="fas fa-eye"></i> <span class="btn-text">View</span> </a> 
            <a href="javascript:void(0);" class="btn btn-primary d-none d-md-inline-block download" title="Download"> <i class="fas fa-download"></i> <span class="btn-text">Download</span> </a> 
            <a href="javascript:void(0);" class="btn btn-primary d-none d-md-inline-block" title="Edit"> <i class="fas fa-pen"></i> <span class="btn-text">Edit</span> </a> 
            <a href="javascript:void(0);" class="btn btn-primary d-none d-md-inline-block copy" title="Copy"> <i class="fas fa-copy"></i> <span class="btn-text">Copy</span> </a> 
            <a href="javascript:void(0);" class="btn btn-primary d-none d-md-inline-block move" title="Move"> <i class="fas fa-file-export"></i> <span class="btn-text">Move</span> </a> 
            <a href="javascript:void(0);" class="btn btn-primary d-none d-md-inline-block rename" title="Rename"> <i class="far fa-edit"></i> <span class="btn-text">Rename</span> </a> 
            <a href="javascript:void(0);" class="btn btn-primary d-none d-md-inline-block delete" title="Delete"> <i class="fas fa-trash-alt"></i> <span class="btn-text">Delete</span> </a> 
            <a href="javascript:void(0);" class="btn btn-primary d-none d-md-inline-block new_subfolder" title="New Subfolder"> <i class="fas fa-folder-plus"></i> <span class="btn-text">New Subfolder</span> </a>
            <div class="dropdown d-md-none d-inline-block"> 
                <a href="javascript:void(0);" class="btn btn-primary dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="fas fa-ellipsis-v"></i> <span class="btn-text">Menu</span> </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton"> 
                    <a href="javascript:void(0);" class="dropdown-item" title="Upload"> <i class="fas fa-upload"></i> <span class="btn-text">Upload</span> </a> 
                    <a href="javascript:void(0);" class="dropdown-item" title="View"> <i class="fas fa-eye"></i> <span class="btn-text">View</span> </a> 
                    <a href="javascript:void(0);" class="dropdown-item" title="Download"> <i class="fas fa-download"></i> <span class="btn-text">Download</span> </a> 
                    <a href="javascript:void(0);" class="dropdown-item" title="Edit"> <i class="fas fa-pen"></i> <span class="btn-text">Edit</span> </a> 
                    <a href="javascript:void(0);" class="dropdown-item" title="Copy"> <i class="fas fa-copy"></i> <span class="btn-text">Copy</span> </a> 
                    <a href="javascript:void(0);" class="dropdown-item" title="Move"> <i class="fas fa-file-export"></i> <span class="btn-text">Move</span> </a> 
                    <a href="javascript:void(0);" class="dropdown-item" title="Rename"> <i class="far fa-edit"></i> <span class="btn-text">Rename</span> </a> 
                    <a href="javascript:void(0);" class="dropdown-item" title="Delete"> <i class="fas fa-trash-alt"></i> <span class="btn-text">Delete</span> </a> 
                    <a href="javascript:void(0);" class="dropdown-item" title="New Subfolder"> <i class="fas fa-folder-plus"></i> <span class="btn-text">New Subfolder</span> </a>
                </div>
            </div>
        </div>
        <div class="ml-auto">
            <div class="form-inline">
                <input type="text" class="form-control custom-control d-inline m-r-10" placeholder="Filter" value="">
                <a href="javascript:void();" class="btn btn-primary btn-round btn-right-side-nav"><i class="fas fa-cog"></i></a> </div>
        </div>
    </div>
    <!-- Header Ends Here --> 
    <!-- Main Section -->
    <main> 
        <!-- Left side Navigation -->
        <div class="directory">
            <div class="left-side-nav border-right" id="left-nav">
                <ul class="left-navigation list-unstyled">
                    <li> <a href="javascript:void(0);">Files</a> </li>
                    <li> <a href="javascript:void(0);">Images <span class="submenu-icon"><i class="fas fa-sort-down"></i><i class="fas fa-caret-right"></i></span></a>
                        <ul class="sub-menu">
                            <li> <a href="javascript:void(0);">Blog</a> </li>
                            <li> <a href="javascript:void(0);">Test <span class="submenu-icon"><i class="fas fa-sort-down"></i><i class="fas fa-caret-right"></i></span></a>
                                <ul class="sub-menu">
                                    <li><a href="javascript:void(0);">Test 1</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- <div class="content-wrapper ml-auto">
            <button type="submit" name="upload_thumbnail" value="Applysjdbcjsdbcj" id="save_thumb" >Save Thumb</button>
        </div> -->
        <!-- Left side Navigation Ends Here -->
        <div class="files">
            <div class="content-wrapper ml-auto">
                <ul class="file-list">
                    <li class="file-item active">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                    <li class="file-item">
                        <figure class="figure"> <a href="javascript:void(0);">
                            <div class="figure-img"><img src="images/connector.php.jpg" class="img-fluid" alt="weight-loss-opt.jpg" /></div>
                            <figcaption class="figure-caption">
                                <h6>weight-loss-opt.jpg</h6>
                                <p><small>4/6/2017 3:54 AM</small></p>
                                <p><small>92.0 KB</small></p>
                            </figcaption>
                            </a> </figure>
                    </li>
                </ul>
            </div>
        </div>
        <div id="divLargerImage"></div>
    </main>

    <button class="browseImage" style="display: none;"></button>
    <!-- Main Section Ends Here --> 
    <!-- Right side navigation -->
    <div class="right-side-nav border-left settings">
        <div class="header bg-light border-bottom d-flex align-items-center"><a class="btn btn-primary btn-round right-side-nav-close" href="javascript:void();"><i class="fas fa-times"></i></a></div>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="">
                        <label>Settings</label>
                    </div>
                    <div class="border bg-light m-b-5 p-10">
                        <div class="form-group form-check custom-control custom-checkbox m-b-0">
                            <input type="checkbox" name="settings[]" class="form-check-input custom-control-input d-block" id="chkFileName" value="filename">
                            <label class="form-check-label custom-control-label d-block" for="chkFileName">File Name</label>
                        </div>
                    </div>
                    <div class="border bg-light m-b-5 p-10">
                        <div class="form-group form-check custom-control custom-checkbox m-b-0">
                            <input type="checkbox" name="settings[]" class="form-check-input custom-control-input d-block" id="chkDate" value="date">
                            <label class="form-check-label custom-control-label d-block" for="chkDate">Date</label>
                        </div>
                    </div>
                    <div class="border bg-light m-b-20 p-10">
                        <div class="form-group form-check custom-control custom-checkbox m-b-0">
                            <input type="checkbox" name="settings[]" class="form-check-input custom-control-input d-block" id="chkFileSize" value="filesize">
                            <label class="form-check-label custom-control-label d-block" for="chkFileSize">File Size</label>
                        </div>
                    </div>
                    <div class="">
                        <label>View</label>
                    </div>
                    <div class="border bg-light m-b-5 p-10">
                        <div class="form-group form-check custom-control custom-radio m-b-0">
                            <input type="radio" class="form-check-input custom-control-input d-block" name="view" id="rdoList">
                            <label class="form-check-label custom-control-label d-block" for="rdoList">List</label>
                        </div>
                    </div>
                    <div class="border bg-light m-b-5 p-10">
                        <div class="form-group form-check custom-control custom-radio m-b-0">
                            <input type="radio" class="form-check-input custom-control-input d-block" name="view" id="rdoThumbnails">
                            <label class="form-check-label custom-control-label d-block" for="rdoThumbnails">Thumbnails</label>
                        </div>
                    </div>
                    <div class="border bg-light m-b-20 p-10">
                        <div class="form-group form-check custom-control custom-radio m-b-0">
                            <input type="radio" class="form-check-input custom-control-input d-block" name="view" id="rdoCompact">
                            <label class="form-check-label custom-control-label d-block" for="rdoCompact">Compact</label>
                        </div>
                    </div>
                    <div class="">
                        <label>Short By</label>
                    </div>
                    <div class="m-b-20">
                        <select class="custom-select d-block bg-light b-r-0" name="sortfile" id="sortfile">
                            <option value="filename">File Name</option>
                            <option value="filesize">File Size</option>
                            <option value="date">Date</option>
                        </select>
                    </div>
                    <div class="">
                        <label>Order</label>
                    </div>
                    <div class="border bg-light m-b-5 p-10">
                        <div class="form-group form-check custom-control custom-radio m-b-0">
                            <input type="radio" class="form-check-input custom-control-input d-block" name="order" value="ascending" id="rdoAscending">
                            <label class="form-check-label custom-control-label d-block" for="rdoAscending">Ascending</label>
                        </div>
                    </div>
                    <div class="border bg-light m-b-20 p-10">
                        <div class="form-group form-check custom-control custom-radio m-b-0">
                            <input type="radio" class="form-check-input custom-control-input d-block" name="order" value="descending" id="rdoDescending">
                            <label class="form-check-label custom-control-label d-block" for="rdoDescending">Descending</label>
                        </div>
                    </div>
                    <div class="">
                        <label>Thumbnails</label>
                    </div>
                    <div class="border bg-light m-b-5 p-10">
                        <div class="row align-items-center">
                            <div class="col-4 p-r-5">
                                <input type="text" id="range_thumbnail" class="form-control custom-control text-center" value="150">
                            </div>
                            <div class="col-8 p-l-5">
                                <input type="range" class="custom-range" id="customRange" name="points1" value="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalDirectory" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <button type="button" class="close btn-close" data-dismiss="modal" aria-label="Close"><img src="{{url('/resources/assets/admin')}}/images/btn-close.png" title="" alt=""></button>
                <!-- Modal Header -->
                <div class="modal-header">
                    
                    <h4 class="modal-title" id="myModalLabel"></h4>
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    <div id="modaldirectories"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalrenamefile" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <button type="button" class="close btn-close" data-dismiss="modal" aria-label="Close"><img src="{{url('/resources/assets/admin')}}/images/btn-close.png" title="" alt=""></button>
                <!-- Modal Header -->
                <div class="modal-header">
                    
                    <h4 class="modal-title" id="myModalLabel">Rename</h4>
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="row">
                        <input type="text" name="file_name" class="form-control">
                        <span class="error_file_rename"></span>
                    </div>
                    <div class="row">
                        <button class="btn btn-primary save_file" >Save</button>
                        <button class="btn btn-danger cancel_file" >Cancel</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalNewFolder" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <button type="button" class="close btn-close" data-dismiss="modal" aria-label="Close"><img src="{{url('/resources/assets/admin')}}/images/btn-close.png" title="" alt=""></button>
                <!-- Modal Header -->
                <div class="modal-header">
                    
                    <h4 class="modal-title" id="myModalLabel">New Folder</h4>
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="row m-b-10">
                        <input type="text" name="folder_name" class="form-control">
                        <span class="error_folder_name"></span>
                    </div>
                    <div class="row">
                        <button class="btn btn-primary save_subfolder m-r-10" >Save</button>
                        <button class="btn btn-danger cancel_subfolder" >Cancel</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="file_image">
    <input type="hidden" name="CKEditorFuncNum" value="<?php echo $_GET['CKEditorFuncNum'];?>" />

    <div class="img-crop" style="display: none;">
        <div id='preview-profile-pic'>
            <img src="" id="preview_image" style="padding-left: 365px;">
        </div>
        <form name="thumbnail" id="cropimage" action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">
            <input type="hidden" name="hdn-x1-axis" id="hdn-x1-axis" value="" />
            <input type="hidden" name="hdn-y1-axis" id="hdn-y1-axis" value="" />
            <input type="hidden" name="hdn-x2-axis" value="" id="hdn-x2-axis" />
            <input type="hidden" name="hdn-y2-axis" value="" id="hdn-y2-axis" />
            <input type="hidden" name="hdn-thumb-width" id="hdn-thumb-width" value="" />
            <input type="hidden" name="hdn-thumb-height" id="hdn-thumb-height" value="" />
            <input type="hidden" name="action" value="uploadCropFile">
            <input type="hidden" name="ajaxurl" id="ajaxurl" value="">
            <input type="hidden" name="path" id="path" value="">

        </form>
        
    </div>
    <div style="display: none;">
        <form id="file_upload" method="POST" enctype="multipart/form-data">
            <input type="file" name="uploadFile">
            <input type="hidden" name="action" value="uploadFile">
            <input type="hidden" name="ajaxurl" id="ajaxurl" value="">
            <input type="hidden" name="path" id="path" value="">
            <input type="submit" name="submit_file" id="submit_file">
        </form>
    </div>
    <!-- Right side navigation Ends Here --> 
</div>
<script src="<?php echo $_GET['ajaxurl'];?>/resources/assets/imageupload/js/jquery-3.3.1.slim.min.js"></script>
<script src="<?php echo $_GET['ajaxurl'];?>/resources/assets/imageupload/js/jquery.min.js"></script> 
<script src="<?php echo $_GET['ajaxurl'];?>/resources/assets/imageupload/js/all.min.js"></script> 
<script src="<?php echo $_GET['ajaxurl'];?>/resources/assets/imageupload/js/popper.min.js"></script> 
<script src="<?php echo $_GET['ajaxurl'];?>/resources/assets/imageupload/js/bootstrap.min.js"></script>
<script src="<?php echo $_GET['ajaxurl'];?>/resources/assets/imageupload/js/jquery.magnific-popup.min.js"></script>
<script src="<?php echo $_GET['ajaxurl'];?>/resources/assets/imageupload/js/jquery.imgareaselect.js"></script>
<script src="<?php echo $_GET['ajaxurl'];?>/resources/assets/imageupload/js/jquery.form.js"></script>
<script type="text/javascript" src="<?php echo $_GET['ajaxurl'];?>/resources/plugins/Imgbrowse/browseimg2.js" ajaxurl="<?php echo $_GET['ajaxurl'];?>" token="<?php echo $_GET['token'];?>"></script>
<script type="text/javascript">
$(document).ready(function(){
    $(document).ready(function(){
        $('.browseImage').trigger('click')
        // $.when($('.browseImage').trigger('click')).then($('#imgbrowse .modal-dialog').addClass('maxwidthreset'));
    })
	$(".btn-right-side-nav").click(function(){
		$(".right-side-nav").toggleClass("active");
	});
	
	$(".right-side-nav-close").click(function(){
		$(".right-side-nav").removeClass("active");
	});
	
	$(".menu-btn").click(function(){     
        $("#left-nav").toggleClass("active");
	});
	
    $(document).on('change','input[name="file_image"]',function(){
        var urlval = $(this).val();
        var funcNum = $('input[name="CKEditorFuncNum"]').val();
        window.opener.CKEDITOR.tools.callFunction(funcNum, urlval, '');
        window.close();
    });
	
});
</script>
</body>
</html>