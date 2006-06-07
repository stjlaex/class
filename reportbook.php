<?php
/**	   												reportbook.php
 *	This is the hostpage for the reportbook.
 */

$host='reportbook.php';
$book='reportbook';
$current='';
$choice='';
$cancel='';

include ('scripts/head_options.php');

if(isset($_SESSION{'reportcurrent'})){$current=$_SESSION{'reportcurrent'};}
if(isset($_SESSION{'reportchoice'})){$choice=$_SESSION{'reportchoice'};}
if(isset($_GET{'current'})){$current=$_GET{'current'};}
if(isset($_GET{'choice'})){$choice=$_GET{'choice'};}
if(isset($_GET{'cancel'})){$choice=$_GET{'cancel'};}
if(isset($_POST{'current'})){$current=$_POST{'current'};} 
if(isset($_POST{'choice'})){$choice=$_POST{'choice'};}
if(isset($_POST{'cancel'})){$cancel=$_POST{'cancel'};}
$_SESSION{'reportcurrent'}=$current;
$_SESSION{'reportchoice'}=$choice;
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
	if(sizeof($rfids)==0 and sizeof($ryids)==0 and $r=='-1'){
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
<fieldset class="reportbook"><legend><?php print_string('reporton');?></legend>
	<form id="reportchoice" name="reportchoice" method="post" 
		action="reportbook.php" target="viewreportbook">
	<select name="current" size="10" onChange="document.reportchoice.submit();">
		<option <?php if($choice=='report_comments.php'){ print
		'selected="selected" ';}?> value='report_comments.php'>
<?php print_string('comments');?>
		  </option>

		<option <?php if($choice=='report_incidents.php'){ print
			'selected="selected" ';}?> value='report_incidents.php'>
<?php print_string('incidents');?>
		  </option>

		<option <?php if($choice=='report_assessments.php'){ print
		'selected="selected" ';}?> value='report_assessments.php'>
<?php print_string('assessments');?>
		  </option>

		<option <?php if($choice=='report_reports.php'){ print
			'selected="selected" ';}?> value='report_reports.php'>
<?php print_string('subjectreports');?>
		  </option>

<?php if($tid=='administrator'){  
?>
		<option <?php if($choice=='new_stats.php'){ print
			'selected="selected" ';}?> value='new_stats.php'>
			<?php print_string('newstatistics');?>
		  </option>

		<option <?php if($choice=='new_estimate.php'){ print
			'selected="selected" ';}?> value='new_estimate.php'>
			<?php print_string('newestimates',$book);?>
		  </option>

		<option <?php if($choice=='new_assessment.php'){ print
			'selected="selected" ';}?> value='new_assessment.php'>
			<?php print_string('newassessments',$book);?>
		  </option>

		<option <?php if($choice=='new_report.php'){ print
			'selected="selected" ';}?> value='new_report.php'>
			<?php print_string('newsubjectreport',$book);?>
		  </option>

<?php	}
?>
		</select>
	  </form>
	</fieldset>
  </div>

<?php
include('scripts/end_options.php');
?>









