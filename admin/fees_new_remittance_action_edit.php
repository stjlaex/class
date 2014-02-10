<?php
/**			  					fees_new_remittance_action_edit.php
 */

$action='fees_new_remittance_action_edit.php';
$choice='fees_remittance_list.php';

include('scripts/sub_action.php');

if($sub=='Submit'){
	$action='fees_remittance_list.php';
	$sids=$_POST['sids'];
	$conids=$_POST['conids'];
	$remid=$_POST['remid'];
	$enrolstatus=$_POST['enrolstatus'];

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
			$enrolyear=get_curriculumyear()+1;

			foreach($sids as $sid){
				mysql_query("INSERT INTO fees_charge (student_id, remittance_id, tarif_id, paymenttype, amount) 
							SELECT a.student_id, '$remid', a.tarif_id, a.paymenttype, t.amount FROM fees_applied AS a, 
							fees_tarif AS t WHERE t.concept_id='$conid' AND t.id=a.tarif_id AND a.student_id='$sid';");
				//$result[]="Inserted ".mysql_insert_id()." >> ".$remid."-".$sid."-".$conid."<br>";
				}
			}
		else{
			/* TODO: improve */
			$comid=0;
			foreach($sids as $sid){
				$d_c=mysql_query("SELECT id FROM fees_charge 
						 WHERE student_id='$sid' AND remittance_id='$remid' AND community_id='$comid';");
				if(mysql_num_rows($d_c)>0){
					$charid=mysql_result($d_c);
					}
				else{
					if($student['special']=='' or $student['special']=='10'){$rate=1;}else{$rate=$student['special']/10;};
					$amount=$community['charge']*$rate;
					add_student_community_charge($sid,$comid,$remid,$amount);
					}
				//$result[]="Inserted ".mysql_insert_id()." >> ".$remid."-".$sid."-".$comid."-".$amount."<br>";
				}
			}
		}

	include('scripts/results.php');
	include('scripts/redirect.php');
}
else{

	$sids=$_GET['sids'];
	$conids=$_GET['conids'];
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
			<th colspan="2"></th>
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
			foreach($charges as $conceptid=>$cs){
				foreach($conids as $cid){
					if($conceptid==$cid){
						foreach($cs as $c){
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
	<input type="hidden" name="enrolstatus" value="<?php print $_GET['enrolstatus'];?>" />
	<input type="hidden" name="remid" value="<?php print $_GET['remid'];?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />

<?php
	foreach($conids as $conid){
?>
		<input type="hidden" name="conids[]" value="<?php print $conid;?>" />
<?php
		}
?>
</form>
</div>
<?php
	}
?>
