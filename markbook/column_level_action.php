<?php 
/**							column_level_action.php
 */

$action='class_view.php';
$action_post_vars=array('displaymid','checkmid');

$checkmid=clean_text($_POST['checkmid']);
$mid=$checkmid;
$markdefname=clean_text($_POST['markdefname']);

include('scripts/sub_action.php');

$lena=$_POST['lena'];
if($lena=='new'){
		$action='define_levels.php';
		}
elseif($lena!=''){
	/* Copy over some details form the original mark*/
	$d_mark=mysql_query("SELECT * FROM mark WHERE id='$mid'");
	$mark=mysql_fetch_array($d_mark,MYSQL_ASSOC);
	$topic=$mark{'topic'};
	$entrydate=$mark{'entrydate'};
	$total=$mark{'total'};

	/* Insert the mark row for the level. The mid to level goes in midlist.*/	
   	if(mysql_query("INSERT INTO mark (entrydate, marktype,
				levelling_name, def_name, topic, total, midlist,
				author, component_id) 
				VALUES ('$entrydate', 'level', '$lena', '$markdefname', '$topic',
				'$total', '$mid', '$tid', '$pid');")){
			$newmid=mysql_insert_id();
			$displaymid=$newmid;
			/*	Do the level for each class that is assigned that mark not just */
			/* those in in the view table.*/

			$d_midcid=mysql_query("SELECT class_id FROM midcid WHERE mark_id='$mid'");	
			while ($midcid=mysql_fetch_array($d_midcid,MYSQL_ASSOC)){
				$cid=$midcid{'class_id'};
				mysql_query("INSERT INTO midcid 
							(mark_id, class_id) VALUES ('$newmid', '$cid')");
				}
			$result[]=get_string('columnlevelled',$book);
			}
	}
include('scripts/redirect.php');
?>
