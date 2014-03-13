<?php 
/**								  		formgroup_matrix.php
 */

$choice='formgroup_matrix.php';
$action='formgroup_matrix_action.php';


/* This is designed to handle groups of any type which are used for
 * pastoral groups, so $newcomtype will be either 'house' or
 * 'form'. There are two principle differences: forms are used as the
 * basis for the academic teaching classes while houses are not; and
 * forms have distinct groups per yeargroup while houses span the
 * yeargroups only having permissions groups for assigning access on a
 * yeargroup basis.
 *
 */
if(isset($_GET['newcomtype'])){$newcomtype=$_GET['newcomtype'];}else{$newcomtype='form';}
if(isset($_POST['newcomtype'])){$newcomtype=$_POST['newcomtype'];}

if($newcomtype=='form'){
	$extrabuttons['createnewgroup']=array('name'=>'current','value'=>'community_group_rename.php');
	}
$extrabuttons['lists']=array('name'=>'current',
							 'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
							 'value'=>'group_print.php',
							 'onclick'=>'checksidsAction(this)');
three_buttonmenu($extrabuttons);
?>
  <div class="content">
	  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >
	      <div class="center">
	  <div class="left">
	  <fieldset class="divgroup">
		  <h5><?php print_string('assigntoteacher',$book);?></h5>
<?php 
	$required='yes';
	include('scripts/list_teacher.php');
?>
<?php
	$listlabel=$newcomtype;
	$listname='gid';
	include('scripts/set_list_vars.php');
	$listgids=array();
	$listcoms=array();
	$coms=list_communities($newcomtype);
	foreach($coms as $com){
		$listgids[]=array('id'=>$com['gid'],'name'=>$com['displayname']);
		$listcoms[]=$com;
		}
	list_select_list($listgids,$listoptions,$book);
	unset($listoptions);
?>
	  </fieldset>
	  </div>
	  <div class="right">
	   <fieldset class="divgroup">
      <h5><?php print_string('changetype',$book);?></h5>

        <div class="center">
<?php
    $listname='pastoraltype';
    $onchange='yes';
    $listlabel='';
    include('scripts/set_list_vars.php');
    $list=array('form'=>'form','house'=>'house','reg'=>'registrationgroup');
    list_select_list($list,$listoptions,$book);
    unset($listoptions);
?>
        </div>

    </fieldset>
    </div>
</div>
<div class="center" id="viewcontent">
	  <table class="listmenu">
		<tr>
		  <th colspan="2"><?php print_string('checkall'); ?>
		  <input type="checkbox" name="checkall" value="yes" onChange="checkAll(this);" />
		  </th>
		  <th><?php print_string('numberofstudents',$book);?></th>
		  <th><?php print_string('formtutor',$book);?></th>
		</tr>
<?php
	$nosidstotal=0;
	foreach($listcoms as $com){
		$yid=$com['yeargroup_id'];
		$comid=$com['id'];
		$perms=get_community_perm($comid,$yid);
		$nosids=countin_community($com);
		$nosidstotal=$nosidstotal+$nosids;
?>
		<tr>
		  <td>
			<input type="checkbox" name="comids[]" value="<?php print $comid;?>" />
		  </td>
		  <td>
<?php
		if($perms['r']==1 and $newcomtype=='form'){
			print '<a href="admin.php?current=form_edit.php&cancel='.$choice.'&choice='.$choice.'&comid='.$com['id'].'&newcomtype='.$newcomtype.'">'.$com['displayname'].'</a>';
			}
		elseif($perms['r']==1 and ($newcomtype=='house' or $newcomtype=='reg')){
	   		print '<a href="admin.php?current=community_group_edit.php&cancel='.$choice.'&choice='.$choice.'&newcomtype='.$newcomtype.'&comid='.$com['id'].'&yid='.$yid.'">'.$com['displayname'].'</a>';
			}
		else{
	   		print $com['displayname'];
	   		}
?>
		  </td>
		  <td><?php print $nosids;?></td>
		  <td>
<?php
		$users=(array)list_community_users($com);
		foreach($users as $uid => $user){
			$Responsible=array('id_db'=>$com['gid'].'-'.$uid);
			if($user['role']!='office' and $user['username']!='administrator'){
				if($perms['w']==1){
?>
			<div  id="<?php print $com['gid'].'-'.$uid;?>" class="rowaction" >
			  <button type="button" title="Remove this responsibility"
				name="current"
				value="responsables_edit_formgroup.php" 
				onClick="clickToAction(this)">
					 <?php print $user['username'].' ';?>
			  </button>
			  <div id="<?php print 'xml-'.$com['gid'].'-'.$uid;?>" style="display:none;">
							  <?php xmlechoer('Responsible',$Responsible);?>
			  </div>
			</div>
<?php
					}
				else{
					print $user['username'].' ';
					}
				}
			}
?>
		  </td>
		</tr>
<?php
		}
?>
		  <tr>
			<th colspan="2">
			  <?php print get_string('total',$book).' '.get_string('numberofstudents',$book);?>
			</th>
			<td><?php print $nosidstotal;?></td>
			<td>&nbsp;</td>
		  </tr>

	  </table>

	  <input type="hidden" name="newcomtype" value="<?php print $newcomtype;?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
      <input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>
  </div>
</div>
  <div id="xml-checked-action" style="display:none;">
	<params>
	  <checkname>comids</checkname>
	  <selectname>newcomtype</selectname>
	  <transform>group_list</transform>
	  <paper>portrait</paper>
	</params>
  </div>

