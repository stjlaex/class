<?php 
/**		   							edit_skills.php
 *
 */

$action='edit_skills_action.php';

$viewtable=$_SESSION['viewtable'];
if(isset($_GET['bid'])){$bid=$_GET['bid'];}
if(isset($_GET['pid'])){$pid=$_GET['pid'];}
if(isset($_GET['midlist'])){$rid=$_GET['midlist'];}
if(isset($_GET['title'])){$title=$_GET['title'];}
if(isset($_GET['sid'])){$sid=$_GET['sid'];}

if(isset($sid)){
	$edit_comments_off='no';
	$Student=fetchStudent_short($sid);
	$title=$Student['DisplayFullName']['value'];
	}
else{
	$edit_comments_off='yes';
	}

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
$ass_colspan=0;
$AssDefs=array();
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

$extrabuttons='';
three_buttonmenu($extrabuttons,$book);
?>
	<div id="heading">
		<label><?php print $subjectname.' '.$componentname;?></label>
	</div>
	<div  id="viewcontent" class="content">
		<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>"> 
			<table class="listmenu center" id="editscores">
				<thead>
					<tr>
						<th></th>
						<th></th>
<?php
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
	$catdefs=get_report_skill_statements($rid,$bid,$pid,$class_stage);
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

	$inc=0;
	$Report=array();
	$Report['Assessments']['Assessment']=array();
	$Report['Subject']=array('id'=>$bid, 'value'=>$subjectname);
	if($pid!=''){$Report['Component']=array('id'=>$pid, 'value'=>$componentname);}
?>

	<h4>
		<a href="infobook.php?current=student_view.php&sid=<?php print $viewtable[$row]['sid'];?>&sids[]=<?php print $viewtable[$row]['sid'];?>" target="viewinfobook" onclick="parent.viewBook('infobook');"<?php if($viewtable[$row]['preferredforename']!=''){$preferredforename='&nbsp;('.$viewtable[$row]['preferredforename'].')';}else{$preferredforename='';}?>>
			<?php print $viewtable[$row]['surname'];?>,&nbsp;<?php print $viewtable[$row]['forename'].'&nbsp;'.$viewtable[$row]['middlenames'].$preferredforename;?>
		</a>
	</h4>

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

		print '<input pattern="decimal" type="hidden" tabindex="'.$tab.'" name="sid'.$sid.':'.$inc++.'" maxlength="8" value="'.$value.'" />';
	}
?>
<?php
	if($reportdef['report']['addcomment']=='yes' or $reportdef['report']['addcategory']=='yes'){ 
		$teacherdone=false;
		if($reportdef['report']['addcomment']=='yes' and $reportdef['report']['addcategory']=='no'){$Report['Comments']=fetchReportEntry($reportdef,$sid,$bid,$pid);}
		if($reportdef['report']['addcategory']=='yes'){$Report['Comments']=fetchSkillLog($reportdef,$sid,$bid,$pid);}
		if(!isset($Report['Comments']['Comment'][0]['Skills']['Skill'])){$Report['Comments']['Comment']=array();}
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
?>
		<input type="hidden" id="inmust<?php print $openId;?>" name="inmust<?php print $sid.':'.$inc++;?>" value="<?php print $inmust;?>" />
<?php
				if($reportdef['report']['addcategory']=='yes'){
					$ass_colspan++;
					unset($Skills);
					if(isset($Comment['Skills'])){$Skills=$Comment['Skills'];}
					else{
						$Skills['Skill']=array();
						$Skills['ratingname']=get_report_ratingname($reportdef,$bid);
						}
					$ratings=get_ratings($Skills['ratingname']);

					foreach($catdefs as $catindex=> $catdef){
						$catid=$catdefs[$catindex]['id'];
						$Statement=array('Value'=>$catdefs[$catindex]['name']);
						$Statement=personaliseStatement($Statement,$Student);
						if($catdefs[$catindex]['rating']!=''){
							if(!isset($cat_grading_grades)){
								/*TODO: Only works with a single uniform grade scheme. */
								$gena=$catdefs[$catindex]['rating_name'];
								$d_g=mysql_query("SELECT grades FROM grading WHERE name='$gena';");
								if(mysql_num_rows($d_g)>0){$cat_grading_grades=mysql_result($d_g,0);}
								else{$cat_grading_grades='';}
								}
							$statementrating='<span>'.scoreToGrade($catdefs[$catindex]['rating'],$cat_grading_grades).'</span>';
							}
						else{
							$statementrating='';
							}

						$extra_colspan=$ass_colspan+1;

						$statementlabel='';

						if($catdefs[$catindex]['rating']!=''){$statementlabel=$statementrating.' ';}
						if($catdefs[$catindex]['subtype']!=''){$statementlabel.='<label style="float:right;">'.get_subjectname($catdefs[$catindex]['subtype']).'</label><br />';}
						elseif($statementrating!=''){
							$statementlabel=$statementrating.'';
							}
						else{$statementlabel='';}
						print '';
						print '<fieldset class="divgroup markbook-img">';
						print '<div class="list-box"><span>'.display_date($setcat_date).'</span>';
						print '<ul class="chk-list">';

						$setcat_value=-1000;
						$setcat_date='';

						if(isset($Skills['Skill'][$catindex]) 
						   and $Skills['Skill'][$catindex]['id_db']==$catid){
							$setcat_value=$Skills['Skill'][$catindex]['value'];
							$setcat_date=$Skills['Skill'][$catindex]['date'];
							}
			   			else{
							foreach($Skills['Skill'] as $Category){
								if($Category['id_db']==$catid){
									$setcat_value=$Category['value'];
									$setcat_date=$Category['date'];
									}
								}
							}
						if(($setcat_value==' ' or $setcat_value=='') and $setcat_value!='0'){
							$setcat_value=-1000;
							$setcat_date='';
							}
						foreach($ratings as $value => $descriptor){
							$checkclass='';
							$checked='';
							$trafficlite='';
							if($setcat_value==$value){
								$checkclass='checked';
								$checked='checked';
								if($value=='1'){$checkclass=' golite';}
								elseif($value=='0'){$checkclass=' pauselite';}
								elseif($value=='-1'){$checkclass='hilite';}
								}
							if($descriptor=='red'){$trafficlite='class="hilite"';}
							elseif($descriptor=='green'){$trafficlite='class="golite"';}
							elseif($descriptor=='yellow'){$trafficlite='class="pauselite"';}
							else{$trafficlite='';}
							print '<li class="row '.$checkclass.'"><label '.$trafficlite.'>'.$descriptor.'</label>';
							print '<input type="radio" name="sid'.$sid.':'.$inc. '" tabindex="'.$tab.'" value="'.$value.'" '.$checked;
							print ' /></li>';
							}

						print '<!--li>'.get_string('uncheck',$book).'</li-->';
						print '<li><input type="radio" name="sid'.$sid.':'.$inc. '" value="uncheck" /></li>';

						$inc++;
						print '';
				
						if($reportdef['report']['addcategory']=='yes' and $reportdef['report']['course_id']=='FS'){
							print '<li>';
							$imagebuttons=array();
							$imagebuttons['clicktoload']=array('name'=>'Attachment',
														 'onclick'=>"clickToAttachFile($sid,$rid,'$catid','$pid','$sid')", 
														 'class'=>'clicktoload',
														 'value'=>'category_editor.php',
														 'title'=>'clicktoattachfile');
							$d_c=mysql_query("SELECT r.id FROM report_skill as r JOIN file as f ON r.id=f.other_id WHERE r.id='$catid' AND r.profile_id='$rid' AND f.owner_id='$sid';");
							rowaction_buttonmenu($imagebuttons,array(),$book);
							require_once('lib/eportfolio_functions.php');

							print '</li></ul>';
							print '</div><h5>'.$statementlabel. $Statement['Value'].'</h5>';

							while($c=mysql_fetch_array($d_c,MYSQL_ASSOC)){
								$files=(array)list_files($Student['EPFUsername']['value'],'assessment',$c['id']);
								display_file($Student['EPFUsername']['value'],'assessment',$catid,'');
								}
							print '</fieldset>';
							}
						if($setcat_date!=' ' and $setcat_date!=''){
							print '<input type="hidden" name="cat'.$sid.':'.$catid.'" value="'.$setcat_value.'"/>';
							print '<input type="hidden" name="dat'.$sid.':'.$catid.'" value="'.$setcat_date.'"/>';
							}
						}
					}
				}
			}
		}
	}

$_SESSION['inorders']=$inorders;
?>
			</table>

			<div id="preview">
				<img id="imgpreview" src="" alt="Preview" onclick="getElementById('preview').style.display='none';getElementById('shadow').style.display='none';">
			</div>
			<div id="shadow" onclick="getElementById('preview').style.display='none';getElementById('shadow').style.display='none';"><div>

			<input type="hidden" name="current" value="<?php print $action;?>" />
			<input type="hidden" name="choice" value="<?php print $choice;?>" />
			<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
		</form>
	</div>
