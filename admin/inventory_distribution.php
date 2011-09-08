<?php
/**                                  inventory_distribution.php    
 */

$cancel='inventory_list.php';
$action='inventory_distribution_action.php';
if(isset($_POST['budid'])){$budid=$_POST['budid'];}else{$budid=-1;}
if(isset($_GET['budid'])){$budid=$_GET['budid'];}
if(isset($_POST['catid'])){$catid=$_POST['catid'];}else{$catid=-1;}
if(isset($_GET['catid'])){$catid=$_GET['catid'];}
if(isset($_POST['newfid'])){$newfid=$_POST['newfid'];}else{$newfid=-1;}
$budgetyear=$_POST['budgetyear'];

include('scripts/sub_action.php');

$Budget=fetchBudget($budid);
$Material=fetchCatalogueMaterial($catid,$budid);
$maxno=5;
$manys=array();
for($c=0;$c<$maxno;$c++){
	$manys[]=array('id'=>$c,'name'=>$c);
	}

three_buttonmenu($book);
?>
  <div id="viewcontent" class="content">

  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >

	<div class="center">
	<table class="listmenu">
	  <tr>
		<th colspan="2"><?php print $Budget['Name']['value'];?></th>
		<th>Available</th>
	  </tr>
	  <tr>
		<td>
		  <?php print $Material['SupplierReference']['value']; ?>
		</td>
		<td>
		  <?php print $Material['Detail']['value']; ?>
		</td>
		<td>
		  <?php print $Material['Stock']['value']; ?>
		</td>
	  </tr>
	</table>
	</div>

	<fieldset class="left">
	  <div class="center">
<?php
$onchange='yes';
include('scripts/list_form.php');
?>
	  </div>
	</fieldset>

	  <table class="listmenu center" id="sidtable">
		<tr>
		  <th colspan="2"></th>
		  <th>Add</th>
		  <th>Delivered</th>
		  <th>Return</th>
		  <th>Paid</th>
		</tr>
<?php

	$com=get_community($newfid);
	$students=(array)listin_community($com);
	$rown=1;
	foreach($students as $student){
		$sid=$student['id'];

		$d_f=mysql_query("SELECT SUM(quantity) FROM fees_charge WHERE student_id='$sid' AND budget_id='$budid' AND catalogue_id='$catid' AND payment='0';");
		$delivered=mysql_result($d_f,0);
		$d_f=mysql_query("SELECT SUM(quantity) FROM fees_charge WHERE student_id='$sid' AND budget_id='$budid' AND catalogue_id='$catid' AND payment='1';");
		$paid=mysql_result($d_f,0);

		print '<tr id="sid-'.$sid.'">';
		print '<td>'.$rown++.'</td>';
		print '<td>'.$student['surname']. ', '.$student['forename'].' '.$student['preferredforename'].'</td>';
		print '<td>';

		$listname='add'.$sid;
		$listlabel='';
		include('scripts/set_list_vars.php');
		list_select_list($manys,$listoptions);
		print '</td>';
		print '<td class="row">'.$delivered.'</td><td>';
		if($delivered>0){
			print '<input type="checkbox" name="remove'.$sid.'" value="'.$sid.'" />';
			}
		print '</td><td>'.$paid.'</td>';
		print '</tr>';
		}

?>
	  </table>

	
	<input type="hidden" name="budgetyear" value="<?php print $budgetyear;?>" />
	<input type="hidden" name="budid" value="<?php print $budid;?>" />
	<input type="hidden" name="catid" value="<?php print $catid;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
  </form>

  </div>

