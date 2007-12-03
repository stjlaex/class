<?php 
/* 									column_average.php
*/

$action='class_view.php';
$action_post_vars=array('displaymid');

/* Make sure a column is checked*/
if(!isset($_POST['checkmid'])){
		$result[]='Choose more than one column to average.';
		include('scripts/results.php');
		include('scripts/redirect.php');
		exit;
		}

$checkmids=(array)$_POST['checkmid'];

/*	Make sure more than one column was checked*/	
	if(sizeof($checkmids)<2){
		$result[]='Choose more than one column to average!';
		include('scripts/results.php');
		include('scripts/redirect.php');
		exit;
		}

	$midlist='';
	for($c=0;$c<sizeof($checkmids);$c++){
		$mid=$checkmids[$c];
		$d_markdef=mysql_query("SELECT markdef.scoretype,
					markdef.grading_name, markdef.name 
				FROM markdef, mark WHERE mark.id='$mid' AND markdef.name=mark.def_name");
		$markdef=mysql_fetch_array($d_markdef, MYSQL_ASSOC);

		/* Will store grading_grades in mark.levelname, as an average has no markdef row*/		

		/* Check all columns are compatible*/
		if($markdef['scoretype']=='grade'){
			if($c==0){$grading_grades=$markdef['grading_name'];$joiner='';}
			if($grading_grades==$markdef['grading_name']){
				$midlist.=$joiner. $mid;
				$scoretype=$markdef['scoretype'];
				$def_name=$markdef['name'];
				}
				else{$result[]='Warning! Mark '.$mid.' must use the same grading scheme.';}
				}
		elseif($markdef['scoretype']=='value' or $markdef['scoretype']=='percentage'){
			if($c==0){$scoretype=$markdef['scoretype']; $grading_grades='';$joiner='';}
			if($markdef['scoretype']==$scoretype){
				$midlist.=$joiner. $mid;
				$def_name=$markdef['name'];
				}
			else{$result[]='Warning! Mark '.$mid.' must also be a '.$scoretype.'.';}	
			}
		else{$result[]='Warning! Mark '.$mid.' must be a grade to be averaged.';}
		$joiner=' ';
		}

	if($midlist!=''){
		$tomonth=date('n');
		$today=date('j');
		$toyear=date('Y');
		$entrydate=$toyear.'-'.$tomonth.'-'.$today;
		$topic='(average)';

		/*this will assign the attributes to the average of the last
		column in the list, which might be good or bad!*/		
		mysql_query("INSERT INTO mark (entrydate, marktype,
			midlist, author, levelling_name, def_name, topic) VALUES
			('$entrydate', 'average', '$midlist', '$tid',
			'$grading_grades', '$def_name', '$topic')");
		$mid=mysql_insert_id();
		for($c=0;$c<sizeof($cids);$c++){
			$cid=$cids[$c];
			mysql_query("INSERT INTO midcid 
			     (mark_id, class_id) VALUES ('$mid', '$cid')");
			}
		$displaymid=$mid;				
		}
	include('scripts/results.php');
	include('scripts/redirect.php');
?>
