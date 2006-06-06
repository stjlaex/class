<?php 
/**											edit_reports.php
 */

$action='edit_reports_action.php';

$viewtable=$_SESSION{'viewtable'};
$umns=$_SESSION{'umns'};
$mid=$_GET{'mid'};
$bid=$_GET{'bid'};
$col=$_GET{'col'};
$title=$_GET{'title'};
$rid=$_GET{'midlist'};
$pid=$_GET{'pid'};

   	$d_report=mysql_query("SELECT * FROM report WHERE id='$rid'");
   	$report=mysql_fetch_array($d_report,MYSQL_ASSOC);

	$d_subject=mysql_query("SELECT name FROM subject WHERE id='$bid'");
   	$subjectname=mysql_result($d_subject,0);	      
	$d_teacher=mysql_query("SELECT forename, surname FROM users WHERE username='$tid'");
   	$teachername=mysql_fetch_array($d_teacher,MYSQL_ASSOC);	      
	if($pid!=''){
		$d_subject=mysql_query("SELECT name FROM subject WHERE id='$pid'");
		$componentname=mysql_result($d_subject,0);
		}
	else{$componentname='';}

/*	find assessment marks specific to this report */
	$d_mids=mysql_query("SELECT DISTINCT eidmid.mark_id FROM eidmid LEFT
				JOIN rideid ON eidmid.assessment_id=rideid.assessment_id 
				WHERE rideid.report_id='$rid'");
	$mids=array();
	$cols=array();
/*  identify which columns represent the assessment marks and store in cols*/
	while($mid=mysql_fetch_array($d_mids,MYSQL_NUM)){
		for($c=0;$c<sizeof($umns);$c++){
   			if($mid[0]==$umns[$c]['id'] and
				($umns[$c]['component']==$pid
//				or $umns[$c]['component']==''
					)){$mids[]=$mid[0];$cols[]=$c;}
   			}
		}

three_buttonmenu();
?>
  <div id="heading">
	<?php print $title;?>
  </div>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>"> 

	  <table class="listmenu" id="editscores">
		<thead>
		  <tr>
			<th></th>
			<td></td>
<?php
/* headers for the entry field columns*/
   	$inasses=array();
	for($c=0;$c<sizeof($cols);$c++){
    /*iterate over all of the assessment mark columns*/
	/* at same time store information in $inorders[] for use in action page*/	
		$umn=$umns[$cols[$c]];
		$markdef_name=$umn['def_name'];
		$d_markdef=mysql_query("SELECT * FROM markdef WHERE name='$markdef_name'");
		$markdef=mysql_fetch_array($d_markdef,MYSQL_ASSOC);	      
		$scoretype=$markdef{'scoretype'};
		$grading_name=$markdef{'grading_name'};
		if($scoretype=='grade'){
			$d_grading=mysql_query("SELECT grades FROM grading WHERE name='$grading_name'");
			$grading_grades=mysql_result($d_grading,0);
			$pairs=explode (';', $grading_grades);
			print '<th>'.$umn['topic'];
			if($umn['component']!=''){print '<br />'.$umn['component'];}
			print '</th>';
		   	$inorder=array('table'=>'score',
					'field'=>'grade', 'scoretype'=>$scoretype, 
					'grading_grades'=>$grading_grades,'id'=>$mids[$c]);
			}
		else{
		    $inorder=array('table'=>'score',
					'field'=>'score', 'scoretype'=>$scoretype, 
					'grading_grades'=>$grading_grades,'id'=>$mids[$c]);
			print '<th>Decimal Values</th>';
			}
		if($scoretype=='percentage'){
			$total=$umn['mark_total'];
			print '<th>Total (default = '.$total.')</th>';
			}
		$inasses[]=$inorder;
		}
?>
		  </tr>
		</thead>
<?php
	$inorders=array('rid'=>$rid, 'subject'=>$bid, 'component'=>$pid, 'inasses'=>$inasses);
   	if($report['addcategory']=='yes'){
		/*the categories and rating details for later use*/
		list($ratingnames, $catdefs)=fetchReportCategories($rid,$bid);
		$inorders['category']='yes';
		$inorders['catdefs']=$catdefs;
		}
   	if($report['addcomment']=='yes'){
		$inorders['comment']='yes';
		}

	if(isset($_GET{'sid'})){
		/*this was called from a clickthrough for one individual student*/
		$edit_comments_off='no';
		$sid=$_GET{'sid'};
		for($c=0;$c<sizeof($viewtable);$c++){if($viewtable[$c]['sid']==$sid){$row=$c;}}
		$tab=$row+1;
		include('onereport.php');
		}
	else{
		/*row for each student*/
		$edit_comments_off='yes';
		for($row=0;$row<sizeof($viewtable);$row++){
			$sid=$viewtable[$row]['sid'];
			$tab=$row+1;
			include('onereport.php');
			}
		}

$_SESSION{'inorders'}=$inorders;
?>
	</table>
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print $choice;?>" />
	</form>
  </div>












