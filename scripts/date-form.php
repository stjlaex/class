<?php
/*											date-form.php
		generic date selection, returns seperate day, month and year variables
*/
if(isset($entrydate)){
		$todate=explode('-',$entrydate);
		$toyear=$todate[0];
		$tomonth=$todate[1];
		$today=$todate[2];
}
	
elseif (!isset($today)){
	$tomonth=date('n');
	$today=date('j');
	$toyear=date('Y');
}

/* Set if this is the ith time that date-form has been called*/
if(!isset($idate)){$idate='';}
?>
<label for="Day">Day</label>
	 <select style="width:4em;" class="required" id="Day" name="day<?php print $idate;?>" size="1">
<?php	
	$c=$today;
	while ($c<32){
		print '<option value="'.$c.'" ';
		if($c==$today){print ' selected="selected" ';}
		print '>'.$c.'</option>';
		$c++;
		}
	$c=$today-1;
	while ($c>0){
		print '<option value="'.$c.'">'.$c.'</option>';
		$c--;
		}
?>
	</select>


<label for="Month">Month</label>
	<select style="width:4em;"  class="required" 
		id="Month" name="month<?php print $idate;?>" size="1">
<?php	
	$c=$tomonth;
	while ($c<13){
		print '<option value="'.$c.'" ';
		if($c==$tomonth){print ' selected="selected" ';}
		print '>'.$c.'</option>';
		$c++;
		}
	$c=$tomonth-1;
	while ($c>0){
		print '<option value="'.$c.'">'.$c.'</option>';
		$c--;
		}
?>
	</select>

<label for="Year">Year</label>
	 <select style="width:5em;" class="required" id="Year" name="year<?php print $idate;?>" size="1">
<?php
	if(!($toyear>1900)){$toyear=2005;}
	$c=$toyear;
	while ($c<$toyear+3){
		print '<option value="'.$c.'" ';
		if($c==$toyear){print ' selected="selected" ';}
		print '>'.$c.'</option>';
		$c++;
		}
	$c=$toyear-1;
	while ($c>$toyear-3){
		print '<option value="'.$c.'" '; 
		print '>'.$c.'</option>';
		$c--;
		}
?>
	</select>

