<?php
/**			       		list_pastoralgroup.php
 */

$required='yes';
if(sizeof($ryids)>0){
	$selyid=$ryids[0];
?>
	<label for="Year Group"><?php print_string('yeargroup');?></label>
	<select id="Year Group" name="newyid" class="required">
    <option value=""></option>
<?php
    	while(list($index,$yid)=each($ryids)){
			print '<option ';
			if($selyid==$yid){print 'selected="selected"';}
			print	' value="'.$yid.'"> Year '.$yid.' </option>';
			}
?>
	</select>
<?php
	}
elseif(sizeof($rfids)>0){
	$selfid=$rfids[0];
?>
	<label for="Form group"><?php print_string('formgroup');?></label>
	<select type="text" id="Form group" name="newfid" class="required">
	<option value=""></option>
<?php
        while(list($index,$fid)=each($rfids)){
			print '<option value="'.$fid.'" ';
			if($selfid==$fid){print ' selected="selected" ';}
			print ' > Form '.$fid.' </option>';
   			}
?>
	</select>
<?php
	}
else{
	}
?>