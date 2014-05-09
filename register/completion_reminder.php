<?php
/**								   completion_reminder.php
 *
 */

$action='completion_reminder_action.php';

$_SESSION[$book.'recipients']=array();

include('scripts/sub_action.php');

$action='completion_reminder_action.php';
$choice='completion_list.php';

if(isset($_POST['comids'])){
	$comids=(array)$_POST['comids'];
	}
else{
	$comids=array();
	}


include('scripts/sub_action.php');

if(sizeof($comids)==0){
	$result[]='Please choose a group for the notice.';
	$action='completion_list.php';
	include('scripts/results.php');
	include('scripts/redirect.php');
	exit;
	}





$recipients=array();

foreach($comids as $comid){

	/* Passed as comid:::yid so yid is already defined*/
	list($comid,$yid)=explode(':::',$comid);

	$com=(array)get_community($comid);
	$com['yeargroup_id']=$yid;

	/* Check just for incomplete registers. */
	list($nosids,$nop,$noa,$nol,$nopl,$noso)=check_community_attendance($com,$currentevent);
	if(($nop+$noa+$nol+$noso)!=$nosids and $nosids!=0){
		$tutors=(array)list_community_users($com,array('r'=>1,'w'=>1,'x'=>1),$yid);
		foreach($tutors as $tutor){
			$recipient=array();
			if($tutor['email']!='' and check_email_valid($tutor['email'])){
				$tutor['explanation']=$com['name'];
				/* Pass a list of recipients as a session var to action script. */
				$recipients[]=$tutor;
				}
			}
		}
	}


$_SESSION[$book.'recipients']=$recipients;


three_buttonmenu();
?>

  <div id="heading">
	<label><?php print_string('message','infobook');?></label>
		<?php print_string('formtutor','infobook');?>
  </div>

  <div id="viewcontent" class="content">

  <div class="center">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="center divgroup">
		<div class="center" style="margin-top:20px;">
		  <div class="left">
			<p><?php print get_string('reminder','register').' - '.get_string('messagetutors','register');?></p>
		  </div>
		  <div class="right">
			<p>
<?php 
			$checkname='all';
			$checkcaption=get_string('readytocontinue');
			$checkcaptionno=get_string('cancelcancel');
			include('scripts/check_yesno.php');
?>
			</p>
		  </div>
		</div>
	  </fieldset> 



	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print 'absence_list.php';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
  </div>


  </div>
