<?php
/**                                  fees_remittance_view.php
 *
 */

$action='fees_remittance_view_action.php';

include('scripts/sub_action.php');

if((isset($_POST['conid']) and $_POST['conid']!='')){$conid=$_POST['conid'];}else{$conid=-1;}
if((isset($_GET['conid']) and $_GET['conid']!='')){$conid=$_GET['conid'];}
if((isset($_POST['remid']) and $_POST['remid']!='')){$remid=$_POST['remid'];}else{$remid='';}
if((isset($_GET['remid']) and $_GET['remid']!='')){$remid=$_GET['remid'];}


$extrabuttons=array();
$extrabuttons['export']=array('name'=>'current',
							   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
							   'value'=>'transport_print.php',
							   'xmlcontainerid'=>'changes',
							   'onclick'=>'checksidsAction(this)');
three_buttonmenu($extrabuttons,$book);



$charges=(array)list_remittance_charges($remid,$conid);
$Remittance=fetchRemittance($remid);

/*$Tarifs=(array)$Concept['Tarifs'];
$tarifs=array();
foreach($Tarifs as $Tarif){
	$tarifs[$Tarif['id_db']]=$Tarif['Name']['value'];
	}
*/
?>
  <div id="heading">
	<label><?php print_string('remittance',$book);?></label>
  </div>

  <div id="viewcontent" class="content">

  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >

	<div class="center">
	  <table class="listmenu sidtable">
	<caption><?php print $Remittance['Name']['value'].' '.display_date($Remittance['EntryDate']['value']);?></caption>
		<thead>
		  <tr>
			<th colspan="5">&nbsp;</th>
			<th>
<?php print_string('amount',$book);?>
			</th>
		  </tr>
		</thead>
<?php
	$rown=1;
	foreach($charges as $charge){
		$sid=$charge['student_id'];
		$Student=(array)fetchStudent_short($sid);
		print '<tr id="sid-'.$sid.'">';
		print '<td>'.'<input type="checkbox" name="sids[]" value="'.$sid.'" />'.$rown++.'</td><td></td>';
		print '<td>'.$Student['EnrolNumber']['value'].'</td>';
		print '<td class="student"><a target="viewinfobook" onclick="parent.viewBook(\'infobook\');" href="infobook.php?current=student_fees.php&sid='.$sid.'">'.$Student['DisplayFullSurname']['value'].'</a></td>';
		print '<td>'.$Student['RegistrationGroup']['value'].'</td>';
		print '<td>';
		print $charge['amount'];
		print '</td>';
		}
?>
	  </table>
	</div>

	<input type="hidden" name="conid" value="<?php print $conid;?>" />
	<input type="hidden" name="remid" value="<?php print $remid;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
  </form>

  </div>
