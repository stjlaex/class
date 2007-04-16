<?php 
/**					list_studentfield.php
 *   returns $displayfields as an array
 */
 
	if(!isset($seldisplayfield)){$seldisplayfield='dob';}
	if(isset($displayfield)){$seldisplayfield=$displayfield;}
	if(!isset($required)){$required='no';}
	if(!isset($onchange)){$onchange='yes';}
	if(!isset($istudentfield)){$istudentfield='';}
?>
	<select id="Displayfield" name="displayfield<?php print $istudentfield++;?>"
		<?php if($onchange=='yes'){print ' onChange="processContent(this);" ';}?>
		<?php if($required=='yes'){ print ' class="required" ';} ?> 
		style="width:12em;" >
<?php
	$displayfield_array=getEnumArray('studentfield');
	if(isset($extra_studentfields)){$displayfield_array+=$extra_studentfields;}
	while(list($val,$description)=each($displayfield_array)){
		print '<option ';
		print ' tabindex="'.$tab++.'" '; 
		if(($seldisplayfield==$val)){print 'selected="selected"';}
		print	' value="'.$val.'">'.get_string($description,'infobook').'</option>';
		}
?>
	</select>
<?php  unset($required); unset($seldisplayfield); unset($onchange);?>





















