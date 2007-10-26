<?php
/* A script to list the subject components belonging to a group of classes*/
/* needs pids[] from markbook.php */

if(sizeof($pids)>0){
  if(isset($pid) and !isset($selpid)){$selpid=$pid;}
	elseif(!isset($selpid)){$selpid='';}
?>
	<label for="newpid" ><?php print_string('subjectcomponent');?>:</label>
	<select id="newpid" name="newpid" size="1">
	<option value=''><?php print_string('all');?></option>
<?php
   foreach($pids as $key => $spid) {
		print "<option ";
		if($spid==$selpid){print "selected='selected'";}
		print	" value='".$spid."'>".$spid."</option>";
		}
?>
	</select>
<?php
}
?>
