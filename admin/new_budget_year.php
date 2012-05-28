<?php
$currentycode=get_budgetyearcode($currentyear);
$newycode=get_budgetyearcode($budgetyear);

/**
 * Double-check first that this has not already run!
 */
$d_n=mysql_query("SELECT * FROM orderbudget 
					WHERE yearcode='$newycode' AND overbudget_id='0';");
if(mysql_num_rows($d_n)==0){
	$d_b=mysql_query("SELECT * FROM orderbudget 
					WHERE yearcode='$currentycode' AND overbudget_id='0';");
	
	while($b=mysql_fetch_array($d_b,MYSQL_ASSOC)){
		$currentbudid=$b['id'];
		$gid=$b['gid'];
		$name=$b['name'];
		$bcode=$b['code'];
		$costlimit=$b['costlimit'];
		$sectionid=$b['section_id'];
		
		mysql_query("INSERT INTO orderbudget SET gid='$gid',
					name='$name', code='$bcode',
					yearcode='$newycode', section_id='$sectionid', 
					overbudget_id='0', costlimit='$costlimit';");
		$newbudid=mysql_insert_id();
		
		$d_sb=mysql_query("SELECT * FROM orderbudget 
					WHERE yearcode='$currentycode' AND overbudget_id='$currentbudid';");
		while($sb=mysql_fetch_array($d_sb,MYSQL_ASSOC)){
			$gid=$sb['gid'];
			$name=$sb['name'];
			$bcode=$sb['code'];
			$costlimit=$sb['costlimit'];
			$sectionid=$sb['section_id'];
			mysql_query("INSERT INTO orderbudget SET gid='$gid',
					name='$name', code='$bcode',
					yearcode='$newycode', section_id='$sectionid', 
					overbudget_id='$newbudid', costlimit='$costlimit';");
			}
		
		}
	}
?>
