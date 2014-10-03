<?php
/**								   message.php
 *
 */

$action='message_action.php';
$choice='message.php';

if(isset($_POST['uids'])){
	$uids=(array)$_POST['uids'];
	}
elseif(isset($_GET['uids'])){
	$uids=(array)$_GET['uids'];
	}
else{
	$uids=array();
	}

if(isset($_POST['messageop'])){$messageop=$_POST['messageop'];}else{$messageop='email';}
$_SESSION[$book.'recipients']=array();

include('scripts/sub_action.php');

if(sizeof($uids)==0){
	$result[]='Please select users.';
	include('scripts/results.php');
	$action='staff_list.php';
	include('scripts/redirect.php');
	exit;
	}


$from_user=get_user($tid);
$recipients=array();
$email_blank_uids=array();


foreach($uids as $uid){
		$User=fetchUser($uid);
		$recipient=array();
		if($messageop=='email'){
			$email=$User['EmailAddress']['value'];
			if($email!='' and $email!=' '){
				$recipient['name']=$User['Forename']['value']." ".$User['Surname']['value'];
				$recipient['explanation']='<p>'.$CFG->schoolname.'</p>';
				$recipient['email']=$email;
				$recipients[]=$recipient;
				$uid_recipient_no++;
				}
			else{
				$email_blank_uids[]=$User['Forename']['value']." ".$User['Surname']['value'];
				}
			}
		elseif($messageop=='sms'){
			$mobile=$User['MobilePhone']['value'];
			if($mobile!='' and $mobile!=' '){
				$recipient['name']=$User['Forename']['value']." ".$User['Surname']['value'];
				$recipient['explanation']='';
				$recipient['mobile']=$mobile;
				$recipients[]=$recipient;
				$uid_recipient_no++;
				}
			else{
				$email_blank_uids[]=$User['Forename']['value']." ".$User['Surname']['value'];
				}
			}
		}

$_SESSION[$book.'recipients']=$recipients;

three_buttonmenu();
?>

  <div id="heading">
	<label><?php print_string('message',$book);?></label>
<?php print_string($messageop,$book);?>
  </div>

  <div id="viewcontent" class="content">

		<div class="divgroup left">
	   	<table class="listmenu">
		  <tr>
			<th>
			  <label for="family"><?php print_string('message',$book);?></label>
			</th>
			<td>
			  <div class="row <?php if($messageop=='email'){print 'checked';}?>">
				<label for="email"><?php print_string('email',$book);?></label>
				<input type="radio" name="messageop" onChange="processContent(this);"
					   title="Email" id="email" 
					   tabindex="<?php print $tab++;?>" 
					   value="email" <?php if($messageop=='email'){print 'checked';}?> />
			  </div>
<?php
		if($_SESSION['role']=='office' or $_SESSION['role']=='admin'){
?>

			  <div class="row <?php if($messageop=='sms'){print 'checked';}?>">
				<label for="sms"><?php print_string('sms',$book);?></label>
				<input type="radio" name="messageop" onChange="processContent(this);"
					   title="SMS" id="sms" 
					   tabindex="<?php print $tab++;?>" 
					   value="sms" <?php if($messageop=='sms'){print 'checked';}?> />
			  </div>
<?php
			}
?>

			</td>
		  </tr>
	   	</table>
	   	</div>

		<div class="divgroup center">
			<form enctype="multipart/form-data" id="formtoprocess" 
				name="formtoprocess" method="post" action="<?php print $host;?>">

<?php
	if($messageop=='email'){
?>

		<input type="hidden" name="messagebcc" id="messagebcc" size="40" value="<?php print $from_user['email'];?>" />

	  <div class="center">
		<label for="subject"><?php print_string('subject',$book);?></label>
		<input class="required" tabindex="<?php print $tab++;?>"  type="text" name="messagesubject" 
			   id="messagesubject" value="" maxlength="100"/>
	  </div>
	  <div class="left">
		<label for="messageatt"><?php print_string('attachment',$book);?></label>
		<input type="file"  name="messageattach" id="messageattach" value="" />
	  </div>
	  <div class="right">
<?php
	/*$formats=array();
	$formats[]=array('id'=>'message_contact_update','name'=>'Contact details update');
	$listname='messageformat';$listlabel='fixedformat';
	include('scripts/set_list_vars.php');
	list_select_list($formats,$listoptions,$book);
	unset($listoptions);*/

	$tags=getTags(true,'default',array('student_id'=>2,'guardian_id'=>2,'user_id'=>2));
	$templates=getTemplates();
?>
		<label><?php print_string('templates',$book);?></label> 
		
		<select id="templates" onchange="tinymceLoad(this);">
			<option> </option>
<?php
			foreach($templates as $template){
				echo "<option value='".$template['comment']."'>".$template['name']."</option>";
				}
?>
		</select>
		<script>
			<?php $jstags=json_encode($tags);?>
			var jstags=<?php echo json_encode($jstags); ?>;
		</script>
		<button name="preview" type="button" onclick="openPreview(jstags,650,950)" style="margin-top:1%;"><?php print_string('preview',$book);?></button>
	  </div>
<?php
		}
?>
	  <div class="center">
		<label for="messagebody"><?php print_string('message',$book);?></label>
		<textarea  tabindex="<?php print $tab++;?>" name="messagebody" 
		cols="78" rows="12" class="<?php if($messageop=='sms'){print 'required';}else{print 'htmleditorarea';}?>" id="messagebody"></textarea>
	  </div>


	  <input type="hidden" name="messageop" value="<?php print $messageop;?>" />
	  <input type="hidden" name="messageto" value="<?php print $messageto;?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print 'staff_list.php';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />

	  <div class="divgroup right">
<?php 
		if($messageop=='email' and $_SESSION['role']=='admin'){
?>
		<label for="replyto"><?php print_string('emailreplyto','admin');?></label>
<?php
			$choices=array();
			if(is_array($CFG->emailnoreply)){
				foreach($CFG->emailnoreply as $address){
					$choices[]=$address;
					}
				}
			else{
				$choices[]=$CFG->emailnoreply;
				}
			$teacher=get_user($tid,'username');
			if($teacher['email']!='' and check_email_valid($teacher['email'])){
				$choices[]=$teacher['email'];
				}
			print '<select name="replyto" id="replyto" size="1" tabindex="'.$tab++.'">';
			foreach($choices as $index=>$choice){
				print '<option ';
				if($index==0){print 'selected="selected"';}
				print	' value="'.$choice.'">'.$choice.'</option>';
				}
			print '</select>';
			}
?>
	  </div>

	</div>


	</form>

	<div class="center">
<?php
	if(sizeof($email_blank_uids)==0){$cssstyle='style="color:#666;"';}
	else{$cssstyle='style="color:#f60;"';}

?>
	<fieldset class="left">
	  <div class="center"><div <?php print $cssstyle;?>>
<?php 
	foreach($email_blank_uids as $uid){
		print $uid."; ";
		}
	print 'Users who will not receive this message<br /> becasue they lack an email address or mobile:'.sizeof($email_blank_uids);
?>
	</div>
	</fieldset>

	<fieldset class="right">
		<div class="center" style="font-weight:600;">The number of recipients scheduled to recieve this message: <?php print sizeof($recipients);?></div>
	</fieldset>

	</div>
  </div>
<script src="lib/tiny_mce/tiny_mce.js" type="text/javascript"></script>
<script src="lib/tiny_mce/loadeditor.js" type="text/javascript"></script>
<script type="text/javascript">loadEditor();</script>
