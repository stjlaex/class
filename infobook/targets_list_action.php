<?php
/**									targets_list_action.php
 */

$action='student_view.php';

include('scripts/sub_action.php');

$todate=date('Y-m-d');

if($sub=='Submit'){
	$Targets=(array)fetchTargets($sid);
	foreach($Targets['Target'] as $index => $Target){
		if(is_array($Target)){
			$cattype=$Target['Category']['value_db'];
			$inname='detail'.$index;
			$inval=clean_text($_POST[$inname]);
			trigger_error($cattype.' '.$inname.' '.$inval,E_USER_WARNING);

			if($Target['Detail']['value']!=$inval){
				$noteid=$Target['id_db'];
				if($noteid==''){
					mysql_query("INSERT INTO background
						(student_id,detail,type,entrydate) 
						VALUES ('$sid','$inval','$cattype','$todate');");
					}
				else{
					mysql_query("UPDATE background SET detail='$inval', entrydate='$todate'
									WHERE id='$noteid';");
					}
				}
			}
		}
	}

include('scripts/redirect.php');
?>