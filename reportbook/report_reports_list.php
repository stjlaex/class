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
		$formperm=getFormPerm($fid);
		$yid=get_form_yeargroup($fid);
		$yearperm=getYearPerm($yid);
		}
	elseif($yid!=''){
		$students=listin_community(array('id'=>'','type'=>'year','name'=>$yid));
		$yearperm=getYearPerm($yid);
		$formperm=$yearperm;
		}
	else{
		$students=listin_cohort(array('id'=>'','course_id'=>$rcrid,'year'=>$year,'stage'=>$stage));
		}

	$resperm=getResidencePerm();

$rids=array();
if(isset($wrapper_rid)){
	$d_rid=mysql_query("SELECT categorydef_id AS report_id FROM ridcatid WHERE
				 report_id='$wrapper_rid' AND subject_id='wrapper' ORDER BY categorydef_id;");
	$rids[]=$wrapper_rid;//add to the start of the rids
	while($rid=mysql_fetch_array($d_rid,MYSQL_ASSOC)){
		$rids[]=$rid['report_id'];
		}
	}
else{
	foreach($postrids as $rid){
		if($rid!=0){$rids[]=$rid;}
		}
	}

$extrabuttons=array();
$extrabuttons['previewselected']=array('name'=>'current',
									   'value'=>'report_reports_print.php',
									   'onclick'=>'checksidsAction(this)');
if($_SESSION['role']=='admin' and isset($CFG->eportfolio_dataroot) and $CFG->eportfolio_dataroot!=''){
	$extrabuttons['publishpdf']=array('name'=>'current',
									  'value'=>'report_reports_publish.php',
									  'onclick'=>'checksidsAction(this)');
	if($_SESSION['username']=='administrator' and $CFG->emailoff=='no'){
		/*
		  $extrabuttons['email']=array('name'=>'current',
		  'value'=>'report_reports_email.php');
		*/

		$extrabuttons['message']=array('name'=>'current',
									   'value'=>'report_reports_message.php');
		}
	}

two_buttonmenu($extrabuttons,$book);
?>
  <div id="heading">
  <?php print get_string('subjectreportsfor',$book).' '.get_yeargroupname($yid).' '.$fid;?>
  </div>
  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <div id="xml-checked-action" style="display:none;">
		<reportids>
<?php
	$reports=array();
	$input_elements='';
	foreach($rids as $rid){
		$reportdef=fetch_reportdefinition($rid);
		$reportdefs[]=$reportdef;
		/*this is to feed the rids to the javascript function*/
		print '<rids>'.$rid.'</rids>';
	    $input_elements.=' <input type="hidden" name="rids[]" value="'.$rid.'" />';
		}
?>
		</reportids>
	  </div>
	  <div class="fullwidth">
		<table class="listmenu sidtable" id="sidtable">
		  <tr>
			<th>
			  <label id="checkall">
				<?php print_string('checkall');?>
				<input type="checkbox" name="checkall" value="yes" onChange="checkAll(this);" />
			  </label>
			</th>
			<th colspan="2"><?php print_string('student');?></th>
<?php
		foreach($rids as $index => $rid){
				$summaries=(array)$reportdefs[$index]['summaries'];
				foreach($summaries as $summary){
					$summaryid=$summary['subtype'];
					if($summary['type']=='com'){
						if($formperm['x']==1 and $summaryid=='form'){
							print '<th style="width:4%;">'.$summary['name'].'</th>';
							}
						elseif($yearperm['x']==1 and $summaryid=='year'){
							print '<th style="width:4%;">'.$summary['name'].'</th>';
							}
						elseif($yearperm['x']==1 and $summaryid=='section'){
							print '<th style="width:4%;">'.$summary['name'].'</th>';
							}
						elseif($resperm['x']==1 and $summaryid=='residence'){
							print '<th style="width:4%;">'.$summary['name'].'</th>';
							}
						}
					}
				}
			
?>
			<th><?php print_string('completedsubjectreports',$book);?></th>
		  </tr>
<?php
	$rown=1;
	foreach($students as $student){
		$sid=$student['id'];
		$comment=comment_display($sid);
		$rowclass=checkReportPub($rids[0],$sid);
?>
		<tr id="sid-<?php print $sid;?>" <?php print 'class="'.$rowclass.'"';?>>
			<td>
			<input type="checkbox" name="sids[]" value="<?php print $sid; ?>" />
			<?php print $rown++;?>
			</td>
			<td>
			<span <?php print ' title="'.$comment['body'].'"';?>>
			  <a onclick="parent.viewBook('infobook');" target="viewinfobook"  
				href='infobook.php?current=comments_list.php&sid=<?php print $sid;?>'
				<?php print ' class="'.$comment['class'].'" ';?>>C</a> 
			</span>
			<a onclick="parent.viewBook('infobook');" target="viewinfobook"  
			  href='infobook.php?current=incidents_list.php&sid=<?php print $sid;?>'>I</a>
		  </td>
		  <td class="student">
			<a onclick="parent.viewBook('infobook');" target="viewinfobook" 
			  href="infobook.php?current=student_view.php&sid=<?php print $sid;?>">
				   <?php print $student['surname']; ?>, <?php print $student['forename']; ?>
			</a>
			<div id="merit-<?php print $sid;?>"></div>
			</td>
<?php
	   foreach($rids as $index => $rid){
   			$summaries=(array)$reportdefs[$index]['summaries'];
			foreach($summaries as $summary){
				$summaryid=$summary['subtype'];
				if($summary['type']=='com'){
					if($formperm['x']==1 and $summaryid=='form'){
						$d_summaryentry=mysql_query("SELECT teacher_id FROM reportentry WHERE report_id='$rid' AND
							student_id='$sid' AND subject_id='summary' AND component_id='$summaryid' AND entryn='1'");
						$openId=$sid.'summary-'.$summaryid;
?>
			<td id="icon<?php print $openId;?>" <?php if(mysql_num_rows($d_summaryentry)>0){print 'class="vspecial"';}?> >
			  <img class="clicktowrite" name="Write"  
				onClick="clickToWriteCommentNew(<?php print $sid.','.$rid.',\'summary\',\''.$summaryid.'\',\'0\',\''.$openId.'\'';?>);" />
			</td>
<?php
						}
					elseif($yearperm['x']==1 and $summaryid=='year'){
						$d_summaryentry=mysql_query("SELECT teacher_id FROM reportentry WHERE report_id='$rid' AND
							student_id='$sid' AND subject_id='summary' AND component_id='$summaryid' AND entryn='1'");
						$openId=$sid.'summary-'.$summaryid;
?>
			<td id="icon<?php print $openId;?>" <?php if(mysql_num_rows($d_summaryentry)>0){print 'class="vspecial"';}?> >
			  <img class="clicktowrite" name="Write"  
				onClick="clickToWriteCommentNew(<?php print $sid.','.$rid.',\'summary\',\''.$summaryid.'\',\'0\',\''.$openId.'\'';?>);" />
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
			  <img class="clicktowrite" name="Write"  
				onClick="clickToWriteCommentNew(<?php print $sid.','.$rid.',\'summary\',\''.$summaryid.'\',\'0\',\''.$openId.'\'';?>);" />
			</td>
<?php
						}
					elseif($resperm['x']==1 and $summaryid=='residence'){
						$boader=(array)fetchStudent_singlefield($sid,'Boarder');
						if($boader['Boarder']['value']!='' and $boader['Boarder']['value']!='N'){
							$d_summaryentry=mysql_query("SELECT teacher_id
												FROM reportentry WHERE report_id='$rid' AND
												student_id='$sid' AND subject_id='summary' AND
												component_id='$summaryid' AND entryn='1';");
							$openId=$sid.'summary-'.$summaryid;
?>
			<td id="icon<?php print $openId;?>" <?php if(mysql_num_rows($d_summaryentry)>0){print 'class="vspecial"';}?> >
			  <img class="clicktowrite" name="Write"  
				onClick="clickToWriteCommentNew(<?php print $sid.','.$rid.',\'summary\',\''.$summaryid.'\',\'0\',\''.$openId.'\'';?>);" />
			</td>
<?php
						}
					else{
						print '<td></td>';
						}
						}
					}
				}
			}

				print '<td>';

		/* Going to check each subject class for completed assessments
		 * and reportentrys and list in the table highlighting those that
		 * met this reports required elements for completion. 
		 */
		 foreach($rids as $rindex => $rid){
			$eids=(array)$reportdefs[$rindex]['eids'];
		    if(isset($reportdefs[$rindex]['report']['course_id'])){
				$crid=$reportdefs[$rindex]['report']['course_id'];
				$reportstage=$reportdefs[$rindex]['report']['stage'];
				$addcomment=$reportdefs[$rindex]['report']['addcomment'];
				$commentcomp=$reportdefs[$rindex]['report']['commentcomp'];
				$compstatus=$reportdefs[$rindex]['report']['component_status'];
				}

			$d_subjectclasses=mysql_query("SELECT DISTINCT subject_id, class_id
					FROM class JOIN cidsid ON cidsid.class_id=class.id
					WHERE cidsid.student_id='$sid' AND class.course_id='$crid' 
					AND (class.stage='$reportstage' OR class.stage LIKE '$reportstage') 
					ORDER BY subject_id;");
			while($subject=mysql_fetch_array($d_subjectclasses,MYSQL_ASSOC)){
			    $bid=$subject['subject_id'];
				$cid=$subject['class_id'];
				$d_teacher=mysql_query("SELECT teacher_id FROM tidcid WHERE class_id='$cid';");
				$reptids=array();
				$subjectperm['x']=0;
				while($teacher=mysql_fetch_array($d_teacher)){
					$reptids[]=$teacher['teacher_id'];	
					if($tid==$teacher['teacher_id']){$subjectperm['x']=1;}
					}

				$components=array();
				if($compstatus!='None'){
					$components=(array)list_subject_components($bid,$crid,$compstatus);
					}
				if(sizeof($components)==0){$components[]=array('id'=>' ','name'=>'');}

			   	foreach($components as $component){
					$pid=$component['id'];

					$strands=(array)list_subject_components($pid,$crid);

					$scoreno=0;
					$eidno=0;
					foreach($eids as $eid){
						$eidno++;
						$Assessments=fetchAssessments_short($sid,$eid,$bid,$pid);
						$scoreno+=sizeof($Assessments);
						foreach($strands as $strand){
							$Assessments=fetchAssessments_short($sid,$eid,$bid,$strand['id']);
							$scoreno+=sizeof($Assessments);
							}
						}
?>
			<p title="
<?php
				   	while(list($tindex, $reptid)=each($reptids)){
						print $reptid.' ';
						}
					reset($reptids);
					$reportentryno=checkReportEntry($rid,$sid,$bid,$pid);
					if(($reportentryno>0 and
						$commentcomp=='yes' and ($scoreno>0 or $eidno==0)) or 
						($commentcomp=='no' and $scoreno>0)){
						print '" class="reporttable vspecial">';}
					else{print '" class="reporttable" >';}
					if($pid!=' '){print $pid;}else{print $bid;}
					/* This allows year responsibles 
							and subject teachers to edit the report comments */
					if($addcomment=='yes' 
							and ($subjectperm['x']==1 or $yearperm['x']==1 or $formperm['x']==1)){
						if($reportentryno==0){$reportentryno=1;$cssclass='class=""';}
						else{$cssclass='class="special"';}
						for($en=0;$en<$reportentryno;$en++){
							$openId=$rid.'-'.$sid.'-'.$bid.'-'.$pid.'-'.$en;
?>
			  <a <?php print $cssclass;?> id="icon<?php print $openId;?>">
				<img class="clicktowrite" name="Write"  
				  onClick="clickToWriteCommentNew(<?php print
				  $sid.','.$rid.',\''.$bid.'\',\''.$pid.'\',\''.$en.'\',\''.$openId.'\'';?>);"
				  />
			  </a>
<?php
							}
						}
?>
			</p>
<?php
			   		}
				}
			}
?>
			</td>
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
<?php
include('scripts/studentlist_extra.php');
?>
