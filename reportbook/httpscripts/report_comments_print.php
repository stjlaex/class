<?php
/**									httpscripts/report_comments_print.php
 *
 *	Printout of selected students' comments	
 */

require_once('../../scripts/http_head_options.php');

if(isset($_GET['sids'])){$sids=(array) $_GET['sids'];}else{$sids=array();}
if(isset($_POST['sids'])){$sids=(array) $_POST['sids'];}
if(isset($_GET['startdate'])){$startdate=$_GET['startdate'];}else{$startdate='';}
if(isset($_POST['startdate'])){$startdate=$_POST['startdate'];}
if(isset($_GET['enddate'])){$enddate=$_GET['enddate'];}else{$enddate='';}
if(isset($_POST['enddate'])){$enddate=$_POST['enddate'];}
if(isset($_GET['transform'])){$transform=$_GET['transform'];}else{$transform='progress_summary';}
if(isset($_POST['transform'])){$transform=$_POST['transform'];}

if(isset($_POST['bid'])){$bid=$_POST['bid'];}else{$bid='';}


	if(sizeof($sids)==0){
		$result[]=get_string('youneedtoselectstudents');
		$returnXML=$result;
		$rootName='Error';
		}
	else{

		$d_categorydef=mysql_query("SELECT id, name FROM categorydef WHERE
        type='con' ORDER BY name, id");
		$cattable['category']=array();
		while($category=mysql_fetch_array($d_categorydef,MYSQL_ASSOC)){
			$cattable['category'][]=$category;
			}
		$Students=array();
		$Students['Student']=array();
		/*doing one student at a time*/
		for($c=0;$c<sizeof($sids);$c++){
			$sid=$sids[$c];
			$Student=fetchStudent_short($sid);
			$Comments=fetchComments($sid,$startdate);
			$Student['Comments']=$Comments;
			$Student['Comments']['cattable']=$cattable;
			$Students['Student'][]=$Student;
			}
		$Students['Paper']='portrait';
		$Students['Transform']=$transform;
		$returnXML=$Students;
		$rootName='Students';
		}

require_once('../../scripts/http_end_options.php');
exit;
?>
<div id='xmlStudent' style='visibility:hidden;'>
<?php
?>
