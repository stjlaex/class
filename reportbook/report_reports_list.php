<?php 
/**				   				report_reports_list.php
 */

$action='report_reports.php';

if(isset($_POST['newfid'])){$fid=$_POST['newfid'];}else{$fid='';}
if(isset($_POST['newyid'])){$yid=$_POST['newyid'];}else{$yid='';}
if(isset($_POST['year'])){$year=$_POST['year'];}
if(isset($_POST['stage'])){$stage=$_POST['stage'];}
if(isset($_POST['rids'])){$rids=$_POST['rids'];}else{$rids=array();}
if(isset($_POST['wrapper_rids'])){
	/*there is always one blank wrapper_rid and one wrapper_rid with a value*/
	$wrapper_rids=(array)$_POST['wrapper_rids'];
	while(list($index,$value)=each($wrapper_rids)){if($value!=''){$wrapper_rid=$value;}}
	}

include('scripts/sub_action.php');

	if($fid!=''){
		$students=listin_community(array('id'=>'','type'=>'form','name'=>$fid));
		$formperm=getFormPerm($fid,$respons);
		}
	elseif($yid!=''){
		$students=listin_community(array('id'=>'','type'=>'year','name'=>$yid));
		$yearperm=getYearPerm($yid,$respons);
		$formperm=$yearperm;
		}
	else{
		$students=listin_cohort(array('id'=>'','course_id'=>$rcrid,'year'=>$year,'stage'=>$stage));
		}

if(isset($wrapper_rid)){
	$d_rid=mysql_query("SELECT categorydef_id AS report_id FROM ridcatid WHERE
				 report_id='$wrapper_rid' AND subject_id='wrapper' ORDER BY categorydef_id");
	$rids=array();
	$rids[]=$wrapper_rid;//add to the start of the rids
	while($rid=mysql_fetch_array($d_rid,MYSQL_ASSOC)){
		$rids[]=$rid['report_id'];
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
		$d_report=mysql_query("SELECT * FROM report WHERE id='$rid'");
		$report=mysql_fetch_array($d_report,MYSQL_ASSOC);
		$report['summaries']=(array)fetchReportSummaries($rid);
		$reports[]=$report;
		$transform=$report['transform'];

		/*all of the marks associated with this report*/
		mysql_query("CREATE TEMPORARY TABLE mids$rid (SELECT eidmid.mark_id FROM eidmid
				JOIN rideid ON eidmid.assessment_id=rideid.assessment_id 
				WHERE rideid.report_id='$rid')");
		if($rid!=''){
			/*this is to feed the rids to the javascript function*/
?>
		  <id_db><?php print $rid;?></id_db>
<?php
			$input_elements.=' <input type="hidden" name="rids[]" value="'.$rid.'" />';
			}
		}

	/*this means the last entry in rids[] defines the transform
		to use (can only use one!) and which is usually the wrapper anyway*/
?>
		  <transform><?php print $transform;?></transform>
</reportids>
	  </div>
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
   			$summaries=(array)$reports[$index]['summaries'];
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
   			$summaries=(array)$reports[$index]['summaries'];
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

		reset($rids);
		while(list($index,$rid)=each($rids)){
		    $crid=$reports[$index]['course_id'];
		    $commentcomp=$reports[$index]['commentcomp'];
			$compstatus=$reports[$index]['component_status'];
			$d_reportentry=mysql_query("SELECT DISTINCT subject_id, 
					component_id FROM reportentry WHERE report_id='$rid' AND
					student_id='$sid' ORDER BY report_id, subject_id, component_id");
			$d_subjects=mysql_query("SELECT DISTINCT subject_id, class_id
					FROM class JOIN cidsid ON cidsid.class_id=class.id
					WHERE cidsid.student_id='$sid' AND
					class.course_id='$crid' ORDER BY subject_id");
			$donereports=array();
			while($reportentry=mysql_fetch_array($d_reportentry,MYSQL_ASSOC)){
			    $repbid=$reportentry['subject_id'];
				$reppid=$reportentry['component_id'];
			    $donereports["$repbid"]["$reppid"]='';
				}
			while($subject=mysql_fetch_array($d_subjects,MYSQL_ASSOC)){
			    $repbid=$subject['subject_id'];
				$repcid=$subject['class_id'];
				$d_teacher=mysql_query("SELECT teacher_id FROM tidcid
						WHERE class_id='$repcid'");
				$reptids=array();
				while($teacher=mysql_fetch_array($d_teacher)){$reptids[]=$teacher['teacher_id'];}

	    		/*list of report marks for this subject class*/
				if(mysql_query("CREATE TEMPORARY TABLE tempmarks (SELECT midcid.mark_id
						FROM midcid JOIN mids$rid ON midcid.mark_id=mids$rid.mark_id
						WHERE midcid.class_id='$repcid')")){}
				else{$error[]=mysql_error();}
				$reppids=array();
				if($compstatus!='None'){
					if($compstatus=='A'){$compstatus='%';}
					$d_components=mysql_query("SELECT DISTINCT id
					FROM component WHERE course_id='$crid' AND
					subject_id='$repbid' AND status LIKE '$compstatus' ORDER BY id");
					while($component=mysql_fetch_array($d_components,MYSQL_ASSOC)){
						$reppids[]=$component['id'];
						}
					}

				if(sizeof($reppids)==0){$reppids[]='';}
			   	while(list($index,$reppid)=each($reppids)){
					if(mysql_query("CREATE TEMPORARY TABLE
						tempscores (SELECT score.mark_id FROM score
						JOIN tempmarks ON tempmarks.mark_id=score.mark_id
						WHERE score.student_id='$sid')")){}
					else{$error[]=mysql_error();}
					$d_scores=mysql_query("SELECT mark.id FROM mark JOIN
						tempscores ON mark.id=tempscores.mark_id WHERE 
						mark.component_id LIKE '$reppid'");
					print '<td style="width:3em;" title="';
				   	while(list($index, $reptid)=each($reptids)){
						print $reptid." ";
						}
					reset($reptids);
					if((isset($donereports[$repbid][$reppid]) and
						$commentcomp=='yes' and mysql_numrows($d_scores)>0) or 
						($commentcomp=='no' and mysql_numrows($d_scores)>0)){
						print '" class="vspecial">';}
					else{print '">';}
					print $repbid.'.'.$reppid.'</td>';
					if(mysql_query("DROP TEMPORARY TABLE tempscores")){}
					else{$error[]=mysql_error();}
			   		}
				if(mysql_query("DROP TEMPORARY TABLE tempmarks")){}
				else{$error[]=mysql_error();}
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
