<?php
/**                                  fees_invoice_list.php
 *
 */

$action='fees_invoice_list_action.php';
$choice='fees_remittance_list.php';

include('scripts/sub_action.php');

if((isset($_POST['remid']) and $_POST['remid']!='')){$remid=$_POST['remid'];}else{$remid='';}
if((isset($_GET['remid']) and $_GET['remid']!='')){$remid=$_GET['remid'];}

if((isset($_POST['paymenttype']) and $_POST['paymenttype']!='')){$filter_paymenttype=$_POST['paymenttype'];}else{$filter_paymenttype='';}
if((isset($_GET['paymenttype']) and $_GET['paymenttype']!='')){$filter_paymenttype=$_GET['paymenttype'];}

if((isset($_POST['invoicenumber']) and $_POST['invoicenumber']!='')){$invoicenumber=$_POST['invoicenumber'];}else{$invoicenumber='';}
if((isset($_GET['invoicenumber']) and $_GET['invoicenumber']!='')){$invoicenumber=$_GET['invoicenumber'];}

$extrabuttons=array();
$extrabuttons['previewselected']=array('name'=>'current',
									   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
									   'value'=>'fees_invoice_print.php',
									   'xmlcontainerid'=>'invoices',
									   'onclick'=>'checksidsAction(this)'
									   );
$extrabuttons['export']=array('name'=>'current',
							  'title'=>'export',
							  'value'=>'fees_invoice_export.php');


two_buttonmenu($extrabuttons,$book);

$Students=array();
if($remid!=''){
	$Remittance=fetchRemittance($remid);
	$invoices=(array)list_remittance_invoices($remid,$filter_paymenttype);
	}
else{
	$invoices=(array)list_invoices($invoicenumber);
	}

if($filter_paymenttype==''){
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
<?php
	}
?>

  <div id="viewcontent" class="content">

  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >

	<div class="center">
<?php
if($remid!=''){
?>
	  <fieldset class="divgroup right">
		<legend><?php print_string('remittance',$book);?></legend>
		<div>
		  <?php print $Remittance['Name']['value'].' ('.display_date($Remittance['IssueDate']['value']).')';?>
		</div>
		<div>
		  <?php print $Remittance['Account']['BankName']['value'].' ('.display_date($Remittance['PaymentDate']['value']).')';?>
		</div>
	  </fieldset>
<?php
	}
else{
	}
?>

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
		if(isset($_POST['startno'])){$startno=$_POST['startno'];$rown=$startno+1;}
		else{$rown=1;$startno=0;}
		$totalno=sizeof($invoices);
		$nextrowstep=90;
		if($startno>$totalno){$startno=$totalno-$nextrowstep;}
		if($startno<0){$startno=0;}
		$endno=$startno+$nextrowstep;
		if($endno>$totalno){$endno=$totalno;}
		for($entryno=$startno;$entryno<$endno;$entryno++){
			$invoice=$invoices[$entryno];

			$Invoice=(array)fetchFeesInvoice($invoice);
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
				/* Do this once only for a student... */
				$Student=(array)fetchStudent_short($sid);
				$Students[$sid]=$Student;
				}
			else{
				$Student=$Students[$sid];
				}

			$rowclass='';
			$payclass='';

			print '<tr id="sid-'.$Invoice['id_db'].'" '.$rowclass.'>';
			print '<td><input type="checkbox" name="sids[]" value="'.$Invoice['id_db'].'" />';
			print $rown++;
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
		<tr>
		<th colspan="6">&nbsp;</th>
		<th colspan="2">
<?php 
			$dstartno=$startno+1;
			print 'Show '. $dstartno.' - '.$endno.' of '.$totalno;
			$buttons=array();
			if($startno>0){
				$buttons['<']=array('title'=>'previous','name'=>'nextrow','value'=>'minus');
				}
			if($endno<$totalno){
				$buttons['>']=array('title'=>'next','name'=>'nextrow','value'=>'plus');
				}
			all_extrabuttons($buttons,'admin','processContent(this)')
?>
		</th>
		</tr>

	  </table>
	</div>

	<input type="hidden" name="startno" value="<?php print $startno;?>" />
	<input type="hidden" name="nextrowstep" value="<?php print $nextrowstep;?>" />
	<input type="hidden" name="remid" value="<?php print $remid;?>" />
	<input type="hidden" name="paymenttype" value="<?php print $filter_paymenttype;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
  </form>

  </div>
  <div id="xml-invoices" style="display:none;">
	<params>
	  <checkname>sids</checkname>
	  <transform>fees_invoice</transform>
	  <paper>portrait</paper>
	</params>
  </div>
