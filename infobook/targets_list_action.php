<?php
/**									targets_list_action.php
 */

$action='student_view.php';

include('scripts/sub_action.php');


if($sub=='Submit'){
	$Targets=(array)fetchTargets($sid);
	foreach($Targets['Target'] as $index => $Target){
		if(is_array($Target)){
			$cattype=$Target['Category']['value_db'];
			$inname='detail'.$index;
			$inval=clean_text($_POST[$inname]);
			$indate=$_POST['entrydate'.$index];
			trigger_error($indate,E_USER_WARNING);
			if($indate=='0000-00-00' or $indate==''){$entrydate=date('Y-m-d');}
			else{$entrydate=$indate;}

			if(clean_text($Target['Detail']['value'])!=$inval){
				$noteid=$Target['id_db'];
				if($noteid==''){
					mysql_query("INSERT INTO background (student_id,detail,type,entrydate) 
									VALUES ('$sid','$inval','$cattype','$entrydate');");
					}
				else{
					mysql_query("UPDATE background SET detail='$inval', entrydate='$entrydate'
									WHERE id='$noteid';");
					}
				}
			}
		}
	}

include('scripts/redirect.php');
?>