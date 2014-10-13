<?php
/**									class_nos_section_yeargroup.php
 *
 */

$action='class_nos_section_yeargroup_action.php';
$choice='class_nos.php';

$curryear=get_curriculumyear();

if(isset($_GET['yid'])){$yid=$_GET['yid'];}else{$yid='';}

$com=array('id'=>'','type'=>'year', 'name'=>''.$yid);
$students=listin_community($com);

two_buttonmenu($extrabuttons);
?>
<div id="viewcontent" class="content">
  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>">

	  <table class="listmenu noborder">
		<tr>
			<th colspan="2"><?php print_string('student',$book);?></th>
			<th><?php print_string('classes',$book);?></th>
		</tr>
<?php
	foreach($students as $student){
		$sid=$student['id'];
		$Student=fetchStudent_short($sid);
		$crids=list_student_courses($sid);
		$classes_list='';
		foreach($crids as $crid){
			$classes=(array)list_student_course_classes($sid,$crid);
			foreach($classes as $c){
				$classes_list.="<a href='admin.php?current=class_edit.php&choice=teacher_matrix.php&cancel=teacher_matrix.php&newcid=".$c['id']."'>".$c['name']."</a> ";
				}
			}
?>
		<tr>
			<td style="width:20%;"><a onclick="parent.viewBook('infobook');" target="viewinfobook" href="infobook.php?current=student_view.php&sid=<?php print $sid; ?>"><?php print $Student['DisplayFullName']['value']; ?></a></td>
			<td style="width:10%"><?php print $Student['RegistrationGroup']['value']; ?></td>
			<td style="width:70%;"><?php print $classes_list;?></td>
		</tr>
<?php
		}
?>
	  </table>

	  <input type="hidden" name="current" value="<?php print $action; ?>" />
	  <input type="hidden" name="cancel" value="<?php print $choice; ?>" />
	  <input type="hidden" name="choice" value="<?php print $choice; ?>" />
	</form>
</div>
