<?php 
/**									 orders_limit_action.php
 */

$action='orders_limit.php';
$action_post_vars=array('budid');

if(isset($_POST['budid'])){$budid=$_POST['budid'];}
if(isset($_POST['costlimit'])){$costlimit=$_POST['costlimit'];}
if(isset($_POST['name'])){$name=clean_text($_POST['name']);}

include('scripts/sub_action.php');

if($sub=='Submit'){
	mysql_query("UPDATE orderbudget SET costlimit='$costlimit',
					name='$name' WHERE id='$budid';");
	}
elseif(isset($_POST['xuid']) and $_POST['xuid']!=''){
	$uid=$_POST['xuid'];
	$perms=array('r'=>1,'w'=>1,'x'=>1,'e'=>0);
	update_budget_perms($uid,$budid,$perms);
	}
elseif(isset($_POST['wuid']) and $_POST['wuid']!=''){
	$uid=$_POST['wuid'];
	$perms=array('r'=>1,'w'=>1,'x'=>0,'e'=>0);
	update_budget_perms($uid,$budid,$perms);
	}

include('scripts/redirect.php');
?>
