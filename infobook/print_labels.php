<?php
/**								   print_labels.php
 *
 */

$cancel='student_list.php';
$action='print_labels.php';
$choice='print_labels.php';

if(isset($_POST['messageto'])){$messageto=$_POST['messageto'];}else{$messageto='family';}
if(isset($_POST['explanation'])){$explanation=$_POST['explanation'];}else{$explanation='blank';}
$_SESSION[$book.'recipients']=array();


include('scripts/sub_action.php');

/* Normally handled by the host page but this has to work differently depending on the sequence. */
if((isset($_POST['groupsearch']) and $_POST['groupsearch']=='yes')){
	$nolist='yes';
	include('group_search_action.php');
	}
elseif(isset($_POST['messageto'])  or isset($_POST['messageop'])){
	$sids=(array)$_SESSION['infosids'];
	}
elseif(isset($_POST['sids'])){
	$sids=(array)$_POST['sids'];
	}
else{
	$sids=array();
	}
/**/

if(sizeof($sids)==0){
	/* Redirect to select sids by group. */
	if(isset($_POST['groupsearch']) and $_POST['groupsearch']=='yes'){
		$result[]='Please choose a group of students.';
		include('scripts/results.php');
		}
	$action='group_search.php';
	include('scripts/redirect.php');
	exit;
	}

$Recipients=array();
$Recipients['Recipient']=array();
$recipient_index=array();
$blank_sids=array();
$blank_gids=array();

	foreach($sids as $sid){
		$Student=fetchStudent_short($sid);
		$field=fetchStudent_singlefield($sid,'EnrolNumber');
		$Student=array_merge($Student,$field);
		$Contacts=(array)fetchContacts($sid);
		$sid_recipient_no=0;
		foreach($Contacts as $cindex => $Contact){
			$Recipient=array();
			$Recipient['StudentName']=$Student['DisplayFullName'];
			$Recipient['StudentNumber']=$Student['EnrolNumber'];
			if($Contact['ReceivesMailing']['value']=='1'){
				/* Only contacts who are flagged to receive all mailings */
				if(sizeof($Contact['Addresses'])>0 and ($Contact['Addresses'][0]['Street']['value']!='' or $Contact['Addresses'][0]['Neighbourhood']['value']!='')){
					$Recipient['Address']=$Contact['Addresses'][0];
					$aid=$Recipient['Address']['id_db'];
					$gid=$Contact['id_db'];
					/* The templates for labels will always use the
					   displayfullname tag for addressing. Fix the recipient
					   value here appropriately but this value is only for labels.
					*/
					if($messageto=='contacts' and !isset($recipient_index[$gid])){
						/* once per contact */
						$recipient_index[$gid]=$gid;
						$Recipient['DisplayFullName']=$Contact['DisplayFullName'];
						$Recipients['Recipient'][]=$Recipient;
						$sid_recipient_no++;
						}
					elseif($messageto=='student' and !isset($recipient_index[$sid.$aid])){
						/* once per contact per student */
						$recipient_index[$sid.$aid]=$sid;
						$Recipient['DisplayFullName']=$Contact['DisplayFullName'];
						$Recipients['Recipient'][]=$Recipient;
						$sid_recipient_no++;
						}
					elseif($messageto=='family' and !isset($recipient_index[$aid])){
						/* once per household */
						$recipient_index[$aid]=$aid;
						$Recipient['DisplayFullName']=$Contact['DisplayAddressName'];
						$Recipients['Recipient'][]=$Recipient;
						$sid_recipient_no++;
						}
					elseif($messageto=='studentname' and !isset($recipient_index[$sid])){
						/* once per household */
						$recipient_index[$sid]=$sid;
						$Recipient['DisplayFullName']=$Student['DisplayFullName'];
						$Recipients['Recipient'][]=$Recipient;
						$sid_recipient_no++;
						}
					elseif((isset($recipient_index[$sid.$aid]) and $messageto=='student') 
						   or (isset($recipient_index[$gid]) and $messageto=='contacts') 
						   or (isset($recipient_index[$aid]) and $messageto=='family')){
						$sid_recipient_no++;
						}
					}
				elseif($sid_recipient_no==0 and $cindex==sizeof($Contacts)){
					/* Only essential for one contact with a postal address. */
					$blank_gids[]=$Contact['id_db'];
					}
				}
			}

		/* Collect a list of sids who won't have any contacts receving this message */
		if($sid_recipient_no==0){
			$blank_sids[]=$sid;
			}
	}

$_SESSION[$book.'recipients']=$Recipients;

$extrabuttons=array();
$extrabuttons['addresslabels']=array('name'=>'current',
									 'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/infobook/',
									 'value'=>'contact_labels_print.php',
									 'xmlcontainerid'=>'labels',
									 'onclick'=>'checksidsAction(this)');

two_buttonmenu($extrabuttons,$book);
?>

  <div id="heading">
	<label><?php print_string('addresslabels',$book);?></label>
  </div>

  <div id="viewcontent" class="content">

		<div class="divgroup center">
	   	<table class="listmenu">
			<tr>
			<td>
			<label for="family"><?php print_string('sendto',$book);?></label>
			</td>
			<td>
			<div class="row <?php if($messageto=='family'){print 'checked';}?>">
			<label for="family"><?php print_string('families',$book);?></label>
			<input type="radio" name="messageto" onChange="processContent(this);" 
			title="Only once per household" id="family" 
				tabindex="<?php print $tab++;?>" 
				value="family" <?php if($messageto=='family'){print 'checked';}?> />
				</div>
			<div class="row <?php if($messageto=='contacts'){print 'checked';}?>">
			<label for="contacts"><?php print_string('contacts',$book);?></label>
			<input type="radio" name="messageto" onChange="processContent(this);"
			title="Once per contact" id="contacts" 
				tabindex="<?php print $tab++;?>" 
				value="contacts" <?php if($messageto=='contacts'){print 'checked';}?> />
				</div>
			<div class="row <?php if($messageto=='student'){print 'checked';}?>">
			<label for="student"><?php print_string('students');?></label>
			<input type="radio" name="messageto" onChange="processContent(this);"
			title="Once per student per household" id="student" 
				tabindex="<?php print $tab++;?>" 
				value="student" <?php if($messageto=='student'){print 'checked';}?> />
				</div>
			</td>
			</tr>
	   	</table>
	   	</div>

		<div class="divgroup center">
	   	<table class="listmenu">
			<tr>
			<td>
			<label for="family"><?php print_string('badges',$book);?></label>
			</td>
			<td>
			<div class="row <?php if($messageto=='studentname'){print 'checked';}?>">
			<label for="studentname"><?php print get_string('student').' '.get_string('name');?></label>
			<input type="radio" name="messageto" onChange="processContent(this);"
			title="Student name badge " id="student" 
				tabindex="<?php print $tab++;?>" 
				value="studentname" <?php if($messageto=='studentname'){print 'checked';}?> />
				</div>
			</td>
			</tr>
	   	</table>
	   	</div>

	<form id="formtoprocess" 
			name="formtoprocess" method="post" action="<?php print $host;?>">

	<div class="divgroup center">
	  <div class="center">
		<br />
		<div class="left">
<?php 		
			$seltemplate='address_labels3x7';
			$listfilter='labels'; 
			include('scripts/list_template.php');
?>
		</div>
	  </div>

	  <div class="center">
		<br />
		<div class="left">
		  <br />
		  <label for="text"><?php print_string('labeltext',$book);?></label><br />
		  <textarea  tabindex="<?php print $tab++;?>" name="text" 
					 cols="28" rows="2" class="nothtmleditorarea" id="text"></textarea>
		</div>


		<div class="right">
		  <br />
		  <br />
		  <div class="row left">
			<label for="contacts"><?php print_string('enrolmentnumber',$book);?></label>
			<input type="radio" name="explanation"
				   title="" id="enrolmentno" tabindex="<?php print $tab++;?>" 
			value="enrolmentno" <?php if($explanation=='enrolmentno'){print 'checked';}?> />
		  </div>
		  <div class="row left">
			<label for="students"><?php print get_string('student',$book).' '.get_string('name',$book);?></label>
			<input type="radio" name="explanation"
				   title="" id="studentname" tabindex="<?php print $tab++;?>" 
			value="studentname" <?php if($explanation=='studentname'){print 'checked';}?> />
		  </div>

		  <div class="row left">
			<label for="blank"><?php print_string('none','infobook');?></label>
			<input type="radio" name="explanation"
				   title="" id="blank" tabindex="<?php print $tab++;?>" 
			value="blank" <?php if($explanation=='blank'){print 'checked';}?> />
		  </div>
		</div>
	  </div>


	  </div>

	  <input type="hidden" name="groupsearch" value="no" />
	  <input type="hidden" name="messageto" value="<?php print $messageto;?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />

	</form>

	<div class="center">
<?php
	if(sizeof($blank_sids)==0){$cssstyle='style="color:#666;"';}
	else{$cssstyle='style="color:#f60;"';}
	if($messageto=='contacts' or $messageto=='family'){
?>
	<fieldset class="left">
	  <div class="center"><a <?php print $cssstyle;?> href="infobook.php?current=student_list.php
<?php foreach($blank_sids as $index =>$sid){print '&sids[]='.$sid;}?>">Students who have no contacts <br /> configured to receive this mailing: <?php print sizeof($blank_sids);?></a>
	</div>
	</fieldset>
<?php
	if(sizeof($blank_gids)==0){$cssstyle='style="color:#666;"';}
	else{$cssstyle='style="color:#f60;"';}
?>
	<fieldset class="right">
	<div class="center"><a  <?php print $cssstyle;?> href="infobook.php?current=contact_list.php
<?php foreach($blank_gids as $index=>$gid){print '&gids[]='.$gid;}?>">Contacts who should receive this mailing <br /> but will not because they have no address: <?php print sizeof($blank_gids);?></a>
	</div>
	</fieldset>
<?php
		}
	elseif($messageto=='student'){
?>
	<fieldset class="right">
	  <div class="center"><a <?php print $cssstyle;?> href="infobook.php?current=student_list.php
<?php foreach($blank_sids as $index =>$sid){print '&sids[]='.$sid;}?>">Students who will not receive this mailing<br /> becasue they lack an address: <?php print sizeof($blank_sids);?></a>
	</div>
	</fieldset>
<?php
	}
?>
	<fieldset class="center">
	<div class="center" style="font-weight:600;">The number of recipients identified for this mailing: <?php print sizeof($Recipients['Recipient']);?></div>
	</fieldset>

	</div>
  </div>

	<div id="xml-labels" style="display:none;">
	  <params>
		<selectname>template</selectname>
		<selectname>explanation</selectname>
		<selectname>messageto</selectname>
		<selectname>text</selectname>
	  </params>
	</div>
