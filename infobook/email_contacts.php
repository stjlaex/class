<?php
/**								   email_contacts.php
 *
 */

$action='email_contacts_action.php';

if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}else{$sids=array();}

include('scripts/sub_action.php');

if(sizeof($sids)==0){
		$result[]=get_string('youneedtoselectstudents');
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
		/* Only contacts who have an email address and are flagged to receive all mailings */
		if($Contact['EmailAddress']['value']!='' 
		   and $Contact['ReceivesMailing']['value']=='1'){
			$recipient['name']=$Contact['DisplayFullName']['value'];
			$recipient['explanation']=get_string(displayEnum($Contact['Relationship']['value'],'relationship'),'infobook'). ' '.$Student['DisplayFullName']['value'];
			$recipient['email']=strtolower($Contact['EmailAddress']['value']);
			$recipients[]=$recipient;
			$sid_recipient_no++;
			}
		elseif($Contact['EmailAddress']['value']=='' 
		   and $Contact['ReceivesMailing']['value']=='1'){
			$email_blank_gids[]=$Contact['id_db'];
			}
		}

	/* Collect a list of sids who won't have any contacts receving this message */
	if($sid_recipient_no==0){
		$email_blank_sids[]=$sid;
		}

	}



three_buttonmenu();
?>

  <div id="heading">
	<label><?php print_string('messagesto');?></label>
	<?php print_string('contacts',$book);?>
  </div>

  <div id="viewcontent" class="content">

  <div class="divgroup center">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <div class="center">
	  </div>
	  <div class="left">
		<label for="messagefrom"><?php print_string('from',$book);?></label>
		<input type="text" name="messagefrom" id="messagefrom" 
			   size="40" value="<?php print $from_user['email'];?>" readonly="readonly" />
	  </div>
	  <div class="left">
		<label for="messagebcc"><?php print_string('bcc',$book);?></label>
		<input type="text" name="messagebcc" id="messagebcc" size="40" value="" />
	  </div>
	  <div class="left">
		<label for="subject"><?php print_string('subject',$book);?></label>
		<input class="required" tabindex="<?php print $tab++;?>"  type="text" name="messagesubject" 
			   id="messagesubject" value="" maxlength="200"/>
	  </div>

	  <div class="center">
		<label for="messageatt"><?php print_string('attachment',$book);?></label>
		<input type="file"  name="messageatt" id="messageatt" size="40" value="" />
	  </div>

	  <div class="center">
		<label for="messagebody"><?php print_string('message',$book);?></label>
		<textarea  tabindex="<?php print $tab++;?>" name="messagebody" 
		cols="78" rows="12" class="required" id="messagebody"></textarea>
	  </div>

<?php
$action_post_vars=array('recipients');
include('scripts/set_action_post_vars.php');
?>
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print 'student_list.php';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
  </div>

  <div class="divgroup center">
	<div class="left">
	  <p>Contacts who should receive this mailing but have no address: <?php print sizeof($email_blank_gids);?></p>
	</div>
	<div class="right">
	  <p>Students without any contacts receivng this message: <?php print sizeof($email_blank_sids);?></p>
	</div>
  </div>

  </div>
