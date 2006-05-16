<?php 
/**								new_mark_action.php
 */

$action='class_view.php';

$comment=$_POST['comment'];
$def_name=$_POST['def_name'];
$topic=$_POST['topic'];
$newcid=$_POST['newcid'];
$entrydate=$_POST['date0'];
if(!isset($_POST['total'])){$total=0;} else {$total=$_POST['total'];}
if(!isset($_POST{'newpid'})){$newpid='';}else{$newpid=$_POST{'newpid'};}

include('scripts/sub_action.php');

/*	Create the new entry in table:mark*/
	if(mysql_query("INSERT INTO mark 
	     (entrydate, marktype, topic, total, comment, author,
	     def_name, component_id) 
	     VALUES ('$entrydate', 'score', '$topic', '$total', 
	     '$comment', '$tid', '$def_name', '$newpid')"))
	     {$result[]=get_string('createdmark',$book).$def_name;}
	     else{$result[]='Failed to create mark: '.$def_name;
					$error[]=mysql_error();}

	/*Get the new marks $mid*/	
	$mid=mysql_insert_id();
	$displaymid=$mid;
	/*Create an entry in table:midcid for each class*/	
	for($c=0;$c<sizeof($newcid);$c++){
			$othercid = $newcid[$c];
			if(mysql_query("INSERT INTO midcid 
			     (mark_id, class_id) VALUES ('$mid', '$othercid')")){}
			else{$result[]="Failed mark already exist for this class!";	
					$error[]=mysql_error();}
			}

	include('scripts/results.php');
	include('scripts/redirect.php');
?>
