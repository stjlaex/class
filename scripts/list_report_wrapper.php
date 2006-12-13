<?php
/**										scripts/list_report_wrapper.php
 *
 * generic script called from within a form, returns array rids[]
 */

	if(sizeof($ryids)==1){
		$selyid=$ryids[0];
		$d_report=mysql_query("SELECT id, title, date FROM report
			    WHERE course_id='wrapper' ORDER BY date DESC, title");
		/*This needs to moved to use cohorts!!!
		$d_report=mysql_query("SELECT DISTINCT report.id,
				report.course_id, report.title, report.date FROM
				report JOIN class ON class.course_id=report.course_id WHERE
				class.yeargroup_id='$selyid' ORDER BY report.date DESC, report.title");
		*/
		}
	elseif(sizeof($rfids)==1){
		$selfid=$rfids[0];
		$d_report=mysql_query("SELECT id, title, date FROM report
			    WHERE course_id='wrapper' ORDER BY date DESC, title");
		/*This needs to moved to use cohorts!!!
		$d_report=mysql_query("SELECT DISTINCT report.id,
				report.course_id, report.title, report.date FROM
				report JOIN class ON class.course_id=report.course_id WHERE
				class.yeargroup_id='$selyid' ORDER BY report.date DESC, report.title");
		*/
		}
	else{
		$d_report=mysql_query("SELECT id, title, date FROM report
			    WHERE course_id='wrapper' ORDER BY date DESC, title");
		}
	$todate=date('Y-m-d');
	$reports=array();
   	while($report=mysql_fetch_array($d_report,MYSQL_ASSOC)){
		$reports[$report['id']]=$report;
		}
?>

<div class="center"> 
  <label for="Current Reports"><?php print_string('current');?></label>
  <select style="width:60%;" id="Current Reports" type="text" name="wrapper_rids[]"
	  class="requiredor" eitheror="Previous Reports"
	  tabindex="<?php print $tab++;?>" size="4" >
		<option value=""></option>
<?php
   	while(list($rid,$report)=each($reports)){
		if($report['date']>=$todate){
?>
		<option value="<?php print $report['id'];?>">
			<?php print $report['title'].' ('.$report['date'].')';?>
		</option>
<?php
			}
 		}
?>
  </select>
</div>

<div class="center"> 
  <label for="Previous Reports"><?php print_string('previous');?></label>
  <select style="width:60%;" id="Previous Reports" type="text" name="wrapper_rids[]"
	  class="requiredor"
		 eitheror="Current Reports" tabindex="<?php print $tab++;?>"  size="12" >
		<option value=""></option>
<?php
	reset($reports);
	while(list($rid,$report)=each($reports)){
		if($report['date']<$todate){
?>
		<option value="<?php print $report['id'];?>">
			<?php print $report['title'].' ('.$report['date'].')';?>
		</option>
<?php
			}
 		}
?>
  </select>
</div>
