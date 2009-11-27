<?php
/**								   email_contacts.php
 *
 */

$action='email_contacts_action.php';

if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}else{$sids=array();}
if(isset($_POST['messageoption'])){$messop=$_POST['messageoption'];}else{$messop='';}
if(isset($_POST['messageto'])){$messto=$_POST['messageto'];}else{$messto='contacts';}
$_SESSION[$book.'recipients']=array();

include('scripts/sub_action.php');

if($messop==''){$messop='email';}//Default on first load.

if(sizeof($sids)==0){

	/*TODO: allow choice of student groups */

	$result[]=get_string('youneedtoselectstudents',$book);
	$action='student_list.php';
	include('scripts/results.php');
	include('scripts/redirect.php');
	exit;

	}

$from_user=get_user($tid);

$recipients=array();
$email_blank_sids=array();
$email_blank_gids=array();



if($messto=='student'){

	while(list($index,$sid)=each($sids)){
		$Student=fetchStudent_short($sid);		
		$recipient=array();
		if($messop=='email'){
			$field=fetchStudent_singlefield($sid,'EmailAddress');
			$Student=array_merge($Student,$field);
			if($Student['EmailAddress']['value']!=''){
				$recipient['name']=$Student['DisplayFullName']['value'];
				$recipient['explanation']=$CFG->schoolname;
				$recipient['email']=strtolower($Student['EmailAddress']['value']);
				$recipients[]=$recipient;
				$sid_recipient_no++;
				}
			elseif($Contact['EmailAddress']['value']==''){
				$email_blank_sids[]=$Student['id_db'];
				}
			}
		elseif($messop=='sms'){
			$mobile=$Student['MobilePhone']['value'];
			if($mobile!=''){
				$recipient['name']=$Student['DisplayFullName']['value'];
				$recipient['explanation']=$CFG->schoolname;
				$recipient['mobile']=$mobile;
				$recipient['email']=strtolower($Student['EmailAddress']['value']);
					$recipients[]=$recipient;
					$sid_recipient_no++;
				}
			elseif($mobile==''){
				$email_blank_gids[]=$Contact['id_db'];
				}
			}
		}

	}
elseif($messto=='contacts'){

	while(list($index,$sid)=each($sids)){
		$Student=fetchStudent_short($sid);
		$Contacts=(array)fetchContacts($sid);
		$sid_recipient_no=0;
		while(list($index,$Contact)=each($Contacts)){
			$recipient=array();
			if($messop=='email'){
				if(($messto=='contacts' or $messto=='family') and $Contact['ReceivesMailing']['value']=='1'){
					/* Only contacts who have an email address and are 
					 * flagged to receive all mailings 
					 */
					$email=strtolower($Contact['EmailAddress']['value']);
					if($email!=''){
						$recipient['name']=$Contact['DisplayFullName']['value'];
						$recipient['explanation']=get_string(displayEnum($Contact['Relationship']['value'],'relationship'),'infobook'). ' to '. $Student['DisplayFullName']['value'];
						$recipient['email']=$email;
						if($messto=='family'){$recipients[$email]=$recipient;}
						else{$recipients[]=$recipient;}
						$sid_recipient_no++;
						}
					elseif($Contact['EmailAddress']['value']==''){
						$email_blank_gids[]=$Contact['id_db'];
						}
					}
				}
			elseif($messop=='sms'){
				if(($messto=='contacts' or $messto=='family') and $Contact['ReceivesMailing']['value']=='1'){
					$mobile='';
					$Phones=(array)$Contact['Phones'];
					foreach($Phones as $index=>$Phone){
						if($Phone['PhoneType']['value']=='M'){
							$mobile=$Phone['PhoneNo']['value'];
							}
						}
					if($mobile!=''){
						$recipient['name']=$Contact['DisplayFullName']['value'];
						$recipient['explanation']=get_string(displayEnum($Contact['Relationship']['value'],'relationship'),'infobook'). ' to '. $Student['DisplayFullName']['value'];
						$recipient['mobile']=$mobile;
						$recipient['email']=strtolower($Contact['EmailAddress']['value']);
						$recipients[]=$recipient;
						$sid_recipient_no++;
						if($messto=='family'){$recipients[$mobile]=$recipient;}
						else{$recipients[]=$recipient;}
						}
					elseif($mobile==''){
						$email_blank_gids[]=$Contact['id_db'];
						}
					}
				}
			}
		/* Collect a list of sids who won't have any contacts receving this message */
		if($sid_recipient_no==0){
		$email_blank_sids[]=$sid;
			}
		}

	}


$_SESSION[$book.'recipients']=$recipients;

three_buttonmenu();
?>

  <div id="heading">
	<label><?php print_string('message',$book);?></label>
<?php print_string($messop,$book);?>
  </div>

  <div id="viewcontent" class="content">

	<form enctype="multipart/form-data" id="formtoprocess" 
			name="formtoprocess" method="post" action="<?php print $host;?>">

	<div class="divgroup center">
<table class="listmenu">
<tr>
<td>
	<label for="family"><?php print_string('sendto',$book);?></label>
</td>
<td>
	  <div class="row <?php if($messto=='family'){print 'checked';}?>">
	<label for="family"><?php print_string('families',$book);?></label>
	<input type="radio" name="messageto" 
		  title="Every contact - only once per family" id="family" 
		  tabindex="<?php print $tab++;?>" 
		  value="family" <?php if($messto=='family'){print 'checked';}?> />
	  </div>
	  <div class="row <?php if($messto=='contacts'){print 'checked';}?>">
	<label for="contacts"><?php print_string('contacts',$book);?></label>
	<input type="radio" name="messageto" 
		  title="Every contact - once per child" id="contacts" 
		  tabindex="<?php print $tab++;?>" 
		  value="contacts" <?php if($messto=='contacts'){print 'checked';}?> />
	  </div>
	  <div class="row <?php if($messto=='student'){print 'checked';}?>">
	<label for="students"><?php print_string('students');?></label>
	<input type="radio" name="messageto" 
		  title="Students - NOT send to any contacts" id="student" 
		  tabindex="<?php print $tab++;?>" 
		  value="student" <?php if($messto=='student'){print 'checked';}?> />
	  </div>
</td>
</tr>
</table>
	</div>
	<div class="divgroup center">
	  <div class="center">
	  </div>
<?php
	if($messop=='email'){
?>
	  <div class="center">
		<label for="messagebcc"><?php print_string('bcc',$book);?></label>
		<input type="text" name="messagebcc" id="messagebcc" size="40" 
			value="<?php print $from_user['email'];?>" />
	  </div>
	  <div class="center">
		<label for="messageatt"><?php print_string('attachment',$book);?></label>
		<input type="file"  name="messageattach" id="messageattach" value="" />
	  </div>
	  <div class="center">
		<label for="subject"><?php print_string('subject',$book);?></label>
		<input class="required" tabindex="<?php print $tab++;?>"  type="text" name="messagesubject" 
			   id="messagesubject" value="" maxlength="100"/>
	  </div>

<?php
		}
?>
	  <div class="center">
		<label for="messagebody"><?php print_string('message',$book);?></label>
		<textarea  tabindex="<?php print $tab++;?>" name="messagebody" 
		cols="78" rows="12" class="required" id="messagebody"></textarea>
	  </div>




	  <input type="hidden" name="messageoption" value="<?php print $messop;?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print 'student_list.php';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</div>

	</form>

	<div class="center">
<?php
	if(sizeof($email_blank_sids)==0){$cssstyle='style="color:#666;"';}
	else{$cssstyle='style="color:#f60;"';}
?>
	<fieldset class="left">
	  <div class="center"><a <?php print $cssstyle;?> href="infobook.php?current=student_list.php
<?php foreach($email_blank_sids as $index =>$sid){print '&sids[]='.$sid;}?>">Students who have no contacts <br /> flagged to receive this message: <?php print sizeof($email_blank_sids);?></a>
	</div>
	</fieldset>
<?php
	if(sizeof($email_blank_gids)==0){$cssstyle='style="color:#666;"';}
	else{$cssstyle='style="color:#f60;"';}
?>
	<fieldset class="right">
	<div class="center"><a  <?php print $cssstyle;?> href="infobook.php?current=contact_list.php
<?php foreach($email_blank_gids as $index=>$gid){print '&gids[]='.$gid;}?>">Contacts who should receive this message <br /> but will not because they have no address or mobile: <?php print sizeof($email_blank_gids);?></a>
	</div>
	</fieldset>
	</div>

  </div>
