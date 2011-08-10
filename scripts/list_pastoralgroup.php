<?php
/**			       		list_pastoralgroup.php
 *
 * Used to distinguish selections available to year heads or form tutors
 */


if(!isset($onchange)){$onchange='no';}
if(!isset($required)){$required='yes';}

if(sizeof($ryids)>0){
	if(!isset($selyid)){$selyid=$ryids[0];}
?>
<div class="left">
	<label for="Year group"><?php print_string('yeargroup');?></label>
	<select id="Year group" name="newyid" eitheror="Form group" tabindex="<?php print $tab++;?>"
			<?php if($onchange=='yes'){ print ' onchange="processContent(this);" ';} ?>
			style="width:20em;" <?php if($required=='yes'){ print ' class="requiredor" ';} ?> >
			<option value=""></option>
<?php
		if(!isset($rforms)){$rforms=array();}
    	foreach($ryids as $yid){
			$d_yeargroup=mysql_query("SELECT name FROM yeargroup WHERE id='$yid';");
			if(mysql_num_rows($d_yeargroup)>0){
				$yeargroup=mysql_result($d_yeargroup,0);
				print '<option ';
				if($selyid==$yid){print 'selected="selected"';}
				print	' value="'.$yid.'">'.$yeargroup.'</option>';
				}
			$rforms=array_merge($rforms,list_formgroups($yid));
			}
?>
	</select>
</div>
<?php
	}

if(sizeof($rforms)>0){
	if(!isset($selfid)){$selfid=$rforms[0]['name'];}
?>
<div class="right">
	<label for="Form group"><?php print_string('formgroup');?></label>
	<select type="text" id="Form group" name="newfid"  eitheror="Year group"
	  tabindex="<?php print $tab++;?>" style="width:20em;" 
	  <?php if($onchange=='yes'){ print ' onchange="processContent(this);" ';} ?>
	  <?php if($required=='yes'){ print ' class="requiredor" ';} ?> >
	<option value=""></option>
<?php
        foreach($rforms as $form){
			print '<option value="'.$form['name'].'" ';
			if($selfid==$form['name']){print ' selected="selected" ';}
			print ' >'.$form['name'].'</option>';
   			}
?>
	</select>
</div>
<?php
	}

if(sizeof($rforms)==0 and sizeof($ryids)==0){
	print '<label>'.get_string('youhavenopastoralresponsibilities').'</label>';
	}
reset($ryids);
reset($rforms);
?>