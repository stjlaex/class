<?php
/*									report_reports_print.php
 */

$action='report_reports_list.php';

if(isset($_POST{'sids'})){$sids=(array) $_POST{'sids'};}else{$sids=array();}
if(isset($_POST{'rids'})){$rids=(array) $_POST{'rids'};}else{$rids=array();}
if(isset($_POST{'yid'})){$yid=$_POST{'yid'};}else{$yid='';}
if(isset($_POST{'fid'})){$fid=$_POST{'fid'};}else{$fid='';}
if(isset($_POST{'selbid'})){$selbid=$_POST{'selbid'};}else{$selbid='%';}
if(isset($_POST{'coversheet'})){$coversheet=$_POST{'coversheet'};}else{$coversheet='no';}

include('scripts/sub_action.php');

if(sizeof($sids)==0){
		$result[]=get_string('youneedtoselectstudents');
   		include('scripts/results.php');
   		include('scripts/redirect.php');
		exit;
		}

/*	find the details, assessments, etc. specific to each report */
	$reportdefs=array();
	for($c=0;$c<sizeof($rids);$c++){
        $reportdefs[]=fetchReportDefinition($rids[$c],$selbid);
		}
   	$result[]=get_string('seperateprintwindow');
  	include('scripts/results.php');
?>
  <div id="xmlStudent" style="visibility:hidden;">
<?php
/*	doing one student at a time*/
	for($c=0;$c<sizeof($sids);$c++){
		$sid=$sids[$c];
		$Student=fetchshortStudent($sid);
		list($Reports,$transform)=fetchSubjectReports($sid,$reportdefs);
		$Reports['Coversheet']=$coversheet;
		$Student['Reports']=nullCorrect($Reports);
		/*Finished with the student's reports. Output the result as xml.*/
		xmlpreparer('Student',$Student);
		}
?>
  </div>
<script>openPrintReport('xmlStudent', '<?php print $transform;?>')</script>
<?php
		include('scripts/redirect.php');
?>
