<?php 
/**				   				report_reports_list.php
 */

$action='report_reports.php';

if(isset($_POST['yid'])){$yid=$_POST['yid'];}else{$yid='';}
if(isset($_POST['formid']) and $_POST['formid']!=''){$comid=$_POST['formid'];}
elseif(isset($_POST['houseid'])  and $_POST['houseid']!=''){$comid=$_POST['houseid'];}
elseif(isset($_POST['comid'])  and $_POST['comid']!=''){$comid=$_POST['comid'];}
else{$comid='';}
if(isset($_POST['wrapper_rid'])){$wrapper_rid=$_POST['wrapper_rid'];}

include('scripts/sub_action.php');

	if($comid!=''){
		$com=get_community($comid);
		if($yid!=''){
			$com['yeargroup_id']=$yid;
			$students=listin_community($com);
			}
		else{
			$students=listin_community($com);
			$yid=get_form_yeargroup($com['name'],$com['type']);
			}
		$formperm=get_community_perm($comid,$yid);
		$yearperm=getYearPerm($yid);
		}
	elseif($yid!=''){
		$students=listin_community(array('id'=>'','type'=>'year','name'=>$yid));
		$yearperm=getYearPerm($yid);
		$formperm=$yearperm;
		}

	$resperm=get_residence_perm();

$rids=array();
if(isset($wrapper_rid)){
	$d_rid=mysql_query("SELECT categorydef_id AS report_id FROM ridcatid WHERE
				 report_id='$wrapper_rid' AND subject_id='wrapper' ORDER BY categorydef_id;");
	$rids[]=$wrapper_rid;//add to the start of the rids
	while($rid=mysql_fetch_array($d_rid,MYSQL_ASSOC)){
		$rids[]=$rid['report_id'];
		}
	}

$extrabuttons=array();
$extrabuttons['previewselected']=array('name'=>'current',
									   'value'=>'report_reports_print.php',
									   'onclick'=>'checksidsAction(this)');
if(($_SESSION['role']=='admin' or $yearperm['x']==1) and isset($CFG->eportfolio_dataroot) and $CFG->eportfolio_dataroot!=''){
	$extrabuttons['publishpdf']=array('name'=>'current',
									  'value'=>'report_reports_publish.php');
	$extrabuttons['unlock']=array('name'=>'current',
								  'value'=>'report_reports_unlock.php');
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
        <h4><?php print get_string('subjectreportsfor',$book).' '.get_yeargroupname($yid).' '.$com['displayname'];?></h4>
    </div>
    <div id="viewcontent" class="content">
        <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
            <div id="xml-checked-action" style="display:none;">
                <reportids>
                    <?php
                    	$reportdefs=array();
                    	$input_elements='';
                    	foreach($rids as $rid){
                    		$reportdefs[]=(array)fetch_reportdefinition($rid);
                    		/*this is to feed the rids to the javascript function*/
                    		print '<rids>'.$rid.'</rids>';
                    	    $input_elements.=' <input type="hidden" name="rids[]" value="'.$rid.'" />';
                    		}
                    ?>
                </reportids>
            </div>
        <div class="center">
		<table class="listmenu sidtable" id="sidtable">
		    <thead>
		        <tr>
		            <th colspan="1" class="checkall">
	                   <input type="checkbox" name="checkall" value="yes" onChange="checkAll(this);" />
		            </th>
		            <th width="2%"></th>
			<th><?php print_string('student');?></th>
                <?php
                	$uploadpic='no';
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
                			elseif($summary['type']=='pic'){
                				$uploadpic='yes';
                				}
                			}
                		}
                	if($uploadpic=='yes'){
                ?>
			<th><?php print_string('uploadfile');?></th>
<?php
		}
?>
			<th><?php print_string('completedsubjectreports',$book);?></th>
		                  </tr>
            </thead>
<?php
	$rown=1;
	foreach($students as $student){
		$sid=$student['id'];
		$Student=(array)fetchStudent_short($sid);
		$success=checkReportPub($rids[0],$sid);
		if($success==1){$rowclass='golite';}
		elseif($success==0){$rowclass='gomidlite';}
		else{$rowclass='';}
?>
		<tr id="sid-<?php print $sid;?>" <?php print 'class="'.$rowclass.'"';?>>
		  <td>
			<input type="checkbox" name="sids[]" value="<?php print $sid; ?>" />
			<?php print $rown++;?>
		  </td>
		  <td>
<?php
			include('scripts/studentlist_shortcuts.php');
?>
		  </td>
		  <td class="student">
			<a onclick="parent.viewBook('infobook');" target="viewinfobook" href="infobook.php?current=student_view.php&sid=<?php print $sid;?>">
			     <?php print $Student['DisplayFullSurname']['value']; ?> 
				 (<?php print $Student['RegistrationGroup']['value']; ?>)
			</a>
			<div class="miniature" id="mini-<?php print $sid;?>"></div>
			<div class="merit" id="merit-<?php print $sid;?>"></div>
		  </td>
<?php
	   foreach($rids as $index => $rid){
	   		if($reportdefs[$index]['report']['course_id']=="wrapper"){
				$addphotos=$reportdefs[$index]['report']['addphotos'];
				}
   			$summaries=(array)$reportdefs[$index]['summaries'];
			foreach($summaries as $summary){
				$summaryid=$summary['subtype'];
				if($summary['type']=='com'){
					if($formperm['x']==1 and $summaryid=='form'){
						$d_summaryentry=mysql_query("SELECT teacher_id FROM reportentry WHERE report_id='$rid' AND
							student_id='$sid' AND subject_id='summary' AND component_id='$summaryid' AND entryn='1'");
						$openId=$sid.'summary-'.$summaryid;
?>
			<td id="icon<?php print $openId;?>" <?php if(mysql_num_rows($d_summaryentry)>0){print 'class="vspecial"';} else {print 'class="txt-center"';} ?>>
<?php
			if($success<1){
				print '<span class="clicktowrite" name="Write" onClick="clickToWriteManyComments('.$sid.','.$rid.',\'summary\',\''.$summaryid.'\',\'0\',\''.$openId.'\');"></span>';
				}
?>
			</td>
<?php
						}
					elseif($yearperm['x']==1 and $summaryid=='year'){
						$d_summaryentry=mysql_query("SELECT teacher_id FROM reportentry WHERE report_id='$rid' AND
							student_id='$sid' AND subject_id='summary' AND component_id='$summaryid' AND entryn='1'");
						$openId=$sid.'summary-'.$summaryid;
?>
			<td id="icon<?php print $openId;?>" <?php if(mysql_num_rows($d_summaryentry)>0){print 'class="vspecial"';} else {print 'class="txt-center"';} ?>>
			    
<?php
			if($success<1){
				print '<span class="clicktowrite" name="Write" onClick="clickToWriteManyComments('.$sid.','.$rid.',\'summary\',\''.$summaryid.'\',\'0\',\''.$openId.'\');"></span>';
				}
?>
			</td>
<?php
						}
					elseif($yearperm['x']==1 and $summaryid=='section'){
						$d_summaryentry=mysql_query("SELECT teacher_id FROM reportentry WHERE report_id='$rid' AND
									student_id='$sid' AND subject_id='summary' AND component_id='$summaryid' AND entryn='1';");
						$openId=$sid.'summary-'.$summaryid;
?>
			<td id="icon<?php print $openId;?>" <?php if(mysql_num_rows($d_summaryentry)>0){print 'class="vspecial"';} else {print 'class="txt-center"';} ?>> 
<?php
			if($success<1){
				print '<span class="clicktowrite" name="Write" onClick="clickToWriteManyComments('.$sid.','.$rid.',\'summary\',\''.$summaryid.'\',\'0\',\''.$openId.'\');"></span>';
				}
?>
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
			<td id="icon<?php print $openId;?>" <?php if(mysql_num_rows($d_summaryentry)>0){print 'class="vspecial"';} else {print 'class="txt-center"';} ?>>
<?php
			if($success<1){
				print '<span class="clicktowrite" name="Write" onClick="clickToWriteManyComments('.$sid.','.$rid.',\'summary\',\''.$summaryid.'\',\'0\',\''.$openId.'\');"></span>';
				}
?>
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
	if($addphotos=="yes"){
?>
		  <td>
		  	<div class="txt-center" id="upload-<?php print $sid;?>">
				<span class="clicktoload" onclick="clickToAttachFile(<?php print $sid;?>,<?php print $wrapper_rid;?>,'','',<?php print $sid;?>)" value="category_editor.php" name="Attachment" title="Click to post file" type="button">
				</span>
			</div>
		  </td>
<?php
						}

		print '<td class="report-td">';

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
				$substatus=$reportdefs[$rindex]['report']['subject_status'];
				$compstatus=$reportdefs[$rindex]['report']['component_status'];
				}

			if($substatus=='A'){$compmatch="(component.status LIKE '%' AND component.status!='U')";}
			elseif($substatus=='AV'){$compmatch="(component.status='V' OR component.status='O')";}
			else{$compmatch="(component.status LIKE '$substatus' AND component.status!='U')";}

			$subjectclasses=(array)list_student_course_classes($sid,$crid);
			foreach($subjectclasses as $class){
			    $bid=$class['subject_id'];
				$cid=$class['id'];
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
						$scoreno+=count_student_assessments($sid,$eid,$bid,$pid);
						foreach($strands as $strand){
							$scoreno+=count_student_assessments($sid,$eid,$bid,$strand['id']);
							}
						}
?>
			<span title="
<?php
					foreach($reptids as $reptid){print $reptid.' ';}
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
				  
<?php
			if($success<1){
				  print '<span class="clicktowrite" name="Write" onClick="clickToWriteCommentNew('. $sid.','.$rid.',\''.$bid.'\',\''.$pid.'\',\''.$en.'\',\''.$openId.'\');"></span>';
				}
?>
				  
			  </a>
<?php
							}
						}
?>
			</span>
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
 	<input type="hidden" name="wrapper_rid" value="<?php print $wrapper_rid;?>" />
 	<input type="hidden" name="comid" value="<?php print $comid;?>" />
 	<input type="hidden" name="yid" value="<?php print $yid;?>" />
 	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
 	<input type="hidden" name="choice" value="<?php print $choice;?>" />
 	<input type="hidden" name="current" value="<?php print $action;?>" />
	</form>
  </div>
<?php
include('scripts/studentlist_extra.php');
?>
