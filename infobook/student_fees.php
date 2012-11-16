<?php
/**
 *                                  student_fees.php
 */

$action='student_fees_action.php';
require_once('lib/fetch_fees.php');


$extrabuttons=array();
three_buttonmenu($extrabuttons);

/*Check user has permission to view*/
$perm=getFormPerm($Student['RegistrationGroup']['value']);
include('scripts/perm_action.php');

?>
  <div id="heading">
	<?php print $Student['Forename']['value'].' '.$Student['Surname']['value'];?>
  </div>

  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="center listmenu">
		<legend><?php print get_string('payee','admin');?></legend>
		<div class="left">
<?php 
		$listlabel='account';
		$listname='gid';
		$required='yes';
		$guardians=(array)list_student_payees($sid);
		if(sizeof($guardians)>0 and $guardians[0]['paymenttype']>0){
			/*the first in the list will be the selected payee*/
			$selgid=$guardians[0]['id'];
			$selpaytype=$guardians[0]['paymenttype'];
			}
		include('scripts/set_list_vars.php');
		list_select_list($guardians,$listoptions,'admin');
?>
		</div>
		<div class="right">
		  <?php include('scripts/list_paymenttypes.php');?>
		</div>
	  </fieldset>

	  <fieldset class="center listmenu">
		<legend><?php print get_string('fees','admin'). ' '.get_string('applied','admin');?></legend>
		<div>
		  <table class="listmenu">
			<tr>
			  <thead>
				<th colspan="2" style="width:40%;">
				</th>
				<th style="width:20%;"><?php print_string('amount','admin');?></th>
				<th><?php print_string('tarif','admin');?></th>
				<th><?php print_string('payment','admin');?></th>
			  </thead>
			</tr>
<?php

	$fees=(array)list_student_fees($sid);
	$no=0;

	foreach($fees as $conid => $concept_fees){
		$no++;
		$Concept=fetchConcept($conid);
		$tarifs=array();
		foreach($Concept['Tarifs'] as $Tarif){
			$tarifs[$Tarif['id_db']]=$Tarif['Name']['value'];
			}

		foreach($concept_fees as $f){
			${'feetarif'.$f['id']}=$f['tarif_id'];
			${'feepaymenttype'.$f['id']}=$f['paymenttype'];
			print '<tr><td><input type="checkbox" name="feeids[]" value="'.$f['id'].'" />'.$no.'</td>';
			print '<td>'.$Concept['Name']['value'].'</td><td>';
			print display_money($f['amount']);
			print '</td><td>';
			$listlabel='';
			$liststyle='width:16em;';
			$listname='feetarif'.$f['id'];
			include('scripts/set_list_vars.php');
			list_select_list($tarifs,$listoptions,$book);
			print '</td><td>';
			$listlabel='';
			$liststyle='width:5em;';
			$listname='feepaymenttype'.$f['id'];
			include('scripts/set_list_vars.php');
			list_select_enum('paymenttype',$listoptions,'admin');
			print '</td>';
			print '</tr>';
			}
		}
?>
			<thead>
			  <th>
				<div class="rowaction">
<?php
					$buttons=array();
					$buttons['delete']=array('name'=>'oldfees','value'=>'delete');
					all_extrabuttons($buttons,'infobook','processContent(this)')
?>
				</div>
			  </th>
			  <th colspan="4">
				<div class="rowaction" style="width:240px;">
<?php
		$listlabel='';
		$liststyle='width:16em;';
		$listname='newconceptid';
		$d_c=mysql_query("SELECT id, name  FROM fees_concept WHERE inactive='0' ORDER BY name;");
		include('scripts/set_list_vars.php');
		list_select_db($d_c,$listoptions,$book);
?>
<?php
	$buttons=array();
	$buttons['add']=array('name'=>'newconcept','value'=>'add');
	all_extrabuttons($buttons,'infobook','processContent(this)')
?>
				</div>
			  </th>
			</thead>
		  </table>
		</div>
	  </fieldset>

<?php
	$charge_lists=array();
	$charge_lists['due']=(array)list_student_charges($sid,'P');
	$charge_lists['paid']=(array)list_student_charges($sid,1);
   	$charge_lists['notpaid']=(array)list_student_charges($sid,2);
	foreach($charge_lists as $paymentstatus => $charges){

?>

	  <fieldset class="center listmenu">
		<legend><?php print get_string('charges','admin').' '. get_string($paymentstatus,'admin');?></legend>
		<div>
		  <table>
			<tr>
			  <thead>
				<th colspan="2" style="width:60%;"></th>
				<th style="width:20%;"><?php print_string('amount','admin');?></th>
				<th colspan="2"><?php print_string('payment','admin');?></th>
			  </thead>
			</tr>
<?php
			foreach($charges as $conid => $charge){
				$Concept=fetchConcept($conid);
				$tarifs=array();
				foreach($Concept['Tarifs'] as $Tarif){
					$tarifs[$Tarif['id_db']]=$Tarif['Name']['value'];
					}

				foreach($charge as $c){

					if($c['remittance_id']>0){
						$remittance=get_remittance($c['remittance_id']);
						$description=$remittance['name'].'<br />'.$Concept['Name']['value'].' - '.$tarifs[$c['tarif_id']];
						$displaydate=$remittance['duedate'];
						}
					else{
						$remittance=array();
						$description=$Concept['Name']['value'].' - '.$tarifs[$c['tarif_id']];
						$displaydate=$c['paymentdate'];
						}

					print '<tr><td>'.$description.'</td>';
					print '<td>'.display_date($displaydate).'</td>';
					print '<td style="text-align:center;">'.display_money($c['amount']).'</td>';
					print '<td>'.get_string(displayEnum($c['paymenttype'],'paymenttype'),'admin').'</td>';


					if($c['payment']=='2'){$checked='checked="yes"';$checkclass='checked';}else{$checked='';$checkclass='';}
					print '<td><div class="'.$checkclass.'"><label>'.get_string('notpaid','admin').'</label>';
					print '<input type="radio" name="payment'.$c['id'].'" tabindex="'.$tab++.'" value="2" '.$checked.'>'.$label.'</input></div>';
					if($c['payment']=='1'){$checked='checked="yes"';$checkclass='checked';}else{$checked='';$checkclass='';}
					print '<div class="'.$checkclass.'"><label>'.get_string('paid','admin').'</label>';
					print '<input type="radio" name="payment'.$c['id'].'" tabindex="'.$tab++.'" value="1" '.$checked.'>'.$label.'</input></div>';
					print '</td></tr>';
					}
				}
?>
			
		  </table>
		</div>
	  </fieldset>
<?php
		}
?>

	    <input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="cancel" value="<?php print $cancel;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
</div>
