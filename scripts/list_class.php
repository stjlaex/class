<?php
/**												scripts/list_class.php
 *
 * Lists a teacher's classes
 * Only used by the side options in MarkBook
 */
	$multiple=true;
	if(!isset($r)){$r=-1;}
	if($r=='register'){
		$cids[]=$newcid;
		$multiple=false;
		}
	elseif($r=='%'){
		if(!isset($stage)){$stage='%';}
		$sel_classes=array();
		foreach($respons as $res){
			$rbid=$res['subject_id'];
			$rcrid=$res['course_id'];
			$sel_classes=array_merge($sel_classes,list_course_classes($rcrid,$rbid,$stage,$curryear,'taught'));
			}
		$cids[]=$newcid;
		$multiple=false;
		}
	elseif($r>-1){
		$rbid=$respons[$r]['subject_id'];
		$rcrid=$respons[$r]['course_id'];
		/* Limit to assigned classes only. */
		$sel_classes=list_course_classes($rcrid,$rbid,'%',$curryear,'taught');
		}
	else{
		$sel_classes=list_teacher_classes($tid,'%','%',$curryear);
		}

	/* It is multiple for Markbook*/
	if($multiple==true){
?>
	<input name="tid" type="hidden" value="<?php print $tid;?>">
	<input name="current" type="hidden" value="class_view.php">
	<select name="cids[]" multiple="multiple" onchange="document.classchoice.submit();" title="<?php print_string('choose');?>">
<?php
		}
	else{
		if(isset($onsidechange)){$onsidechange=' onchange="document.'.$onsidechange.'.submit();" ';}
		else{$onsidechange='';}
?>
	<select  name="newcid" <?php echo $onsidechange; ?> title="<?php print_string('choose');?>">
<?php
		print '<option value=""></option>';
		}

	foreach($sel_classes as $sel_class){
		print '<option ';
		if(in_array($sel_class['id'], $cids)){print 'selected="selected"';}
		print ' value="'.$sel_class['id'].'">'.$sel_class['name'].'</option>';
		}
?>
	</select>
