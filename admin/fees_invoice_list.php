<?php
/**                                  fees_invoice_list.php
 *
 */

$action='fees_invoice_list_action.php';
$choice='fees_remittance_list.php';

include('scripts/sub_action.php');

if((isset($_POST['remid']) and $_POST['remid']!='')){$remid=$_POST['remid'];}else{$remid='';}
if((isset($_GET['remid']) and $_GET['remid']!='')){$remid=$_GET['remid'];}

$extrabuttons=array();
$extrabuttons['previewselected']=array('name'=>'current',
									   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
									   'value'=>'fees_invoice_print.php',
									   'xmlcontainerid'=>'invoices',
									   'onclick'=>'checksidsAction(this)'
									   );


two_buttonmenu($extrabuttons,$book);


$Remittance=fetchRemittance($remid);
$Students=array();
$invoices=(array)list_remittance_invoices($remid);

?>
  <div id="heading">
<?php
	$listname='filtervalue';$listlabel='';
	//$listdescriptionfield='name';$listvaluefield='value';
	include('scripts/set_list_vars.php');
	list_select_enum('paymenttype',$listoptions,'admin');
	$button['filterlist']=array('name'=>'filter','value'=>'paymenttype');
	all_extrabuttons($button,'entrybook','sidtableFilter(this)');
?>
  </div>

  <div id="viewcontent" class="content">

  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >

	<div class="center">
	  <fieldset class="divgroup right">
		<legend><?php print_string('remittance',$book);?></legend>
		<div>
		  <?php print $Remittance['Name']['value'].' ('.display_date($Remittance['IssueDate']['value']).')';?>
		</div>
		<div>
		  <?php print $Remittance['Account']['BankName']['value'].' ('.display_date($Remittance['PaymentDate']['value']).')';?>
		</div>
	  </fieldset>


	  <table id="sidtable" class="listmenu sidtable">
		<thead>
		  <tr>
			<th colspan="2">
			  <input type="checkbox" name="checkall"  value="yes" onChange="checkAll(this);" />
			  <?php print_string('checkall'); ?>
			</th>
			<th colspan="6">&nbsp;</th>
		  </tr>
		</thead>
		<thead>
		  <tr>
			<th colspan="8">&nbsp;</th>
		  </tr>
		  <tr>
			<th colspan="6"></th>
			<th>
			  <?php print_string('payment',$book);?>
			</th>
			<th>
			  <?php print_string('amount',$book);?>
			</th>
		  </tr>
		</thead>
		<tbody>
<?php
		$rown=1;
		foreach($invoices as $invoice){
			$Invoice=fetchFeesInvoice($invoice);
			$sid=$Invoice['student_id_db'];
			/*
			if($charge['payment']=='1'){
				$rowclass='class="lowlite"';
				}
			else{
				$rowclass='class=""';
				}
			*/
			if(!array_key_exists($sid,$Students)){
				/* Do certain things once only for a student... */
				$Student=(array)fetchStudent_short($sid);
				$guardians=(array)list_student_payees($sid);
				$Students[$sid]=$Student;
				}

			$rowclass='';
			$payclass='';

			/* first entry for this student for this concept (group by concept and then by student in on other words) */
			print '<tr id="sid-'.$Invoice['id_db'].'" '.$rowclass.'>';
			print '<td><input type="checkbox" name="sids[]" value="'.$Invoice['id_db'].'" />';
			//print $rown++;
			print '</td>';
			print '<td></td><td>'.$Invoice['Reference']['value'].'</td><td></td>';
			print '<td class="student"><a target="viewinfobook" onclick="parent.viewBook(\'infobook\');" href="infobook.php?current=student_fees.php&cancel=student_view.php&sids[]='.$sid.'&sid='.$sid.'">'.$Student['DisplayFullSurname']['value'].'</a></td>';
			print '<td>'.$Student['RegistrationGroup']['value'].'</td>';
			print '<td>'.'<div class="hidden">';
			$listname='paymenttype'.$Invoice['id_db'];
			${'paymenttype'.$Invoice['id_db']}=$Invoice['PaymentType']['value'];
			include('scripts/list_paymenttypes.php');
			print '</div>';
			print get_string(displayEnum($Invoice['PaymentType']['value'],'paymenttype'),$book).'</td>';
			print '<td '.$payclass.'>'.display_money($Invoice['TotalAmount']['value']).'</td></tr>';

			}
?>
		</tbody>
	  </table>
	</div>

	<input type="hidden" name="remid" value="<?php print $remid;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
  </form>

  </div>
  <div id="xml-invoices" style="display:none;">
	<params>
	  <checkname>sids</checkname>
	  <transform>order_form</transform>
	  <paper>portrait</paper>
	</params>
  </div>
