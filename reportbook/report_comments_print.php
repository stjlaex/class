<?php
/**									report_comments_print.php
 *
 *	Printout of selected students' comments	
 */

$action='report_comments_list.php';
$action_post_vars=array('newyid','newfid','date0','date1','bid');

$sids=$_POST['sids'];
$date0=$_POST['date0'];
if(isset($_POST['date1'])){$date1=$_POST['date1'];}else{$date1=date("Y-m-d");}
if(isset($_POST['bid'])){$bid=$_POST['bid'];}else{$bid='';}
if(isset($_POST['newyid'])){$newyid=$_POST['newyid'];}else{$newyid='';}
if(isset($_POST['newfid'])){$newfid=$_POST['newfid'];}else{$newfid='';}

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
	$cattable['category']=array();
    while($category=mysql_fetch_array($d_categorydef,MYSQL_ASSOC)){
		$cattable['category'][]=$category;
		}

	for($c=0;$c<sizeof($sids);$c++){
		$sid=$sids[$c];
		$Student=fetchStudent_short($sid);
		$Student['publishdate']=date('jS M Y',strtotime($date1));
		$Comments=fetchComments($sid,$date0);
		$Student['Comments']=$Comments;
		$Student['Comments']['cattable']=$cattable;
		xmlechoer('Student',$Student);
		}
?>
</div>
<script>openPrintReport('xmlStudent', 'comments','')</script>
<?php
	include('scripts/redirect.php');
?>
