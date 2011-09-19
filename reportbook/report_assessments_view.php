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
if(isset($_POST['cid'])){$cid=$_POST['cid'];}else{$cid='';}
if(isset($_POST['gender'])){$gender=$_POST['gender'];}else{$gender='';}
if(isset($_POST['profid'])){$profid=$_POST['profid'];}
if(isset($_POST['bids'])){$selbids=(array)$_POST['bids'];}
if(isset($_POST['eids'])){$eids=(array)$_POST['eids'];}else{$eids=array();}

include('scripts/sub_action.php');

$extrabuttons=array();
$extrabuttons['previewselected']=array('name'=>'print',
									   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/markbook/',
									   'value'=>'report_profile_print.php',
									   'onclick'=>'checksidsAction(this)');

/*
$extrabuttons['displaybysubject']=array('name'=>'breakdown',
										'value'=>'subject'
										);
$extrabuttons['displaybyassessment']=array('name'=>'breakdown',
										   'value'=>'assessment'
										   );
*/
two_buttonmenu($extrabuttons,$book);

$students=array();

	if($comid!=''){
		$students=listin_community(array('id'=>$comid));
		}
	elseif($yid!=''){
		$students=listin_community(array('id'=>'','type'=>'year','name'=>$yid));
		}
	elseif($cid!=''){
		$students=listin_class($cid);
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
	$assdefs=array();
	$assbids=array();/*index all the bids these asses may relate to*/
	$asscrids=array();/*index all the crids these asses may relate to*/


	/****************/
	foreach($eids as $eid){
		$AssDef=fetchAssessmentDefinition($eid);
		$assdefs[$eid]=$AssDef;
		$asscrids[$AssDef['Course']['value']]=$AssDef['Course']['value'];
		}


	if(!isset($selbids)){
		/*all subjects selected so fetch bids for each crid*/
		/*the assessments may be across multiple crids to make even harder!*/
		foreach($asscrids as $asscrid){
			$subjects=(array)list_course_subjects($asscrid);
			foreach($subjects as $subject){
				$bid=$subject['id'];
				$assbids[$bid]=$bid.' ';
				$compstatus='%';
				$comps=list_subject_components($bid,$asscrid,$compstatus);
				foreach($comps as $comp){
					$assbids[$bid.$comp['id']]=$bid . $comp['id'];
					}
				}
			}
		}
	else{
		/*selbids can only be returned when rcrid is set so easier!*/
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

/****************/

	$viewtable[0]['out']=array();
	$viewtable[0]['count']=array();
	$c=1;/*$c=0 in viewtable is for column headers*/
	foreach($students as $student){
		$sid=$student['id'];
		$Student=(array)fetchStudent_short($sid);
		/* TODO: improve and extend the filter methods... */
		if($gender=='' or $gender==$Student['Gender']['value']){
			$viewtable[$c]=array();
			$viewtable[$c]['student']=(array)$student;
			$viewtable[$c]['out']=array();
			$c++;
			}
		}



	/* First row of table is column headers - starting with identifying the student group. */
	if($yid!=''){$viewtable[0]['cohort']=get_string('yeargroup').' '.$yid;}
	elseif($comid!=''){$viewtable[0]['cohort']=get_string('formgroup').' '.$comid;}
	else{$viewtable[0]['cohort']=get_string('cohort').' '.$stage.' '.$year;}

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
			$result='';
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
							$result.=$Assessment['Result']['value'].' ';
							}
						else{
							$result.=$Assessment['Value']['value'].' ';
							}
						}
					}
				}
			
			if($result!=''){
				$viewtable[$rowno]['out'][]=$result;
				$viewtable[0]['count'][$colcount]=1;
				}
			else{
				$viewtable[$rowno]['out'][]='';
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
	$onchange='yes';$required='no';
   	$d_catdef=mysql_query("SELECT DISTINCT comment AS id, name AS name FROM categorydef WHERE
								  type='pro' AND comment!='' ORDER BY course_id;");
	$listname='template';$onchange='no';$required='yes';
	include('scripts/set_list_vars.php');
	list_select_db($d_catdef,$listoptions,$book);
	unset($listoptions);
?>
		</div>
	  </fieldset>
 
		<table class="listmenu sidtable center" id="sidtable">
			<tr>
		<th colspan="1"><?php print_string('checkall'); ?>
		  <input type="checkbox" name="checkall" 
				value="yes" onChange="checkAll(this);" />
		</th>
<th>
</th>
<?php
		  /*  display the column headers*/
		  print '<th colspan="1">'.$viewtable[0]['cohort'].'</th>';
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
					<?php print $row['student']['surname']; ?>,
					<?php print $row['student']['forename']; ?>
					(<?php print $row['student']['form_id']; ?>)
					</td>
<?php
		for($c2=0;$c2<sizeof($row['out']);$c2++){
			  if($viewtable[0]['count'][$c2]>0 and $row['out'][$c2]!=' ()'){
?>
					<td>
<?php 				print $row['out'][$c2]; ?>
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
	if(isset($selbids)){
		while(list($index,$bid)=each($selbids)){
?>
			<input type="hidden" name="bids[]" value="<?php print $bid;?>" />
<?php
			}
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
$profile['stage']=$stage;
$profile['pid']='';
//$profile['stage']='R';
/* selectname needed for js to capture the template field */
$profile['selectname']='template';
xmlechoer('Profile',$profile);
?>
		</div>
