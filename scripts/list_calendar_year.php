<?php 
/*						list_calendar_year.php
 returns $year
*/

	if(!isset($year)){$toyear=date('Y');}else{$toyear=$year;}
	if(!isset($newyear)){$newyear='';}
	if(!isset($required)){$required='no';}
?>
<label for="Year"><?php print_string('year');?></label>
	 <select style="width:7em;" class="required" id="Year" 
				 tabindex="<?php print $tab++;?>" name="year" size="1">
	 <option value=""></option>
<?php
	if(!($toyear>1900)){$toyear=2005;}
	$c=$toyear;
	while ($c<$toyear+5){
		print "<option value='$c' ";
		if($c==$toyear){print " selected='selected' ";}
		print ">".$c."</option>";
		$c++;
		}
	$c=$toyear-1;
	while ($c>$toyear-5){
		print "<option value='$c' "; 
		print ">".$c."</option>";
		$c--;
		}
?>
	</select>
<?php  unset($required);?>
