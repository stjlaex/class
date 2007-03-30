<?php	
/**								  scripts/find_sid.php
 *
 *	Returns array of $d_sids and number of $rows in it
 *  $table should be set to student or guardian
 */

if(isset($surname) and $surname!=''){
	if(isset($forename) and $forename!=''){
		$d_sids=mysql_query("SELECT id FROM $table WHERE
			 MATCH (surname) AGAINST ('$surname*' IN BOOLEAN MODE) 
				AND (MATCH (forename,preferredforename) AGAINST ('$forename*' IN BOOLEAN MODE) 
				OR forename='$forename' OR preferredforename='$forename')
						ORDER BY surname, forename");
		$rows=mysql_num_rows($d_sids);
		if($rows==0) {$result[]='Failed to find '.$surname.', '.$forename.'.';}
		}
	else{
		$d_sids=mysql_query("SELECT id FROM $table WHERE
		MATCH (surname) AGAINST ('$surname*' IN BOOLEAN MODE) 
		OR surname='$surname' 
		ORDER BY surname, forename");
		$rows=mysql_num_rows($d_sids);
		if($rows==0) {$result[]='Failed to find surname '.$surname.'.';}	
		}
	}
elseif(isset($forename) and $forename!=''){
	$d_sids=mysql_query("SELECT id FROM $table WHERE 
				MATCH (forename,preferredforename) AGAINST ('*$forename*' IN BOOLEAN MODE) 
				OR forename='$forename' OR preferredforename='$forename' 
				ORDER BY surname, forename");
	$rows=mysql_num_rows($d_sids);
	if($rows==0) {$result[]='Failed to find '.$forename.'.';}
	}
?>
