<?php 
/**		   							new_edit_reports.php
 *
 */

$action='new_edit_reports_action.php';

$viewtable=$_SESSION['viewtable'];
if(isset($_GET['bid'])){$bid=$_GET['bid'];}
if(isset($_GET['pid'])){$pid=$_GET['pid'];}
if(isset($_GET['midlist'])){$rid=$_GET['midlist'];}
if(isset($_GET['title'])){$title=$_GET['title'];}
if(isset($_GET['sid'])){$sid=$_GET['sid'];}
if(isset($_GET['nextrow'])){$nextrow=$_GET['nextrow'];}
if(isset($_POST['nextrow'])){$nextrow=$_POST['nextrow'];}

if(isset($sid) or isset($nextrow)){
	/* This was called from a clickthrough for one individual student
	 * so give access to editing comments. 
	 */
	$edit_comments_off='no';
	if(!isset($_POST['nextnav'])){$nextnav='table';}else{$nextnav=$_POST['nextnav'];}
	if(!isset($sid)){
		$sid=$viewtable[$nextrow]['sid'];
		$bid=$_SESSION['inorders']['subject'];
		$pid=$_SESSION['inorders']['component'];
		$rid=$_SESSION['inorders']['rid'];
		}
	if($nextnav=='component'){
		$bid=$_SESSION['inorders']['subject'];
		$pid=$_POST['nextarea'];
		$rid=$_SESSION['inorders']['rid'];
		}
	$Student=fetchStudent_short($sid);
	$title=$Student['DisplayFullName']['value'];
	}
else{
	/* Viewing the whole list of sids so restrict to editing just the */
	/* assessment columns. */
	$edit_comments_off='yes';
	}

	/* Need the status of the currently selected component
	*/
	$class_crid=$classes[$cids[0]]['crid'];
	$class_stage=$classes[$cids[0]]['stage'];
	if($pid!=''){
		$d_comp=mysql_query("SELECT status FROM component
		   		WHERE id='$pid' AND course_id='$class_crid' AND subject_id='$bid';");
		$compstatus=mysql_result($d_comp,0);
		}
	else{
		$compstatus='%';
		}

	$reportdef=fetch_reportdefinition($rid,$bid);
	$eids=(array)$reportdef['eids'];
	/* The eids from reportdef are all the assessments which may
	 *  possibly be linked to this report entry. Need to filter out
	 *  those not relevant to the current bid/pid combination we are
	 *  dealing with.
	 */
	$ass_colspan=0;
	$AssDefs=array();//	Contains the relevant assessments only.
	foreach($eids as $eid){
		$AssDef=fetchAssessmentDefinition($eid);
		if(($AssDef['Stage']['value']=='%' or $AssDef['Stage']['value']==$class_stage) 
		   and ($AssDef['Subject']['value']=='%' or $AssDef['Subject']['value']==$bid)){
			$AssDefs[]=$AssDef;
			}
		}

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
	<?php print $title;?>
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


<?php
	if(isset($nextrow)){
?>

		<div class="divgroup right">
	   	<table class="listmenu">
			<tr>
			<td>
			<label for="family"><?php print_string('next',$book);?></label>
			</td>
			<td>
			<div class="row <?php if($nextnav=='table'){print 'checked';}?>">
			<label for="table"><?php print_string('table',$book);?></label>
			<input type="radio" name="nextnav" title="Table" id="table" 
				value="table" <?php if($nextnav=='table'){print 'checked';}?> />
				</div>
			  </td>
			<td>
			<div class="row <?php if($nextnav=='student'){print 'checked';}?>">
			<label for="student"><?php print_string('student',$book);?></label>
			<input type="radio" name="nextnav" title="Student" id="student" 
				value="student" <?php if($nextnav=='student'){print 'checked';}?> />
				</div>
			  </td>
			  <td>
			<div class="row <?php if($nextnav=='component'){print 'checked';}?>">
			<label for="component"><?php print_string('component',$book);?></label>
			<input type="radio" name="nextnav" title="Component" id="component" 
				value="component" <?php if($nextnav=='component'){print 'checked';}?> />
				</div>
			</td>
			</tr>
	   	</table>
	   	</div>
<?php
		}
?>


	  <table class="listmenu center" id="editscores">
		<thead>
		  <tr>
<?php
		if($edit_comments_off=='yes'){
?>
			<th><label>
			  <?php print_string('checkall'); ?>
			</label>
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
			</th>
<?php
	/* Headers for the entry field columns. Iterate over the assessment columns and
	 * at the same time store information in $inorders[] for use in the action page. 
	 */
   	$inasses=array();
	foreach($AssDefs as $AssDef){
		$eid=$AssDef['id_db'];
		$grading_grades=$AssDef['GradingScheme']['grades'];
		$ass_compstatus=$AssDef['ComponentStatus']['value'];
		$strands=array();
		if($pid!=''){
			/* If this column is for a component then include any strands*/
			$ass_strandstatus=$AssDef['StrandStatus']['value'];
			$strands=(array)list_subject_components($pid,$AssDef['Course']['value'],$ass_strandstatus);
			//trigger_error($ass_strandstatus.' : '.sizeof($strands),E_USER_WARNING);
			if(sizeof($strands)==0){
				if(($ass_compstatus=='A' or $ass_compstatus==$compstatus 
					  or $compstatus=='%' or ($ass_compstatus=='AV' and ($compstatus='V' or $compstatus='O')))){
					$strands[]=array('id'=>$pid);
					}
				}
			}
		else{
			/* If this column is for a subject then include any
				components - but the strands won't be there*/
			$strands=(array)list_subject_components($bid,$AssDef['Course']['value'],$ass_compstatus);
			if(sizeof($strands)==0){
				$strands[]=array('id'=>$pid);
				}
			}
		foreach($strands as $strand){
			/* Need to identify the mid (if one exists) that is related to 
			 * this assessment for updating scores in the action page.
			 */
			$mids=(array)get_assessment_mids($AssDef,$bid,$strand['id']);
			$ass_colspan++;
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
							 'mids'=>$mids);
				}
			else{
				$inass=array('table'=>'score',
							 'pid'=>$strand['id'],
							 'field'=>'value', 
							 'scoretype'=>'value', 
							 'grading_grades'=>'',
							 'eid'=>$eid,
							 'mids'=>$mids);
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
		//trigger_error('!!!!! '.$bid. ' : '.$pid,E_USER_WARNING);
		$catdefs=get_report_categories($rid,$bid,$pid,'cat',$class_stage);
		$ratings=$reportdef['ratings'];
		$inorders['category']='yes';
		$inorders['catdefs']=$catdefs;
		$inorders['rating_name']=$reportdef['report']['rating_name'];
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
<?php
	if(isset($nextrow)){
?>
	  <input type="hidden" name="nextrow" value="<?php print $nextrow;?>" />
<?php
		}
?>
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>
