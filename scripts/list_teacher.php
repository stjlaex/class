<?php 
/*	Script to list teachers
	Optional $tid
	Returns $newtid
	Called within a form
*/
 

		print "<select name='newtid'>";	

   	$d_tids = mysql_query("SELECT id  FROM teacher ORDER BY id");
    	while($tids = mysql_fetch_array($d_tids,MYSQL_ASSOC)) {
		print "<option ";
		if(isset($tid)){if($tid==$tids{'id'}){print "selected";}}
		print	" value='".$tids{'id'}."'>".$tids{'id'}."</option>";
			}
			
		print "</select>";
?>
 





















