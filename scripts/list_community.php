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
	if(!isset($onchange)){$onchange='no';}
	if(!isset($selcomids)){$selcomids=(array)$comids;}
	$listypes=array();
	if(!isset($type)){$listtypes[]='year';}
	else{$listtypes[]=$type;}
	if($listtypes[0]=='year' and ($_SESSION['role']=='office' 
						   or $_SESSION['role']=='administrator')){
		$listtypes[]='enquired';$listtypes[]='applied';$listtypes[]='accepted';}

	$listcoms=array();
	while(list($index,$listtype)=each($listtypes)){
		$d_community=mysql_query("SELECT * FROM community WHERE
									type='$listtype' ORDER BY type, name");
   		while($listcom=mysql_fetch_array($d_community,MYSQL_ASSOC)){
			$listcoms[]=$listcom;
			}
		}
?>
	<select style="width:20em;" id="Community"
		<?php if($required=='yes'){ print ' class="required" ';} ?>
		size="<?php print $multi;?>"
		<?php if($onchange=='yes'){print ' onChange="processContent(this);" ';} ?>
		<?php if($multi>1){print ' name="newcomids'.$icomid.'[]" multiple="multiple"';}
				else{print ' name="newcomid'.$icomid.'"';}?> >
    <option value=""></option>
<?php
   		while(list($index,$listcom)=each($listcoms)){
?>
		<option 
		<?php if(in_array($listcom['id'], $selcomids)){print " selected='selected' ";} ?>
			value="<?php print $listcom['id'];?>" >
				<?php print $listcom['type'].':'.$listcom['name'];?>
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
unset($type1);
unset($type2);
?>