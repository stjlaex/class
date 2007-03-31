<?php
/**									contact_list.php
 *
 *   	Lists contacts identified in array gids.
 */

$action='contact_list.php';
$choice='contact_list.php';

if(!isset($_SESSION['infogid'])){$_SESSION['infogid']='';}
if(!isset($_SESSION['infogids'])){$_SESSION['infogids']=array();}

if(isset($_GET['gid'])){
	if($_SESSION['infogid']!=$_GET['gid']){
		$_SESSION['infogid']=$_GET['gid']; 
		$_SESSION['umnrank']='surname';
		}
	}
if(isset($_POST['gid'])){
	if($_SESSION['infogid']!=$_POST['gid']){
		$_SESSION['infogid']=$_POST['gid']; 
		$_SESSION['umnrank']='surname';
		}
	}

$gids=$_SESSION['infogids'];
$gid=$_SESSION['infogid'];

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
<table class="listmenu" name="listmenu">
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
				  <a href="infobook.php?current=contact_details.php&cancel=contact_list.php&gid=<?php print $gid;?>">
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
?>
				  <table>
					<tr><td><p>
						<?php 
			$relation=displayEnum($Dependent['Relationship']['value'],'relationship');
			print get_string($relation,$book).':  ' .$Student['DisplayFullName']['value']; 
						?>
					</p></td></tr>
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