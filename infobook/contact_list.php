<?php
/**									contact_list.php
 *
 *   	Lists contacts identified in array gids.
 */

$action='contact_list.php';
$choice='contact_list.php';

if(!isset($_SESSION['infosearchgid'])){$_SESSION['infosearchgid']='';}
if(!isset($_SESSION['infosearchgids'])){$_SESSION['infosearchgids']=array();}

if(isset($_GET['gid'])){$_SESSION['infosearchgid']=$_GET['gid'];}
if(isset($_POST['gid'])){$_SESSION['infosearchgid']=$_POST['gid'];}
if(isset($_GET['gids'])){$_SESSION['infosearchgids']=$_GET['gids'];}
if(isset($_POST['gids'])){$_SESSION['infosearchgids']=$_POST['gids'];}

$gids=$_SESSION['infosearchgids'];
$gid=$_SESSION['infosearchgid'];

include('scripts/sub_action.php');


$extrabuttons='';
/*
if($_SESSION['role']=='office' or $_SESSION['role']=='admin'){
     	$extrabuttons['exportstudentrecords']=array('name'=>'current',
										 'title'=>'exportstudentrecords',
										 'value'=>'export_students.php');
	}
*/
two_buttonmenu($extrabuttons,$book);
?>

<div id="viewcontent" class="content">
<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
<table class="listmenu">
		<thead>
		  <tr>
			<th></th>
			<th><?php print_string('checkall'); ?><input type="checkbox" name="checkall" 
				value="yes" onChange="checkAll(this);" /></th>
			<th colspan="2"><?php print_string('contacts',$book); ?></th>
		  </tr>
		<thead>
<?php

	if(sizeof($gids)<3){$rowclass='revealed';$rowstate='rowminus';}
	else{$rowclass='hidden';$rowstate='rowplus';}

	while(list($index,$gid)=each($gids)){
		$Contact=fetchContact(array('guardian_id'=>$gid));
		$Dependents=fetchDependents($gid);
		$rown=0;
?>
			<tbody id="<?php print $gid;?>">
			  <tr class="<?php print $rowstate;?>" onClick="clickToReveal(this)" id="<?php print $gid.'-'.$rown++;?>">
				<th>&nbsp</th>
				<td>
				  <input type="checkbox" name="gids[]" value="<?php print $gid;?>" />
				</td>
				<td colspan="2">
				  <a href="infobook.php?current=contact_details.php&cancel=contact_list.php&sid=&gid=<?php print $gid;?>">
						  <?php print $Contact['Surname']['value']; ?>
						  <?php print ', '.$Contact['Forename']['value']; ?>
				  </a>
				</td>
			  </tr>
			  <tr class="<?php print $rowclass;?>" id="<?php print $gid.'-'.$rown++;?>">
				<td colspan="2">
				</td>
				<td>
<?php
		while(list($index,$Dependent)=each($Dependents)){
			$Student=$Dependent['Student'];
			$relation=displayEnum($Dependent['Relationship']['value'],'relationship');
?>
				  <table>
					<tr>
					  <td>
						<p>
						  <?php print get_string($relation,$book) 
							 .' '.get_string('to',$book).' ';?>
						  <a href="infobook.php?current=student_view.php&cancel=contact_list.php&sid=<?php print $Student['id_db'];?>&sids[]=<?php print $Student['id_db'];?>">
							<?php print $Student['DisplayFullName']['value']; ?>
						  </a>
						</p>
					  </td>
					</tr>
				  </table>
<?php
			}
?>
				</td>
			  </tr>
			</tbody>
<?php
		}
	reset($gids);
?>
	  </table>
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print '$choice';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
  </div>