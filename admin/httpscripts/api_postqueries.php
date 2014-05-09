<?php

require('../../scripts/api_head_options.php');

$action=$_GET['action'];
$postdata=(array)json_decode(stripslashes($_POST['data']),true);

if($action=='poststatementphoto'){
	$students=$postdata['students'];
	$statement=$postdata['statement'];
	$photos=$postdata['photos'];

	if(count($students)==0 or count($photos)==0 or count($statement)==0){$errors[]='Invalid parameters';}

	$result['success']='true';
	$result['action']=$action;

	foreach($photos as $photo){
		$image=$photo['photo'];
		list($type, $image)=explode(';', $image);
		list($type, $filetype)=explode(':', $type);
		list($encode, $image)=explode(',', $image);
		if($filetype==='image/jpeg'){$extension='.jpg';}
		elseif($filetype==='image/png'){$extension='.png';}
		elseif($filetype==='image/gif'){$extension='.gif';}
		$image=str_replace(' ', '+', $image);
		$data=base64_decode($image);
		$filename=$photo['name'].$extension;
		$uniquename=uniqid().$extension;

		foreach($students as $sid){
			$d_e=mysql_query("SELECT epfusername FROM info WHERE student_id='$sid';");
			$epfu=mysql_result($d_e,0,'epfusername');
			$dir=$CFG->eportfolio_dataroot.'/files/'.substr($epfu,0,1).'/'.$epfu.'';
			$file=$dir.'/'.$uniquename;
			if(file_put_contents($file, $data)){$success='true';}
			else{$success='false';}
			$result['photos'][]=array(
				'success'=>$success,
				'sid'=>$sid,
				'filepath'=>$file,
				'filename'=>$filename
				);

			$publishdata['foldertype']='assessment';
			$publishdata['title']='';
			$publishdata['batchfiles'][]=array('epfusername'=>$epfu,
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
	$errors[]="Invalid action: $action";
	}

require('../../scripts/api_end_options.php');
?>
