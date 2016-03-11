<?php 
/**											list_subjects.php
 *
 *	$multi=1 returns bid or $multi>1 returns bids[] (default=1)
 *	set $required=no to make not required (default=yes)
 */
	if($r>-1){
		$rbid=$respons[$r]['subject_id'];
		$rcrid=$respons[$r]['course_id'];
		if($rbid!='%'){
			$subjects[]=array('id'=>$rbid,'name'=>get_subjectname($rbid));
			}
		else{
			$subjects=list_course_subjects($rcrid);
			}
		}
	else{
		/* otherwise choose subjects based on classes taught */
		$subjects=list_teacher_subjects($tid);
		}

	if(!isset($required)){$required='yes';}
	if(!isset($multi)){$multi='1';}
	if(!isset($bid)){$current_bid='nowt';}else{$current_bid=$bid;}
	if(isset($selbid)){$current_bid=$selbid;}
?>
<p>
<label for="Subject"><?php print_string('subject');?></label>
<select tabindex="<?php print $tab++;?>"
	<?php if($multi>1){print 'name="bids[]" multiple="multiple"';}else{print 'name="bid"';}?>" 
	id="Subject" size="<?php print $multi;?>"
<?php if($required=='yes'){ print ' class="required" ';} ?>
	>
	<option value="" 
		<?php if($current_bid==''){print 'selected="selected"';}?> ></option>
	<option value="%" 
		<?php if($current_bid=='%'){print 'selected="selected"';}?> ><?php print_string('all');?></option>
<?php
	foreach($subjects as $subject){
		print '<option ';
		if($current_bid==$subject['id']){print 'selected="selected"';}
		print ' value="'.$subject['id'].'">'.$subject['name'].'</option>';
		}
?>
	<option value="G" 
		<?php if($current_bid==''){print 'selected="selected"';}?> >General</option>
</select>
</p>
<?php 
	unset($required);
	unset($multi);
	unset($listlabel);
?>
