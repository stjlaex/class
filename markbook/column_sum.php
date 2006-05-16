<?php 
/** 									column_sum.php
 */

$action='class_view.php';

/* Make sure a column is checked*/
if(!isset($_POST{'checkmid'})){
	$action='class_view.php';
	$result[]='Choose more than one column to sum.';
	}
/*	Make sure more than one column was checked*/	
elseif(sizeof($_POST{'checkmid'})<2){
	$result[]='Choose more than one column to sum.';
	}
else{
	$checkmid=$_POST{'checkmid'};
	$midlist='';
	for ($c=0; $c < sizeof($checkmid); $c++){
		$mid=$checkmid[$c];	
		$d_markdef=mysql_query("SELECT markdef.scoretype, markdef.name 
				FROM markdef, mark WHERE mark.id='$mid' AND markdef.name=mark.def_name");
		$markdef=mysql_fetch_array($d_markdef, MYSQL_ASSOC);
		if($markdef{'scoretype'}=='value' or $markdef{'scoretype'}=='percentage'){
			if($c==0){$scoretype=$markdef{'scoretype'};}
			if($markdef{'scoretype'}==$scoretype){
				$midlist=$midlist.' '.$mid;
				$def_name=$markdef{'name'};
				}
			else{$result[]= 'Warning! All marks must be of the same type: '.$scoretype;}	
			}
		else{$result[]= 'Warning! All marks must be numerical values to be summed.';}	
		}
		
	if($midlist!=''){
		$tomonth = date('n');
		$today	= date('j');
		$toyear = date('Y');
		$entrydate = $toyear.'-'.$tomonth.'-'.$today;
		$topic='(sum)';		

		if(mysql_query("INSERT INTO mark (entrydate, marktype, def_name,
				midlist, author, topic) VALUES ('$entrydate', 'sum',
				'$def_name', '$midlist', '$tid', '$topic')")){
			$mid = mysql_insert_id();
			$displaymid = $mid;

/*	Do the sum for each class that is currently in the view table.*/
/*			- not for all the classes for which the original midcid exists*/	

			for ($i=0;$i<sizeof($cids);$i++){
				$cid=$cids[$i];
/*              create an entry in table:midcid*/	
				if(mysql_query("INSERT INTO midcid 
					(mark_id, class_id) VALUES ('$mid', '$cid')")){}
				else{$error[]=mysql_error();}
				}
			}
		$result[]='Sum created as a new mark column.';
		}
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
