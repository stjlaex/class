<?php
/**										scripts/list_report_wrapper.php
 *
 */

	$todate=date('Y-m-d');
	/* Only include reports which are no more than 7 weeks ahead. */
	$startdate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')+49,date('Y')));

	$reports=array();

	foreach($cohorts as $cohort){
		$crid=$cohort['course_id'];
		$year=$cohort['year'];
		$d_r=mysql_query("SELECT report_id FROM ridcatid JOIN report ON ridcatid.categorydef_id=report.id
								WHERE ridcatid.subject_id='wrapper' AND report.year='$year' AND report.course_id='$crid' 
								AND report.date<'$startdate' ORDER BY report.date DESC, report.title;");
		while($r=mysql_fetch_array($d_r,MYSQL_ASSOC)){
			$rid=$r['report_id'];
			$d_report=mysql_query("SELECT id, title, date FROM report WHERE id='$rid';");
			$reports[$rid]=mysql_fetch_array($d_report,MYSQL_ASSOC);
			}
		}

?>

<div class="center"> 
  <label for="Reports"><?php print_string('reports');?></label>
  <select style="width:70%;" id="Reports" name="wrapper_rid"
	  class="required"  tabindex="<?php print $tab++;?>" size="14" >
	  <option value="">----<?php print_string('current');?>----------------</option>
<?php
   	foreach($reports as $rid => $report){
		if(strtotime($report['date'])>=strtotime($todate)){
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

   	foreach($reports as $rid => $report){
		if(strtotime($report['date']) < strtotime($todate)){
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
