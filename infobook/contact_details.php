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
	if(sizeof($Dependents['Dependents'])>0){
		/* Default relationship options to an existing sibling. */
		$Contact['Order']['value']=$Dependents['Dependents'][0]['Order']['value'];
		$Contact['Relationship']['value']=$Dependents['Dependents'][0]['Relationship']['value'];
		$Contact['ReceivesMailing']['value']=$Dependents['Dependents'][0]['ReceivesMailing']['value'];
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

$LinkableContacts=get_linkable_contacts($Contact);

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
threeplus_buttonmenu($contactno,sizeof($gids),$extrabuttons,$book,"guardian");
?>
  <div class="content" id="viewcontent">
	<form id="formtoprocess" name="formtoprocess" method="post" autocomplete="off" novalidate="novalidate" action="<?php print $host;?>">

	  <div class="left">
	  <div class="center">
		<?php $tab=xmlarray_form($Contact,'','contactdetails',$tab,$book); ?>
	  </div>

	  <div class="center">
<?php
	$addressno='0';/* Only doing one address. */
	$tab=xmlarray_form($Address,$addressno,'contactaddress',$tab,$book); 
?>


<?php
	if($CFG->enrol_geocode_off=='no'){
?>
		<div id="gmaps">
		  <div id="map_canvas" style="width:auto; height:180px; overflow:visible; color:orange;"></div>
		  <table>
			<tr>
			  <td>
				<div id="public_time" style="border-left:1px solid black;padding-left:5px"></div>
			  </td>
			  <td id="transit">
				<input id="display_public_route" type="checkbox" onclick="calcPublicRoute();" style="cursor:pointer;" />
				Route
			  </td>
			  <td>
				<div id="car_time" style="border-left:1px solid black;padding-left:5px"></div>
			  </td>
			  <td id="car">
				<input id="display_car_route" type="checkbox" onclick="calcCarRoute();" style="cursor:pointer;" /> 
				Route
			  </td>
			</tr>
		  </table>
		  <input id="address_map" type="hidden" value="<?php echo $Address['Street']['value'].' '.$Address['Postcode']['value'].' '.$Address['Neighbourhood']['value'].' '.$Address['Country']['value'];?>" />
		  <input id="lat" type="hidden" value="<?php echo $Address['Latitude']['value'];?>" />
		  <input id="lon" type="hidden" value="<?php echo $Address['Longitude']['value'];?>" />
		</div>

<?php
		}
?>

	  </div>

	</div>


	  <div class="right">
		  <table class="listmenu listinfo">
			<caption><?php print_string('relationships',$book);?></caption>
<?php
		$Sibs=(array)array_merge($Dependents['Dependents'],$Dependents['Others']);
		foreach($Sibs as $Dependent){
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


<?php
		if(empty($_SESSION['accessfees'])){

?>
	  <fieldset class="right listmenu">
		<legend>
		  <?php print_string('bankdetails',$book);?>
		</legend>
		<input type="password" name="accesstest" maxlength="20" value="" />
		<input type="password" name="accessfees" maxlength="4" value="" />
<?php
			$buttons=array();
			$buttons['access']=array('name'=>'access','value'=>'access');
			all_extrabuttons($buttons,$book,'');
?>
	  </fieldset>
<?php
			}
		else{
			require_once('lib/fetch_fees.php');
			$Account=(array)fetchAccount($gid);
			if(checkIBAN($Account['Iban']['value']) or $Account['Iban']['value']==''){$valid='true';}
			else{$valid='false';}
?>
		<div class="right">
		  <?php $tab=xmlarray_form($Account,'','bankdetails',$tab,$book); ?>
		</div>
<?php
			}
?>

	  <fieldset class="right listmenu">
		<legend>
		  <?php print_string($Contact['EPFUsername']['label'],$book);?>
		</legend>
		<input type="text" readonly="readonly" value="<?php print  $Contact['EPFUsername']['value'];?>" />
	  </fieldset>

<?php
	if(count($LinkableContacts)>0){
?>
	..<fieldset class="right listmenu">
		<legend>
		  <?php print_string('linkablecontacts',$book);?>
		</legend>
		<table class="listmenu">
			<thead>
			  <tr>
				<th><?php print_string('linkablecontacts',$book);?></th>
				<th><?php print_string('email',$book);?></th>
				<th><?php print_string('phones',$book);?></th>
				<th><?php print_string('students',$book);?></th>
				<th></th>
			  </tr>
			</thead>
<?php
	foreach($LinkableContacts as $LinkableContact){
		$rown=0;
		$entryno=$LinkableContact['id_db'];
?>
		<tbody id="<?php print $entryno;?>">
		  <tr id="<?php print $entryno.'-'.$rown++;?>">
			<td>
<?php
			echo "<a href='infobook.php?current=contact_details.php&cancel=contact_list.php&sid=&gid=$entryno'>".$LinkableContact['Surname']['value'].", ".$LinkableContact['Forename']['value']."</a>";
?>
			</td>
			<td>
<?php
			echo $LinkableContact['EmailAddress']['value'];
?>
			</td>
			<td>
<?php
			foreach($LinkableContact['Phones'] as $Phone){
				echo $Phone['PhoneNo']['value']."; ";
				}
?>
			</td>
			<td>
<?php
			$d_s=mysql_query("SELECT student_id, relationship, surname, forename FROM gidsid JOIN student ON student.id=gidsid.student_id WHERE guardian_id='$entryno';");
			while($linkablesibling=mysql_fetch_array($d_s,MYSQL_ASSOC)){
				$relation=displayEnum($linkablesibling['relationship'],'relationship');
				$relationship=get_string($relation,$book).' '.get_string('to',$book).' ';
?>
				<a href="infobook.php?current=student_view.php&cancel=contact_list.php&sid=<?php print $linkablesibling['student_id'];?>&sids[]=<?php print $linkablesibling['student_id'];?>">
					<?php print $relationship.' '.$linkablesibling['forename'].' '.$linkablesibling['surname'].'; '; ?>
				</a>
<?php
				}
?>
			</td>
			<td>
<?php
			$imagebuttons=array();
			$extrabuttons=array();
			$imagebuttons['clicktolink']=array('name'=>'current',
												'id'=>'link'.$entryno,
												'value'=>'link_contacts.php',
												'title'=>'link');

			if($perms['x']==1 or $_SESSION['role']=='office' or $_SESSION['role']=='admin' or $tid=='administrator'){
				rowaction_buttonmenu($imagebuttons,$extrabuttons,$book);
				}
?>
			</td>
		  </tr>
		  <div id="<?php print 'xml-'.$entryno;?>" style="display:none;">
<?php
				$LinkableContact['LinkedToGid']=$Contact['id_db'];
				$LinkableContact['addparams']=true;
				xmlechoer('Contact',$LinkableContact);
?>
		  </div>
		</tbody>
<?php
		}
?>
		</table>
	  </fieldset>
<?php
		}
?>

 	<input type="hidden" name="contactno" value="<?php print $contactno;?>">
 	<input type="hidden" name="gid" value="<?php print $gid;?>">
 	<input type="hidden" name="current" value="<?php print $action;?>">
 	<input type="hidden" name="cancel" value="<?php print $cancel;?>">
 	<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>

  <script>
	$(document).ready(function () {
		var valid=<?php echo $valid;?>;
		if(!valid){
			var message='IBAN is invalid';
			alert(message);
			}
	  });
  </script>
