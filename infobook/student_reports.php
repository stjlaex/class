<?php
/**                                  student_reports.php    
 */

$action="student_reports_print.php";

	$Assessments=fetchAssessments($sid);
	/*generate an index to lookup values from the assessments array*/
	$eids=array();
	while(list($assno,$Assessment)=each($Assessments)){
		$eid=$Assessment['id_db'];
		$eids[$eid][]=$assno;
		}

	$reports=array();
	while(list($eid,$assnos)=each($eids)){
	   	$d_report=mysql_query("SELECT * FROM report JOIN rideid ON 
				rideid.report_id=report.id WHERE rideid.assessment_id='$eid'
			    ORDER BY  date, title, course_id");
		while($report=mysql_fetch_array($d_report,MYSQL_ASSOC)){
		    $rid=$report['id'];
    		$reports[$rid]=$report;
			}
		}

three_buttonmenu();
?>
  <div id="heading">
	<label><?php print_string('subjectreports'); ?></label>
	<?php print $Student['DisplayFullName']['value'];?>
  </div>
  <div class="content">
	<fieldset class="center">
	  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
<?php	  include('scripts/list_report_wrapper.php');?>
		<input type="hidden" name="current" value="<?php print $action;?>"/>
		<input type="hidden" name="cancel" value="<?php print $cancel;?>"/>
		<input type="hidden" name="choice" value="<?php print $choice;?>"/>
	  </form>
	</fieldset>
  </div>