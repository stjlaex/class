<?php 
/**											new_edit_reports.php
 */

$action='new_edit_reports_action.php';

$viewtable=$_SESSION['viewtable'];
$bid=$_GET['bid'];
$title=$_GET['title'];
$rid=$_GET['midlist'];
$pid=$_GET['pid'];
if(isset($_GET['sid'])){
	/* This was called from a clickthrough for one individual student */
	/* so give access to editing comments. */
	$edit_comments_off='no';
	$sid=$_GET['sid'];
	}
else{
	/* Viewing the whole list of sids so restrict to editing just the */
	/* assessment columns. */
	$edit_comments_off='yes';
	}

	$reportdefs=array();
	$reportdef=fetchReportDefinition($rid,$bid);
	$reportdefs[]=fetchReportDefinition($rid,$bid);
	$report=$reportdef['report'];
	$eids=(array)$reportdef['eids'];

	$subjectname=get_subjectname($bid);
	$teachername=display_teachername($tid);
	if($pid!=''){
   		$componentname=get_subjectname($pid);
		}
	else{$componentname='';}

$extrabuttons='';
if($edit_comments_off=='yes'){
	$extrabuttons['printreportsummary']=array('name'=>'current',
											  'title'=>'printreportsummary',
											  'value'=>'report_summary_preview.php',
											  'onclick'=>'checksidsAction(this)'
											  );
	}
three_buttonmenu($extrabuttons,$book);
?>
  <div id="heading">
	<label><?php print $title;?></label>
	<label><?php print $subjectname.' '.$componentname;?></label>
  </div>

  <div  id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>"> 

	  <div id="xml-checked-action" style="display:none;">
		<reportids>
		  <rids><?php print $rid;?></rids>
		  <bid><?php print $bid;?></bid>
		  <pid><?php print $pid;?></pid>
		  <transform>subject_report_summary</transform>
		</reportids>
	  </div>

	  <table class="listmenu center" id="editscores">
		<thead>
		  <tr>
<?php
		if($edit_comments_off=='yes'){
?>
			<th>
			  <?php print_string('checkall'); ?>
			  <input type="checkbox" name="checkall" value="yes" onChange="checkAll(this);" />
			</th>
<?php
			}
		else{
?>
			<th>
			</th>
<?php
			}
?>	
			<th>
			  <?php print_string('student'); ?>
			</th>
<?php
	/* Headers for the entry field columns. Iterate over the assessment columns and
		at the same time store information in $inorders[] for use in the action page. */	
   	$inasses=array();
	while(list($index,$eid)=each($eids)){
		$AssDef=fetchAssessmentDefinition($eid);
		$AssDefs[]=$AssDef;
		/* Need to identify the mid (if one exists) that is related to 
			this assessment for updating scores in the action page.*/
		$mid=get_assessment_mid($eid,$AssDef['Course']['value'],$bid,$pid);
?>
			<th>
<?php
		print $AssDef['Description']['value'];
		if($AssDef['Component']['value']!=''){print '<br />'.$AssDef['Component']['value'];}
		$grading_grades=$AssDef['GradingScheme']['grades'];
		if($grading_grades!='' and $grading_grades!=' '){
			$pairs=explode(';', $grading_grades);
		   	$inass=array('table'=>'score','pid'=>$pid,
					'field'=>'grade', 'scoretype'=>'grade', 
					'grading_grades'=>$grading_grades,'eid'=>$eid,'mid'=>$mid);
			}
		else{
		    $inass=array('table'=>'score','bid'=>$bid,'pid'=>$pid,
					'field'=>'value', 'scoretype'=>'value', 
					'grading_grades'=>'','eid'=>$eid,'mid'=>$mid);
			}
?>
		  </th>
<?php
		$inasses[]=$inass;
		}
?>
		  </tr>
		</thead>
<?php
	$inorders=array('rid'=>$rid, 'subject'=>$bid, 'component'=>$pid, 'inasses'=>$inasses);
   	if($report['addcategory']=='yes'){
		/*the categories and rating details for later use*/
		list($ratingnames,$catdefs)=fetchReportCategories($rid,$bid);
		$inorders['category']='yes';
		$inorders['catdefs']=$catdefs;
		}
	else{
		$inorders['category']='no';
		}
   	if($report['addcomment']=='yes'){
		$inorders['comment']='yes';
		}
	else{
		$inorders['comment']='no';
		}

	if($edit_comments_off!='yes'){
		for($c=0;$c<sizeof($viewtable);$c++){if($viewtable[$c]['sid']==$sid){$row=$c;}}
		$tab=$row+1;
		include('new_onereport.php');
		}
	else{
		/*row for each student*/
		for($row=0;$row<sizeof($viewtable);$row++){
			$sid=$viewtable[$row]['sid'];
			$tab=$row+1;
			include('new_onereport.php');
			}
		}

$_SESSION['inorders']=$inorders;
?>
	</table>
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>
