<?php 
/**												scripts/list_class.php
 *	
 * Lists a teacher's classes
 * Only used by the side options in MarkBook
 */
	if(!isset($r)){$r=-1;}
	if($r>-1){
		$rbid=$respons[$r]['subject_id'];
		$rcrid=$respons[$r]['course_id'];
		/* Limit to assigned classes only. */
		$sel_classes=list_course_classes($rcrid,$rbid,'%',$curryear,'taught');
		}
	else{
		$sel_classes=list_teacher_classes($tid,'%','%',$curryear);
		}
?>

<input name="tid" type="hidden" value="<?php print $tid;?>">
<input name="current" type="hidden" value="class_view.php">		
<select name="cids[]" multiple="multiple" onchange="document.classchoice.submit();" title="<?php print_string('choose');?>">
<?php
	foreach($sel_classes as $sel_class){
		print '<option ';
		if(in_array($sel_class['id'], $cids)){print 'selected="selected"';}
		print ' value="'.$sel_class['id'].'">'.$sel_class['name'].'</option>';
		}
?>
</select>
