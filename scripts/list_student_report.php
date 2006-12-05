<?php
/**										scripts/list_student_report.php
 *
 * returns array rids[]
 * list reports for which a student has assessments entered
 */

	$todate=date('Y-m-d');
	$d_rideid=mysql_query("SELECT DISTINCT report_id FROM rideid JOIN eidsid ON 
				rideid.assessment_id=eidsid.assessment_id WHERE eidsid.student_id='$sid'
			    ORDER BY report_id DESC");
	$reports=array();
	$reportwrappers=array();
	while($rideid=mysql_fetch_array($d_rideid,MYSQL_ASSOC)){
		$rid=$rideid['report_id'];
		$d_report=mysql_query("SELECT id, title, course_id, date FROM report 
			    WHERE id='$rid' ORDER BY date DESC, title");
		while($report=mysql_fetch_array($d_report,MYSQL_ASSOC)){
			$reports[$report['id']]=$report;
			}
		$d_report=mysql_query("SELECT id, title, date FROM report JOIN
				ridcatid ON ridcatid.report_id=report.id  
			    WHERE ridcatid.categorydef_id='$rid' 
				AND ridcatid.subject_id='wrapper' ORDER BY date DESC, title");
		while($report=mysql_fetch_array($d_report,MYSQL_ASSOC)){
			$reportwrappers[$report['id']]=$report;
			}
		}
?>

<div class="center"> 
  <label for="Current Reports"><?php print_string('current');?></label>
  <select style="width:60%;" id="Current Reports" type="text" name="wrapper_rid"
	  class="requiredor" eitheror="Previous Reports"
			tabindex="<?php print $tab++;?>" size="4">
<?php
	while(list($rid,$report)=each($reportwrappers)){
		if($report['date']>=$todate){
?>
		<option value="<?php print $report['id'];?>">
			<?php print $report['title'].' ('.$report['date'].')';?>
		</option>
<?php
			}
 		}
?>
		<option value="">--------------------</option>
<?php
   	while(list($rid,$report)=each($reports)){
		if($report['date']>=$todate){
?>
		<option value="<?php print $report['id'];?>">
			<?php print $report['course_id'].' '.$report['title'].' ('.$report['date'].')';?>
		</option>
<?php
			}
 		}
?>
  </select>
</div>

<div class="center">
  <label for="Previous Reports"><?php print_string('previous');?></label>
  <select style="width:60%;" id="Previous Reports" type="text" name="wrapper_rid"
	  class="requiredor" eitheror="Current Reports"
			size="18" multiple="multiple" tabindex="<?php print $tab++;?>">
<?php
	reset($reportwrappers);
	while(list($rid,$report)=each($reportwrappers)){
		if($report['date']<$todate){
?>
		<option value="<?php print $report['id'];?>">
			<?php print $report['title'].' ('.$report['date'].')';?>
		</option>
<?php
			}
 		}
?>
		<option value="">--------------------</option>
<?php
	reset($reports);
	while(list($rid,$report)=each($reports)){
		if($report['date']<$todate){
?>
		<option value="<?php print $report['id'];?>">
			<?php print $report['course_id'].' '.$report['title'].' ('.$report['date'].')';?>
		</option>
<?php
			}
 		}
?>
  </select>
</div>
