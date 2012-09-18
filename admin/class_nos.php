<?php
/**											class_nos.php
 *
 *	Select which subjects are taught for which courses
 */

$action='class_nos.php';
$choice='class_nos.php';

two_buttonmenu($extrabuttons);
$curryear=get_curriculumyear();
?>
  <div class="content" id="viewcontent">
	<form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host; ?>">
<?php
	$courses=(array)list_courses();
	foreach($courses as $course){
		$crid=$course['id'];
		$stages=(array)list_course_stages($crid);
		$subjects=(array)list_course_subjects($crid);
?>

	  <table class="listmenu">
		<tr>
		<th><?php print_string('stage',$book);?></th>
<?php
		foreach($stages as $stage){
			print '<th>'.$stage['name'].'</th>';
			}
?>
		</tr>
		<tr>
		<th>&nbsp;</th>
<?php
		foreach($stages as $stage){
?>
			<th>
			<table>
			  <tr>
				<th style="width:10em;">&nbsp</th>
			<th style="width:10em;">&nbsp<?php print_string('average',$book);?></th>	
			<th style="width:10em;text-align:left;">&nbsp<?php print_string('total',$book);?></th>
			  </tr>
			</table>
			</th>
<?php
			}
?>
		</tr>
<?php
		foreach($subjects as $subno => $subject){
			$bid=$subject['id'];
			print '<tr id="'.$subno.'" >';
			print '<th>'.$crid.': '.$subject['name'].'</th>';
			
			foreach($stages as $stage){
				$stageid=$stage['id'];
				$cohid=update_cohort(array('year'=>$curryear,'course_id'=>$crid,'stage'=>$stageid));
				$d_classes=mysql_query("SELECT many,generate FROM classes WHERE
							subject_id='$bid' AND stage='$stageid' AND course_id='$crid'");
				$d_class=mysql_query("SELECT id FROM class WHERE
							subject_id='$bid' AND cohort_id='$cohid'");
				$classes=mysql_fetch_array($d_classes,MYSQL_ASSOC);
				$many=$classes['many'];
				$generate=$classes['generate'];
				$nosids=0;
				$nocids=0;
				while($class=mysql_fetch_array($d_class,MYSQL_ASSOC)){
					$cid=$class['id'];
					$d_cidsid=mysql_query("SELECT COUNT(student_id) AS no FROM cidsid WHERE class_id='$cid';");
					$no=mysql_result($d_cidsid,0);
					if($no>0){
						$nosids+=$no;
						$nocids++;
						}
					}
?>
		  <td>
			<table>
			  <tr>
				<td style="width:10em;">&nbsp <?php print $nocids.' '.$generate;?></td>
				<td style="width:10em;">&nbsp <?php print round($nosids/$nocids);?></td>	
				<td style="width:10em;text-align:left;">&nbsp <?php print $nosids;?></td>
			  </tr>
			</table>
		  </td>
<?php
				}
?>
		</tr>
<?php
			}
?>
	  </table>
	  <br />
<?php
		}
?>

	    <input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>
  </div>
