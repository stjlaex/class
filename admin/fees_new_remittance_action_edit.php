<?php
/**			  					fees_new_remittance_action_edit.php
 */

$action='fees_new_remittance_action_edit.php';
$choice='fees_remittance_list.php';

include('scripts/sub_action.php');

if($sub=='Submit'){
	$action='fees_remittance_list.php';
	$sids=$_POST['sids'];
	$conids=$_SESSION['conidsvars'];
	$remid=$_SESSION['remidvar'];
	$enrolstatus=$_SESSION['enrolstatusvar'];

	foreach($conids as $conid){
		$concept=get_concept($conid);

		if($concept['community_type']=='' and $enrolstatus=='C'){
			foreach($sids as $sid){
				mysql_query("INSERT INTO fees_charge (student_id, remittance_id, tarif_id, paymenttype, amount) 
					SELECT a.student_id, '$remid', a.tarif_id, a.paymenttype, t.amount FROM fees_applied AS a, 
					fees_tarif AS t WHERE t.concept_id='$conid' AND t.id=a.tarif_id AND a.student_id='$sid';");
				//$result[]="Inserted ".mysql_insert_id()." >> ".$remid."-".$sid."-".$conid."<br>";
				}
			}
		elseif($enrolstatus!='C'){
			if($enrolstatus=='EN'){$community_type='enquired';}
			elseif($enrolstatus=='AC'){$community_type='accepted';}
			else{$community_type='applied';}

			foreach($sids as $sid){
				mysql_query("INSERT INTO fees_charge (student_id, remittance_id, tarif_id, paymenttype, amount) 
							SELECT a.student_id, '$remid', a.tarif_id, a.paymenttype, t.amount FROM fees_applied AS a, 
							fees_tarif AS t WHERE t.concept_id='$conid' AND t.id=a.tarif_id AND a.student_id='$sid';");
				//$result[]="Inserted ".mysql_insert_id()." >> ".$remid."-".$sid."-".$conid."<br>";
				}
			}
		else{
			$comtype=$concept['community_type'];
			$communities=list_communities($comtype);
			foreach($sids as $sid){
				foreach($communities as $community){
					$communityid=$community['id'];
					$community=(array)get_community($communityid);
					$students=(array)listin_community($community);
					foreach($students as $student){
						if($student['id']==$sid){
							$com=$community;
							$comid=$communityid;
							$special=$student['special'];
							}
						}
					}
				$d_c=mysql_query("SELECT id FROM fees_charge 
						 WHERE student_id='$sid' AND remittance_id='$remid' AND community_id='$comid';");
				if(mysql_num_rows($d_c)>0){
					$charid=mysql_result($d_c);
					}
				else{
					if($special=='' or $special=='10'){$rate=1;}else{$rate=$special/10;};
					$amount=$com['charge']*$rate;
					add_student_community_charge($sid,$comid,$remid,$amount);
					}
				//$result[]="Inserted ".mysql_insert_id()." >> ".$remid."-".$sid."-".$comid."-".$amount."<br>";
				}
			}
		}
		
	unset($_SESSION['sidsvars']);
	unset($_SESSION['conidsvars']);
	unset($_SESSION['enrolstatusvar']);
	unset($_SESSION['remidvar']);

	include('scripts/results.php');
	include('scripts/redirect.php');
}
else{

	$sids=$_SESSION['sidsvars'];
	$conids=$_SESSION['conidsvars'];

	three_buttonmenu();
?>
<div id="viewcontent" class="content">
  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >

	<div class="center">
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
			<th colspan="2">&nbsp;</th>
			<th colspan="3">
			  <?php print_string('student',$book);?>
			</th>
			<th>
			  <?php print_string('enrolmentnumber','infobook');?>
			</th>
			<th>
			  <?php print_string('formgroup',$book);?>
			</th>
			<th>
			  <?php print_string('amount',$book);?>
			</th>
		  </tr>
		</thead>
		<tbody>
<?php
		$rown=1;$startno=0;
		foreach($sids as $sid){
			$Student=(array)fetchStudent_short($sid);
			$Students[$sid]=$Student;

			$rowclass='';
			$payclass='';

			print '<tr id="sid-'.$sid.'" '.$rowclass.'>';
			print '<td colspan="2"><input type="checkbox" name="sids[]" value="'.$sid.'" />';
			print $rown++;
			print '</td>';
			print '<td colspan="3" class="student"><a target="viewinfobook" onclick="parent.viewBook(\'infobook\');" href="infobook.php?current=student_fees.php&cancel=student_view.php&sids[]='.$sid.'&sid='.$sid.'">'.$Student['DisplayFullSurname']['value'].'</a></td>';
			print '<td>'.$Student['EnrolNumber']['value'].'</td>';
			print '<td>'.$Student['RegistrationGroup']['value'].'</td>';

			$charges=list_student_fees($sid);
			$amount=0;
			foreach($charges as $chargeconid=>$charge){
				foreach($conids as $conid){
					if($chargeconid==$conid){
						foreach($charge as $c){
							$amount+=$c['amount'];
							}
						}
					}
				}

			print '<td>'.$amount.'</td></tr>';
			}
?>
		</tbody>
	  </table>
	</div>

	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />

</form>
</div>
<?php
	}
?>
