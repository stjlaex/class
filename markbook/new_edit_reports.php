<?php 
/**											new_edit_reports.php
 */

$action='new_edit_reports_action.php';

$viewtable=$_SESSION['viewtable'];
$bid=$_GET['bid'];
$pid=$_GET['pid'];
$title=$_GET['title'];
$rid=$_GET['midlist'];
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

	$reportdef=fetchReportDefinition($rid,$bid);
	$eids=(array)$reportdef['eids'];

	$subjectname=get_subjectname($bid);
	$teachername=get_teachername($tid);
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
		  <paper>portrait</paper>
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
		$grading_grades=$AssDef['GradingScheme']['grades'];
		$compstatus=$AssDef['ComponentStatus']['value'];
		$strands=array();
		if($pid!=''){
			/* If this column is for a component then include any strands*/
			$strandstatus=$AssDef['StrandStatus']['value'];
			$strands=(array)list_subject_components($pid,$AssDef['Course']['value'],$strandstatus);
			if(sizeof($strands)==0 and $compstatus!='None'){
				$strands[]=array('id'=>$pid);
				}
			}
		else{
			/* If this column is for a subject then include any
				components - but the strands won't be there*/
			$strands=(array)list_subject_components($bid,$AssDef['Course']['value'],$compstatus);
			if(sizeof($strands)==0){
				$strands[]=array('id'=>$pid);
				}
			}
		while(list($index,$strand)=each($strands)){
			/* Need to identify the mid (if one exists) that is related to 
				this assessment for updating scores in the action page.*/
			$mid=get_assessment_mid($eid,$AssDef['Course']['value'],$bid,$strand['id']);
?>
			<th>
<?php
			print $AssDef['Description']['value'];
			if($AssDef['Component']['value']!=' '){print '<p>'.$AssDef['Component']['value'].'</p>';}
			if(isset($strand['name'])){print '<p>'.$strand['name'].'</p>';}
			if($grading_grades!='' and $grading_grades!=' '){
				$pairs=explode(';', $grading_grades);
				$inass=array('table'=>'score', 
							 'pid'=>$strand['id'],
							 'field'=>'grade', 
							 'scoretype'=>'grade', 
							 'grading_grades'=>$grading_grades,
							 'eid'=>$eid,
							 'mid'=>$mid);
				}
			else{
				$inass=array('table'=>'score',
							 'pid'=>$strand['id'],
							 'field'=>'value', 
							 'scoretype'=>'value', 
							 'grading_grades'=>'',
							 'eid'=>$eid,
							 'mid'=>$mid);
				}
?>
		  </th>
<?php
			$inasses[]=$inass;
			}
		}
?>
		  </tr>
		</thead>
<?php
	$inorders=array('rid'=>$rid,'subject'=>$bid,'component'=>$pid,'inasses'=>$inasses);
   	if($reportdef['report']['addcategory']=='yes'){
		/*the categories and rating details for later use*/
		list($ratingnames,$catdefs)=fetchReportCategories($rid,$bid);
		$inorders['category']='yes';
		$inorders['catdefs']=$catdefs;
		}
	else{
		$inorders['category']='no';
		}
   	if($reportdef['report']['addcomment']=='yes'){
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
