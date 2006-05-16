<?php
/**									report_incidents_print.php
 *
 *	Printout of selected students' incidents	
 */

$action='report_incidents_list.php';

$sids=$_POST{'sids'};
$date0=$_POST{'date0'};
if(isset($_POST{'date1'})){$date1=$_POST{'date1'};}else{$date1=$today;}
if(isset($_POST{'bid'})){$bid=$_POST{'bid'};}else{$bid='';}
if(isset($_POST{'newyid'})){$newyid=$_POST{'newyid'};}else{$newyid='';}
if(isset($_POST{'fid'})){$fid=$_POST{'fid'};}else{$fid='';}

include('scripts/sub_action.php');

if($sids==''){
 	 $error[]=get_string('youneedtoselectstudents');
   	 include('scripts/results.php');
   	 include('scripts/redirect.php');
	 exit;
	 }

$result[]=get_string('printwindowwillopen');
include('scripts/results.php');

?>
<div id='xmlStudent' style='visibility:hidden;'>
<?php
    $d_categorydef=mysql_query("SELECT id, name FROM categorydef WHERE
        type='con' ORDER BY name, id");
	$catids=array();
	$catnames=array();
    while($category=mysql_fetch_array($d_categorydef,MYSQL_ASSOC)) {
		$catids[]=$category{'id'};
		$catnames[]=$category{'name'};
		}

	for($c=0;$c<sizeof($sids);$c++){
		$sid=$sids[$c];
		$Student=fetchshortStudent($sid);
		$Student['pubdate']=$date1;
		$thisncyear=$Student['NCyearActual']['value'];
		$Comments=fetchComments($sid,$date0,$thisncyear);
		$Student['Comments']=$Comments;
		$cattable=array();
		$cattable['catnames']=$catnames;
		$Student['cattable']=$cattable;
		xmlpreparer('Student',$Student);
		}
?>
</div>
<script>openPrintReport('xmlStudent', 'commentsprint')</script>
<?php
	include('scripts/redirect.php');
?>
