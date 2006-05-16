<?php 
/*											list_subjects.php

Called within a form to list teacher's subjects.

set $multi qual to size of select list (default=1 and not mutiple)
set $required to 'no' to make not required (default=yes)
*/
	if(!isset($required)){$required="yes";}
	if(!isset($multi)){$multi="1";}

   	$d_subject=mysql_query("SELECT DISTINCT subject_id FROM
				class JOIN cidsid ON class.id=cidsid.class_id WHERE
				cidsid.student_id='$sid'");
?>

<select style="width:14em;" size="1" 
	<?php if($multi>1){print 'name="bids[]" multiple="multiple" ';}else{print 'name="bid"';}?> 
	id="Subject" size="<?php print $multi;?>"
<?php if($required=='yes'){ print ' class="required" ';} ?>
	>
   	<option value="%" 
		<?php if(!isset($bid)){print "selected='selected'";}?> >General</option>
<?php
	while($subject=mysql_fetch_array($d_subject,MYSQL_ASSOC)){
		print '<option ';
		if(isset($bid)){if($bid==$subject{'subject_id'}){print 'selected="selected"';}}
		print ' value="'.$subject{'subject_id'}.'">'.$subject{'subject_id'}.'</option>';
		}
?>			
</select>

 





















