<?php
/**											httpscripts/file_upload.php
 *
 * HTML5 Image uploader with Jcrop
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright 2012, Script Tutorials
 * http://www.script-tutorials.com/
 *
 * Extended and adapted for ClaSS
 *
 */

//ini_set('display_errors', 'On');
//error_reporting(E_ALL ^ E_NOTICE);

require_once('../../scripts/http_head_options.php');
require_once('../../lib/eportfolio_functions.php');

/**
 *
 * Uploads a browsed or dropped photo resized with jcrop tool or a simple file.
 *
 * @
 *
 */

//POST Info for browsed file
if($_POST['DRAG']=='false') {
	$owner=$_POST['FILEOWNER'];
	$context=$_POST['FILECONTEXT'];
	$linkedid=$_POST['FILELINKEDID'];
	$lid=$_POST['FILESID'];
	$ownertype=$_POST['OWNERTYPE'];
	$filename=$_POST['FILENAME'];
	$x1=$_POST['x1'];
	$y1=$_POST['y1'];
	$x2=$_POST['x2'];
	$y2=$_POST['y2'];
	$h=$_POST['h'];
	$w=$_POST['w'];
}

//POST Info for dropped file
if($_SERVER['HTTP_DRAG']=='true') {
	$owner=$_SERVER['HTTP_FILEOWNER'];
	$context=$_SERVER['HTTP_FILECONTEXT'];
	$linkedid=$_SERVER['HTTP_FILELINKEDID'];
	$filename=$_SERVER['HTTP_FILENAME'];
	$lid=$_SERVER['HTTP_FILESID'];
	$ownertype=$_SERVER['HTTP_OWNERTYPE'];
	$x1=$_SERVER['HTTP_X1'];
	$y1=$_SERVER['HTTP_Y1'];
	$x2=$_SERVER['HTTP_X2'];
	$y2=$_SERVER['HTTP_Y2'];
	$h=$_SERVER['HTTP_H'];
	$w=$_SERVER['HTTP_W'];
}

//Image size and quality
$iHeight=292.5; 
$iWidth=260;
$iJpgQuality=100;
$varName='';
$fSize=1536*1024;

if($_FILES or $_SERVER['HTTP_DRAG']=='true') {
	if(!$_FILES['image_file']['error'] && $_FILES['image_file']['size'] < $fSize or $_SERVER['HTTP_DRAG']=='true') {
		if(is_uploaded_file($_FILES['image_file']['tmp_name']) or $_SERVER['HTTP_DRAG']=='true') {
			global $CFG;
//			if($filename!='' and $owner!=''){
				if($context=='icon') {
					$filepath=$CFG->eportfolio_dataroot.'/cache/images/';
					$uniquename=$owner.'.jpeg';
					if(file_exists($filepath.$uniquename)){
						if(unlink($filepath.$uniquename)){
							}
						else{
							trigger_error('FAILED TO UNLINK: '.$filepath.$uniquename,E_USER_WARNING);
							}
						}
					}
				elseif($context=='report'){
					$filepath=$CFG->eportfolio_dataroot. '/cache/reports/';
					$uniquename=uniqid();
					}
				else{
					$filepath=$CFG->eportfolio_dataroot. '/cache/files/';
					$uniquename=uniqid();
					}

				// new unique filename
				$sTempFileName=$CFG->eportfolio_dataroot.'/cache/images/'.$owner;  //temp file (just for resizing)
				$tmp=$filepath.$uniquename;

				// move temporal uploaded file into cache folder
				if($_SERVER['HTTP_DRAG']=='true'){
					$varName=$tmp;
					file_put_contents($tmp,file_get_contents('php://input'));
					}

				elseif($_POST['DRAG']=='false') {
					$varName=$sTempFileName;
					move_uploaded_file($_FILES['image_file']['tmp_name'], $sTempFileName);
					}

				if($context=='icon') {
					// change file permission to 777 to modify it
					@chmod($varName, 0777);

					if(file_exists($varName) && filesize($varName) > 0){
						
						$aSize=getimagesize($varName); // try to obtain image info
						if(!$aSize){
							@unlink($sTempFileName);
							return;
							}

						// check for image type
						switch($aSize[2]) {
							case IMAGETYPE_JPEG:
								$sExt='.jpeg';
								// create a new image from jpg/jpeg file 
								$vImg=@imagecreatefromjpeg($varName);
							break;
							case IMAGETYPE_PNG:
								$sExt='.jpeg';
								// create a new image from png file 
								$vImg=@imagecreatefrompng($varName);
							break;
							default:
								@unlink($sTempFileName);
							return;
							}

						// create a new true color image (final size)
						$vDstImg=@imagecreatetruecolor($iWidth, $iHeight);

						// copy and resize part of an image with resampling
						imagecopyresampled($vDstImg, $vImg, 0, 0, (int)$x1, (int)$y1, $iWidth, $iHeight, (int)$w, (int)$h);

						// define a result image filename
						$sResultFileName=$sTempFileName.$sExt;

						// output image to file with the final features and delete the temporal one
						imagejpeg($vDstImg, $sResultFileName, $iJpgQuality);
						@unlink($sTempFileName);
						}
					}

				//Info for publish data
				$publishdata['foldertype']=$context;
				$publishdata['title']='';
				$publishdata['batchfiles'][]=array('epfusername'=>$owner,
												   'filename'=>$uniquename,
												   'originalname'=>$filename,
												   'linkedid'=>$linkedid,
												   'description'=>'',
												   'tmpname'=>$tmp
												   );

				//Upload the file to eportfolio directory
				upload_files($publishdata);
//				}
			}
		}
	}

require_once('../../scripts/http_end_options.php');

/*If context is icon redirect to student/staff view*/
if($context=='icon'){
	global $CFG;
	$site=$CFG->siteaddress.$CFG->sitepath."/".$CFG->applicationdirectory;

	//Check if the connection is HTTPS or HTTP
	if(isset($_SERVER['HTTPS'])){
  		$httpcheck='https';
		}
	else{
		$httpcheck='http';
		}

	/* Redirects to profile */
	if($_POST['DRAG']=='false'){
		if($ownertype=='staff'){ 
			header("Location: ".$httpcheck."://".$site."/admin.php?current=staff_details.php&seluid=$lid");
			}
		else{
			header("Location: ".$httpcheck."://".$site."/infobook.php?current=student_view.php&sid=$lid");
			}
		}
	}
exit();
?>
