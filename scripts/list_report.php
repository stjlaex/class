<?php
/**										scripts/list_report.php
 *
 * generic script called from within a form, returns array rids[]
 */

 	if($rcrid!='' and $r>-1){
		$d_report=mysql_query("SELECT * FROM report WHERE course_id
		LIKE '$rcrid' ORDER BY date DESC, title");
		}
 	elseif($rcrid!='' and $r>-1){
		$d_report=mysql_query("SELECT * FROM report WHERE course_id
		LIKE '$rcrid' ORDER BY date DESC, title");
		}
	else{
		$d_report=mysql_query("SELECT * FROM report
			    ORDER BY date DESC, title, course_id");
		}
?>
  <label for="Reports"><?php print_string('reports');?></label>
	<select style="width:25em;" id="Reports" type="text" name="rids[]"
			class="required" size="10" multiple="multiple" >
<?php
   	while ($report=mysql_fetch_array($d_report,MYSQL_ASSOC)){
?>   				
		<option value="<?php print $report['id'];?>">
			<?php print $report['course_id'].' '.$report['title'].' ('.$report['date'].')';?>
		</option>
<?php
   		}
?>
	</select>
