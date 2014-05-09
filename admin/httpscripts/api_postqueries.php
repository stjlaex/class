<?php

require('../../scripts/api_head_options.php');

$action=$_GET['action'];

if($action=='poststatementphoto'){
	$students=$_POSTls['students'];
	$statement=$_POST['statement'];
	$photos=$_POST['photos'];
	//if(!is_array($photos)){$photos[]=$photos;}
	//if(!is_array($students)){$students[]=$students;}

	$result['success']='true';
	$result['action']=$action;
	//$result['test']=$_POST;

	foreach($photos as $photo){
		$image=$photo;
		if(strpos($image,'image/jpeg')!==false){$filetype='image/jpeg';$extension='.jpg';}
		elseif(strpos($image,'image/png')!==false){$filetype='image/png';;$extension='.png';}
		elseif(strpos($image,'image/gif')!==false){$filetype='image/gif';;$extension='.gif';}
		$image=str_replace('data:'.$filetype.';base64,', '', $image);
		$image=str_replace(' ', '+', $image);
		$data=base64_decode($image);

		foreach($students as $sid){
			$d_e=mysql_query("SELECT epfusername FROM info WHERE student_id='$sid';");
			$epfu=mysql_result($d_e,0,'epfusername');
			$dir=$CFG->eportfolio_dataroot.'/files/'.substr($epfu,0,1).'/'.$epfu.'/';
			$dir='/home/epfdata/cache/files';
			$file=$dir.'/apitestfile'.uniqid().$extension;
			$success=file_put_contents($file, $data);
			$result['photos'][]=array(
				'success'=>$success,
				'sid'=>$sid,
				'filepath'=>$file
				);

			$publishdata['foldertype']=$context;
			$publishdata['title']='';
			$publishdata['batchfiles'][]=array('epfusername'=>$owner,
											   'filename'=>$uniquename,
											   'originalname'=>$filename,
											   'linkedid'=>$linkedid,
											   'description'=>'',
											   'tmpname'=>$tmp
											   );
			//upload_files($publishdata);
			}
		}
	}
else{
	$errors[]=print_string('invalidaction','admin').": $action";
	}

require('../../scripts/api_end_options.php');
?>
