<?php
/**										scripts/list_report.php
 *
 * if rcrid is blank then actually lists report wrappers
 */

if(!isset($selrids)){$selrids=array();}

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
  <label for="Reports"><?php print_string('reports');?></label>
  <select style="width:70%;" id="Reports" type="text" name="rids[]"
	  class="required" tabindex="<?php print $tab++;?>" size="10" multiple="multiple" >
	  <option value="">----<?php print_string('current');?>----------------</option>
<?php
while(list($rid,$report)=each($reports)){
	if($report['date']>=$todate){
?>
		<option 
<?php
		if(in_array($report['id'], $selrids)){print ' selected="selected" ';}
?>
		value="<?php print $report['id'];?>">
		<?php print $report['course_id'].' '.$report['title'].' ('.$report['date'].')';?>
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
		<option 
<?php
		if(in_array($report['id'], $selrids)){print ' selected="selected" ';}
?>
		value="<?php print $report['id'];?>">
		<?php print $report['course_id'].' '.$report['title'].' ('.$report['date'].')';?>
		</option>
<?php
			}
 		}
?>
  </select>
</div>