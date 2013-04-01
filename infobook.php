<?php
/**												infobook.php
 *	This is the hostpage for the infobook.
 *	The page to be included is set by $current
 *	If a single sid is set then student_view.php is displayed
 *	or student_list.php if more than one student is set in sids.
 *	Alternatively, a limited set of pages are based around
 *	contact_list and gid (independent of any sid by setting sid='').
 */

$host='infobook.php';
$book='infobook';

include('scripts/head_options.php');
include('scripts/set_book_vars.php');

if(!isset($_SESSION['infosid']) or $current=='contact_list.php'){$_SESSION['infosid']='';}
if(!isset($_SESSION['infosids']) or $current=='contact_list.php'){$_SESSION['infosids']=array();}

if(isset($_GET['sids'])){$_SESSION['infosids']=$_GET['sids'];}
if(isset($_POST['sids'])){$_SESSION['infosids']=$_POST['sids'];}
if(isset($_GET['sid'])){$_SESSION['infosid']=$_GET['sid'];}
if(isset($_POST['sid'])){$_SESSION['infosid']=$_POST['sid'];}

$sids=(array)$_SESSION['infosids'];
$sid=$_SESSION['infosid'];
$sidskey=array_search($sid,$sids);

if($current!='student_list.php' and $sid!=''){
	$Student=fetchStudent($sid);
	$student_secid=get_student_section($sid);
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
include('infobook/search.php');

include('scripts/end_options.php');
?>
