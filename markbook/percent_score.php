<?php
/*								percent_score.php
	Expects $score_value, $score_total
	Returns formated $percent, and floating point $cent
*/
	unset($percent);
	unset($cent);
	if (isset($score_value)){
		if ($score_total>0){
			$cent=($score_value/$score_total)*100;
//			$cent=round($cent,1);
			$percent=sprintf("% 01.1f%%",$cent);
//			$percent=sprintf("% 2d%%",$cent);
			}
		}
?>
