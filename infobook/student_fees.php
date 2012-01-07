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
		<legend><?php print get_string('current',$book);?></legend>
		<div>
		  <table>
			<tr>
			  <thead>
				<th style="width:30%;"></th>
				<th style="width:20%;"><?php print_string('tarif','admin');?></th>
				<th style="width:20%;"><?php print_string('amount','admin');?></th>
				<th style="width:20%;"><?php print_string('payment','admin');?></th>
				<th style="width:10%;"><?php print_string('paid','admin');?></th>
			  </thead>
			</tr>
<?php

	$charges=(array)list_student_charges($sid);
	$no=0;

	foreach($charges as $conid => $concept_charges){
		$no++;
		$Concept=fetchConcept($conid);
		$tarifs=array();
		foreach($Concept['Tarifs'] as $Tarif){
			$tarifs[$Tarif['id_db']]=$Tarif['Name']['value'];
			}

		foreach($concept_charges as $index => $c){
			${'tarif'.$c['id']}=$c['tarif_id'];
			${'paymenttype'.$c['id']}=$c['paymenttype'];
			${'payment'.$c['id']}=$c['payment'];
			print '<tr><td>'.$no.'.  '.$Concept['Name']['value'].'</td><td>';
			$listlabel='';
			$liststyle='width:8em;';
			$listname='tarif'.$c['id'];
			include('scripts/set_list_vars.php');
			list_select_list($tarifs,$listoptions,$book);
			print '</td><td>';
			print $c['amount'];
			print '</td><td>';

			$listlabel='';
			$liststyle='width:8em;';
			$listname='paymenttype'.$c['id'];
			include('scripts/set_list_vars.php');
			list_select_enum('paymenttype',$listoptions,'admin');
			print '</td><td>';

			print '<div>';
			print '<input type="radio" name="payment'.$c['id'].'"
						tabindex="'.$tab++.'" value="1" ';
			print '>'.$label.'</input></div>';

			print '</td></tr>';
			}
		}
?>

		<tr>
		  <td colspan="5">

		  <div class="rowaction" style="width:240px;">
<?php
		$listlabel='';
		$liststyle='width:16em;';
		$listname='newconceptid';
		$d_c=mysql_query("SELECT id, name  FROM fees_concept WHERE inactive='0' ORDER BY name;");
		include('scripts/set_list_vars.php');
		list_select_db($d_c,$listoptions,$book);
?>
		  </div>

		  <div class="rowaction">
<?php
	$buttons=array();
	$buttons['add']=array('name'=>'newconcept','value'=>'add');
	all_extrabuttons($buttons,'infobook','processContent(this)')
?>
		  </div>
		  </td>
		</tr>

		  </table>
		</div>
	  </fieldset>

	  <fieldset class="center listmenu">
		<legend><?php print get_string('previous',$book);?></legend>
		<div>
		  <table>
			<tr>
			  <thead>
				<th style="width:40%;></th>
				<th style="width:20%;"><?php print_string('tarif','admin');?></th>
				<th style="width:20%;"><?php print_string('amount','admin');?></th>
				<th style="width:20%;"><?php print_string('payment','admin');?></th>
				<th style="width:20%;"><?php print_string('date','admin');?></th>
			  </thead>
			</tr>
<?php
	$charges=(array)list_student_charges($sid,'1');
	foreach($charges as $conid => $charge){
		$Concept=fetchConcept($conid);
		$tarifs=array();
		foreach($Concept['Tarifs'] as $Tarif){
			$tarifs[$Tarif['id_db']]=$Tarif['Name']['value'];
			}
?>
<?php
		foreach($charge as $index => $c){
			print '<tr><td>'.$Concept['Name']['value'];
			print ' - '.$tarifs[$c['tarif_id']].'</td>';
			print '<td>'.$c['amount'].'</td>';
			print '<td>'.displayEnum($c['paymenttype'],'paymenttype').'</td>';
			print '<td>'.display_date($c['paymentdate']).'</td>';
			print '</tr>';
			}
		}
?>
			
		  </table>
		</div>
	  </fieldset>


	    <input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="cancel" value="<?php print $cancel;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
</div>
