<?php 
/* 									column_average.php
*/

$action='class_view.php';

/* Make sure a column is checked*/
if(!isset($_POST{'checkmid'})){
		$result[]='Choose more than one column to average.';
		include('scripts/results.php');
		include('scripts/redirect.php');
		exit;
		}

$checkmid=$_POST{'checkmid'};

/*	Make sure more than one column was checked*/	
	if(sizeof($checkmid)<2){
		$result[]='Choose more than one column to average!';
		include('scripts/results.php');
		include('scripts/redirect.php');
		exit;
		}

	$midlist='';
	for($c=0; $c<sizeof($checkmid); $c++){
		$mid=$checkmid[$c];
		$d_markdef=mysql_query("SELECT markdef.scoretype,
					markdef.grading_name, markdef.name 
				FROM markdef, mark WHERE mark.id='$mid' AND markdef.name=mark.def_name");
		$markdef=mysql_fetch_array($d_markdef, MYSQL_ASSOC);

/*		will store grading_grades in mark.levelname, as an average has no markdef row*/		

/*		check all columns are compatible*/
		if($markdef['scoretype']=='grade'){
			if($c==0){$grading_grades=$markdef['grading_name'];}
			if($grading_grades==$markdef['grading_name']){
				$midlist=$midlist.' '.$mid;
				$scoretype=$markdef['scoretype'];
				$def_name=$markdef['name'];
				}
				else{$result[]='Warning! Mark '.$mid.' must use the same grading scheme.';}
				}
		elseif($markdef{'scoretype'}=='value' or $markdef{'scoretype'}=='percentage'){
			if($c==0){$scoretype=$markdef{'scoretype'}; $grading_grades='';}
			if($markdef{'scoretype'}==$scoretype){
				$midlist=$midlist.' '.$mid;
				$def_name=$markdef{'name'};
				}
			else{$result[]='Warning! Mark '.$mid.' must also be a '.$scoretype.'.';}	
			}
		else{$result[]='Warning! Mark '.$mid.' must be a grade to be averaged.';}
		}
		
	if($midlist!=''){
		$tomonth = date('n');
		$today	= date('j');
		$toyear = date('Y');
		$entrydate = $toyear.'-'.$tomonth.'-'.$today;
		$topic='(average)';

		/*this will assign the attributes to the average of the last
		column in the list, which might be good or bad!*/		
		if(mysql_query("INSERT INTO mark (entrydate, marktype,
			midlist, author, levelling_name, def_name, topic) VALUES
			('$entrydate', 'average', '$midlist', '$tid',
			'$grading_grades', '$def_name', '$topic')")){
			$mid = mysql_insert_id();
			for($c=0;$c<sizeof($cids);$c++){
			  $cid=$cids[$c];
				if(mysql_query("INSERT INTO midcid 
			     (mark_id, class_id) VALUES ('$mid', '$cid')")){}
				else{$result[]='Failed average already exists for class!';	
					$error[]=mysql_error();}
				}
			}
		else{$result[]='Failed! '; $error[]=mysql_error();}
		$displaymid=$mid;				
		}
	include('scripts/results.php');
	include('scripts/redirect.php');
?>
