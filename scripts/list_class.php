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
	$nocids=sizeof($sel_classes);
	if($nocids>6){$nocids=6;}
?>

<input name="tid" type="hidden" value="<?php print $tid;?>">
<input name="current" type="hidden" value="class_view.php">		
<select name="cids[]" size="<?php print $nocids; ?>"
		  multiple="multiple" onchange="document.classchoice.submit();">
<?php
	foreach($sel_classes as $sel_class){
		print '<option ';
		if(in_array($sel_class['id'], $cids)){print 'selected="selected"';}
		print ' value="'.$sel_class['id'].'">'.$sel_class['name'].'</option>';
		}
?>
</select>
