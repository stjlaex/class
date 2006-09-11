<?php
/**								  		community_group.php
 */

$choice='community_group.php';
$action='community_group_action.php';

if(isset($_POST['newcomtype'])){$newcomtype=$_POST['newcomtype'];}else{$newcomtype='academic';}

$extrabuttons['createnewgroup']=array('name'=>'current','value'=>'community_group_rename.php');
three_buttonmenu($extrabuttons);
?>
  <div class="content">
	  <form id="formtoprocess" name="formtoprocess" method="post"
		action="<?php print $host; ?>" >

	<fieldset class="right">
		  <legend><?php print_string('changetype',$book);?></legend>

		<div class="center">
		  <?php $onchange='yes';include('scripts/list_community_type.php');?>
		</div>

	</fieldset>

	<div class="left">
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('communitytype',$book);?></th>
		  <th><?php print_string('numberofstudents',$book);?></th>
		  <th><?php print_string('',$book);?></th>
		</tr>
<?php

	$nosidstotal=0;
	$d_com=mysql_query("SELECT * FROM community WHERE type='$newcomtype' ORDER BY name");
	while($com=mysql_fetch_array($d_com,MYSQL_ASSOC)){
		$comid=$com['id'];
		$nosids=countinCommunity(array('id'=>$comid));
		$nosidstotal=$nosidstotal+$nosids;
	   	print '<tr><td>';
	   		print '<a href="admin.php?current=community_group_edit.php&cancel='.$choice.'&choice='.$choice.'&newcomtype='.$newcomtype.'&comid='.$comid.'">'.$com['name'].'</a>';
		print '</td>';
	   	print '<td>'.$nosids.'</td><td>';
		print '</td></tr>';
		}
?>
		  <tr>
			<th>
			  <?php print get_string('total',$book).' '.get_string('numberofstudents',$book);?>
			</th>
			<td><?php print $nosidstotal;?></td>
			<td>&nbsp;</td>
		  </tr>
	  </table>
	</div>


	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>
  </div>