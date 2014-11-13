<?php
/**									statements_print.php
 *
 */

require_once('../../scripts/http_head_options.php');

if(isset($_GET['rids']) and $_GET['rids']!=''){$rids=$_GET['rids'];}
if(isset($_GET['stage']) and $_GET['stage']!=''){$stage=$_GET['stage'];}

foreach($rids as $rid){
	$stagestatements=get_report_skill_statements($rid,'%','%',$stage,true);
	foreach($stagestatements as $stagestatement){
		$Statements['Statement'][]=$stagestatement;
		}
	$Statements['rids'][]=$rid;
	}

$Statements['Stage']=$stage;
$Statements['Paper']='portrait';
$Statements['Transform']='statements_print';

$returnXML=$Statements;
$rootName='Statements';

require_once('../../scripts/http_end_options.php');
exit;
?>
