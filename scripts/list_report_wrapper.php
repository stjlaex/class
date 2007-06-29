<?php
/**										scripts/list_report_wrapper.php
 *
 */

	if(sizeof($ryids)==1){
		$selyid=$ryids[0];
		$d_report=mysql_query("SELECT id, title, date FROM report
			    WHERE course_id='wrapper' ORDER BY date DESC, title");
		/*This needs to moved to use cohorts!!!*/
		}
	elseif(sizeof($rfids)==1){
		$selfid=$rfids[0];
		$d_report=mysql_query("SELECT id, title, date FROM report
			    WHERE course_id='wrapper' ORDER BY date DESC, title");
		/*This needs to moved to use cohorts!!!*/
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
  <label for="Reports"><?php print_string('reports');?></label>
  <select style="width:70%;" id="Reports" name="wrapper_rid"
	  class="required"  tabindex="<?php print $tab++;?>" size="14" >
	  <option value="">----<?php print_string('current');?>----------------</option>
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
	  <option value="">----<?php print_string('previous');?>----------------</option>
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
