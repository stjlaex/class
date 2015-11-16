<?php
/**                                  fees_remittance_view.php
 *
 */

$action='fees_remittance_view_action.php';
$choice='fees_remittance_list.php';

include('scripts/sub_action.php');

if((isset($_POST['conid']) and $_POST['conid']!='')){$conid=$_POST['conid'];}else{$conid=-1;}
if((isset($_GET['conid']) and $_GET['conid']!='')){$conid=$_GET['conid'];}
if((isset($_POST['remid']) and $_POST['remid']!='')){$remid=$_POST['remid'];}else{$remid='';}
if((isset($_GET['remid']) and $_GET['remid']!='')){$remid=$_GET['remid'];}
if((isset($_POST['payment']) and $_POST['payment']!='')){$payment=$_POST['payment'];}else{$payment='';}
if((isset($_GET['payment']) and $_GET['payment']!='')){$payment=$_GET['payment'];}
if((isset($_POST['paymenttype']) and $_POST['paymenttype']!='')){$filter_paymenttype=$_POST['paymenttype'];}else{$filter_paymenttype='';}
if((isset($_GET['paymenttype']) and $_GET['paymenttype']!='')){$filter_paymenttype=$_GET['paymenttype'];}


$extrabuttons=array();
if($conid==-1 and ($payment==1 or $payment==2)){
/*
TODO: print invoices from here....
	$extrabuttons['invoice']=array('name'=>'current',
								   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
								   'value'=>'fees_invoice_print.php',
								   'xmlcontainerid'=>'invoices',
								   'onclick'=>'checksidsAction(this)'
								   );
*/
	$extrabuttons['export']=array('name'=>'current',
								  'title'=>'export',
								  'value'=>'fees_remittance_charge_export.php');
	}
elseif($conid==-1 and $payment==''){
	$extrabuttons['chargeexport']=array('name'=>'current',
										'title'=>'export',
										'value'=>'fees_remittance_concept_export.php');
	}


two_buttonmenu($extrabuttons,$book);


$Remittance=fetchRemittance($remid);
$Concepts=array();
$Tarifs=array();
foreach($Remittance['Concepts'] as $Concept){
	$Concepts[$Concept['id_db']]=$Concept;
	foreach($Concept['Tarifs'] as $Tarif){
		$Tarifs[$Tarif['id_db']]=$Tarif;
		}
	}


$Students=array();
$conids=array();
if($conid==-1){
	/*
	foreach($Remittance['Concepts'] as $Concept){
		$conids[]=$Concept['id_db'];
		}
	*/
	$conids[]='';
	}
else{
	$conids[]=$conid;
	}

if($filter_paymenttype==''){
?>
<div id="heading">
<?php
	$listname='filtervalue';$listlabel='paymenttype';
	//$listdescriptionfield='name';$listvaluefield='value';
	$listlabelstyle='internal';
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
<!--
	<fieldset class="divgroup left">
	<div>
	</div>
	</fieldset>
-->
	  <fieldset class="divgroup right">
		<legend><?php print_string('remittance',$book);?></legend>
		<div>
		  <?php print $Remittance['Name']['value'].' ('.display_date($Remittance['IssueDate']['value']).')';?>
		</div>
		<div>
		  <?php print $Remittance['Account']['BankName']['value'].' ('.display_date($Remittance['PaymentDate']['value']).')';?>
		</div>
		<div style="float:right;">
<?php
if($payment==''){
	$morebuttons['message']=array('name'=>'current',
								   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
								   'value'=>'message.php',
								   'xmlcontainerid'=>'messageremittance',
								   'onclick'=>'checksidsAction(this)'
								   );
	all_extrabuttons($morebuttons,'entrybook','sidtableFilter(this)');
	}
?>
		</div>
	  </fieldset>


	  <table id="sidtable" class="listmenu sidtable">
		<thead>
		  <tr>
			<th colspan="2" class="checkall">
			  <input type="checkbox" name="checkall"  value="yes" onChange="checkAll(this);" />
			</th>
			<th colspan="7">&nbsp;</th>
		  </tr>
		</thead>
<?php
	foreach($conids as $conid){

		$charges=(array)list_remittance_charges($remid,$conid,$payment);
?>
		<thead>
		  <tr>
			<th colspan="2">&nbsp;</th>
			<th colspan="9">
<?php
			if($conid!=''){
				$Concept=(array)$Concepts[$conid];
				print $Concept['Name']['value']. ' <div class="right">'.display_money($Concept['TotalAmount']['value']).'</div>';
				}
?>
			</th>
		  </tr>
		  <tr>
			<th colspan="2">&nbsp;</th>
			<th>
			  <?php print_string('enrolmentnumber','infobook');?>
			</th>
			<th colspan="2">
			  <?php print_string('student',$book);?>
			</th>
			<th>
			  <?php print_string('tarif',$book);?>
			</th>
			<th>
			  <?php print_string('payment',$book);?>
			</th>
			<th colspan="2">
			  <?php print_string('amount',$book);?>
			</th>
			<th colspan="2">
			  <?php print_string('type',$book);?>
			</th>
		  </tr>
		</thead>
		<tbody>
<?php
		$rown=1;
		foreach($charges as $charge){
			if($charge['paymenttype']==$filter_paymenttype or $filter_paymenttype==''){
				$sid=$charge['student_id'];
				if($charge['payment']=='1'){
					$rowclass='class="lowlite"';
					}
				else{
					$rowclass='class=""';
					}
				if(!array_key_exists($sid,$Students)){
					/* Do certain things once only for a student... */
					$Student=(array)fetchStudent_short($sid);
					$guardians=(array)list_student_payees($sid);
					if(sizeof($guardians)>0 and $guardians[0]['paymenttype']>0){
						$Student['payee']=$guardians[0];
						$Student['paymenttype']=$guardians[0]['paymenttype'];
						$Student['accountsno']=$guardians[0]['accountsno'];
						}
					else{
						$Student['payee']='';
						$Student['paymenttype']='';
						$Student['accountsno']=0;
						}
					$Students[$sid]=$Student;
					$first=1;
					}
				else{
					$Student=$Students[$sid];
					$first++;
					}


				/* Warn when no payee is set or the payment type is bank and the payee has no valid bank account. */
				$payclass='';$payspan='';
				if($Student['paymenttype']==''){$payclass='class="hilite"';$payspan=get_string('nopayeeset',$book);}
				elseif($charge['paymenttype']=='1' and $Student['accountsno']==0){$payclass='class="midlite"';$payspan=get_string('novalidbankaccount',$book);}

				if($conid=='' and $payment!=''){
					$rowid=$charge['id'];
					}
				else{
					$rowid=$sid;
					}

				/* first entry for this student for this concept (group by concept and then by student in on other words) */
				print '<tr id="sid-'.$rowid.'" '.$rowclass.'>';
				print '<td><input type="checkbox" name="sids[]" value="'.$rowid.'" /></td>';
				print '<td>'.$rown++.'</td>';
				print '<td>'.$Student['EnrolNumber']['value'].'</td>';
				print '<td class="student"><a target="viewinfobook" onclick="parent.viewBook(\'infobook\');" href="infobook.php?current=student_fees.php&cancel=student_view.php&sids[]='.$sid.'&sid='.$sid.'">'.$Student['DisplayFullSurname']['value'].'</a></td>';
				print '<td>'.$Student['TutorGroup']['value'].'</td>';
				print '<td>'.$Tarifs[$charge['tarif_id']]['Name']['value'].'</td>';
				print '<td>'.get_string(displayEnum($charge['paymenttype'],'paymenttype'),$book).'</td>';
				if($payclass!=''){
					print '<td '.$payclass.' ><span title="'.$payspan.'">'.display_money($charge['amount']).'</span></td>';
					}
				else{
					print '<td>'.display_money($charge['amount']).'</td>';
					}
				print '<td style="width:1em;">'.'<div class="hidden">';
				$listname='paymenttype'.$rowid;
				${'paymenttype'.$rowid}=$charge['paymenttype'];
				include('scripts/list_paymenttypes.php');
				print '</div></td>';
				print '<td>'.get_string(displayEnum($charge['payment'],'payment'),$book).'</td></tr>';

				}
			}
?>
		</tbody>
<?php
		}
?>
	<tfoot class="noprint">
		<tr>
		  <th colspan="2">
			<div class="rowaction">
<?php
$buttons=array();
if($conid=='' and ($payment==1)){
	$buttons['notpaid']=array('name'=>'sub','value'=>'notpaid');
	}
elseif($conid=='' and ($payment==2)){
	$buttons['paid']=array('name'=>'sub','value'=>'paid');
	}
all_extrabuttons($buttons,'infobook','processContent(this)');
?>
		  </div>
		</th>
		<th colspan="7">
		</th>
		</tr>
	  </tfoot>
	  </table>
	</div>

	<input type="hidden" name="payment" value="<?php print $payment;?>" />
	<input type="hidden" name="paymenttype" value="<?php print $filter_paymenttype;?>" />
	<input type="hidden" name="conid" value="<?php print $conid;?>" />
	<input type="hidden" name="remid" value="<?php print $remid;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
  </form>

  </div>
  <div id="xml-invoices" style="display:none;">
	<params>
	  <checkname>sids</checkname>
	  <length>short</length>
	  <transform>fees_invoice</transform>
	  <paper>portrait</paper>
	</params>
  </div>
  <div id="xml-messageremittance" style="display:none;">
	<params>
	  <checkname>remids</checkname>
	  <messagetype>remittance</messagetype>
	  <remids><?php print $remid;?></remids>
	  <conids><?php print $conid;?></conids>
	  <payment><?php print $payment;?></payment>
	  <paymenttype><?php print $filter_paymenttype;?></paymenttype>
	</params>
  </div>
