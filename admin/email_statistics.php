<?php
/**
 *							email_statistics.php
 *
 */

$choice='email_statistics.php';
$action='email_statistics.php';

include('scripts/sub_action.php');


/* Mandrill API */
set_include_path(get_include_path() .':'. $CFG->smtp_mandrill_lib_path);
include('src/Mandrill.php');
$mandrill = new Mandrill($CFG->smtppasswd);

two_buttonmenu();
?>

<div id="heading" style="padding:0.3px 0.3px 0.3px 0.3px">
	<form name="daterange" action="#" method="POST" >
		<select name="date" id="date" onchange="this.form.submit()" style="font-size:x-small">
			<option><?php print_string('selectdaterange',$book);?></option>
			<option value="1"><?php print_string('today',$book);?></option>
			<option value="2"><?php print_string('lastsevendays',$book);?></option>
			<option value="3"><?php print_string('lastfourteendays',$book);?></option>
			<option value="4"><?php print_string('lastthirtydays',$book);?></option>
		</select>
	</form>
</div>

<div class="content" id="viewcontent">
	<form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host; ?>">
		<div class="center">
<?php
	/* Dates */
	$today=date('Y-m-d');
	$date_from = date("Y-m-d",strtotime('-30 days', strtotime($today)));
	$date_to = $today;
	$days=get_string('lastthirtydays',$book);

	if(isset($_POST['date']) and $_POST['date']!='') {
		$selected_date=$_POST['date'];
		if($selected_date==1) {$date_from = date("Y-m-d",strtotime('-1 day', strtotime($today))); $days=get_string('today',$book);}
		if($selected_date==2) {$date_from = date("Y-m-d",strtotime('-7 days', strtotime($today))); $days=get_string('lastsevendays',$book);}
		if($selected_date==3) {$date_from = date("Y-m-d",strtotime('-14 days', strtotime($today))); $days=get_string('lastfourteendays',$book);}
		if($selected_date==4) {$date_from = date("Y-m-d",strtotime('-30 days', strtotime($today))); $days=get_string('lastthirtydays',$book);}
	}

	$limit = 1000;
	/* Displays */
	$unsuccessful='
	</div><div class="center divgroup">
	';
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
	else {
		$senders[]=$CFG->emailnoreply;
	}
	foreach($senders as $main_sender){
		$html.=$main_sender.'<br />';
	}
	$html.='</td>';
	$html.='<td>';
	$html.=display_date($date_from).' - '.display_date($date_to).' <br />('.$days.')';
	$html.='</td>';

	/* Headers for not delivered emails table */
	$unsuccessful.='<h4>'.get_string('notdeliveredemails',$book).' ('.$days.')</h4>';
$unsuccessful.='<table><tr><th><h4>'.get_string('email',$book).'</h4></th><th><h4>'.get_string('sender',$book).'</h4></th><th style="width:30%;"><h4>'.get_string('subject',$book).'</h4></th><th><h4>'.get_string('status',$book).'</h4></th><th><h4>'.get_string('description',$book).'</h4></th><th><h4>'.get_string('details',$book).'</h4></th></tr>';

	/* API queries */
	$query_rejected = 'NOT state:sent';
	//$address = $senders[0]; /* for stats only is used one email address, that should be the first in the senders array */

	/* API results */
	try {
		$result_rejected = $mandrill->messages->search($query_rejected, $date_from, $date_to, '', $senders, $limit);
		}
	catch(Mandrill_Error $e) {
		trigger_error('A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage());
		//throw $e;
		$result_rejected=array();
		}

	$result_senders=array();
	foreach($senders as $sender){
		try {
			$result_senders[] = $mandrill->senders->info($sender);
			}
		catch(Mandrill_Error $e) {
			trigger_error('A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage());
			//throw $e;
			$result_rejected=array();
			}
		}
	/* Counts for success */
	$rejected_no=count($result_rejected);

	/* Create rows for unsuccessful messages table */
	foreach($result_rejected as $rejected_email){
		if(is_array($rejected_email)) {
			$unsuccessful.='<tr><td>'.$rejected_email['email'].'</td><td>'.$rejected_email['sender'].'</td><td>'.$rejected_email['subject'].'</td><td style="color:red">'.$rejected_email['state'].'</td><td style="color:red">'.$rejected_email['bounce_description'].'</td><td style="color:red">'.$rejected_email['diag'].'</td></tr>';
		}
	}
	$unsuccessful.='</table></div>';

	/* Create rows for Mandrill statistics table */
	$total=0;
	$stats=array("today"=>get_string('today',$book),"last_7_days"=>get_string('lastsevendays',$book),"last_30_days"=>get_string('lastthirtydays',$book),"last_60_days"=>get_string('lastsixtydays',$book),"last_90_days"=>get_string('lastninetydays',$book));
	$html_stats.='<fieldset class="center divgroup"><legend>'.get_string('statistics',$book).'</legend>';
	foreach($result_senders as $result_sender){
		$html_stats.=get_string('overall',$book).': '.$result_sender['address'];
		$html_stats.='<table class="listmenu smalltable"><tr><td></td><td>'.get_string('sent',$book).'</td><td>'.get_string('rejected',$book).'</td><td>'.get_string('hardbounced',$book).'</td><td>'.get_string('softbounced',$book).'</td><td>'.get_string('spamcomplaints',$book).'</td><td>'.get_string('success',$book).'</td></tr>';
		foreach($stats as $index => $stat){
			/* The percentage of success: sent messages/(sent_messages+not_sent_messages)*100 */
			$success=$result_sender['stats'][$index]['sent']/($result_sender['stats'][$index]['sent']+$result_sender['stats'][$index]['rejects']+$result_sender['stats'][$index]['hard_bounces']+$result_sender['stats'][$index]['soft_bounced']+$result_sender['stats'][$index]['complaints'])*100;
			if($success==0 and $result_sender['stats'][$index]['sent']==0 and $result_sender['stats'][$index]['rejects']==0 and $result_sender['stats'][$index]['hard_bounces']==0 and $result_sender['stats'][$index]['soft_bounces']==0 and $result_sender['stats'][$index]['complaints']==0) {$success=100;}
			/* Set the colors for percentage */
			if($success>=50 and $success<90) {$color='orange';}
			if($success<50) {$color='red';}
			if($success>=90 and $success<=100) {$color='green';}
			if($index=='last_30_days') {$total=$total+$success;}
			/* Stats rows */
			$html_stats.='<tr><td>'.$stat.'</td><td style="color:green">'.$result_sender['stats'][$index]['sent'].'</td><td style="color:red">'.$result_sender['stats'][$index]['rejects'].'</td><td style="color:orange">'.$result_sender['stats'][$index]['hard_bounces'].'</td><td style="color:orange">'.$result_sender['stats'][$index]['soft_bounces'].'</td><td style="color:orange">'.$result_sender['stats'][$index]['complaints'].'</td><td style="color:'.$color.'">'.number_format($success,2).'%</td></tr>';
		}
		$html_stats.='</table>';
	}

	/* Total success (overall main senders)*/
	$total=$total/(count($senders));
	if($total>=50 and $total<90) {$tcolor='orange';}
	if($total<50) {$tcolor='red';}
	if($total>=90 and $total<=100) {$tcolor='green';}
	$success_message='<span style="color:'.$tcolor.'">'.number_format($total,2).'%</span>';
	/* Create the last rows and cells for info table */
	$html.='<td>'.$success_message.'</td></tr>';
	$html.='</table></fieldset>';
	/* Create the last rows and cells for stats table */
	$html.=$html_stats;
	$html.='</fieldset>';
	/* Adds the unsuccessful table to html */
	$html.=$unsuccessful;
	/* Display all tables */
	echo $html;
?>
		</div>
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>
</div>
