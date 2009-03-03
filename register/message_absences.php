<?php
/**								   message_absences.php
 *
 */

$action='message_absences_action.php';

if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}else{$sids=array();}
$_SESSION[$book.'unauthrecipients']=array();
$_SESSION[$book.'authrecipients']=array();

include('scripts/sub_action.php');

if(sizeof($sids)==0){
	$result[]=get_string('youneedtoselectstudents',$book);
	$action='absence_list.php';
	include('scripts/results.php');
	include('scripts/redirect.php');
	exit;
	}

$recipients=array();
$email_blank_sids=array();
$email_blank_gids=array();

while(list($index,$sid)=each($sids)){

	$Student=fetchStudent_short($sid);
	$Contacts=(array)fetchContacts($sid);
	$Attendance=fetchcurrentAttendance($sid);
	if($Attendance['Code']['value']=='O'){

		$sid_recipient_no=0;
		while(list($index,$Contact)=each($Contacts)){
			$recipient=array();
			/* Only contacts who have an email address and are 
			 * flagged to receive all mailings 
			 */
			if($Contact['ReceivesMailing']['value']=='1'){
				if($Contact['EmailAddress']['value']!=''){
					
					$recipient['name']=$Contact['DisplayFullName']['value'];
					
					$recipient['relationship']=$Contact['Relationship']['value'];
					
					$recipient['studentname']=$Student['DisplayFullName']['value'];
					
					$recipient['email']=strtolower($Contact['EmailAddress']['value']);
					
					$recipients[]=$recipient;
					
					$sid_recipient_no++;
					}
				elseif($Contact['EmailAddress']['value']==''){
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


$_SESSION[$book.'unauthrecipients']=$recipients;

three_buttonmenu();
?>

  <div id="heading">
	<label><?php print_string('message','infobook');?></label>
		<?php print_string('contacts','infobook');?>
  </div>

  <div id="viewcontent" class="content">

  <div class="center">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="center divgroup"> 
		<legend><?php print_string('confirm',$book);?></legend>
		<p><?php print get_string('messagecontactsunauthorisedabsences','register');?>

		<div class="right">
	<?php $checkname='unauth'; include('scripts/check_yesno.php');?>
		</div>
	  </fieldset> 

	  <fieldset class="center divgroup"> 
		<legend><?php print_string('confirm',$book);?></legend>
		<p><?php print get_string('messagecontactsallabsences','register');?>

		<div class="right">
		  <?php $checkname='all';include('scripts/check_yesno.php');?>
		</div>
	  </fieldset> 



	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print 'absence_list.php';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
  </div>


  </div>
