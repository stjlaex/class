<?php 
/**												seneeds.php
 *	This is the hostpage for the seneeds.
 */

$host='seneeds.php';
$book='seneeds';

include('scripts/head_options.php');

include('scripts/book_variables.php');

if(!isset($_SESSION['sensid'])){$_SESSION['sensid']='';}

if(isset($_POST['sensid'])){
	if($_SESSION['sensid']!=$_POST['sensid']){
		$_SESSION['sensid']=$_POST['sensid'];
		}
	}
if(isset($_GET['sensid'])){
	if($_SESSION['sensid']!=$_GET['sensid']){
		$_SESSION['sensid']=$_GET['sensid'];
		}
	}
$sid=$_SESSION['sensid'];

if($sid=='' or $current==''){
	$d_info=mysql_query("SELECT student_id FROM info WHERE sen='Y' AND enrolstatus='C';");
	$sids=array();
	while($info=mysql_fetch_array($d_info,MYSQL_ASSOC)){
		$sids[]=$info['student_id'];
		}
	$current='sen_student_list.php';
	}
elseif($sid!=''){
	$Student=fetchStudent($sid);
	$SEN=$Student['SEN'];
	}
?>
  <div id="bookbox" class="seneedscolor">
<?php
	if($current!=''){
		include($book.'/'.$current);
		}
?>
  </div>

  <div style="visibility:hidden;" id="hiddenbookoptions" class="bookoptions">
	<form id="seneedschoice" name="seneedschoice" method="post" 
		action="seneeds.php" target="viewseneeds">
	  <fieldset class="seneeds">
		<legend><?php print_string('filterlistbyneeds',$book);?></legend>
<?php
	$enum=getEnumArray('sentype');
	print '<select id="Type"
			name="sentype">';
	print '<option value="">'.get_string('all').'</option>';
	while(list($inval,$description)=each($enum)){	
		print '<option ';
		if($selsentype==$inval){print 'selected="selected" ';}
		print ' value="'.$inval.'">'.get_string($description,'infobook').'</option>';
		}
    print '</select>';	
?>
	  </fieldset>
	</form>
  </div>

<?php
include('scripts/end_options.php');
?>