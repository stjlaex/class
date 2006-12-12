<?php
/**												infobook.php
 *	This is the hostpage for the infobook.
 *	The page to be included is set by $current
 *	If a single sid is set then student_view.php is displayed
 *	or student_list.php if more than one student is set in sids.
 *	Alternatively $current can be POSTed by another script.
 */

$host='infobook.php';
$book='infobook';

include('scripts/head_options.php');

include('scripts/book_variables.php');

if(!isset($_SESSION['infosid'])){$_SESSION['infosid']='';}
if(!isset($_SESSION['infosids'])){$_SESSION['infosids']=array();}

if(isset($_GET['sids'])){
	if($_SESSION['infosids']!=$_GET['sids']){
		$_SESSION['infosids']=$_GET['sids']; 
		$_SESSION['umnrank']='surname';
		}
	}
if(isset($_POST['sids'])){
	if($_SESSION['infosids']!=$_POST['sids']){
		$_SESSION['infosids']=$_POST['sids']; 
		$_SESSION['umnrank']='surname';
		}
	}
if(isset($_GET['sid'])){
	if($_SESSION['infosid']!=$_GET['sid']){
		$_SESSION['infosid']=$_GET['sid']; 
		$_SESSION['umnrank']='surname';
		}
	}
if(isset($_POST['sid'])){
	if($_SESSION['infosid']!=$_POST['sid']){
		$_SESSION['infosid']=$_POST['sid']; 
		$_SESSION['umnrank']='surname';
		}
	}

$sids=$_SESSION['infosids'];
$sid=$_SESSION['infosid'];
$sidskey=array_search($sid,$sids);
if($current!='student_list.php'){
	$Student=fetchStudent($sid);
	}
?>
  <div id="bookbox" class="infocolor">
<?php
	if($current!=''){
		include($book.'/'.$current);
		}
?>
  </div>
<?php
unset($yid);
include('infobook/quick_search.php');
include('scripts/end_options.php');
?>