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


$extrabuttons=array();
if($conid==-1){
	$extrabuttons['invoice']=array('name'=>'current',
								   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
								   'value'=>'fees_invoice_print.php',
								   'xmlcontainerid'=>'invoices',
								   'onclick'=>'checksidsAction(this)'
								   );
	$extrabuttons['export']=array('name'=>'current',
								  'value'=>'fees_remittance_export.php'
								  );
/*
  $extrabuttons['export']=array('name'=>'current',
							  'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
							  'value'=>'fees_remittance_export.php',
							  'xmlcontainerid'=>'changes',
							  'onclick'=>'checksidsAction(this)');
*/
	}


two_buttonmenu($extrabuttons,$book);


$Remittance=fetchRemittance($remid);
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
	  <fieldset class="right">
		<legend><?php print_string('remittance',$book);?></legend>
		<div>
		  <?php print $Remittance['Name']['value'].' '.display_date($Remittance['IssueDate']['value']);?>
		</div>
	  </fieldset>


	  <table id="sidtable" class="listmenu sidtable">
		<thead>
		  <tr>
			<th colspan="2">
			  <input type="checkbox" name="checkall"  value="yes" onChange="checkAll(this);" />
			  <?php print_string('checkall'); ?>
			</th>
			<th colspan="5">&nbsp;</th>
		  </tr>
		</thead>
<?php
	foreach($conids as $conid){
		$concept=get_concept($conid);
		$charges=(array)list_remittance_charges($remid,$conid);
?>
		<thead>
		  <tr>
			<th colspan="7">&nbsp;</th>
		  </tr>
		  <tr>
			<th colspan="5"><?php print $concept['name'];?></th>
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
		foreach($charges as $charge){
			$sid=$charge['student_id'];
			/* Do certain things once only for a student... */
			if(!array_key_exists($sid,$Students)){
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

			/* Warn when no payee is set or the payment type is bank and the payee has no bank account. */
			$payclass='';
			if($Student['paymenttype']==''){$payclass='class="hilite"';}
			elseif($charge['paymenttype']=='1' and $Student['accountsno']==0){$payclass='class="midlite"';}

			/* first entry for this student for this concept (group by concept and then by student in on other words) */
			print '<tr id="sid-'.$charge['id'].'">';
			print '<td><input type="checkbox" name="sids[]" value="'.$charge['id'].'" />';
			print $rown++;
			print '</td><td></td>';
			print '<td>'.$Student['EnrolNumber']['value'].'</td>';
			print '<td class="student"><a target="viewinfobook" onclick="parent.viewBook(\'infobook\');" href="infobook.php?current=student_fees.php&cancel=student_view.php&sids[]='.$sid.'&sid='.$sid.'">'.$Student['DisplayFullSurname']['value'].'</a></td>';
			print '<td>'.$Student['RegistrationGroup']['value'].'</td>';
			print '<td>'.'<div class="hidden">';
			$listname='paymenttype'.$charge['id'];
			${'paymenttype'.$charge['id']}=$charge['paymenttype'];
			include('scripts/list_paymenttypes.php');
			print '</div>';
			print get_string(displayEnum($charge['paymenttype'],'paymenttype'),$book).'</td>';
			print '<td '.$payclass.'>'.display_money($charge['amount']).'</td></tr>';
			}
?>
		</tbody>
<?php
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
  <div id="xml-invoices" style="display:none;">
	<params>
	  <checkname>sids</checkname>
	  <length>short</length>
	  <transform>fees_invoice_form</transform>
	  <paper>portrait</paper>
	</params>
  </div>
