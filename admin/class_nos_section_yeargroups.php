	<div class="table-scrollable">
<?php
	$curryear=get_curriculumyear();
	$sections=(array)list_sections(true);
	if(count($sections)==0){$sections=(array)list_sections();}
	foreach($sections as $section){
		$yeargroups=(array)list_yeargroups($section['id']);
		$crids=array();
		foreach($yeargroups as $yeargroup){
			$yid=$yeargroup['id'];
			$com=array('id'=>'','type'=>'year', 'name'=>''.$yid);
			$comid=update_community($com);
			$d_c=mysql_query("SELECT course_id FROM cohidcomid JOIN cohort ON cohort.id=cohidcomid.cohort_id WHERE community_id='$comid' AND cohort.year='$curryear' GROUP BY course_id;");
			while($courses=mysql_fetch_array($d_c,MYSQL_ASSOC)){
				$crids[$courses['course_id']]=$courses['course_id'];
				}
			}
		$subjects=array();
		foreach($crids as $crid){
			$subjects=array_merge($subjects,list_course_subjects($crid));
			}
?>
	  <table class="listmenu noborder">
		<tr>
		<th><?php echo $section['name'];?></th>
<?php
		foreach($subjects as $subno => $subject){
			$bid=$subject['id'];
			print '<th>'.$bid.'</th>';
			}
?>
		</tr>
<?php
		foreach($yeargroups as $yeargroup){
			$yid=$yeargroup['id'];
			$yname=get_yeargroupname($yid);
			$com=array('id'=>'','type'=>'year', 'name'=>''.$yid);
			$comid=update_community($com);
			$d_c=mysql_query("SELECT cohort_id FROM cohidcomid JOIN cohort ON cohort.id=cohidcomid.cohort_id WHERE community_id='$comid' AND cohort.year='$curryear' LIMIT 1;");
			$cohid=mysql_result($d_c,0);
?>
		<tr>
			<th>
				<a href="admin.php?current=class_nos_section_yeargroup.php&yid=<?php print $yid; ?>">
					<?php print $yname;?>
				</a>
			</th>
<?php
			foreach($subjects as $subno => $subject){
				$bid=$subject['id'];
				$nosids=0;
				$nocids=0;
				$d_class=mysql_query("SELECT id FROM class WHERE
							subject_id='$bid' AND cohort_id='$cohid'");
				while($class=mysql_fetch_array($d_class,MYSQL_ASSOC)){
					$cid=$class['id'];
					$d_cidsid=mysql_query("SELECT COUNT(student_id) AS no FROM cidsid WHERE class_id='$cid';");
					$no=mysql_result($d_cidsid,0);
					if($no>0){
						$nosids+=$no;
						$nocids++;
						}
					}
				print "<td>".$nosids."</td>";
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
	</div>
