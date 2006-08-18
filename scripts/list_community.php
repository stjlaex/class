<?php
/**										scripts/list_community.php
 *
 *$multi>1 returns comids[] or $multi=1 returns comid (default=10)
 *set $required='no' to make not required (default=yes)
 *first call returns comid, second call returns comid1
 */


	if(!isset($required)){$required='yes';}
	if(!isset($multi)){$multi='4';}
	if(!isset($icomid)){$icomid='';}else{$icomid++;}
	if(!isset($type)){$type='%';}
	/*keeping this simple and making no distinction between community types*/
	$d_community=mysql_query("SELECT * FROM community WHERE type LIKE
								'$type' ORDER BY name");
?>
	<select style="width:20em;" id="Community"
		<?php if($required=='yes'){ print ' class="required" ';} ?>
		size="<?php print $multi;?>"
		<?php if($multi>1){print ' name="comids'.$icomid.'[]" multiple="multiple"';}
				else{print ' name="comid'.$icomid.'"';}?> >
    <option value=""></option>
<?php
   		while($community=mysql_fetch_array($d_community,MYSQL_ASSOC)){
?>
		<option 
		<?php if(in_array($community['id'], $comids)){print " selected='selected' ";} ?>
			value="<?php print $community['id'];?>" >
				<?php print $community['type'].':'.$community['name'];?>
		</option>
<?php
				}
?>
	</select>
<?php
unset($required);
unset($multi);
?>