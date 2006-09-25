<?php 
/**												infobook.php
 *	This is the hostpage for the infobook.
 *	The page to be included is set by $current
 *	student_search.php is displayed by default
 *	unless a single sid is set when student_view.php is displayed
 *	or student_list.php if more than one student is set in sids.
 *	Alternatively $current can be POSTed by another script.
 */
$host='infobook.php';
$book='infobook';
$current='';
$choice='';
$cancel='';

include('scripts/head_options.php');

if(!isset($_SESSION['infosid'])){$_SESSION['infosid']='';}
if(!isset($_SESSION['infosids'])){$_SESSION['infosids']=array();}
if(!isset($_SESSION['infoStudent'])){$_SESSION['infoStudent']='';}
if(isset($_SESSION['infocurrent'])){$current=$_SESSION['infocurrent'];}

if(isset($_GET['sids'])){
	/*If the students selection has changed then*/
	if($_SESSION['infosids']!=$_GET['sids']){
		$_SESSION['infosids']=$_GET['sids']; 
		$_SESSION['umnrank']='surname';
		}
	$current='student_list.php';
	}
	
if(isset($_POST['sids'])){
	/*If the students selection has changed then*/
	if($_SESSION['infosids']!=$_POST['sids']){
		$_SESSION['infosids']=$_POST['sids']; 
		$_SESSION['umnrank']='surname';
		}
	$current='student_list.php';
	}

if(isset($_GET['sid'])){
	/*If the students selection has changed then*/
	if($_SESSION['infosid']!=$_GET['sid']){
		$_SESSION['infosid']=$_GET['sid']; 
		$_SESSION['umnrank']='surname';
		}
	$current='student_view.php';
	}

if(isset($_POST['sid'])){
	/*If the students selection has changed then*/
	if($_SESSION['infosid']!=$_POST['sid']){
		$_SESSION['infosid']=$_POST['sid']; 
		$_SESSION['umnrank']='surname';
		}
	$current='student_view.php';
	}

if(isset($_POST['current'])){$current=$_POST['current'];}
if(isset($_POST['choice'])){$choice=$_POST['choice'];}
if(isset($_POST['cancel'])){$cancel=$_POST['cancel'];}
if(isset($_GET['choice'])){$choice=$_GET['choice'];}
if(isset($_GET['cancel'])){$cancel=$_GET['cancel'];}
if(isset($_GET['current'])){$current=$_GET['current'];}
$_SESSION['infocurrent']=$current;

$sids=$_SESSION['infosids'];
$sid=$_SESSION['infosid'];
$sidskey=array_search($sid,$sids);	
if($current!='student_list.php'){
	$Student=fetchStudent($sid);
	}
?>
  <div id="bookbox" class="infocolor">
<?php
	$tab=1;
	if($current!=''){
		$view = 'infobook/'.$current;
		include($view);
		}
?>
  </div>
<?php
unset($yid);
include('infobook/quick_search.php');/*to be in the sidebar at all times*/
include('scripts/end_options.php');
?>