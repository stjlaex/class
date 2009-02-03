<?php
/**								   email_contacts.php
 *
 */

$action='email_contacts_action.php';

if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}else{$sids=array();}
if(isset($_POST['messageoption'])){$messop=$_POST['messageoption'];}else{$messop='';}
$_SESSION[$book.'recipients']=array();

include('scripts/sub_action.php');

if(sizeof($sids)==0){
	$result[]=get_string('youneedtoselectstudents');
	$action='student_list.php';
	include('scripts/results.php');
	include('scripts/redirect.php');
	exit;
	}
if($messop==''){
	$result[]=get_string('selectamessageoption');
	$action='student_list.php';
	include('scripts/results.php');
	include('scripts/redirect.php');
	exit;
	}

$from_user=get_user($tid);

$recipients=array();
$email_blank_sids=array();
$email_blank_gids=array();

while(list($index,$sid)=each($sids)){

	$Student=fetchStudent_short($sid);
	$Contacts=(array)fetchContacts($sid);

	$sid_recipient_no=0;
	while(list($index,$Contact)=each($Contacts)){
		$recipient=array();
		/* Only contacts who have an email address and are 
		 * flagged to receive all mailings 
		 */
		if($messop=='emailcontacts' and $Contact['ReceivesMailing']['value']=='1'){
			if($Contact['EmailAddress']['value']!=''){
				$recipient['name']=$Contact['DisplayFullName']['value'];
				$recipient['explanation']=get_string(displayEnum($Contact['Relationship']['value'],'relationship'),'infobook'). ' to '. $Student['DisplayFullName']['value'];
				$recipient['email']=strtolower($Contact['EmailAddress']['value']);
				$recipients[]=$recipient;
				$sid_recipient_no++;
				}
			elseif($Contact['EmailAddress']['value']==''){
				$email_blank_gids[]=$Contact['id_db'];
				}
			}
		elseif($messop=='smscontacts' and $Contact['ReceivesMailing']['value']=='1'){
			$Phones=(array)$Contact['Phones'];
			foreach($Phones as $index=>$phone){
				if($Phone['PhoneType']['value']=='M'){

					}
				}
			if($Contact['EmailAddress']['value']!=''){
				$recipient['name']=$Contact['DisplayFullName']['value'];
				$recipient['explanation']=get_string(displayEnum($Contact['Relationship']['value'],'relationship'),'infobook'). ' to '. $Student['DisplayFullName']['value'];
				$recipient['phone']=$mobile;
				$recipients[]=$recipient;
				$sid_recipient_no++;
				}
			elseif($Phone['PhoneNo']['value']==''){
				$email_blank_gids[]=$Contact['id_db'];
				}

			}
		}

	/* Collect a list of sids who won't have any contacts receving this message */
	if($sid_recipient_no==0){
		$email_blank_sids[]=$sid;
		}

	}


$_SESSION[$book.'recipients']=$recipients;

three_buttonmenu();
?>

  <div id="heading">
	<label><?php print_string('message',$book);?></label>
	<?php print_string('contacts',$book);?>
  </div>

  <div id="viewcontent" class="content">

  <div class="divgroup center">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <div class="center">
	  </div>
	  <div class="center">
		<label for="messagebcc"><?php print_string('bcc',$book);?></label>
		<input type="text" name="messagebcc" id="messagebcc" size="40" 
			value="<?php print $from_user['email'];?>" />
	  </div>
	  <div class="center">
		<label for="subject"><?php print_string('subject',$book);?></label>
		<input class="required" tabindex="<?php print $tab++;?>"  type="text" name="messagesubject" 
			   id="messagesubject" value="" maxlength="100"/>
	  </div>
<?php
// TODO: file attacments
//	  <div class="center">
//		<label for="messageatt"></label>
//		<input type="file"  name="messageatt" id="messageatt" size="40" value="" />
//	  </div>
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
	</form>
  </div>

	<fieldset class="right">
	  <p><a href="infobook.php?current=contact_list.php
<?php 
foreach($email_blank_gids as $index=>$gid){
print '&gids[]='.$gid;
}
?>">Contacts who will not receive this <br /> message because they have no address: <?php print sizeof($email_blank_gids);?></a></p>
	</fieldset>
	<fieldset class="left">
	  <p><a href="infobook.php?current=student_list.php
<?php 
foreach($email_blank_sids as $index =>$sid){
print '&sids[]='.$sid;
}
?>
">Students who have no contacts <br /> flagged to receive this message: <?php print sizeof($email_blank_sids);?></a></p>
	</fieldset>

  </div>
