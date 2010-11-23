<?php
/**								  		community_group.php
 */

$choice='community_group.php';
$action='community_group_action.php';

if(isset($_POST['newcomtype'])){$newcomtype=$_POST['newcomtype'];}
elseif($_SESSION['role']=='teacher'){$newcomtype='ACADEMIC';}
else{$newcomtype='TUTOR';}


$extrabuttons['previewselected']=array('name'=>'current',
									   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
									   'value'=>'group_print.php',
									   'onclick'=>'checksidsAction(this)');
$extrabuttons['export']=array('name'=>'current','value'=>'community_group_export.php');
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
		<th colspan="2"><?php print_string('checkall'); ?>
		  <input type="checkbox" name="checkall" value="yes" onChange="checkAll(this);" />
		</th>
		<th><?php print_string('numberofstudents',$book);?></th>
<?php
		if($newcomtype=='HOUSE'){
			print '<th>House points</th>';
			}
?>
		</tr>
<?php
	$nosidstotal=0;
	$communities=list_communities($newcomtype);
	foreach($communities as $com){
		$nosids=countin_community($com);
		$nosidstotal=$nosidstotal+$nosids;
		
		if($newcomtype=='HOUSE'){unset($HouseTotal);$HouseTotal=fetchHouseMeritsTotal($com['name']);}

?>	   	
		<tr>
		<td>
		<input type="checkbox" name="comids[]" value="<?php print $com['id'];?>" />
			</td>
		<td>
<?php
	   		print '<a href="admin.php?current=community_group_edit.php&cancel='.$choice.'&choice='.$choice.'&newcomtype='.$newcomtype.'&comid='.$com['id'].'">'.$com['name'].'</a>';
		print '</td>';
	   	print '<td>'.$nosids.'</td>';
		if($newcomtype=='HOUSE'){
			print '<td>'.$HouseTotal['Sum']['value'].'</td>';
			}
		print '</tr>';
		}
?>
		  <tr>
			<th colspan="2">
			  <?php print get_string('total',$book).' '.get_string('numberofstudents',$book);?>
			</th>
			<td><?php print $nosidstotal;?></td>
		  </tr>
	  </table>
	</div>


	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>
  </div>
  <div id="xml-checked-action" style="display:none;">
	<params>
	  <checkname>comids</checkname>
	  <selectname>newcomtype</selectname>
	  <transform>group_list</transform>
	</params>
  </div>
