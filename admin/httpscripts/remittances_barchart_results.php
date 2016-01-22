<?php
	require_once('../../scripts/http_head_options.php');
	require_once($CFG->dirroot.'/lib/fetch_fees.php');

	$remittances=array();
	/*Select just the current year to speed up the bar chart temporarily*/
	$curryear=get_curriculumyear();
	$d_c=mysql_query("SELECT id FROM fees_remittance WHERE year='$curryear' ORDER BY year DESC, issuedate ASC;");
	while($remittance=mysql_fetch_array($d_c)){
		$remid=$remittance['id'];
		$Remittance=fetchRemittance($remid);
		$remyear=$Remittance['Year']['value'];
		$date_parts=explode("-",$Remittance['PaymentDate']['value']);
		$remmonth=(int)$date_parts[1];
		foreach($Remittance['Concepts'] as $Concept){
			$total[$remyear][$remmonth]+=$Concept['TotalAmount']['value'];
			$total_paid[$remyear][$remmonth]+=$Concept['AmountPaid']['value'];
			$total_notpaid[$remyear][$remmonth]+=$Concept['AmountNotPaid']['value'];
			}
		$remittances[$remyear][$remmonth]=array(
					$remmonth,array(
						$total[$remyear][$remmonth],
						$total_paid[$remyear][$remmonth],
						$total_notpaid[$remyear][$remmonth]
						)
					);
		}
	foreach($remittances as $year=>$months){
		foreach($months as $month){
			$totals[$year][]=$month;
			}
		}

	header("Content-Type: application/json; charset=utf-8"); 
	echo json_encode($totals);
?>
