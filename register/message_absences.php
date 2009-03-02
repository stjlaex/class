<?php
/**								   message_absences.php
 *
 */

$action='message_absences_action.php';

if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}else{$sids=array();}
$_SESSION[$book.'recipients']=array();

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

	$sid_recipient_no=0;
	while(list($index,$Contact)=each($Contacts)){
		$recipient=array();
		/* Only contacts who have an email address and are 
		 * flagged to receive all mailings 
		 */
		if($Contact['ReceivesMailing']['value']=='1'){
			if($Contact['EmailAddress']['value']!=''){

				$studentname=$Student['DisplayFullName']['value'];

				$recipient['name']=$Contact['DisplayFullName']['value'];

				$recipient['explanation']=get_string(displayEnum($Contact['Relationship']['value'],'relationship'),'infobook'). ' to '. $studentname;

				$recipient['messagebody']='At 10:00AM this morning '.$studentname.' had not registered in school. Please could you contact the school to inform us of the reason the reason for your child\'s absence.'."\r\n";
				$recipient['messagebody'].="\r\n".'A las 10 de la manana de hoy '.$studentname.' no se ha registrado en el colegio. Podria, por favor, contactar con el colegio e informarnos de los motivos por los que su hijo/a ha estado ausente?'."\r\n";

				$recipient['email']=strtolower($Contact['EmailAddress']['value']);

				$recipients[]=$recipient;

				$sid_recipient_no++;
				}
			elseif($Contact['EmailAddress']['value']==''){
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
	<label><?php print_string('message','infobook');?></label>
		<?php print_string('contacts','infobook');?>
  </div>

  <div id="viewcontent" class="content">

  <div class="center">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="center divgroup"> 
		<legend><?php print_string('confirm',$book);?></legend>
		<p><?php print get_string('message','infobook').' '.get_string('contacts','infobook');?>
		<?php print_string('',$book);?></p>

		<div class="right">
		  <?php include('scripts/check_yesno.php');?>
		</div>
	  </fieldset> 



	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print 'absence_list.php';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
  </div>


  </div>
