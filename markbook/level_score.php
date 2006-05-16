<?php 
/* 									level_score.php
	Expects $cent, $levelling_levels
	Returns $grade
*/
	unset($grade);
	$pairs=explode(";",$levelling_levels);
	if (!isset($cent)){$cent=$score_value;}
	for($c3=0; $c3<sizeof($pairs); $c3++){
			list($level_grade, $level)=split(":",$pairs[$c3]);
			if ($cent>=$level){$grade=$level_grade;}
			}
	if (!isset($grade)){$grade=""; $cent=-100;}
?>
















































