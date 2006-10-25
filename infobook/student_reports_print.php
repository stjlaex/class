<?php
/**									student_reports_print.php
 */

$action='student_reports.php';

include('scripts/sub_action.php');

if(isset($_POST['wrapper_rid'])){$wrapper_rid=$_POST['wrapper_rid'];}else{$wrapper_rid='';}
	$coversheet='yes';
	$d_rid=mysql_query("SELECT categorydef_id AS report_id FROM ridcatid WHERE
				 report_id='$wrapper_rid' AND subject_id='wrapper' ORDER BY categorydef_id");
	$rids=array();
	$rids[]=$wrapper_rid;
	while($rid=mysql_fetch_array($d_rid,MYSQL_ASSOC)){
		$rids[]=$rid['report_id'];
		}
	$reportdefs=array();
	for($c=0;$c<sizeof($rids);$c++){
		$reportdefs[]=fetchReportDefinition($rids[$c]);
		}
   	$result[]=get_string('seperateprintwindow');
  	include('scripts/results.php');
?>

  <div id="xmlStudent" style="visibility:hidden;">
<?php
	/*this taken straight from report_reports_print*/
	$Student=fetchStudent_short($sid);
	$Student['coversheet']=$coversheet;
	list($Reports,$transform)=fetchSubjectReports($sid,$reportdefs);
	$Reports['Coversheet']=$coversheet;
	$Student['Reports']=nullCorrect($Reports);
	xmlpreparer('Student',$Student);
?>
  </div>
  <script>openPrintReport('xmlStudent', '<?php print $transform;?>')</script>
<?php		include('scripts/redirect.php');?>
