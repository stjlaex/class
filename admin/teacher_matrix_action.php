<?php 
/**				   	   					teacher_matrix_action.php
 */

$action='teacher_matrix.php';

if($_POST{'subtid'}!='' and $_POST{'tid'}!=''){
	$result[]='Please choose only one teacher at a time!';
	$newtid='';
	}
elseif($_POST{'subtid'}!=''){$newtid=$_POST{'subtid'};}
elseif($_POST{'tid'}!=''){$newtid=$_POST{'tid'};}
if(isset($_POST{'newcid'})){$newcid=$_POST{'newcid'};} else{$newcid='';}

include('scripts/sub_action.php');

	if($newtid!='' AND $newcid!=''){
	    $c=0;
		while(isset($newcid[$c])){
			if(mysql_query("INSERT INTO tidcid (teacher_id, class_id) 
				VALUES ('$newtid', '$newcid[$c]')")){ 
				$result[]="Assigned classes";	
				}
			else{$error[]=mysql_error();}
			$c++;
			}
		}

	else if($newtid!='' AND $newcid!=''){
		$c=0;
		while(isset($newcid[$c])){
			if(mysql_query("INSERT INTO tidcid (teacher_id, class_id) 
				VALUES ('$newtid', '$newcid[$c]')")){ 
				$result[]="Assigned class".$newcid[$c];
				}
			else{$error[]="Failed, teacher already has class ".$newcid[$c];}
			$c++;
			}
		}
include('scripts/results.php');
include('scripts/redirect.php');
?>
