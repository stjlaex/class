<?php
/**									contact_details.php
 *
 */

$action='contact_details_action.php';

include('scripts/sub_action.php');

if(isset($_GET['contactno'])){$contactno=$_GET['contactno'];}else{$contactno=-2;}
if(isset($_POST['contactno'])){$contactno=$_POST['contactno'];}

/* Check user has permission to view. */
if($sid!=''){
	$yid=$Student['YearGroup']['value'];
	$perm=getYearPerm($yid);
	include('scripts/perm_action.php');
	}

if($contactno>-1){
	/* Editing a pre-existing link to a contact*/
	$Contact=$Student['Contacts'][$contactno];
	$Phones=$Contact['Phones'];
	$Addresses=$Contact['Addresses'];
	$gid=$Contact['id_db'];
	$Dependents=fetchDependents($gid);
	}
elseif($contactno==-1){
	/* This is a new link to a contact*/
	if(isset($_POST['pregid']) and $_POST['pregid']!=''){$gid=$_POST['pregid'];}
	else{$gid=-1;}
	$gidsid=array('guardian_id'=>$gid,'student_id'=>-1,'priority'=>'',
				  'mailing'=>'','relationship'=>'');
	$Contact=fetchContact($gidsid);
	$Phones=$Contact['Phones'];
	$Addresses=$Contact['Addresses'];
	$Dependents=fetchDependents($gid);
	if(sizeof($Dependents)>0){
		$Contact['Order']['value']=$Dependents[0]['Order']['value'];
		$Contact['Relationship']['value']=$Dependents[0]['Relationship']['value'];
		$Contact['ReceivesMailing']['value']=$Dependents[0]['ReceivesMailing']['value'];
		}
	}
else{
	/* Called from the contact_list.php as the result of a contact search*/
	if(isset($_GET['gid'])){$_SESSION['infosearchgid']=$_GET['gid'];}
	if(isset($_POST['gid'])){$_SESSION['infosearchgid']=$_POST['gid'];}
	$gid=$_SESSION['infosearchgid'];

	if($gid!=''){
		$Contact=fetchContact(array('guardian_id'=>$gid));
		$Phones=$Contact['Phones'];
		$Addresses=$Contact['Addresses'];
		$Dependents=fetchDependents($gid);
		}
	else{
		/*returns a blank for new contact*/
		$Contact=fetchContact();
		}
	}

//trigger_error('contact:'.$contactno.' gid:' . $gid .' sid:'.$sid,E_USER_WARNING);

/* Allow up to 4 records with blanks for new entries*/
while(sizeof($Phones)<4){$Phones[]=fetchPhone();}
$Addresses[]=fetchAddress();

/* TODO: currently only one address for display.*/
$Address=$Addresses[0];

$extrabuttons=array();
if($contactno>-1){
	$extrabuttons['unlinkcontact']=array('name'=>'sub','value'=>'Unlink');
?>
  <div id="heading">
	<label><?php print $Contact['DisplayFullName']['value'].' - '; ?> <?php print_string('contactfor',$book); ?></label>
	<?php print $Student['DisplayFullName']['value'];?>
  </div>
<?php
	}
elseif($contactno==-1){
	/* The select existing contact box when start with a blank new contact form.*/
	$d_guardian=mysql_query("SELECT id, CONCAT(surname,', ',forename)
								AS name FROM guardian ORDER BY surname;");
?>
  <div id="heading">
	<form id="headertoprocess" name="headertoprocess" 
							method="post" action="<?php print $host;?>">
<?php
		$listname='pregid';$listlabel='';$liststyle='width:16em;';
		include('scripts/set_list_vars.php');
		list_select_db($d_guardian,$listoptions,$book);
		$buttons=array();
		$buttons['linkcontact']=array('name'=>'sub','value'=>'Link');
		all_extrabuttons($buttons,'entrybook','processHeader(this)');
?>
 	<input type="hidden" name="contactno" value="<?php print $contactno;?>">
 	<input type="hidden" name="gid" value="<?php print $gid;?>">
 	<input type="hidden" name="current" value="<?php print $current;?>">
 	<input type="hidden" name="cancel" value="<?php print $cancel;?>">
 	<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>
<?php
	}
three_buttonmenu($extrabuttons,$book);
?>
  <div class="content" id="viewcontent">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <div class="left">
		<?php $tab=xmlarray_form($Contact,'','contactdetails',$tab,$book); ?>
	  </div>

	  <div class="right">
		  <table class="listmenu listinfo">
			<caption><?php print_string('relationships',$book);?></caption>
<?php
		foreach($Dependents as $Dependent){
			$Student=$Dependent['Student'];
			$relation=displayEnum($Dependent['Relationship']['value'],'relationship');
?>
					<tr>
					  <td style="padding:5px 2px 2px 6px;">
						  <?php print get_string($relation,$book) 
							 .' '.get_string('to',$book).' ';?>
						  <a href="infobook.php?current=student_view.php&cancel=contact_list.php&sid=<?php print $Student['id_db'];?>&sids[]=<?php print $Student['id_db'];?>">
							<?php print $Student['DisplayFullName']['value']; ?>
						  </a>
					  </td>
					</tr>
<?php
			}
?>
		  </table>
	  </div>


	  <fieldset class="right listmenu">
		<legend>
		  <?php print_string($Contact['Note']['label'],$book);?>
		</legend>
		<?php	$tab=xmlelement_input($Contact['Note'],'',$tab,$book);?>
	  </fieldset>


<?php
	foreach($Phones as $phoneno => $Phone){
?>
		<div class="right">
		  <?php $tab=xmlarray_form($Phone,$phoneno,'',$tab,$book); ?>
		</div>
<?php
		}
?>


	  <div class="left">
<?php
	$addressno='0';/*Only doing one address.*/
	$tab=xmlarray_form($Address,$addressno,'contactaddress',$tab,$book); 
?>
	  </div>


<?php
		if(empty($_SESSION['accessfees'])){

?>	  <fieldset class="right listmenu">
		<legend>
		  <?php print_string('bankdetails',$book);?>
		</legend>

		<input type="password" name="accessfees" maxlength="4" value="" />
<?php
			$buttons=array();
			$buttons['access']=array('name'=>'access','value'=>'access');
			all_extrabuttons($buttons,$book,'');
			}
		else{
			require_once('lib/fetch_fees.php');
			$Account=(array)fetchAccount($gid);
?>
		<div class="right">
		  <?php $tab=xmlarray_form($Account,'','bankdetails',$tab,$book); ?>
		</div>
<?php
			}
?>

	  </fieldset>


	  <fieldset class="left listmenu">
		<legend>
		  <?php print_string($Contact['EPFUsername']['label'],$book);?>
		</legend>
		<input type="text" readonly="readonly" value="<?php print  $Contact['EPFUsername']['value'];?>" />
	  </fieldset>





 	<input type="hidden" name="contactno" value="<?php print $contactno;?>">
 	<input type="hidden" name="gid" value="<?php print $gid;?>">
 	<input type="hidden" name="current" value="<?php print $action;?>">
 	<input type="hidden" name="cancel" value="<?php print $cancel;?>">
 	<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>
