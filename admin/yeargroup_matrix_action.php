<?php 
/**				   	   				   yeargroup_matrix_action.php
 */

$action='yeargroup_matrix.php';

if($_POST{'tid'}!=''){$newtid=$_POST{'tid'};}
if(isset($_POST{'newfid'})){$newfid=$_POST{'newfid'};}else{$newfid='';}

include('scripts/sub_action.php');

if($newtid!='' AND $newfid!=''){
		}

include('scripts/results.php');
include('scripts/redirect.php');
?>
