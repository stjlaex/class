<?php
	$courses=(array)list_courses();
	foreach($courses as $course){
		$crid=$course['id'];
		$stages=(array)list_course_stages($crid);
		$subjects=(array)list_course_subjects($crid);
?>

	  <table class="listmenu noborder">
		<tr>
		<th><?php print_string('stage',$book);?></th>
<?php
		foreach($stages as $stage){
			print '<th>'.$stage['name'].'</th>';
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
				$cids=array();
				while($class=mysql_fetch_array($d_class,MYSQL_ASSOC)){
					$cid=$class['id'];
					$d_cidsid=mysql_query("SELECT COUNT(student_id) AS no FROM cidsid WHERE class_id='$cid';");
					$no=mysql_result($d_cidsid,0);
					if($no>0){
						$cids[]=$no;
						$nosids+=$no;
						$nocids++;
						}
					}
?>
		  <td style="border:solid 3px #ddd;">
<?php
				if($nosids>0){
					foreach($cids as $no){
						print '<span style="margin-right:2em;width:10em;">'.$no.'</span>';	
						}
					print '<span style="float:right;margin-right:4em;">'.get_string('total',$book).': '.$nosids. ' in '.$generate.'</span>';
					}
?>
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
