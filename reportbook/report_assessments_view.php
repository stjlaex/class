<?php
/**									report_assessments_view.php
 *
 *	Uses yid, bid and eids to collate and display assessment
 *	grades. The table displayed depends on the combination of (yid,
 *	bid, eid) as follows, multiple values are indicated by X:
 *
 *	(yid, bid, X) multiple assessment grades, values for one subject
 *	(yid, %, eid) single assessment grade, values for each subject 
 *	(yid, %,   X) multiple assessment grades,
 *
 *	NB. This can only handle scoretypes which are grades. Needs to have
 *	raw scores added.
 */

$action='report_assessments.php';

if(isset($_POST['year'])){$year=$_POST['year'];}
if(isset($_POST['stage'])){$stage=$_POST['stage'];}
if(isset($_POST['yid'])){$yid=$_POST['yid'];}else{$yid='';}
if(isset($_POST['comid'])){$comid=$_POST['comid'];}else{$comid='';}
if(isset($_POST['gender'])){$gender=$_POST['gender'];}else{$gender='';}
if(isset($_POST['profid'])){$profid=$_POST['profid'];}
if(isset($_POST['limitbid'])){$limitbid=$_POST['limitbid'];}
if(isset($_POST['eids'])){$eids=(array)$_POST['eids'];}else{$eids=array();}
if(isset($_POST['cids'])){$cids=(array)$_POST['cids'];}else{$cids=array();}

include('scripts/sub_action.php');

$extrabuttons=array();
$extrabuttons['export']=array('name'=>'current',
							  'title'=>'export',
							  'value'=>'report_assessments_export.php'
							  );
$extrabuttons['previewselected']=array('name'=>'print',
									   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/markbook/',
									   'value'=>'report_profile_print.php',
									   'onclick'=>'checksidsAction(this)');
two_buttonmenu($extrabuttons,$book);

$students=array();
$description='';

	if(sizeof($cids)>0){
		foreach($cids as $cid){
			$description.=$cid.' ';
			$students=array_merge($students,listin_class($cid));
			}
		}
	elseif($comid!=''){
		$com=get_community($comid);
		$description=$com['name'];
		$students=listin_community(array('id'=>$comid));
		}
	elseif($yid!=''){
		$description=get_yeargroupname($yid);
		$students=listin_community(array('id'=>'','type'=>'year','name'=>$yid));
		}
	else{
		if($rcrid=='%'){
			/*User has a subject not a course responsibility selected*/
			$d_course=mysql_query("SELECT DISTINCT cohort.course_id FROM
				cohort JOIN component ON component.course_id=cohort.course_id WHERE
				component.subject_id='$rbid' AND component.id='' AND cohort.stage='$stage' AND cohort.year='$year'");
			$rcrid=mysql_result($d_course,0);
			}

		/*TODO: this just guesses a date in the middle of the academic year! */
		$todate=$year-1;
		$todate=$todate.'-12-31';
		$students=listin_cohort(array('id'=>'','course_id'=>$rcrid,'year'=>$year,'stage'=>$stage),$todate);
		}



	$viewtable=array();/*The array used to store the information to display*/
	$viewtable[0]['results']=array();
	$assdefs=array();
	$assbids=array();/*index all the bids these asses may relate to*/
	$asscrids=array();/*index all the crids these asses may relate to*/


	/****************/
	foreach($eids as $eid){
		$AssDef=fetchAssessmentDefinition($eid);
		$assdefs[$eid]=$AssDef;
		$asscrids[$AssDef['Course']['value']]=$AssDef['Course']['value'];
		$viewtable[0]['results'][]=array('label'=>$AssDef['PrintLabel']['value'],'date'=>$AssDef['Deadline']['value']);
		}


		/*all subjects selected so fetch bids for each crid*/
		/*the assessments may be across multiple crids to make even harder!*/
		foreach($asscrids as $asscrid){
			$subjects=(array)list_course_subjects($asscrid);
			$subjects[]='G';
			foreach($subjects as $subject){
				if(!isset($limitbid) or $limitbid=='' or $limitbid=='%' or $subject['id']==$limitbid){
					$bid=$subject['id'];
					$assbids[$bid]=$bid;
					$compstatus='A';
					//trigger_error($bid,E_USER_WARNING);
					$comps=list_subject_components($bid,$asscrid,$compstatus);
					foreach($comps as $comp){
						$assbids[$bid.$comp['id']]=$bid . $comp['id'];
						$strands=list_subject_components($comp['id'],$asscrid);
						foreach($strands as $strand){
							$assbids[$bid.$strand['id']]=$bid . $strand['id'];
							}
						}
					}
				}
			}

		/*selbids can only be returned when rcrid is set so easier!*/
/*
	else{
		if($selbids[0]=='%'){
			$selbids=array();
			$subjects=list_course_subjects($rcrid);
			foreach($subjects as $subject){
				$selbids[]=$subject['id'];
				}
			}

		foreach($selbids as $bid){
			$assbids[]=$bid.' ';
			$compstatus='%';
			$comps=list_subject_components($bid,$rcrid,$compstatus);
			foreach($comps as $component){
				$assbids[]=$bid . $component['id'];
				$strands=list_subject_components($component['id'],$rcrid,$compstatus);
				foreach($strands as $strand){
					$assbids[]=$bid . $strand['id'];
					}
				}
			}

		}
*/

/****************/

	$viewtable[0]['out']=array();
	$viewtable[0]['count']=array();
	$c=1;/*$c=0 in viewtable is for column headers*/
	foreach($students as $student){
		$sid=$student['id'];
		$Student=(array)fetchStudent_short($sid);
		/* TODO: improve and extend the filter methods... */
		if($gender=='' or $gender==$Student['Gender']['value']){
			$Student=array_merge($Student,fetchStudent_singlefield($sid,'PersonalNumber'));
			//trigger_error($Student['PersonalNumber']['value'],E_USER_WARNING);
			$viewtable[$c]=array();
			$viewtable[$c]['student']=(array)$student;
			$viewtable[$c]['Student']=(array)$Student;
			$viewtable[$c]['out']=array();
			$c++;
			}
		}



	/* First row of table is column headers - starting with identifying the student group. */
	if($yid!=''){$cohortname=get_string('yeargroup').' '.$yid;}
	elseif($comid!=''){$cohortname=get_string($com['type']).' '.$com['name'];}
	else{$chortname=get_string('cohort').' '.$stage.' '.$year;}
	$viewtable[0]['cohort']=$cohortname;

	foreach($assbids as $bidpid){
		$viewtable[0]['out'][]=$bidpid;
		$viewtable[0]['count'][]=0;
		}

	/* the main loop - working the values for each student row in the table*/
	for($rowno=1;$rowno<sizeof($viewtable);$rowno++){
		$sid=$viewtable[$rowno]['student']['id'];
		$Assessments=array();
		foreach($eids as $eid){
			$Assessments=array_merge($Assessments,fetchAssessments($sid,$eid));
			}

		/* generate an index to lookup values from the assessments' array*/
		$ass_indexes=array();
		foreach($Assessments as $assno => $Assessment){
			$bid=''.$Assessment['Subject']['value'];
			$pid=''.$Assessment['SubjectComponent']['value'];
			$ass_indexes[trim($bid . $pid)][]=$assno;
			}

		/* each cell averages over all selected assessments for one bid*/
		$colcount=-1;
		foreach($assbids as $bidpid){
			$bidpid=trim($bidpid);
			$colcount++;
			$out='';
			$results=array();
			/* Any assessments for this bid? */
			if(array_key_exists($bidpid,$ass_indexes)){
				/*all of the entries in Assessments for this bid*/
				$assnos=(array)$ass_indexes[$bidpid];

				/*average over all possible Assessments, indexed by assnos*/
				foreach($assnos as $assno){
					$Assessment=$Assessments[$assno];
					$eid=$Assessment['id_db'];

					/* if this matches one of the chosen eids then include in average*/
					if(in_array($eid,$eids)){
						/* get the marktype for this cell  based on one of the
						   Assessments, this simplistically assumes all assessments have
						   the same marktype - can't do much else until there is the ability
						   to translate between grading schemes
						*/
						if($assdefs[$eid]['GradingScheme']['grades']!=''){
							$out.=$Assessment['Result']['value'].' ';
							$results[$eid]=$Assessment['Result']['value'];
							$viewtable[0]['scoretype'][$colcount]='grade';
							}
						else{
							$out.=$Assessment['Value']['value'].' ';
							$results[$eid]=$Assessment['Value']['value'];
							$viewtable[0]['scoretype'][$colcount]='value';
							}
						}
					}
				}
			
			if($out!=''){
				$viewtable[$rowno]['out'][]=$out;
				$viewtable[$rowno]['results'][]=$results;
				$viewtable[0]['count'][$colcount]=1;
				}
			else{
				$viewtable[$rowno]['out'][]='';
				$viewtable[$rowno]['results'][]='';
				}
			}
		}
?>

	  <div id="viewcontent" class="content fullwidth">
		<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">


	  <fieldset class="right">
		<legend><?php print_string('template',$book);?></legend>
		<div class="center">
<?php
		$required='yes';
		include('scripts/list_profile_template.php');
?>
		</div>
	  </fieldset>
 
		<table class="listmenu sidtable center" id="sidtable">
			<tr>
		<th colspan="1" class="checkall">
		  <input type="checkbox" name="checkall" 
				value="yes" onChange="checkAll(this);" />
		</th>
<?php
		  /*  display the column headers*/
		print '<th>'.get_string('enrolmentnumber','infobook').'</th>';
		print '<th>'.$viewtable[0]['cohort'].'</th>';
		for($c2=0;$c2<sizeof($viewtable[0]['out']);$c2++){
			  if($viewtable[0]['count'][$c2]>0){
				  print '<th style="font-weight:300;">'.$viewtable[0]['out'][$c2].'</th>';
				  }
			  }
?>
			</tr>
<?php
	for($rowno=1;$rowno<sizeof($viewtable);$rowno++){
		$row=$viewtable[$rowno];
		$sid=$row['student']['id'];
?>
				  <tr>
		<tr id="sid-<?php print $sid;?>">
		  <td>
			<input type="checkbox" name="sids[]" value="<?php print $sid;?>" />
			<?php print $rowno;?>
		  </td>
		  <td>
			<?php print $sid;?>
		  </td>
					<td>
					<?php print $row['Student']['DisplayFullSurname']['value']; ?> 
					(<?php print $row['student']['form_id']; ?>)
					</td>
<?php
		for($c2=0;$c2<sizeof($row['out']);$c2++){
			if($viewtable[0]['count'][$c2]>0 and $row['out'][$c2]!=' ()'){
				if(trim($row['out'][$c2])=='Yellow'){$class=" class='pauselite' style='border:1px solid #BAC1C8;' ";}
				elseif(trim($row['out'][$c2])=='Red'){$class=" class='hilite' style='border:1px solid #BAC1C8;' ";}
				elseif(trim($row['out'][$c2])=='Green'){$class=" class='golite' style='border:1px solid #BAC1C8;' ";}
				else{$class='';}
?>
					<td <?php print $class; ?>>
<?php
				if(trim($row['out'][$c2])!='Yellow' and trim($row['out'][$c2])!='Green' and trim($row['out'][$c2])!='Red'){
					print $row['out'][$c2];
					}
?>
					</td>
<?php 
				}
			}
?>
				  </tr>
<?php	
		}
?>
				</table>

<?php if(isset($stage)){?>
			<input type="hidden" name="stage" value="<?php print $stage;?>" />
<?php	} ?>
<?php if(isset($year)){?>
			<input type="hidden" name="year" value="<?php print $year;?>" />
<?php	} ?>
<?php if(isset($yid)){?>
			<input type="hidden" name="yid" value="<?php print $yid;?>" />
<?php	} ?>
<?php if(isset($comid)){?>
			<input type="hidden" name="comid" value="<?php print $comid;?>" />
<?php	} ?>
<?php 
	if(isset($limitbid)){
?>
			<input type="hidden" name="limitbid" value="<?php print $limitbid;?>" />
<?php
		}
	foreach($eids as $eid){
?>
			<input type="hidden" name="eids[]" value="<?php print $eid;?>" />
<?php
		}
?>
			<input type="hidden" name="current" value="<?php print $action;?>" />
			<input type="hidden" name="choice" value="<?php print $choice;?>" />
			<input type="hidden" name="cancel" value="<?php print $choice;?>" />
			</form>
		  </div>

		  <div id="xml-checked-action" style="display:none;">
			<params>
<?php 
if(isset($profid)){
	$profile=get_assessment_profile($profid);
	}
else{
	$profile['name']='blank';
	$profile['course_id']='';
	$profile['subject_id']='%';
	$profile['component_status']='All';
	$profile['rating_name']='average';
	$profile['bid']='%';
	}
$profile['eids']=(array)$eids;
$profile['year']=$year;
if($profile['crid']=='FS'){
	$profile['bid']='EY';
	}
if(isset($limitbid)){
	$profile['bid']=$limitbid;
	}
else{
	$profile['bid']='%';
	}
$profile['stage']=$stage;
$profile['pid']='';
/* selectname needed for js to capture the template field */
print '<id>'.$profile['id'].'</id>';
print '<name>'.$profile['name'].'</name>';
print '<crid>'.$profile['crid'].'</crid>';
print '<bid>'.$profile['bid'].'</bid>';
print '<stage>'.$stage.'</stage>';
print '<component_status>'.$profile['component_status'].'</component_status>';
print '<description>'.$description.'</description>';
print '<selectname>template</selectname>';
///xmlechoer('Profile',$profile,'Profile');
/*	All finished.*/
$_SESSION[$book.'viewtable']=$viewtable;
?>
			</params>
		  </div>
