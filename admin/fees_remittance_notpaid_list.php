<?php
/**									fees_remittance_notpaid_list.php
 */

$action='fees.php';

if(isset($_GET['paymenttype']) and $_GET['paymenttype']!=''){$paymenttype=$_GET['paymenttype'];}else{$paymenttype='';}
if(isset($_GET['yeargroup']) and $_GET['yeargroup']!=''){$yeargroup=$_GET['yeargroup'];}else{$yeargroup='';}

include('scripts/sub_action.php');

$extrabuttons['export']=array('name'=>'current','value'=>'fees_remittance_notpaid_export.php');

two_buttonmenu($extrabuttons,$book);
?>
  <div id="heading">
<?php
	$listname='yeargroupoptions';$listlabel='yeargroup';
	if($yeargroup!=''){$selyeargroupoptions=$yeargroup;}
	$onchangeaction="document.location.href='admin.php?current=fees_remittance_notpaid_list.php&yeargroup='+this.value+'&paymenttype='+document.getElementById('Paymenttypeoptions').value";
	include('scripts/set_list_vars.php');
	$yearoptions=list_yeargroups();
	list_select_list($yearoptions,$listoptions,'admin');

	$listname='paymenttypeoptions';$listlabel='paymenttype';
	$selpaymenttypeoptions=$paymenttype;
	$onchangeaction="document.location.href='admin.php?current=fees_remittance_notpaid_list.php&paymenttype='+this.value+'&yeargroup='+document.getElementById('Yeargroupoptions').value";
	include('scripts/set_list_vars.php');
	$paymenttypeoptions=getEnumArray('paymenttype');
	$paymenttypeoptions['%']='all';
	list_select_list($paymenttypeoptions,$listoptions,'admin');
?>
  </div>

  <div id="viewcontent" class="content">

  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >

	<div class="center">
	  <table class="listmenu">
		<caption><?php print_string('notpaidlist',$book); if($paymenttype!='' and $paymenttype!='%'){echo " (".get_string($paymenttypeoptions[$paymenttype],$book).")";}?></caption>
		<tbody>
			<tr>
				<th class="checkall"><input type="checkbox" name="checkall" value="yes" onChange="checkAll(this);" /></th>
				<th style="width:25%;"><?php print get_string('student',$book);?></th>
				<th><?php print get_string('date',$book);?></th>
				<th><?php print_string('remittance',$book);?></th>
				<th><?php print_string('tarif',$book);?></th>
				<th><?php print_string('paymenttype',$book);?></th>
				<th><?php print_string('amount',$book);?></th>
			</tr>
		</tbody>
<?php
		$entryno=0;
		$year=get_curriculumyear();
		if($yeargroup!=''){$yeargroup=" AND student.yeargroup_id LIKE '$yeargroup' ";}
		if($paymenttype!='' and $paymenttype!='%'){$ptype=" AND fees_charge.paymenttype='$paymenttype' ";}else{$ptype='';}
		$d_c=mysql_query("SELECT fees_charge.id,fees_charge.student_id,fees_charge.amount,fees_charge.tarif_id, fees_charge.remittance_id,fees_charge.paymenttype FROM fees_charge JOIN fees_remittance ON fees_remittance.id=fees_charge.remittance_id JOIN student ON fees_charge.student_id=student.id WHERE fees_charge.payment='2' AND fees_remittance.year='$year' $yeargroup $ptype ORDER BY student_id ASC, paymentdate ASC;");
		while($charge=mysql_fetch_array($d_c)){
			$chargeid=$charge['id'];
			$remid=$charge['remittance_id'];
			$chargeamount=$charge['amount'];
			$tarifid=$charge['tarif_id'];
			$sid=$charge['student_id'];
			$paytype=$charge['paymenttype'];
			$Student=fetchStudent_short($sid);
			$Remittance=fetchRemittance($remid);
			$d_t=mysql_query("SELECT * FROM fees_tarif WHERE id='$tarifid';");
			$tarifname=mysql_result($d_t,0,'name');
			$entryno++;
			$rown=0;
?>
		<tbody id="<?php print $entryno;?>">
		  <tr id="<?php print $entryno.'-'.$rown++;?>">
			<td >
<?php 
			echo "<input type='checkbox' name='ids[]' value='$sid-$chargeid'>";
?>
			</td>
			<td class="student">
				<a href="infobook.php?current=student_fees.php&cancel=student_view.php&sids[]=<?php echo $sid;?>&sid=<?php echo $sid;?>" onclick="parent.viewBook('infobook');" target="viewinfobook">
					<?php echo $Student['DisplayFullName']['value'].' ('.$Student['RegistrationGroup']['value'].')';?>
				</a>
			</td>
			<td><?php echo $Remittance['IssueDate']['value'];?></td>
			<td><?php echo $Remittance['Name']['value'];?></td>
			<td><?php echo $tarifname;?></td>
			<td><?php print_string($paymenttypeoptions[$paytype],$book);?></td>
			<td><?php echo $chargeamount;?></td>
		  </tr>
		</tbody>
<?php
			}
?>
	  </table>
	</div>

	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
  </form>

  </div>

