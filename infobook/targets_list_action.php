<?php
/**									targets_list_action.php
 */

$action='targets_list.php';

include('scripts/sub_action.php');


if($sub=='Submit'){
	$Targets=(array)fetchTargets($sid);
	foreach($Targets['Target'] as $index => $Target){
		if(is_array($Target)){
			$cattype=$Target['Category']['value_db'];
			$inname='detail'.$index;
			$inval=clean_text($_POST[$inname]);
			$indate=$_POST['entrydate'.$index];
			$insuccess=$_POST['success'.$index];
			if($indate=='0000-00-00' or $indate==''){$entrydate=date('Y-m-d');}
			else{$entrydate=$indate;}
			if(clean_text($Target['Detail']['value_db'])!=$inval or ($insuccess!='' and $insuccess>0 and $inval!='')){
				$noteid=$Target['id_db'];
				if($noteid==''){
					mysql_query("INSERT INTO background (student_id,detail,type,entrydate,category) 
									VALUES ('$sid','$inval','$cattype','$entrydate','$insuccess');");
					}
				else{
					mysql_query("UPDATE background SET detail='$inval', entrydate='$entrydate', category='$insuccess'
									WHERE id='$noteid';");
					}



				}
			}
		}
	}

include('scripts/redirect.php');
?>
