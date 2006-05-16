<?php
/*										scripts/list_stats.php

returns array statids[] for $multi>1 statid for multi=1 (default=10)
filters by rcrid if set, shows all if not
set $required to 'no' to make not required (default=yes)
*/

if(isset($rcrid)){$crid=$rcrid;}
else{$crid='%';}
if(!isset($required)){$required="yes";}
if(!isset($multi)){$multi="10";}
?>
	<label for="Stats"><?php print_string('statistics');?></label>
<?php
		$d_stats=mysql_query("SELECT DISTINCT id, description
				FROM stats WHERE (course_id LIKE '$crid' or
				course_id='%') ORDER BY id DESC");
?>
	<select style="width:20em;" id="Stats"
	<?php if($required=='yes'){ print ' class="required" ';} ?>
	size="<?php print $multi;?>"
	<?php if($multi>1){print 'name="statids[]" multiple="multiple" ';}
				else{print 'name="statid"';}?>" 
	>
    <option value=""></option>
<?php
   		while($stats=mysql_fetch_array($d_stats,MYSQL_ASSOC)){
?>   				
		<option value="<?php print $stats['id'];?>"><?php print $stats['description'];?></option>
<?php
				}
?>
	</select>
<?php
unset($required);
unset($multi);
?>