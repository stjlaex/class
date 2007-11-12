<?php 
/**				   				report_reports_list.php
 */

$action='report_reports.php';

if(isset($_POST['newfid'])){$fid=$_POST['newfid'];}else{$fid='';}
if(isset($_POST['newyid'])){$yid=$_POST['newyid'];}else{$yid='';}
if(isset($_POST['year'])){$year=$_POST['year'];}
if(isset($_POST['stage'])){$stage=$_POST['stage'];}
if(isset($_POST['rids'])){$postrids=$_POST['rids'];}else{$postrids=array();}
if(isset($_POST['wrapper_rid'])){$wrapper_rid=$_POST['wrapper_rid'];}

include('scripts/sub_action.php');

	if($fid!=''){
		$students=listin_community(array('id'=>'','type'=>'form','name'=>$fid));
		$formperm=getFormPerm($fid,$respons);
		$yid=get_form_yeargroup($fid);
		$yearperm=getYearPerm($yid,$respons);
		}
	elseif($yid!=''){
		$students=listin_community(array('id'=>'','type'=>'year','name'=>$yid));
		$yearperm=getYearPerm($yid,$respons);
		$formperm=$yearperm;
		}
	else{
		$students=listin_cohort(array('id'=>'','course_id'=>$rcrid,'year'=>$year,'stage'=>$stage));
		}

$rids=array();
if(isset($wrapper_rid)){
	$d_rid=mysql_query("SELECT categorydef_id AS report_id FROM ridcatid WHERE
				 report_id='$wrapper_rid' AND subject_id='wrapper' ORDER BY categorydef_id");
	$rids[]=$wrapper_rid;//add to the start of the rids
	while($rid=mysql_fetch_array($d_rid,MYSQL_ASSOC)){
		$rids[]=$rid['report_id'];
		}
	}
else{
	while(list($index,$rid)=each($postrids)){
		if($rid!=0){$rids[]=$rid;}
		}
	}
$extrabuttons=array();
$extrabuttons['previewselected']=array('name'=>'current',
								'value'=>'report_reports_print.php',
								'onclick'=>'checksidsAction(this)');
if($_SESSION['role']=='admin'){
	$extrabuttons['publishpdf']=array('name'=>'current',
								   'value'=>'report_reports_publish.php');
	$extrabuttons['email']=array('name'=>'current',
								   'value'=>'report_reports_email.php');
	}

two_buttonmenu($extrabuttons,$book);
?>
  <div id="heading">
  <?php print get_string('subjectreportsfor',$book).' '.$fid;?>
  </div>
  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <div id="xml-checked-action" style="display:none;">
		<reportids>
<?php
	$reports=array();
	$input_elements='';
	while(list($index,$rid)=each($rids)){
		$reportdef=fetchReportDefinition($rid);
		$reportdefs[]=$reportdef;
			/*this is to feed the rids to the javascript function*/
?>
		  <rids><?php print $rid;?></rids>
<?php
	    $input_elements.=' <input type="hidden" name="rids[]" value="'.$rid.'" />';
		}
?>
		</reportids>
	  </div>
	  <div class="fullwidth">
		<table class="listmenu">
		  <tr>
			<th>
			  <label id="checkall">
				<?php print_string('checkall');?>
				<input type="checkbox" name="checkall" value="yes" onChange="checkAll(this);" />
			  </label>
			</th>
			<th><?php print_string('student');?></th>
<?php
		reset($rids);
		while(list($index,$rid)=each($rids)){
				$summaries=(array)$reportdefs[$index]['summaries'];
				while(list($index2,$summary)=each($summaries)){
					$summaryid=$summary['subtype'];
					if($summary['type']=='com'){
						if($formperm['x']==1 and $summaryid=='form'){
							print '<th>'.get_string('formtutor').'</th>';
							}
						elseif($yearperm['x']==1 and $summaryid=='year'){
							print '<th>'.get_string('yearhead').'</th>';
							}
						elseif($yearperm['x']==1 and $summaryid=='section'){
							print '<th>'.get_string('sectionhead').'</th>';
							}
						}
					}
				}
			
?>
			<th colspan="16"><?php print_string('completedsubjectreports',$book);?></th>
		  </tr>
<?php
	while(list($index,$student)=each($students)){
		$sid=$student['id'];
?>
		  <tr>
			<td>
			  <input type="checkbox" name="sids[]" value="<?php print $sid; ?>" />
			</td>
			<td>
				   <?php print $student['surname']; ?>, <?php print $student['forename']; ?>
					  (<?php print $student['form_id']; ?>)
			</td>
<?php
		reset($rids);
		while(list($index,$rid)=each($rids)){
   			$summaries=(array)$reportdefs[$index]['summaries'];
			while(list($index2,$summary)=each($summaries)){
				$summaryid=$summary['subtype'];
				if($summary['type']=='com'){
					if($formperm['x']==1 and $summaryid=='form'){
						$d_summaryentry=mysql_query("SELECT teacher_id
					FROM reportentry WHERE report_id='$rid' AND
					student_id='$sid' AND subject_id='summary' AND
					component_id='$summaryid' AND entryn='1'");
						$openId=$sid.'summary-'.$summaryid;
?>
			<td id="icon<?php print $openId;?>" <?php if(mysql_num_rows($d_summaryentry)>0){print 'class="vspecial"';}?> >
			  <img class="clicktoedit" name="Write"  
				onClick="clickToWriteComment(<?php print $sid.','.$rid.',\'summary\',\''.$summaryid.'\',\'0\',\''.$openId.'\'';?>);" />
			</td>
<?php
						}
					elseif($yearperm['x']==1 and $summaryid=='year'){
						$d_summaryentry=mysql_query("SELECT teacher_id
					FROM reportentry WHERE report_id='$rid' AND
					student_id='$sid' AND subject_id='summary' AND
					component_id='$summaryid' AND entryn='1'");
						$openId=$sid.'summary-'.$summaryid;
?>
			<td id="icon<?php print $openId;?>" <?php if(mysql_num_rows($d_summaryentry)>0){print 'class="vspecial"';}?> >
			  <img class="clicktoedit" name="Write"  
				onClick="clickToWriteComment(<?php print $sid.','.$rid.',\'summary\',\''.$summaryid.'\',\'0\',\''.$openId.'\'';?>);" />
			</td>
<?php
						}
					elseif($yearperm['x']==1 and $summaryid=='section'){
						$d_summaryentry=mysql_query("SELECT teacher_id
					FROM reportentry WHERE report_id='$rid' AND
					student_id='$sid' AND subject_id='summary' AND
					component_id='$summaryid' AND entryn='1'");
						$openId=$sid.'summary-'.$summaryid;
?>
			<td id="icon<?php print $openId;?>" <?php if(mysql_num_rows($d_summaryentry)>0){print 'class="vspecial"';}?> >
			  <img class="clicktoedit" name="Write"  
				onClick="clickToWriteComment(<?php print $sid.','.$rid.',\'summary\',\''.$summaryid.'\',\'0\',\''.$openId.'\'';?>);" />
			</td>
<?php
						}
					}
				}
			}

		/* Going to check each subject class for completed assessments
		and reportentrys and list in the table highlighting those that
		met this reports required elements for completion. */
		reset($rids);
		while(list($index,$rid)=each($rids)){
			$eids=(array)$reportdefs[$index]['eids'];
		    if(isset($reportdefs[$index]['report']['course_id'])){
				$crid=$reportdefs[$index]['report']['course_id'];
				$commentcomp=$reportdefs[$index]['report']['commentcomp'];
				$compstatus=$reportdefs[$index]['report']['component_status'];
				}

			$d_subjectclasses=mysql_query("SELECT DISTINCT subject_id, class_id
					FROM class JOIN cidsid ON cidsid.class_id=class.id
					WHERE cidsid.student_id='$sid' AND
					class.course_id='$crid' ORDER BY subject_id");
			while($subject=mysql_fetch_array($d_subjectclasses,MYSQL_ASSOC)){
			    $bid=$subject['subject_id'];
				$cid=$subject['class_id'];
				$d_teacher=mysql_query("SELECT teacher_id FROM tidcid
						WHERE class_id='$cid'");
				$reptids=array();
				while($teacher=mysql_fetch_array($d_teacher)){
					$reptids[]=$teacher['teacher_id'];
					}

				$components=array();
				if($compstatus!='None'){
					$components=list_subject_components($bid,$crid,$compstatus);
					}
				if(sizeof($components)==0){$components[]=array('id'=>' ','name'=>'');}

				reset($components);
			   	while(list($compindex,$component)=each($components)){
					$pid=$component['id'];
					$strands=(array)list_subject_components($pid,$crid);

					reset($eids);
					$scoreno=0;
					while(list($eidindex,$eid)=each($eids)){
						$Assessments=fetchAssessments_short($sid,$eid,$bid,$pid);
						$scoreno+=sizeof($Assessments);
						while(list($strandindex,$strand)=each($strands)){
							$Assessments=fetchAssessments_short($sid,$eid,$bid,$strand['id']);
							$scoreno+=sizeof($Assessments);
							}
						}

					print '<td style="width:3em;" title="';
				   	while(list($index, $reptid)=each($reptids)){
						print $reptid.' ';
						}
					reset($reptids);
					$reportentryno=checkReportEntry($rid,$sid,$bid,$pid);
					if(($reportentryno>0 and
						$commentcomp=='yes' and $scoreno>0) or 
						($commentcomp=='no' and $scoreno>0)){
						print '" class="vspecial">';}
					else{print '">';}
					print $bid.'.'.$pid.'</td>';
			   		}
				}
			}
?>
		 </tr>
<?php
		}
?>
		</table>
	  </div>
  <?php print $input_elements;?>
 	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
 	<input type="hidden" name="choice" value="<?php print $choice;?>" />
 	<input type="hidden" name="current" value="<?php print $action;?>" />
	</form>
  </div>
