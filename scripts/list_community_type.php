<?php 
/**					list_community_type.php
 *   returns $newcomtype
 */
 
	if(!isset($selcomtype)){$selcomtype='';}
	if(isset($comtype)){$selcomtype=$comtype;}
	if(isset($newcomtype)){$selcomtype=$newcomtype;}
	if(!isset($required)){$required='no';}
	if(!isset($onchange)){$onchange='no';}
?>
	<label for="Community type"><?php print_string('communitytype','admin');?></label>
	<select id="Community type" name="newcomtype" 
		<?php print ' tabindex="'.$tab++.'" ';?>
		<?php if($onchange=='yes'){print ' onChange="processContent(this);" ';}?>
			<?php if($required=='yes'){ print ' class="required" ';} ?> >
<?php
	$comtype_array=getEnumArray('community_type');
	foreach($comtype_array as $val => $description){
		print '<option ';
		if(($selcomtype==$val)){print 'selected="selected"';}
		print	' value="'.$val.'">'.get_string($description,'admin').'</option>';
		}
?>
	</select>
<?php  unset($required); unset($selcomtype); unset($onchange);?>





















