<?php
/**			       		list_pastoralgroup.php
 *
 * Used to distinguish selections available to year heads or form tutors
 */

$required='yes';

if(sizeof($ryids)>0){
	$selyid=$ryids[0];
?>
<div class="left">
	<label for="Year group"><?php print_string('yeargroup');?></label>
	<select id="Year group" name="newyid" eitheror="Form group" tabindex="<?php print $tab++;?>"
		 style="width:20em;" <?php if($required=='yes'){ print ' class="requiredor" ';} ?> >
    <option value=""></option>
<?php
		if(!isset($rfids)){$rfids=array();}
    	while(list($index,$yid)=each($ryids)){
			$d_yeargroup=mysql_query("SELECT name FROM yeargroup
									WHERE id='$yid'");
			if(mysql_num_rows($d_yeargroup)>0){
				$yeargroup=mysql_result($d_yeargroup,0);
				print '<option ';
				if($selyid==$yid){print 'selected="selected"';}
				print	' value="'.$yid.'">'.$yeargroup.'</option>';
				}

			$d_group=mysql_query("SELECT id FROM form
									WHERE yeargroup_id='$yid'");
			while($group=mysql_fetch_array($d_group,MYSQL_ASSOC)){
				$rfids[]=$group['id'];
				}
			}
?>
	</select>
</div>
<?php
	}

if(sizeof($rfids)>0){
	$selfid=$rfids[0];
?>
<div class="right">
	<label for="Form group"><?php print_string('formgroup');?></label>
	<select type="text" id="Form group" name="newfid"  eitheror="Year group"
	  tabindex="<?php print $tab++;?>" style="width:20em;" 
	  <?php if($required=='yes'){ print ' class="requiredor" ';} ?> >
	<option value=""></option>
<?php
        while(list($index,$fid)=each($rfids)){
			$d_group=mysql_query("SELECT name FROM form
									WHERE id='$fid'");
			$formgroup=mysql_result($d_group,0);
			print '<option value="'.$fid.'" ';
			if($selfid==$fid){print ' selected="selected" ';}
			print ' >'.$formgroup.'</option>';
   			}
?>
	</select>
</div>
<?php
	}

if(sizeof($rfids)==0 and sizeof($ryids)==0){
	print '<label>'.get_string('youhavenopastoralresponsibilities').'</label>';
	}
reset($ryids);
reset($rfids);
?>