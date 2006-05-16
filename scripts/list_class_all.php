<?php 
/*	Script to list teacher's class
	Expects 
	Returns $cid
	Called within a form
*/
 

	print "<select name='newcid'>";	

   	$d_cids = mysql_query("SELECT id  FROM class ORDER BY id");
		print "<option value=''>New Class</option>";
    	while($cids = mysql_fetch_array($d_cids,MYSQL_ASSOC)) {
		print "<option ";
		if(isset($cid)){if($cid==$cids{'id'}){print "selected='selected'";}}
		print	" value='".$cids{'id'}."'>".$cids{'id'}."</option>";
			}
			
	print "</select>";
?>
 





















