<?php
/**	   												reportbook.php
 *	This is the hostpage for the reportbook.
 */

$host='reportbook.php';
$book='reportbook';

include('scripts/head_options.php');
include('scripts/set_book_vars.php');
?>
<div id="bookbox" class="reportcolor">
<?php
	/**
	 * All scripts except report_reports is sensitive to whether an
	 * academic responsibility is selected, if it is students can be
	 * listed by cohort, otherwise by pastoral groups.
	 */
	$rforms=array();
	$rhouses=array();
	$ryids=array();
	if($r>-1 and $current!='report_reports.php' and $current!='report_merits.php'){
		$rcrid=$respons[$r]['course_id'];
	 	$rbid=$respons[$r]['subject_id'];
		$ryid=$respons[$r]['yeargroup_id'];
		if($ryid==''){$ryid='%';}
		$listgroup='list_cohort.php';
		$reportpubs='no';
		}
	else{
		$pastorals=(array)list_pastoral_respon();
		$rforms=$pastorals['forms'];
		$rhouses=$pastorals['houses'];
		$ryids=$pastorals['years'];
		$listgroup='list_pastoralgroup.php';
		$reportpubs='yes';
		}

	if(sizeof($rforms)==0 and sizeof($ryids)==0 and sizeof($rhouses)==0 and $r=='-1' and $_SESSION['role']!='admin'){
		/* Could not find any responsibility for this user which gives them access to a group. */
		$error[]=get_string('selectresponsibility');
		include('scripts/results.php');
		$current='';
		$choice='';
		}

	if($current!=''){
		include($book.'/'.$current);
		}
?>
</div>

<div style="visibility:hidden;" id="hiddenbookoptions">	
	<form id="reportchoice" name="reportchoice" method="post" 
		action="reportbook.php" target="viewreportbook">
	  <fieldset class="reportbook selery">
		<legend><?php print_string('reporton');?></legend>
<?php
	$choices=array(
				   'report_comments.php' => 'comments'
				   ,'report_incidents.php' => 'incidents'
				   ,'report_assessments.php' => 'assessments'
				   ,'report_merits.php' => 'merits'
				   ,'report_attendance.php' => 'attendance'
				   ,'report_reports.php' => 'subjectreports'
				   //,'report_results.php' => 'finalresults'
				   );
	selery_stick($choices,$choice,$book);
?>
	  </fieldset>
	</form>

<?php

	if($_SESSION['role']=='admin'){
		$choices=array(
					   'manage_homework.php' => 'homework'
					   ,'new_stats.php' => 'newstatistics'
					   ,'new_estimate.php' => 'newestimates'
					   ,'new_assessment.php' => 'newassessments'
					   ,'new_report.php' => 'subjectreports'
					   );
		}
	else{
		$choices=array(
					   'manage_homework.php' => 'homework'
					   ,'new_assessment.php' => 'newassessments'
					   );
		}
?>
	<form id="reportadminchoice" name="reportadminchoice" method="post" 
	  action="reportbook.php" target="viewreportbook">
	  <fieldset class="reportbook selery">
		<legend><?php print_string('manage');?></legend>
<?php
		selery_stick($choices,$choice,$book);
?>
	  </fieldset>
	</form>
  </div>

<?php
include('scripts/end_options.php');
?>
