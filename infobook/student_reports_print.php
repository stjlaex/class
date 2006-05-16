<?php
/**									student_reports_print.php
 */

$action='student_reports.php';

include('scripts/sub_action.php');

if(isset($_POST{'rids'})){$rids=(array) $_POST{'rids'};}else{$rids=array();}
	$selbid='%';
	$coversheet='yes';
	$reportdefs=array();
	for($c=0;$c<sizeof($rids);$c++){
		$reportdefs[]=fetchReportDefinition($rids[$c]);
		}
   	$result[]=get_string('seperateprintwindow');
  	include('scripts/results.php');
?>

  <div id="xmlStudent" style="visibility:hidden;">
<?php
/*	this taken straight from report_reports_print*/
		$Student=nullCorrect(fetchshortStudent($sid));
		$Student['todate']=$date;
		$Student['coversheet']=$coversheet;
		list($Reports,$transform)=fetchSubjectReports($sid,$reportdefs);
		$Student['Reports']=nullCorrect($Reports);

		xmlpreparer('Student',$Student);
?>
  </div>
  <script>openPrintReport('xmlStudent', '<?php print $transform;?>')</script>
<?php		include('scripts/redirect.php');?>
