<?php 
/**											list_subjects.php
 *
 *	$multi=1 returns bid or $multi>1 returns bids[] (default=1)
 *	set $required=no to make not required (default=yes)
 */
 	if($r>-1){
		$rbid=$respons[$r]['subject_id'];
		$rcrid=$respons[$r]['course_id'];
		$ryid=$respons[$r]['yeargroup_id'];
		if($rbid=='%' AND $rcrid!=''){
			$d_subject=mysql_query("SELECT DISTINCT subject_id FROM cridbid
				WHERE course_id LIKE '$rcrid' ORDER BY subject_id");
			}
		elseif($rbid!='%' AND $rcrid=='%'){
			$d_subject=mysql_query("SELECT DISTINCT subject_id FROM cridbid
				WHERE subject_id LIKE '$rbid' ORDER BY subject_id");
			}
		else{
			$d_subject=mysql_query("SELECT DISTINCT subject_id FROM
				cridbid WHERE subject_id LIKE '$rbid' AND course_id
				LIKE '$rcrid' ORDER BY subject_id");
			}
		}
	else{
/*		otherwise choose subjects based on classes taught */
		$d_subject=mysql_query("SELECT DISTINCT subject_id FROM
				class JOIN tidcid ON class.id=tidcid.class_id WHERE
				tidcid.teacher_id='$tid'");
		}

	if(!isset($required)){$required='yes';}
	if(!isset($multi)){$multi='1';}
	if(!isset($bid)){$current_bid='nowt';}else{$current_bid=$bid;}
?>
<label for="Subject"><?php print_string('subject');?></label>
<select  style="width:14em;" tabindex="<?php print $tab++;?>"
	<?php if($multi>1){print 'name="bids[]" multiple="multiple"';}else{print 'name="bid"';}?>" 
	id="Subject" size="<?php print $multi;?>"
<?php if($required=='yes'){ print ' class="required" ';} ?>
	>
	<option value="" 
		<?php if($current_bid==''){print 'selected="selected"';}?> ></option>
  	<option value="%" 
		<?php if($current_bid=='%'){print 'selected="selected"';}?> ><?php print_string('all');?></option>
<?php
	while($subject=mysql_fetch_array($d_subject,MYSQL_ASSOC)){
		print '<option ';
		if($current_bid==$subject['subject_id']){print 'selected="selected"';}
		print ' value="'.$subject['subject_id'].'">'.$subject['subject_id'].'</option>';
		}
?>
  	<option value="G" 
		<?php if($current_bid==''){print 'selected="selected"';}?> >General</option>
</select>
<?php 
	unset($required);
	unset($multi);
?>
