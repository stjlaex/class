<?php
/**										scripts/list_student_report.php
 *
 * returns array rids[]
 * list reports for which a student has assessments entered
 */

	$reportwrappers=array();

	/* Reports with assessments 
	$d_rideid=mysql_query("SELECT DISTINCT report_id FROM rideid JOIN eidsid ON 
				rideid.assessment_id=eidsid.assessment_id WHERE eidsid.student_id='$sid';");
	while($rep=mysql_fetch_array($d_rideid,MYSQL_ASSOC)){
		$rid=$rep['report_id'];
		$d_report=mysql_query("SELECT id, title, date FROM report JOIN
				ridcatid ON ridcatid.report_id=report.id  
			    WHERE ridcatid.categorydef_id='$rid' 
				AND ridcatid.subject_id='wrapper';");
		while($report=mysql_fetch_array($d_report,MYSQL_ASSOC)){
			$reportwrappers[$report['id']]=$report;
			}
		}
	*/
	/* Reports with subject comments (with or without assessments) */
	$d_report=mysql_query("SELECT id, title, date, year FROM report JOIN ridcatid ON ridcatid.report_id=report.id  
			    WHERE ridcatid.categorydef_id=ANY(SELECT DISTINCT report_id FROM reportentry WHERE student_id='$sid') 
				AND ridcatid.subject_id='wrapper' ORDER BY year DESC, date DESC;");
	while($report=mysql_fetch_array($d_report,MYSQL_ASSOC)){
			$reportwrappers[$report['id']]=$report;
			}
	/* Reports with summary comments */
	$d_report=mysql_query("SELECT id, title, date, year FROM report   
			    WHERE id=ANY(SELECT DISTINCT report_id FROM reportentry WHERE student_id='$sid') 
				AND course_id='wrapper' ORDER BY year DESC, date DESC;");
	while($report=mysql_fetch_array($d_report,MYSQL_ASSOC)){
			$reportwrappers[$report['id']]=$report;
			}

krsort($reportwrappers);
?>

    <div class="center"> 
        <label for="Current Reports"><?php print_string('reports');?></label>
        <select id="Current Reports" name="wrapper_rid" tabindex="<?php print $tab++;?>" size="18">
            <?php
            	while(list($rid,$report)=each($reportwrappers)){
            ?>
    		<option value="<?php print $report['id'];?>">
    			<?php print $report['title'].' ('.$report['date'].')';?>
    		</option>
            <?php
            	}
            ?>
        </select>