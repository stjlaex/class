<?php
/**									contact_details.php
 *
 */

$action='contact_details_action.php';

include('scripts/sub_action.php');

if(isset($_GET['contactno'])){$contactno=$_GET['contactno'];}else{$contactno='-2';}
if(isset($_POST['contactno'])){$contactno=$_POST['contactno'];}

if($contactno>'-1'){
	$Contact=$Student['Contacts'][$contactno];
	$Phones=$Contact['Phones'];
	$Addresses=$Contact['Addresses'];
	/*Check user has permission to view*/
	$yid=$Student['YearGroup']['value'];
	$gid=$Contact['id_db'];
	$perm=getYearPerm($yid,$respons);
	include('scripts/perm_action.php');
	}
elseif($contactno=='-1'){
	/*returns a blank for new contact*/
	$Contact=fetchContact();
	}
else{
	if(isset($_GET['gid'])){$_SESSION['infosearchgid']=$_GET['gid'];}
	if(isset($_POST['gid'])){$_SESSION['infosearchgid']=$_POST['gid'];}
	$gid=$_SESSION['infosearchgid'];

	if($gid!=''){
		$Contact=fetchContact(array('guardian_id'=>$gid));
		$Phones=$Contact['Phones'];
		$Addresses=$Contact['Addresses'];
		}
	else{
		/*returns a blank for new contact*/
		$Contact=fetchContact();
		}
	}

/*always add a blank record for new entries*/
$Phones[]=fetchPhone();
$Addresses[]=fetchAddress();

/*TODO: temporarily only one address for display*/
$Address=$Addresses[0];

//$extrabuttons['removecontact']=array('name'=>'sub','value'=>'Delete Checked');
$extrabuttons=array();
three_buttonmenu($extrabuttons,$book);
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <div class="left">
		<?php $tab=xmlarray_form($Contact,'','contactdetails',$tab,$book); ?>
	  </div>

<?php
	reset($Phones);
	while(list($phoneno,$Phone)=each($Phones)){
?>
		<div class="right">
		  <?php $tab=xmlarray_form($Phone,$phoneno,'',$tab,$book); ?>
		</div>
<?php
			}
?>

	  <div class="left">
<?php
	  /* not implementing more than one address*/
//	while(list($addressno,$Address)=each($Addresses)){
$addressno='0';
?>
		  <?php $tab=xmlarray_form($Address,$addressno,'contactaddress',$tab,$book); ?>
	  </div>

	  <fieldset class="right">
		<legend><?php print_string('addresssharedwith',$book);?></legend>
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
						print $guardian['forename'].' '.$guardian['surname'].'<br /> ';
						while($gidsid=mysql_fetch_array($d_gidsid,MYSQL_ASSOC)){
							$siblingsid=$gidsid['student_id'];
							$d_student=mysql_query("SELECT * FROM
													student WHERE id='$siblingsid'");
							$student=mysql_fetch_array($d_student,MYSQL_ASSOC);
							print displayEnum($gidsid['relationship'],'relationship'). 
									' of &nbsp;'.$student['forename'].' ' 
										.$student['surname'].' ';
							}
?>
		</div>
	  </fieldset>
<?php
					}
//			}
?>

 	<input type="hidden" name="contactno" value="<?php print $contactno;?>">
 	<input type="hidden" name="gid" value="<?php print $gid;?>">
 	<input type="hidden" name="current" value="<?php print $action;?>">
 	<input type="hidden" name="cancel" value="<?php print $cancel;?>">
 	<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>
