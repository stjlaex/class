<?php 
/**									edit_report_scores.php
 *
 */

$action='edit_report_scores_action.php';

$viewtable=$_SESSION['viewtable'];
if(isset($_GET['bid'])){$bid=$_GET['bid'];}
if(isset($_GET['pid'])){$pid=$_GET['pid'];}
if(isset($_GET['midlist'])){$rid=$_GET['midlist'];}
if(isset($_GET['title'])){$title=$_GET['title'];}


/* Need the status of the currently selected component*/
$class_crid=$classes[$cids[0]]['crid'];
$class_stage=$classes[$cids[0]]['stage'];
if($pid!=''){
	$d_comp=mysql_query("SELECT status FROM component 
							WHERE id='$pid' AND course_id='$class_crid' AND subject_id='$bid';");
	if(mysql_num_rows($d_comp)==0){
		$d_comp=mysql_query("SELECT status FROM component WHERE id='$pid' AND course_id='$class_crid';");
		}
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
	if(($AssDef['Stage']['value']=='%' or $AssDef['Stage']['value']==$class_stage) and ($AssDef['Subject']['value']=='%' or $AssDef['Subject']['value']==$bid)){
		$AssDefs[]=$AssDef;
		}
	}

$subjectname=get_subjectname($bid);
$teachername=get_teachername($tid);
if($pid!=''){
	$componentname=get_subjectname($pid);
	}
else{$componentname='';}

$rowno=0;
$extrabuttons='';
$extrabuttons['printreportsummary']=array('name'=>'current',
										  'title'=>'printreportsummary',
										  'value'=>'report_summary_preview.php',
										  'onclick'=>'checksidsAction(this)'
										  );
three_buttonmenu($extrabuttons,$book);
?>
	<div id="heading">
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
						<th colspan='2'>
							<label class="checkall">
 								<input type="checkbox" name="checkall" value="yes" onChange="checkAll(this);" />
							</label>
						</th>
						<th>
							<label><?php print_string('student'); ?></label>
						</th>
<?php
	$rowno++;
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
	$inorders['category']='no';
	if($reportdef['report']['addcomment']=='yes'){
		$inorders['comment']='yes';
		}
	else{
		$inorders['comment']='no';
		}
	for($row=0;$row<sizeof($viewtable);$row++){
		$sid=$viewtable[$row]['sid'];
		$tab=$row+1;
		$inc=0;
		$Report=array();
		$Report['Assessments']['Assessment']=array();
		/*this is the xml-ready array*/
		$Report['Subject']=array('id'=>$bid, 'value'=>$subjectname);
		if($pid!=''){$Report['Component']=array('id'=>$pid, 'value'=>$componentname);}
?>
				<tbody id="<?php echo $sid;?>">
					<!--tr id="<?php echo $sid.'-0';?>" class="rowplus" onclick="clickToReveal(this);"-->
					<tr id="<?php echo $sid.'-0';?>">
						<!--th></th-->
						<td colspan='2'>
							<input type="checkbox" name="sids[]" value="<?php print $sid;?>" />
						</td>
						<td>
							<h4>
								<a href="infobook.php?current=student_view.php&sid=<?php print $viewtable[$row]['sid'];?>&sids[]=<?php print $viewtable[$row]['sid'];?>" target="viewinfobook" onclick="parent.viewBook('infobook');"<?php if($viewtable[$row]['preferredforename']!=''){$preferredforename='&nbsp;('.$viewtable[$row]['preferredforename'].')';}else{$preferredforename='';}?>>
									<?php print $viewtable[$row]['surname'];?>,&nbsp;<?php print $viewtable[$row]['forename'].'&nbsp;'.$viewtable[$row]['middlenames'].$preferredforename;?>
								</a>
							</h4>
						</td>

<?php
		reset($inasses);
		while(list($index,$inass)=each($inasses)){
			$eid=$inass['eid'];
			$Assessments=(array)fetchAssessments_short($sid,$eid,$bid,$inass['pid']);
			if(sizeof($Assessments)>0){
				$Report['Assessments']['Assessment'][]=$Assessments[0];
				$value=$Assessments[0]['Value']['value'];
				}
			else{
				$value='';
				}
			$grading_grades=$inass['grading_grades'];
			if($grading_grades!='' and $grading_grades!=' '){
				$pairs=explode (';', $grading_grades);
?>
						<td>
							<select tabindex="<?php print $tab;?>" name="sid<?php print $sid.':'.$inc++;?>">
<?php 
				print '<option value="" ';
				if($value==''){print 'selected';}
				print ' ></option>';
				for($c3=0;$c3<sizeof($pairs);$c3++){
					list($level_grade, $level)=explode(':',$pairs[$c3]);
					print '<option value="'.$level.'" ';
					if($value==$level){print 'selected';}
					print '>'.$level_grade.'</option>';
					}
?>
							</select>
						</td>
<?php
				}
			else{
				print '<td><input  style="float:none;text-align:right;" pattern="decimal" type="text" tabindex="'.$tab.'" name="sid'.$sid.':'.$inc++.'" maxlength="8" value="'.$value.'" /></td>';
				}
			}
?>
					</tr>
					<tr id="<?php echo $sid.'-1';?>" class='hidden'>
						<td colspan="<?php echo (3+count($inasses));?>">
<?php
		if($reportdef['report']['addcomment']=='yes'){ 
			$teacherdone=false;
			$Report['Comments']=fetchReportEntry($reportdef,$sid,$bid,$pid);
			$totalentryn=sizeof($Report['Comments']['Comment']);
			for($entryn=0;$entryn<=$totalentryn;$entryn++){
				if($reportdef['report']['addcomment']=='no' and !$teacherdone){
					if($totalentryn<1){
						$inmust='yes';
						$Comment=array('Text'=>array('value'=>'','value_db'=>''),
									   'Teacher'=>array('value'=>''));
						}
					else{
						$Comment=$Report['Comments']['Comment'][$entryn];
						$inmust=$Comment['id_db'];
						}
					$rowstate='rowminus';
					$rowclass='revealed';
					$teacherdone=true;
					}
				elseif($entryn==$totalentryn and !$teacherdone){
					$Comment=array('Text'=>array('value'=>'','value_db'=>''),
					'Teacher'=>array('value'=>'ADD NEW ENTRY'));
					$inmust='yes';
					$rowstate='rowminus';
					$rowclass='revealed';
					}
				else{
					if($tid==$Report['Comments']['Comment'][$entryn]['Teacher']['id_db']){$teacherdone=true;}
					$Comment=$Report['Comments']['Comment'][$entryn];
					$inmust=$Comment['id_db'];
					if($totalentryn<1 or $tid==$Report['Comments']['Comment'][$entryn]['Teacher']['id_db']){
						$rowstate='rowminus';
						$rowclass='revealed';
						}
					else{
						$rowstate='rowplus';
						$rowclass='hidden';
						}
					}
				$rown=0;
				$en=$entryn+1;
				$openId=$rid.'-'.$sid.'-'.$bid.'-'.$pid.'-'.$en;
				$Comment['id_db']=$openId;

				if($edit_comments_off!='yes' and ((!$teacherdone and $entryn==$totalentryn) or ($entryn<$totalentryn) or $totalentryn<1)){
					if($reportdef['report']['addcomment']=='yes'){
?>
							<div id="<?php echo $openId;?>">
								<?php print_string('teachercomment');?>:
								<div class="special"><?php print $Comment['Teacher']['value'];?></div>
<?php
					if($Comment['Teacher']['id_db']==$tid or (!$teacherdone and $entryn==$totalentryn)){
?>
		<span class="clicktowrite" name="Write" onClick="clickToWriteCommentNew(<?php print $sid.','.$rid.',\''.$bid.'\',\''.$pid.'\',\''.$entryn.'\',\''.$openId.'\'';?>);" title="<?php print_string('clicktowritecomment');?>" /></span>
		<input type="hidden" id="inmust<?php print $openId;?>" name="inmust<?php print $sid.':'.$inc++;?>" value="<?php print $inmust;?>" />
<?php
						}
					if($reportdef['report']['commentlength']=='0'){$commentlength='';}
					else{$commentlength=' maxlength="'.$reportdef['report']['commentlength'].'"';}
					if($Comment['Text']['value_db']!=''){$display='';}
					else{$display='display:none;';}
					print '';
					print '';
					print '<div '.$commentlength.' rows="1" cols="80" readonly="readonly" style="'.$display.' "';
					if($Comment['Teacher']['id_db']==$tid){
						print 'onClick="clickToWriteCommentNew('.$sid.','.$rid.',\''.$bid.'\',\''.$pid.'\',\''.$entryn.'\',\''.$openId.'\');"'; 
						}
					print ' tabindex="'.$tab.'" name="sid'.$sid.':'.$inc++.'" id="text'.$openId.'">';
					print $Comment['Text']['value_db'];
					print '</div>';
					$imagebuttons=array();
					if($inmust!='yes' and $reportdef['report']['addcomment']=='yes' and $Comment['Teacher']['id_db']==$tid){
						$imagebuttons['clicktodelete']=array('name'=>'current',
															 'id'=>'delete'.$openId,
															 'value'=>'delete_reportentry.php',
															 'title'=>'deletethiscomment');
						}
					rowaction_buttonmenu($imagebuttons,array(),$book);
					print '';
					}
?>
							</div>
							<div id="<?php print 'xml-'.$openId;?>" style="display:none;">
								<?php xmlechoer('Comment',$Comment); ?>
							</div>
<?php
				}
			}
?>
						</td>
					</tr>
				</tbody>
<?php
		}


				}

		$_SESSION['inorders']=$inorders;
?>
				</tbody>
			</table>

			<input type="hidden" name="current" value="<?php print $action;?>" />
			<input type="hidden" name="choice" value="<?php print $choice;?>" />
			<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
		</form>
	</div>
