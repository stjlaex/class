<?php
/**									contact_details.php
 *
 */

$action='contact_details_action.php';
$cancel='student_view.php';

include('scripts/sub_action.php');

if(isset($_GET['contactno'])){$contactno=$_GET['contactno'];}
else{$contactno=$_POST['contactno'];}

if($contactno!='-1'){
	$Contact=$Student['Contacts'][$contactno];
	$Phones=$Contact['Phones'];
	$Addresses=$Contact['Addresses'];
	}
else{
	$Contact=fetchContact();
	}
$Phones[]=fetchPhone();
$Addresses[]=fetchAddress();

$Address=$Addresses[0];/*temporarily one address for display*/

/*Check user has permission to view*/
$yid=$Student['YearGroup']['value'];
$contactgid=$Contact['id_db'];
$perm=getYearPerm($yid,$respons);
include('scripts/perm_action.php');

//$extrabuttons['removecontact']=array('name'=>'sub','value'=>'Delete Checked');
three_buttonmenu($extrabuttons);
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <div class="left">
		<?php $tab=xmlarray_form($Contact,'','contactdetails',$tab); ?>
	  </div>

	  <div class="right">
<?php
	reset($Phones);
	while(list($phoneno,$Phone)=each($Phones)){
?>
		<div class=center">
		  <?php $tab=xmlarray_form($Phone,$phoneno,'',$tab); ?>
		</div>
<?php
			}
?>
	  </div>

	  <fieldset class="center">
		<legend><?php print_string('contactaddress',$book);?></legend>
<?php

//	while(list($addressno,$Address)=each($Addresses)){
$addressno='0';
?>
		<div class="left">
		  <?php $tab=xmlarray_form($Address,$addressno,'',$tab); ?>
		</div>

		<div class="right">
		  <label><?php print_string('addresssharedwith',$book);?></label>
<?php
				/*find other contacts who share this address*/
				$aid=$Address['id_db'];
				$d_gidaid=mysql_query("SELECT * FROM gidaid WHERE address_id='$aid'");
					while($gidaid=mysql_fetch_array($d_gidaid,MYSQL_ASSOC)){
						$gid=$gidaid['guardian_id'];
				   		$d_guardian=mysql_query("SELECT * FROM guardian WHERE id='$gid'");
				   		$d_gidsid=mysql_query("SELECT * FROM gidsid WHERE guardian_id='$gid'");
						$guardian=mysql_fetch_array($d_guardian,MYSQL_ASSOC);
?>
		  <div class="center">
			<input type="checkbox" name="ungidaids[]" 
			  value="<?php print $gid.':'.$aid; ?>" />
<?php
						print $guardian['forename'].' '.$guardian['surname'].' ';
						while($gidsid=mysql_fetch_array($d_gidsid,MYSQL_ASSOC)){
							$siblingsid=$gidsid['student_id'];
							$d_student=mysql_query("SELECT * FROM
													student WHERE id='$siblingsid'");
							$student=mysql_fetch_array($d_student,MYSQL_ASSOC);
							print displayEnum($gidsid['relationship'],'relationship'). 
									' of &nbsp;'.$student['forename'].' ' 
										.$student['surname'].'<br /> ';
							}
?>
		  </div>
<?php
					}
//			}
?>
		  </div>
	  </fieldset>
 	<input type="hidden" name="contactno" value="<?php print $contactno;?>">
 	<input type="hidden" name="contactgid" value="<?php print $contactgid;?>">
 	<input type="hidden" name="current" value="<?php print $action;?>">
 	<input type="hidden" name="cancel" value="<?php print $cancel;?>">
 	<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
</div>
