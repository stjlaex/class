<?php
/**                                  message_list.php
 *
 */

$cancel='student_view.php';

include('scripts/sub_action.php');

if($sid!=''){
	$yid=$Student['YearGroup']['value'];
	$perm=getYearPerm($yid);
	include('scripts/perm_action.php');
	}

/* Mandrill API */
set_include_path(get_include_path() .':'. $CFG->smtp_mandrill_lib_path);
include('src/Mandrill.php');
$mandrill = new Mandrill($CFG->smtppasswd);

/* TODO: Display the messages by student id with metadata from mail sending */

two_buttonmenu();
?>

<div id="heading">
	<label><?php print_string('student',$book);?></label>
	<?php print $Student['DisplayFullName']['value'];?>
</div>

<div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
		<div class="center">
<?php

	/* Dates */
	$today=date('Y-m-d');
	$date_from=date('Y-m-d',strtotime('-1 month',strtotime($today)));
	$date_to=$today;

	$limit = 10;
	/* Displays */
	$delivered='
	<fieldset class="center listmenu">
		<legend style="padding-left:0.5%;margin-left:0%">'.get_string('messages',$book).'</legend>
		<div class="center">
	';
	$unsuccessful='';
	/* Messages display header */
	$html='';
	$html.='
	<fieldset class="center divgroup">
		<legend>'.get_string('information',$book).'</legend>
		<table class="listmenu smalltable">
			<tr>
				<th style="font-weight:bold;text-align:center;">'.get_string('mainsenders',$book).'</th>
				<th style="font-weight:bold;text-align:center;">'.get_string('daterange',$book).'</th>
				<th style="font-weight:bold;text-align:center;">'.get_string('deliverysuccess',$book).'</th>
			</tr>
			<tr>
				<td>
	';

	/* Displays main sender and dates */
	$senders=array();
	if(is_array($CFG->emailnoreply)){
		foreach($CFG->emailnoreply as $main_sender){
			$senders[]=$main_sender;
			}
		}
	else{
		$senders[]=$CFG->emailnoreply;
		}
	foreach($senders as $main_sender){
		$html.=$main_sender.'<br />';
		}
	$html.='<td>'.$date_from.' - '.$date_to.' ('.get_string('lastthirtydays',$book).')</td>';

	$success_message='';

	/* Headers for delivered/not delivered emails table */
	$delivered.='<h4>'.get_string('deliveredemails',$book).'</h4>';
	$delivered.='<table><tr><th><h4>'.get_string('email',$book).'</h4></th><th><h4>'.get_string('sender',$book).'</h4></th><th><h4>'.get_string('subject',$book).'</h4></th><th><h4>'.get_string('status',$book).'</h4></th><th><h4>'.get_string('opens',$book).'</h4></th></tr>';
	$unsuccessful.='<h4>'.get_string('notdeliveredemails',$book).'</h4>';
	$unsuccessful.='<table><tr><th><h4>'.get_string('email',$book).'</h4></th><th><h4>'.get_string('sender',$book).'</h4></th><th><h4>'.get_string('subject',$book).'</h4></th><th><h4>'.get_string('status',$book).'</h4></th><th><h4>'.get_string('description',$book).'</h4></th><th><h4>'.get_string('details',$book).'</h4></th></tr>';

		/* MySQL queries to search guardians */
		$d_guardian_id=mysql_query("SELECT guardian_id,relationship FROM gidsid WHERE student_id='$sid';");
		while($gid=mysql_fetch_array($d_guardian_id,0)){
			$d_email_fetch=mysql_query("SELECT surname,forename,email FROM guardian WHERE id='".$gid['guardian_id']."';");
			while($guardian=mysql_fetch_array($d_email_fetch,0)){

				/* Guardian info */
				$contact=$guardian['surname'].' '.$guardian['forename'].' ('.$gid['relationship'].')';
				$email=$guardian['email'];

				/* API queries */
				$query_sent='state:sent AND full_email:'.$email;
				$query_rejected='NOT state:sent AND full_email:'.$email;

				/* API results */
				try {
					$result_sent=$mandrill->messages->search($query_sent, $date_from, $date_to, '', $senders, $limit);
					}
				catch(Mandrill_Error $e) {
					trigger_error('A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage());
					//throw $e;
					$result_sent=array();
					}
				try {
					$result_rejected=$mandrill->messages->search($query_rejected, $date_from, $date_to, '', $senders, $limit);
					}
				catch(Mandrill_Error $e) {
					trigger_error('A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage());
					//throw $e;
					$result_rejected=array();
					}

				/* Counts for success */
				$sent_no=count($result_sent);
				$rejected_no=count($result_rejected);
				$success=$sent_no/($rejected_no+$sent_no)*100;
				/* Set percentage color */
				if($success>=50 and $success<90){$color='orange';}
				if($success<50){$color='red';}
				if($success>=90 and $success<=100){$color='green';}
				/* Store delivery success for every contact */
				$success_message.=$contact.': ';
				if(isset($rejected_no) and $rejected_no!=0){
					$success_message.='<span style="color:'.$color.'">'.number_format($success,2).'% </span><br />';
					}
				else{
					$success_message.='<span style="color:green">100% </span><br />';
					}
				/* Create table for delivered */
				foreach($result_sent as $sent_email){
					if(is_array($sent_email)){
						$delivered.='<tr><td>'.$sent_email['email'].'</td><td>'.$sent_email['sender'].'</td><td>'.$sent_email['subject'].'</td><td style="color:green">'.$sent_email['state'].'</td>';
						if($sent_email['opens']>0){$delivered.='<td style="font-weight:bold;>'.$sent_email['opens'].'</td></tr>';}
						else{$delivered.='<td>'.$sent_email['opens'].'</td></tr>';}
						}
					}

				/* Create table for unsuccessful messages */
				foreach($result_rejected as $rejected_email){
					if(is_array($rejected_email)){
						$unsuccessful.='<tr><td>'.$rejected_email['email'].'</td><td>'.$rejected_email['sender'].'</td><td>'.$rejected_email['subject'].'</td><td style="color:red">'.$rejected_email['state'].'</td><td style="color:red">'.$rejected_email['bounce_description'].'</td><td style="color:red">'.$rejected_email['diag'].'</td></tr>';
						}
					}
				}
			}
		$delivered.='</table>';
		$unsuccessful.='</table></fieldset>';

		/* Display all tables */
		$html.='<td>'.$success_message.'</td></tr></table></fieldset>';
		$html.=$delivered;
		$html.=$unsuccessful;
		echo $html;
?>
		</div>

	  <input type="hidden" name="cancel" value="<?php print $cancel;?>"/>
	  <input type="hidden" name="choice" value="<?php print $choice;?>"/>
	</form>
</div>
