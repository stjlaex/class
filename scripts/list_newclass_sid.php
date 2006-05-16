<?php 
/*	Script to list student's classes
	Expects $sid, optional $cid
	Returns $newcid
	Called within a form
*/
 

	print "<SELECT name='newcid'>";	

   	$cids = mysql_query("SELECT class_id  FROM cidsid WHERE student_id='$sid' ORDER BY class_id");
		print "<option value=''>Class</option>";
    	while($d = mysql_fetch_array($cids,MYSQL_ASSOC)) {
		print "<option ";
		if(isset($cid)){if($cid==$d{'class_id'}){print "selected";}}
		print	" value='".$d{'class_id'}."'>".$d{'class_id'}."</option>";
			}
			
	print "</SELECT>";
?>
 





















