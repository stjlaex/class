<?php
/*script to list the subject components belonging to a group of classes*/
/* needs pids[] from markbook.php */

if(sizeof($pids)>0){
  if(isset($pid) and !isset($selpid)){$selpid=$pid;}
	elseif(!isset($selpid)){$selpid='';}

?>
	<label for="newpid" >Subject Component:</label>
	<select id="newpid" name="newpid" size="1">
	<option value=''>all components</option>

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
