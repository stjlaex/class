<?php
/**	   												reportbook.php
 *	This is the hostpage for the reportbook.
 */

$host='reportbook.php';
$book='reportbook';
$current='';
$choice='';
$action='';
$cancel='';

include ('scripts/head_options.php');

if(isset($_SESSION['reportcurrent'])){$current=$_SESSION['reportcurrent'];}
if(isset($_SESSION['reportchoice'])){$choice=$_SESSION['reportchoice'];}
if(isset($_GET['current'])){$current=$_GET['current'];}
if(isset($_GET['choice'])){$choice=$_GET['choice'];}
if(isset($_GET['cancel'])){$choice=$_GET['cancel'];}
if(isset($_POST['current'])){$current=$_POST['current'];} 
if(isset($_POST['choice'])){$choice=$_POST['choice'];}
if(isset($_POST['cancel'])){$cancel=$_POST['cancel'];}
$_SESSION['reportcurrent']=$current;
$_SESSION['reportchoice']=$choice;
?>
<div id="bookbox" class="reportcolor">
<?php
	$rfids=array();
	$ryids=array();
	if($r>-1){
	 	$rbid=$respons[$r]['subject_id'];
		$rcrid=$respons[$r]['course_id'];
		$ryid=$respons[$r]['yeargroup_id'];
		if($ryid==''){$ryid='%';}
		}
	$pastorals=listPastoralRespon($respons);
	$rfids=$pastorals['forms'];
	$ryids=$pastorals['years'];
	if(sizeof($rfids)==0 and sizeof($ryids)==0 and $r=='-1' and $_SESSION['role']!='admin'){
			$error[]=get_string('selectresponsibility');
			include('scripts/results.php');
			$current='';
			$choice='';
			}

	if($current!=''){
		$view='reportbook/'.$current;
		include($view);
		}
?>
</div>

<div style="visibility:hidden;" id="hiddenbookoptions">	
	<form id="reportchoice" name="reportchoice" method="post" 
		action="reportbook.php" target="viewreportbook">
	  <fieldset class="reportbook selery">
		<legend><?php print_string('reporton');?></legend>
<?php
	$choices=array('report_comments.php' => 'comments'
			   ,'report_incidents.php' => 'incidents'
			   ,'report_assessments.php' => 'assessments'
			   ,'report_reports.php' => 'subjectreports'
			   );
	selery_stick($choices,$choice,$book);
?>
	  </fieldset>
	</form>

<?php
 if($tid=='administrator' or $_SESSION['role']=='admin'){
?>

	<form id="reportadminchoice" name="reportadminchoice" method="post" 
	  action="reportbook.php" target="viewreportbook">
	  <fieldset class="reportbook selery"><legend><?php print_string('manage');?></legend>
<?php
	$choices=array('new_stats.php' => 'newstatistics'
			   ,'new_estimate.php' => 'newestimates'
			   ,'new_assessment.php' => 'newassessments'
			   ,'new_report.php' => 'subjectreports'
			   );
	selery_stick($choices,$choice,$book);
?>
	  </fieldset>
	</form>
<?php
	}
?>
  </div>

<?php
include('scripts/end_options.php');
?>









