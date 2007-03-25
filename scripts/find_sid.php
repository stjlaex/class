<?php	
/**								  scripts/find_sid.php
 *
 *	Returns array of $d_sids and number of $rows in it
 */


if($surname!=''){
	if($forename!=''){
		$d_sids=mysql_query("SELECT * FROM student WHERE
			 MATCH (surname) AGAINST ('$surname*' IN BOOLEAN MODE) 
				AND (MATCH (forename,preferredforename) AGAINST ('$forename*' IN BOOLEAN MODE) 
				OR forename='$forename' OR preferredforename='$forename')
						ORDER BY surname, forename");
		$rows=mysql_num_rows($d_sids);
		if($rows==0) {$result[]='Failed to find '.$surname.', '.$forename.'.';}
		}
	elseif($newfid!=''){
		$d_sids=mysql_query("SELECT * FROM student WHERE 
		MATCH (surname) AGAINST ('$surname*' IN BOOLEAN MODE) 
		AND form_id='$newfid' ORDER BY surname, forename");
		$rows=mysql_num_rows($d_sids);
		if($rows==0) {$result[]='Failed to find '.$surname.', in year '.$newfid.'.';}	
		}
	elseif($newyid!=''){
		$d_sids=mysql_query("SELECT * FROM student WHERE 
		MATCH (surname) AGAINST ('$surname*' IN BOOLEAN MODE) 
		AND yeargroup_id='$newyid' ORDER BY surname, forename");
		$rows=mysql_num_rows($d_sids);
		if($rows==0) {$result[]='Failed to find '.$surname.', in year '.$newyid.'.';}	
		}

	if(!isset($rows)){
		$d_sids=mysql_query("SELECT * FROM student WHERE
		MATCH (surname) AGAINST ('$surname*' IN BOOLEAN MODE) 
		OR surname='$surname' 
		ORDER BY surname, forename");
		$rows=mysql_num_rows($d_sids);
		if($rows==0) {$result[]='Failed to find surname '.$surname.'.';}	
		}		
	}
elseif($forename!=''){
	if($newfid!=''){
		$d_sids=mysql_query("SELECT * FROM student WHERE 
		MATCH (forename) AGAINST ('$forename*' IN BOOLEAN MODE) 
		AND form_id='$newfid' ORDER BY surname, forename");
		$rows=mysql_num_rows($d_sids);
		if($rows==0) {$result[]='Failed to find '.$forename.', in form '.$newfid.'.';}	
		}
	elseif($newyid!=''){
		$d_sids=mysql_query("SELECT * FROM student WHERE 
		MATCH (forename) AGAINST ('$forename*' IN BOOLEAN MODE)
	    AND yeargroup_id='$newyid' ORDER BY surname, forename");
		$rows=mysql_num_rows($d_sids);
		if($rows==0) {$result[]='Failed to find '.$forename.', in year '.$newyid.'.';}	
		}	
	if(!isset($rows)){
		$d_sids=mysql_query("SELECT * FROM student WHERE 
				MATCH (forename,preferredforename) AGAINST ('*$forename*' IN BOOLEAN MODE) 
				OR forename='$forename' OR preferredforename='$forename' 
				ORDER BY surname, forename");
		$rows=mysql_num_rows($d_sids);
		if($rows==0) {$result[]='Failed to find '.$forename.'.';}
		}
	}
elseif($newfid!=''){
		$d_sids=mysql_query("SELECT * FROM student WHERE
		form_id='$newfid' ORDER BY surname, forename");
		$rows=mysql_num_rows($d_sids);
		if($rows==0) {$result[]='Failed to find any in form '.$newfid.'.';}	
		}	
elseif($newyid!=''){
		$d_sids=mysql_query("SELECT * FROM student WHERE
		yeargroup_id='$newyid' ORDER BY surname, forename");
		$rows=mysql_num_rows($d_sids);
		if($rows==0) {$result[]='Failed to find any in '.$newyid.'.';}	
		}

if(!isset($rows)){
	$rows=0;
	$result[]='No matches found!';
	}
?>
