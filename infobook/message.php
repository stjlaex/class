<?php
/**								   message.php
 *
 */

$action='message_action.php';
$choice='message.php';

/* Normally handled by the host page but this has to work differently depending on the sequence. */
if(isset($_POST['groupsearch']) and $_POST['groupsearch']=='yes'){
	$nolist='yes';
	include('group_search_action.php');
	}
elseif(isset($_POST['messageto'])  or isset($_POST['messageop'])){
	$sids=(array)$_SESSION['infosids'];
	}
elseif(isset($_POST['sids'])){
	$sids=(array)$_POST['sids'];
	}
elseif(isset($_GET['sids'])){
	$sids=(array)$_GET['sids'];
	}
else{
	$sids=array();
	}
/*send messages for transport*/
if(isset($_GET['messagetype']) and $_GET['messagetype']!=''){$sendfor=$_GET['messagetype'];}else{$sendfor='';}

if(isset($_POST['yid']) and $_POST['yid']!=''){$yid=$_POST['yid'];}elseif(isset($_GET['yid']) and $_GET['yid']!=''){$yid=$_GET['yid'];}else{$yid='';}
if(isset($_POST['comid']) and $_POST['comid']!=''){$comid=$_POST['comid'];}elseif(isset($_GET['comid']) and $_GET['comid']!=''){$comid=$_GET['comid'];}else{$comid='';}
if($comid!=''){
	$com=get_community($comid);
	if($yid!=''){
		$com['yeargroup_id']=$yid;
		$students=listin_community($com);
		}
	else{
		$students=listin_community($com);
		$yid=get_form_yeargroup($com['name'],$com['type']);
		}
	$formperm=get_community_perm($comid,$yid);
	$yearperm=getYearPerm($yid);
	}
elseif($yid!=''){
	$students=listin_community(array('id'=>'','type'=>'year','name'=>$yid));
	$yearperm=getYearPerm($yid);
	$formperm=$yearperm;
	}
$crid=get_yeargroup_course($yid);
$courseperm=getCoursePerm($crid, $respons);

if($sendfor=='transport'){
	$busnames=$sids;
	$students=array();
	foreach($busnames as $typebusname){
		list($type,$busname)=explode('-',$typebusname);
		$students=array_merge($students,list_bus_journey_students($busname));
		}
	foreach($students as $student){
		$sids[]=$student['id'];
		}
	}
elseif($sendfor=='remittance'){
	include('lib/fetch_fees.php');
	if(isset($_GET['remids']) and $_GET['remids']!=''){$remids=(array)$_GET['remids'];}else{$remids[]='';}
	if(isset($_GET['conids']) and $_GET['conids']!=''){$conids=(array)$_GET['conids'];}else{$conids[]='';}
	if(isset($_GET['payment']) and $_GET['payment']!=''){$payment=$_GET['payment'];}else{$payment='';}
	if(isset($_GET['paymenttype']) and $_GET['paymenttype']!=''){$paymenttype=$_GET['paymenttype'];}else{$paymenttype='';}
	$sids=array();
	foreach($conids as $conid){
		foreach($remids as $remid){
			$charges=(array)list_remittance_charges($remid,$conid,$payment);
			foreach($charges as $charge){
				if($charge['paymenttype']==$paymenttype or $paymenttype==''){
					$sid=$charge['student_id'];
					if(!array_key_exists($sid,$sids)){
						$sids[]=$sid;
						}
					}
				}
			}
		}
	}
/**/

if(isset($_POST['messageop'])){$messageop=$_POST['messageop'];}else{$messageop='email';}
if(isset($_POST['messageto'])){$messageto=$_POST['messageto'];}else{$messageto='family';}
if(isset($_POST['share0'])){$share=$_POST['share0'];}else{$share='yes';}
$_SESSION[$book.'recipients']=array();
$_SESSION[$book.'tutors']=array();

/* Locked down for teachers to only email and only to students. */
if($_SESSION['role']=='teacher' and $yearperm['x']!='1' and $formperm['x']!='1' and $courseperm['x']!='1'){
	$messageop='email';
	$messageto='student';
	}

include('scripts/sub_action.php');

if(sizeof($sids)==0){
	if(isset($_POST['groupsearch']) and $_POST['groupsearch']=='yes'){
		$result[]='Please choose a group of students.';
		include('scripts/results.php');
		}
	$action='group_search.php';
	include('scripts/redirect.php');
	exit;
	}


$from_user=get_user($tid);
$recipients=array();
$email_blank_sids=array();
$email_blank_gids=array();


if($messageto=='student'){

	foreach($sids as $sid){
			$Student=fetchStudent_short($sid);		
			$recipient=array();
			if($messageop=='email'){
				$field=fetchStudent_singlefield($sid,'EmailAddress');
				$email=$field['EmailAddress']['value'];
				if($email!='' and $email!=' '){
					$recipient['name']=$Student['DisplayFullName']['value'];
					$recipient['explanation']='<p>'.$CFG->schoolname.'</p>';
					$recipient['email']=$email;
					$recipients[]=$recipient;
					$sid_recipient_no++;
					}
				else{
					$email_blank_sids[]=$Student['id_db'];
					}
				}
			elseif($messageop=='sms'){
				$field=fetchStudent_singlefield($sid,'MobilePhone');
				$mobile=$field['MobilePhone']['value'];
				if($mobile!='' and $mobile!=' '){
					$recipient['name']=$Student['DisplayFullName']['value'];
					$recipient['explanation']='';
					$recipient['mobile']=$mobile;
					$recipients[]=$recipient;
					$sid_recipient_no++;
					}
				else{
					$email_blank_sids[]=$Student['id_db'];
					}
				}
			}

		}
	elseif($messageto=='contacts' or $messageto=='family'){
		
		$tutors=array();
		$yids=array();
		foreach($sids as $sid){
			$Student=fetchStudent_short($sid);
			foreach($Student['RegistrationTutor'] as $Tutor){
				if($Tutor['email']!=''){
					$tutors[$Tutor['email']]=array('email'=>$Tutor['email'],
												   'explanation'=>$CFG->schoolname.': message sent to parents of '.$Student['RegistrationGroup']['value']);
					}
				$yids[$Student['YearGroup']['value']]=$Student['YearGroup']['value'];
				}
			$Contacts=(array)fetchContacts($sid);
			$sid_recipient_no=0;
			foreach($Contacts as $Contact){
				$recipient=array();
				if($messageop=='email'){
					if($Contact['ReceivesMailing']['value']=='1'){
						/* Only contacts who have an email address and are 
						 * flagged to receive all mailings 
						 */
						$email=strtolower($Contact['EmailAddress']['value']);
						if(!empty($email)){
							$recipient['name']=$Contact['DisplayFullName']['value'];
							//$recipient['explanation']=$CFG->schoolname;
							$recipient['explanation']='';
							if(($messageto=='contacts' or $messageto=='family')){
								$recipient['explanation'].=get_string(displayEnum($Contact['Relationship']['value'],'relationship'),'infobook'). ' of '. $Student['DisplayFullName']['value'];
								}

							$recipient['email']=$email;
							$recipient['gid']=$Contact['id_db'];
							$recipient['sid']=$sid;
							$recipient['Student']=$Student;
							$recipient['Siblings']=(array)fetchDependents($Contact['id_db']);

							if($messageto=='family'){
								$recipients[$email]=$recipient;
								}
							else{
								$recipients[]=$recipient;
								}
							$sid_recipient_no++;
							}
						elseif($Contact['EmailAddress']['value']==''){
							$email_blank_gids[]=$Contact['id_db'];
							}
						}
					}
				elseif($messageop=='sms'){
					if($Contact['ReceivesMailing']['value']=='1'){
						$mobile='';
						$Phones=(array)$Contact['Phones'];
						foreach($Phones as $Phone){
							if($Phone['PhoneType']['value']=='M'){
								$mobile=$Phone['PhoneNo']['value'];
								}
							}
						if($mobile!='' and $mobile!=' '){
							$recipient['name']=$Contact['DisplayFullName']['value'];
							$recipient['explanation']='';
							$recipient['mobile']=$mobile;
							$recipient['email']=strtolower($Contact['EmailAddress']['value']);
							if($messageto=='family'){$recipients[$mobile]=$recipient;}
							else{$recipients[]=$recipient;}
							$sid_recipient_no++;
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

		foreach($yids as $yid){
			$yeartutors=(array)list_pastoral_users($yid,array('r'=>1,'w'=>1,'x'=>1));
			$yeartutors=array_merge($yeartutors,list_pastoral_users($yid,array('r'=>1,'w'=>1,'x'=>0)));
			foreach($yeartutors as $tutor){
				if($tutor['email']!=''){
					$tutors[$tutor['email']]=array('email'=>$tutor['email'],
												   'explanation'=>$CFG->schoolname.': message sent to parents of Year '.get_yeargroupname($yid));
					}
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
	   	<table class="listmenu">
		  <tr>
			<th>
			  <label for="family"><?php print_string('sendto',$book);?></label>
			</th>
			<td>
<?php
if($_SESSION['role']=='office' or $_SESSION['role']=='admin' or $yearperm['x']=='1' or $formperm['x']=='1' or $courseperm['x']=='1'){
?>
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
<?php
}
?>
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
<?php
	if($CFG->dropbox_access_token!='' or $CFG->drive_access_token!=''){
?>
		<input type="file" name="messageattach" id="messageattach" value="" onchange="uploadInstantFile(this.files);">
		<img alt="Loading" id="loading" src="images/roller.gif" style="display:none;width:20px;height:20px;">
		<input type="hidden" id="messagefooter" value='1'>
<?php
		}
	else {
?>
		<input type="file"  name="messageattach" id="messageattach" value="" />
<?php
		}
?>
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


	  <input type="hidden" name="yid" value="<?php print $yid; ?>" />
	  <input type="hidden" name="comid" value="<?php print $comid; ?>" />
	  <input type="hidden" name="messageop" value="<?php print $messageop;?>" />
	  <input type="hidden" name="messageto" value="<?php print $messageto;?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print 'student_list.php';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" id="scriptpath" value="<?php print $book.'/httpscripts/file_upload.php';?>" />

	  <div class="divgroup left">
<?php 
		if($messageop=='email'){
			print_string('bcctutors',$book);
			$checkname='share';
			$checkchoice=$share;
			include('scripts/check_yesno.php');
			$_SESSION[$book.'tutors']=$tutors;
			}
?>
	  </div>

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
	if(sizeof($email_blank_sids)==0){$cssstyle='style="color:#666;"';}
	else{$cssstyle='style="color:#f60;"';}
	if($messageto=='contacts' or $messageto=='family'){
?>
	<fieldset class="left">
	  <div class="center"><a <?php print $cssstyle;?> href="infobook.php?current=student_list.php
<?php foreach($email_blank_sids as $sid){print '&sids[]='.$sid;}?>">Students who have no contacts <br /> configured to receive this message: <?php print sizeof($email_blank_sids);?></a>
	</div>
	</fieldset>
<?php
	if(sizeof($email_blank_gids)==0){$cssstyle='style="color:#666;"';}
	else{$cssstyle='style="color:#f60;"';}
?>
	<fieldset class="right">
	<div class="center"><a  <?php print $cssstyle;?> href="infobook.php?current=contact_list.php
<?php foreach($email_blank_gids as $gid){print '&gids[]='.$gid;}?>">Contacts who should receive this message <br /> but will not because they have no address or mobile: <?php print sizeof($email_blank_gids);?></a>
	</div>
	</fieldset>
<?php
		}
	elseif($messageto=='student'){
?>
	<fieldset class="right">
	  <div class="center"><a <?php print $cssstyle;?> href="infobook.php?current=student_list.php
<?php foreach($email_blank_sids as $sid){print '&sids[]='.$sid;}?>">Students who will not receive this message<br /> becasue they lack an email address or mobile: <?php print sizeof($email_blank_sids);?></a>
	</div>
	</fieldset>
<?php
	}
?>
	<fieldset class="center">
	<div class="center" style="font-weight:600;">The number of recipients scheduled to recieve this message: <?php print sizeof($recipients);?></div>
	</fieldset>

	</div>
  </div>
<script src="lib/tiny_mce/tiny_mce.js" type="text/javascript"></script>
<script src="lib/tiny_mce/loadeditor.js" type="text/javascript"></script>
<script type="text/javascript">loadEditor();</script>
