<?php
/**										scripts/list_report_wrapper.php
 *
 */

	$todate=date('Y-m-d');
	/* Only include reports which are no more than 10 weeks ahead. */
	$startdate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')+70,date('Y')));

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
    <label for="Reports"><?php print_string('reports');?></label>
    <ul id="Reports" class="required">
        <li><?php print_string('current');?>
            <ul>
            <?php
                foreach($reports as $rid => $report){
                    if(strtotime($report['date'])>=strtotime($todate)){
            ?>
            <li>
                <input type="radio" value="<?php print $report['id'];?>" name="report">
                <?php print $report['title'].' ('.$report['date'].')';?>
            </li>
            <?php
                    }
                }
            ?>
            </ul>
        </li>
        <li><?php print_string('previous');?>
            <a href="#" class="button">click me</a>
            <ul class="listhide" style="display: none;">
                <?php
                    foreach($reports as $rid => $report){
                        if(strtotime($report['date']) < strtotime($todate)){
                ?>
                <li>
                    <input type="radio" value="<?php print $report['id'];?>"  name="report">
                    <?php print $report['title'].' ('.$report['date'].')';?>
                </li>
                <?php
                        }
                    }
                ?>                   
            </ul>
        </li>  
    </ul>          
    <script>
        $( ".button" ).click(function() {
            $( ".listhide" ).slideToggle( "slow" );
        });
    </script>
