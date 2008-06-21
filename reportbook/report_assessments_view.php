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

$action='report_assessments_view.php';

if(isset($_POST['year'])){$year=$_POST['year'];}
if(isset($_POST['stage'])){$stage=$_POST['stage'];}
if(isset($_POST['newyid'])){$yid=$_POST['newyid'];}else{$yid='';}
if(isset($_POST['newfid'])){$fid=$_POST['newfid'];}else{$fid='';}
if(isset($_POST['bids'])){$selbids=(array)$_POST['bids'];}
if(isset($_POST['eids'])){$eids=(array)$_POST['eids'];}else{$eids=array();}
if(isset($_POST['breakdown'])){$breakdown=$_POST['breakdown'];}else{$breakdown='subject';}

include('scripts/sub_action.php');

$extrabuttons=array();
/*$extrabuttons['showstatistics']=array('name'=>'stats',
								'value'=>'',
								'onclick'=>'stats(grades,gradestats,percents)');
*/
$extrabuttons['displaybysubject']=array('name'=>'breakdown',
						'value'=>'subject'
						);
$extrabuttons['displaybyassessment']=array('name'=>'breakdown',
						'value'=>'assessment'
						);
two_buttonmenu($extrabuttons,$book);

	if($fid!=''){
		$students=listin_community(array('id'=>'','type'=>'form','name'=>$fid));
		}
	elseif($yid!=''){
		$students=listin_community(array('id'=>'','type'=>'year','name'=>$yid));
		}
	else{
		if($rcrid=='%'){
			/*User has a subject not a course responsibility selected*/
			$d_course=mysql_query("SELECT DISTINCT cohort.course_id FROM
				cohort JOIN cridbid ON cridbid.course_id=cohort.course_id WHERE
				cridbid.subject_id='$rbid' AND cohort.stage='$stage' AND cohort.year='$year'");
			$rcrid=mysql_result($d_course,0);
			}

		$students=listin_cohort(array('id'=>'','course_id'=>$rcrid,'year'=>$year,'stage'=>$stage));
		}

	$viewtable=array();/*The array used to store the information to display*/
	$sids=array();
	$gradestats=array();
	$assdefs=array();
	$assbids=array();/*index all the bids these asses may relate to*/
	$asscrids=array();/*index all the crids these asses may relate to*/
	while(list($index,$eid)=each($eids)){
		$AssDef=fetchAssessmentDefinition($eid);
		$assdefs[$eid]=$AssDef;
		$asscrids[$AssDef['Course']['value']]=$AssDef['Course']['value'];
		}
	reset($eids);

	if(!isset($selbids)){
		/*all subjects selected so fetch bids for each crid*/
		/*the assessments may be across multiple crids to make even harder!*/
		while(list($index,$asscrid)=each($asscrids)){
			$d_cridbid=mysql_query("SELECT DISTINCT subject_id FROM cridbid
							WHERE course_id='$asscrid' ORDER BY subject_id");
			while($subject=mysql_fetch_array($d_cridbid,MYSQL_ASSOC)){
				$bid=$subject['subject_id'];
				$assbids[$bid]=$bid.' ';
				$d_component=mysql_query("SELECT DISTINCT id FROM component
						WHERE course_id='$asscrid' AND subject_id='$bid' ORDER BY id");
				while($comp=mysql_fetch_array($d_component,MYSQL_ASSOC)){
					$assbids[$bid.$comp['id']]=$bid . $comp['id'];
					}
				}
			}
		}
	else{
		/*selbids can only be returned when rcrid is set so easier!*/
		if($selbids[0]=='%'){
			$selbids=array();
			$d_cridbid=mysql_query("SELECT DISTINCT subject_id FROM cridbid
							WHERE course_id='$rcrid' ORDER BY subject_id");
			while($subject=mysql_fetch_array($d_cridbid,MYSQL_ASSOC)){
				$selbids[]=$subject['subject_id'];
				}
			}
		while(list($index,$bid)=each($selbids)){
			$assbids[]=$bid.' ';
			$d_component=mysql_query("SELECT DISTINCT id FROM component
				WHERE course_id='$rcrid' AND subject_id='$bid' ORDER BY id");
			while($pid=mysql_fetch_array($d_component,MYSQL_ASSOC)){
				$assbids[]=$bid . $pid['id'];
				}
			}
		reset($selbids);
		}

	$viewtable[0]['out']=array();
	$viewtable[0]['count']=array();
	$c=1;/*$c=0 in viewtable is for column headers*/
	while(list($index,$student)=each($students)){
		$sid=$student['id'];
		$viewtable[$c]=array();
		$viewtable[$c]['student']=(array)$student;
		$viewtable[$c]['out']=array();
		$c++;
		}


	/* the displayed table will either show columns for subject (and */
	/* average over the assesmments) or show columns for assessments (and */
	/* average over the subjects) --- averaging is done over $aids for */
	/* each column in $hids */

	/* First row of table is column headers - starting with yeargroup*/
	if($yid!=''){$viewtable[0]['cohort']=get_string('yeargroup').' '.$yid;}
	elseif($fid!=''){$viewtable[0]['cohort']=get_string('formgroup').' '.$fid;}
	else{$viewtable[0]['cohort']=get_string('cohort').' '.$stage.' '.$year;}
	if($breakdown=='subject'){
		$hids=$assbids;
		$aids=$eids;
		while(list($c3,$bidpid)=each($assbids)){
			$viewtable[0]['out'][]=$bidpid;
			$viewtable[0]['count'][]=0;
			}
		}
	elseif($breakdown=='assessment'){
		$hids=$eids;
		$aids=$assbids;
		while(list($c3,$eid)=each($hids)){
			$viewtable[0]['out'][]=$assdefs[$eid]['Description']['value'];
			}
		}

	/* the main loop - working the values for each student row in the table*/
	for($rowno=1;$rowno<sizeof($viewtable);$rowno++){
		$sid=$viewtable[$rowno]['student']['id'];
		$Assessments=array();
		while(list($index,$eid)=each($eids)){
			$Assessments=array_merge($Assessments,fetchAssessments($sid,$eid));
			}
		reset($eids);

		/* generate an index to lookup values from the assessments' array*/
		$assaids=array();
		$asshids=array();
		if($breakdown=='subject'){
			while(list($assno,$Assessment)=each($Assessments)){
				$eid=$Assessment['id_db'];
				$bid=$Assessment['Subject']['value'];
				$pid=$Assessment['SubjectComponent']['value'];
				$asshids[$bid. $pid][]=$assno;
				$assaids[$eid][]=$assno;
				}
			}
		elseif($breakdown=='assessment'){
			while(list($assno,$Assessment)=each($Assessments)){
				$eid=$Assessment['id_db'];
				$bid=$Assessment['Subject']['value'];
				$pid=$Assessment['SubjectComponent']['value'];
				$asshids[$eid][]=$assno;
				$assaids[$bid. $pid][]=$assno;
				}
			}

		/* each cell averages over all selected assessments for one hid*/
		reset($hids);
		$colcount=-1;
		while(list($c3,$hid)=each($hids)){
			$colcount++;
		  if(array_key_exists($hid,$asshids)){
			  /*any assessments for this hid?*/
			$assnos=$asshids[$hid];/*all of the entries in Assessments for this hid*/
			$gradesum=0;
			$gradecount=0;
			$scorecount=0;
			/*average over all possible Assessments, indexed by assnos*/
			for($c=0;$c<sizeof($assnos);$c++){
				$assno=$assnos[$c];
				$Assessment=$Assessments[$assno];
				$eid=$Assessment['id_db'];
				if($breakdown=='subject'){$aid=$eid;}
				else{$aid=$Assessment['Subject']['value'].$Assessment['SubjectComponent']['value'];}
				/* if this matches one of the chosen aids then include in average*/
				if(in_array($aid,$aids)){
					/* get the marktype for this cell  based on one of the
					Assessments, this simplistically assumes all assessments have
					the same marktype - can't do much else until there is the ability
					to translate between grading schemes*/
			   		$crid=$Assessment['Course']['value'];
					if($assdefs[$eid]['GradingScheme']['grades']!=''){
						$result=$Assessment['Result']['value'];
						$score=gradeToScore($result,$assdefs[$eid]['GradingScheme']['grades']);
	   					$gradesum=$gradesum+$score;
		   				$gradecount++;
						}
					else{
						$result=$Assessment['Value']['value'];
						$score=$result;
	   					$gradesum=$gradesum+$score;
		   				$scorecount++;
						}
					}
				}
			}
		  else{$assnos=array(); $gradecount=0; $scorecount=0;}

		  /*display the assessment average*/
		  if($gradecount>0){
					$scoreaverage=$gradesum/$gradecount;
					$scoreaverage=round($scoreaverage,1);
					$score=round($scoreaverage);
					$grade=scoreToGrade($score,$assdefs[$eid]['GradingScheme']['grades']);
					$viewtable[0]['count'][$colcount]=1;
					}
		  elseif($scorecount>0){
					$scoreaverage=$gradesum/$scorecount;
					$scoreaverage=round($scoreaverage,1);
					$grade=round($scoreaverage);
					$viewtable[0]['count'][$colcount]=1;
					}
		  else{$score='';$scoreaverage='';$grade='';}
		  if(isset($gradestats[$grade])){$gradestats[$grade]=$gradestats[$grade]+1;/*sum for stats*/}
		  else{$gradestats[$grade]=0;$gradestats[$grade]=$gradestats[$grade]+1;}
		  if($gradecount>1){
			  $viewtable[$rowno]['out'][]=$grade.' ('.$scoreaverage.')';
			  }
		  elseif($scorecount>1){
			  $viewtable[$rowno]['out'][]=$scoreaverage;
			  }
		  else{
			  $viewtable[$rowno]['out'][]=$grade;
			  }
			}

		/*end of row average needed here*/
		}
?>
		  <script>
<?php 
	/*write the grade statistics for display in the javascripts
	ksort($gradestats);
	$grades=array_keys($gradestats);
	$sum=0;
	$percents=array();
	print 'var gradestats = [';
	for ($c=0, $max=sizeof($grades);$c<$max;$c++){ 
		print '"'.$gradestats[$grades[$c]].'"';
		if($c<$max-1){print ',';}else{print '];';}
		if($c>0){$sum=$sum+$gradestats[$grades[$c]];}
		}
	print ' var grades = [';
	for($c=0,$max=sizeof($grades);$c<$max;$c++){ 
		print '"'.$grades[$c].'"';
		if($c<$max-1){print ',';}else{print '];';}
		$percents[$c]=100*$gradestats[$grades[$c]]/$sum;
		}
	print ' var percents = [';
	for($c=0,$max=sizeof($grades);$c<$max;$c++){ 
		print '"'.round($percents[$c],1).'"';
		if($c<$max-1){print ',';}else{print '];';}
		}
*/
?>
		  </script>
	  <div id="viewcontent" class="content fullwidth">
		<form id="formtoprocess" name="formtoprocess" 
						method="post" action="<?php print $host;?>"> 
		  <table class="listmenu">
			<tr>
<?php
		  /*  display the column headers*/
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
?>
				  <tr>
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
			<input type="hidden" name="newyid" value="<?php print $yid;?>" />
<?php	} ?>
<?php if(isset($fid)){?>
			<input type="hidden" name="newfid" value="<?php print $fid;?>" />
<?php	} ?>
<?php 
	if(isset($selbids)){
		while(list($index,$bid)=each($selbids)){
?>
			<input type="hidden" name="bids[]" value="<?php print $bid;?>" />
<?php
			}
		}
	while(list($index,$eid)=each($eids)){
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
