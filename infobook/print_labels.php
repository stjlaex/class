<?php
/**								   print_labels.php
 *
 */

$cancel='student_list.php';
$action='print_labels.php';
$choice='print_labels.php';

if(isset($_POST['messageto'])){$messageto=$_POST['messageto'];}else{$messageto='contacts';}
$_SESSION[$book.'recipients']=array();

include('scripts/sub_action.php');

/* Normally handled by the host page but this has to work differently depending on the sequence. */
if((isset($_POST['groupsearch']) and $_POST['groupsearch']=='yes')){
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

while(list($sindex,$sid)=each($sids)){
		$Student=fetchStudent_short($sid);
		$Contacts=(array)fetchContacts($sid);
		$sid_recipient_no=0;
		while(list($cindex,$Contact)=each($Contacts)){
			$Recipient=array();
			if($Contact['ReceivesMailing']['value']=='1'){
				/* Only contacts who are flagged to receive all mailings */
				if(sizeof($Contact['Addresses'])>0){
					$Recipient['Address']=$Contact['Addresses'];
					if($messageto=='contacts'){
						$Recipient['DisplayFullName']=$Contact['DisplayFullName'];
						$Recipient['explanation']=$Student['DisplayFullName'];
						$Recipients['Recipient'][]=$Recipient;
						$sid_recipient_no++;
						}
					elseif($messageto=='student' and !isset($recipient_index[$sid])){
						$recipient_index[$sid]=$sid;
						$Recipient['DisplayFullName']=$Student['DisplayFullName'];
						$Recipients['Recipient'][]=$Recipient;
						$sid_recipient_no++;
						}
					elseif($messageto=='family' and !isset($recipient_index[$Contact['id_db']])){
						$recipient_index[$Contact['id_db']]=$Contact['id_db'];
						$Recipient['DisplayFullName']=$Contact['DisplayFullName'];
						$Recipients['Recipient'][]=$Recipient;
						$sid_recipient_no++;
						}
					elseif(isset($recipient_index[$sid]) or isset($recipient_index[$Contact['id_db']])){
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
			title="Every contact - only once per family" id="family" 
				tabindex="<?php print $tab++;?>" 
				value="family" <?php if($messageto=='family'){print 'checked';}?> />
				</div>
			<div class="row <?php if($messageto=='contacts'){print 'checked';}?>">
			<label for="contacts"><?php print_string('contacts',$book);?></label>
			<input type="radio" name="messageto" onChange="processContent(this);"
			title="Every contact - once per child" id="contacts" 
				tabindex="<?php print $tab++;?>" 
				value="contacts" <?php if($messageto=='contacts'){print 'checked';}?> />
				</div>
			<div class="row <?php if($messageto=='student'){print 'checked';}?>">
			<label for="students"><?php print_string('students');?></label>
			<input type="radio" name="messageto" onChange="processContent(this);"
			title="Students - is NOT sent to any contacts" id="student" 
				tabindex="<?php print $tab++;?>" 
				value="student" <?php if($messageto=='student'){print 'checked';}?> />
				</div>
			</td>
			</tr>
	   	</table>
	   	</div>

	<form id="formtoprocess" 
			name="formtoprocess" method="post" action="<?php print $host;?>">

	   <div class="divgroup center">
			<div class="center">
			</div>

	  <input type="hidden" name="groupsearch" value="no" />
	  <input type="hidden" name="messageto" value="<?php print $messageto;?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</div>

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
