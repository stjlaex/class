<?php
/**										scripts/list_report.php
 *
 * generic script called from within a form, returns array rids[]
 */

 	if($rcrid!='' and $r>-1){
		$d_report=mysql_query("SELECT DISTINCT id, course_id, 
			title, date FROM report WHERE course_id
			LIKE '$rcrid' ORDER BY date DESC, title");
		}
	elseif(sizeof($ryids)==1){
		$selyid=$ryids[0];
		$d_report=mysql_query("SELECT DISTINCT report.id,
				report.course_id, report.title, report.date FROM
				report JOIN class ON class.course_id=report.course_id WHERE
				class.yeargroup_id='$selyid' ORDER BY report.date DESC, report.title");
		}
	elseif(sizeof($rfids)==1){
		$selfid=$rfids[0];
		$d_yeargroup=mysql_query("SELECT yeargroup_id FROM form WHERE id='$selfid'");
		$selyid=mysql_result($d_yeargroup,0);
		$d_report=mysql_query("SELECT DISTINCT report.id,
				report.course_id, report.title, report.date FROM
				report JOIN class ON class.course_id=report.course_id WHERE
				class.yeargroup_id='$selyid' ORDER BY report.date DESC, report.title");
		}
	else{
		$d_report=mysql_query("SELECT id, course_id, title, date FROM report
			    ORDER BY date DESC, title, course_id");
		}
	$todate=date("Y-m-d");
	$reports=array();
   	while($report=mysql_fetch_array($d_report,MYSQL_ASSOC)){
		$reports[$report['id']]=$report;
		}
?>
  <label for="Current Reports"><?php print_string('currentreports');?></label>
  <select style="width:25em;" id="Current Reports" type="text" name="rids[]"
			size="4" multiple="multiple" >
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

  <label for="Reports"><?php print_string('reports');?></label>
  <select style="width:25em;" id="Reports" type="text" name="rids[]"
			size="10" multiple="multiple" >
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
