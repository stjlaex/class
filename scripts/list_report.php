<?php
/**										scripts/list_report.php
 *
 * generic script called from within a form, returns array rids[]
 * if rcrid is blank then actually lists report wrappers
 */

 	if($rcrid!='' and $r>-1){
		$d_report=mysql_query("SELECT DISTINCT id, course_id, 
			title, date FROM report WHERE course_id
			LIKE '$rcrid' ORDER BY date DESC, title");
		}
	else{
		$d_report=mysql_query("SELECT id, course_id, title, date FROM report
			    WHERE course_id!='wrapper' ORDER BY date DESC, title, course_id");
		}
	$todate=date('Y-m-d');
	$reports=array();
   	while($report=mysql_fetch_array($d_report,MYSQL_ASSOC)){
		$reports[$report['id']]=$report;
		}
?>

<div class="center"> 
  <label for="Current Reports"><?php print_string('current');?></label>
  <select style="width:60%;" id="Current Reports" type="text" name="rids[]"
	  class="requiredor" eitheror="Previous Reports"
			tabindex="<?php print $tab++;?>" size="6" multiple="multiple" >
		<option value=""></option>
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
  <select style="width:60%;" id="Previous Reports" type="text" name="rids[]"
	  class="requiredor" eitheror="Current Reports"
			size="8" multiple="multiple" tabindex="<?php print $tab++;?>">
		<option value=""></option>
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