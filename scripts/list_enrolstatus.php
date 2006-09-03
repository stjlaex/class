<?php 
/**					list_enrolstatus.php
 *   returns $newenrolstatus
 */
 
	if(!isset($selenrolstatus)){$selenrolstatus='';}
	if(isset($enrolstatus)){$selenrolstatus=$enrolstatus;}
	if(isset($newenrolstatus)){$selenrolstatus=$newenrolstatus;}
	if(!isset($required)){$required='no';}
	if(!isset($onsidechange)){$onsidechange='no';}
?>
	<label for="Enrolstatus"><?php print_string('enrolstatus','infobook');?></label>
	<select id="Enrolstatus" name="newenrolstatus"
			<?php if($onsidechange=='yes'){ print ' onChange="document.entrybookchoice.submit();" ';} ?>
			<?php if($required=='yes'){ print ' class="required" ';} ?> >
<?php
	$enrolstatus_array=getEnumArray('enrolstatus');
	while(list($val,$description)=each($enrolstatus_array)){
		print '<option ';
		if(($selenrolstatus==$val)){print 'selected="selected"';}
		print	' value="'.$val.'">'.get_string($description,'infobook').'</option>';
		}
?>
	</select>
<?php  unset($required); unset($selenrolstatus); unset($onsidechange);?>





















