<?php 
/**
 * 									column_average_action.php
 */

$action='class_view.php';
$action_post_vars=array('displaymid');

include('scripts/sub_action.php');

$midlist=$_POST['midlist'];
$grading_name=$_POST['grading_name'];
$def_name=$_POST['def_name'];
$comment=$_POST['comment'];
$topic=$_POST['topic'];
$entrydate=$_POST['date0'];


	$mids=explode(' ',$midlist);
	$midlist='';
	$joiner='';
	foreach($mids as $c => $mid){
		$weight=$_POST['weight'.$mid];
		if($weight==''){$weight=100;}
		$midlist.=$joiner. $mid.':::'.$weight;
		if($c==0){$joiner=' ';}
		}

	if($midlist!=''){

		/**
		 * This will assign the attributes to the average of the last
		 * column in the list, which might be good or bad!
		 *
		 * Will store grading_name in mark.levelname, as an average has no markdef row.
		 */		
		mysql_query("INSERT INTO mark (entrydate, marktype, midlist, author, levelling_name, 
					def_name, topic,component_id) VALUES ('$entrydate', 'average', '$midlist', '$tid',
					'$grading_name', '$def_name', '$topic','$pid')");
		$mid=mysql_insert_id();
		for($c=0;$c<sizeof($cids);$c++){
			$cid=$cids[$c];
			mysql_query("INSERT INTO midcid (mark_id, class_id) VALUES ('$mid', '$cid')");
			}

		$displaymid=$mid;				
		}

	include('scripts/results.php');
	include('scripts/redirect.php');
?>
