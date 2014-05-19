<?php
/**								  		community_group.php
 */

$choice='community_group.php';
$action='community_group_action.php';

if(isset($_POST['newcomtype'])){$newcomtype=$_POST['newcomtype'];}
elseif($_SESSION['role']=='office' or $_SESSION['role']=='admin'){$newcomtype='TUTOR';}
else{$newcomtype='ACADEMIC';}


$extrabuttons['form']=array('name'=>'current',
							'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
							'value'=>'group_print.php',
							'xmlcontainerid'=>'forms',
							'onclick'=>'checksidsAction(this)');
$extrabuttons['groups']=array('name'=>'current',
							  'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
							  'value'=>'group_print.php',
							  'xmlcontainerid'=>'groups',
							  'onclick'=>'checksidsAction(this)');
$extrabuttons['export']=array('name'=>'current','value'=>'community_group_export.php');
$extrabuttons['createnewgroup']=array('name'=>'current','value'=>'community_group_rename.php');
$extrabuttons['delete']=array('name'=>'current','value'=>'community_group_delete.php');
two_buttonmenu($extrabuttons);
?>
  <div class="content">
	  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >

	<div class="center">
	<fieldset class="divgroup">
		  <h5><?php print_string('changetype',$book);?></h5>
		<div class="center">
		  <?php $onchange='yes';include('scripts/list_community_type.php');?>
		</div>
	</fieldset>

	<div class="center" id="viewcontent">
	  <table class="listmenu">
		<tr>
		<th colspan="2" class="checkall">
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
//$houses=list_communities('house');

	foreach($communities as $com){
		$nosids=countin_community($com);
		$nosidstotal=$nosidstotal+$nosids;		
		if($newcomtype=='HOUSE'){unset($HouseTotal);$HouseTotal=fetchHouseMeritsTotal($com['name']);}
?>
		<tr>
		<td>
		<input type="checkbox" name="comids[]" value="<?php print $com['id'];?>" />
			</td>
		<td class="student">
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
  <div id="xml-groups" style="display:none;">
	<params>
	  <checkname>comids</checkname>
	  <selectname>newcomtype</selectname>
	  <selectname>date0</selectname>
	  <selectname>date1</selectname>
	  <transform>group_list</transform>
	</params>
  </div>
  <div id="xml-forms" style="display:none;">
	<params>
	  <checkname>comids</checkname>
	  <selectname>newcomtype</selectname>
	  <selectname>date0</selectname>
	  <selectname>date1</selectname>
	  <transform>group_list_forms</transform>
	</params>
  </div>
