<?php 
/**					   				list_studentsubjects.php
 *
 *set $multi qual to size of select list (default=1 and not mutiple)
 *set $required to 'no' to make not required (default=yes)
 */
	if(!isset($required)){$required='yes';}
	if(!isset($multi)){$multi='1';}

   	$d_class=mysql_query("SELECT DISTINCT subject_id FROM
				class JOIN cidsid ON class.id=cidsid.class_id WHERE
				cidsid.student_id='$sid'");
?>

<select style="width:14em;" size="1"   tabindex="<?php print $tab++;?>"
	<?php if($multi>1){print 'name="bids[]" multiple="multiple" ';}else{print 'name="bid"';}?> 
	id="Subject" size="<?php print $multi;?>"
<?php if($required=='yes'){ print ' class="required" ';} ?>
	>
   	<option value="%" 
		<?php if(!isset($bid)){print "selected='selected'";}?> >General</option>
<?php
	while($subject=mysql_fetch_array($d_class,MYSQL_ASSOC)){
		$newbid=$subject['subject_id'];
		$d_subject=mysql_query("SELECT name FROM subject WHERE id='$newbid'");
		$subjectname=mysql_result($d_subject,0);
		print '<option ';
		if(isset($bid)){if($bid==$newbid){print 'selected="selected"';}}
		print ' value="'.$newbid.'">'.$subjectname.'</option>';
		}
?>
</select>
<?php
unset($required);
unset($multi);
?>