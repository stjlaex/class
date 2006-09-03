<?php
/**										scripts/list_community.php
 *
 * $multi>1 returns newcomids[] or $multi=1 returns newcomid (default=10)
 * set $required='no' to make not required (default=yes)
 * first call returns newcomid, second call returns newcomid1
 */


	if(!isset($required)){$required='yes';}
	if(!isset($multi)){$multi='4';}
	if(!isset($icomid)){$icomid='';}else{$icomid++;}
	if(!isset($type)){$type='%';}
	if(!isset($onchange)){$onchange='no';}
	if(!isset($selcomids)){$selcomids=(array)$comids;}

	/*keeping this simple and making no distinction between community types*/
	$d_community=mysql_query("SELECT * FROM community WHERE type LIKE
								'$type' ORDER BY name");
?>
	<select style="width:20em;" id="Community"
		<?php if($required=='yes'){ print ' class="required" ';} ?>
		size="<?php print $multi;?>"
		<?php if($onchange=='yes'){print ' onChange="processContent(this);" ';} ?>
		<?php if($multi>1){print ' name="newcomids'.$icomid.'[]" multiple="multiple"';}
				else{print ' name="newcomid'.$icomid.'"';}?> >
    <option value=""></option>
<?php
   		while($listcommunity=mysql_fetch_array($d_community,MYSQL_ASSOC)){
?>
		<option 
		<?php if(in_array($listcommunity['id'], $selcomids)){print " selected='selected' ";} ?>
			value="<?php print $listcommunity['id'];?>" >
				<?php print $listcommunity['type'].':'.$listcommunity['name'];?>
		</option>
<?php
				}
?>
	</select>
<?php
unset($required);
unset($multi);
unset($selcomids);
unset($onchange);
?>