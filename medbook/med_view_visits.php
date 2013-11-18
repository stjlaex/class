<?php
/**                                  med_view_visits.php
 */

$action='med_student_list.php';
$choice3='med_student_list.php';$choice='';

?>
  <div id="heading"><label><?php print_string('medicalrecord',$book);?></label>
	<a href="infobook.php?current=student_view.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>"
			  target="viewinfobook" onclick="parent.viewBook('infobook');">
  <?php print $Student['Forename']['value'].' '.$Student['Surname']['value'];?>
	</a>
  </div>
<?php
	two_buttonmenu(array('addvisit'=>array('name'=>'current','value'=>'med_add_visit.php')),$book);
?>
<div class="content">
<?php

	include("view_info_student.php");

	include("view_table_visits.php");

?>
</div>
