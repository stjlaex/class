<?php 
/*												subject_edit.php

*/

$host="admin.php";
$current="subject_edit.php";
$action="subject_ecit_action..php";
$choice="class_matrix.php";

$crid=$_GET{'crid'};
$bid=$_GET{'bid'};
$yid=$_GET{'yid'};
$many=$_GET{'many'};
$generate=$_GET{'generate'};

?>
	
	<form method="post" action="admin.php">
	<table width="40%">
	<tr><td>Number of classes <select id="many" name="many">
<?php
	$c=0;
   while($c<20 ) {
		print "<option value='".$c."' ";
		if($c==$many){print " selected ";}
		print" >".$c."</option>";
		$c++;
		}
	print "</select></td></tr>";
	
	print "<tr><td>Generate using <select='generate' name='generate'>";
		print "<option value='forms' ";
		if($generate=="forms"){print " selected ";}
		print" >forms</option>";
		
		print "<option value='sets' ";
		if($generate=="sets"){print " selected ";}
		print" >sets</option>";
		
		print "<option value='none' ";
		if($generate=="none"){print " selected ";}
		print" >none</option>";
		
	
?>
	</select></td></tr>
	<tr><td><input type="Submit" name="submit" value="Enter"></td></tr>

	</table>
	<input type="hidden" name="yid" value="<?php print $yid;?>"> 
	<input type="hidden" name="crid" value="<?php print $crid;?>"> 
	<input type="hidden" name="bid" value="<?php print $bid;?>">
	<input type="hidden" name="choice" value="subject_matrix.php">
	<input type="hidden" name="current" value="subject_edit_action.php">
	</form>
































