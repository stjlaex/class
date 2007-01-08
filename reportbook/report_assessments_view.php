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

if(isset($_POST['day'])){$date=$_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];}else{$date='';}
if(isset($_POST['bids'])){$bids=(array)$_POST['bids'];}else{$bids=array();}
if(isset($_POST['newyid'])){$yid=$_POST['newyid'];}else{$yid='';}
if(isset($_POST['newfid'])){$fid=$_POST['newfid'];}else{$fid='';}
if(isset($_POST['selcrid'])){$selcrid=$_POST['selcrid'];}else{$selcrid=$rcrid;}
if(isset($_POST['eids'])){$eids=(array)$_POST['eids'];}else{$eids=array();}
if(isset($_POST['breakdown'])){$breakdown=$_POST['breakdown'];}else{$breakdown='subject';}

include('scripts/sub_action.php');

	/*Select a group of students by yeargroup_id*/
	if($yid!=''){
		$d_student=mysql_query("SELECT * FROM student WHERE
				yeargroup_id='$yid' ORDER BY form_id, surname");
		}
	/*Select a group of students by form_id*/
	elseif($fid!=''){
		$d_student=mysql_query("SELECT * FROM student WHERE
				form_id='$fid' ORDER BY surname");
		}
	else{$error[]=get_string('youneedtoselectstudents'); 
		$action=$choice;
		include('scripts/results.php');
		include('scripts/redirect.php');
		exit;
		}

	$viewtable=array();/*The array used to store the information to display*/
	$sids=array();
	$gradestats=array();

	$c=1;/*$c=0 in viewtable is for column headers*/
	while($student=mysql_fetch_array($d_student,MYSQL_ASSOC)){
		$viewtable[$c]['student']=$student;
		$viewtable[$c]['out']=array();
		$c++;
		}


	/*using ALL subjects, fetch all bids for this crid*/
	if($bids[0]=='%'){
		$bids=array();
		$d_cridbid=mysql_query("SELECT DISTINCT subject_id FROM cridbid
				WHERE course_id LIKE '$selcrid' ORDER BY subject_id");
		while($bid=mysql_fetch_array($d_cridbid,MYSQL_ASSOC)){
			$bid=$bid['subject_id'];
			$bids[]=$bid.' ';
			$d_component=mysql_query("SELECT DISTINCT id FROM component
				WHERE course_id LIKE '$selcrid' AND subject_id='$bid' ORDER BY id");
			while($pid=mysql_fetch_array($d_component,MYSQL_ASSOC)){
				$bids[]=$bid . $pid['id'];
				}
			}
		}

/* the displayed table will either show columns for subject (and */
/* average over the assesmments) or show columns for assessments (and */
/* average over the subjects) --- averaging is done over $aids for */
/* each column in $hids */


/*	first row of table is column headers - starting with yeargroup*/
	if($yid!=''){$viewtable[0]='<tr><th>Year '.$yid.'</th>';}
	if($fid!=''){$viewtable[0]='<tr><th>Form '.$fid.'</th>';}

	if($breakdown=='subject'){
		$hids=$bids;
		$aids=$eids;
		while(list($c3,$bidpid)=each($bids)){
			$viewtable[0]=$viewtable[0].'<th>'.$bidpid.'</th>';
			}
		}
	elseif($breakdown=='assessment'){
		$hids=$eids;
		$aids=$bids;
		while(list($c3,$hid)=each($hids)){
			$d_assessment=mysql_query("SELECT description FROM assessment WHERE id='$hid'");
			$hid=mysql_result($d_assessment,0);
			$viewtable[0]=$viewtable[0].'<th>'.$hid.'</th>';
			}
		}

   	$viewtable[0]=$viewtable[0].'<th></th></tr>';

/*	the main loop - working the values for each student row in the table*/
	for($rowno=0;$rowno<sizeof($viewtable);$rowno++){
		$sid=$viewtable[$rowno]['student']['id'];
		$Assessments=fetchAssessments($sid);

/*		generate an index to lookup values from the assessments' array*/
		$assaids=array();
		$asshids=array();
		if($breakdown=='subject'){
			while(list($assno,$Assessment)=each($Assessments)){
				$eid=$Assessment['id_db'];
				$bid=$Assessment['Subject']['value'];
				$pid=$Assessment['SubjectComponent']['value'];
				$asshids[$bid.$pid][]=$assno;
				$assaids[$eid][]=$assno;
				}
			}
		else if($breakdown=='assessment'){
			while(list($assno,$Assessment)=each($Assessments)){
				$eid=$Assessment['id_db'];
				$bid=$Assessment['Subject']['value'];
				$pid=$Assessment['SubjectComponent']['value'];
				$asshids[$eid][]=$assno;
				$assaids[$bid.$pid][]=$assno;
				}
			}

/*		each cell averages over all selected assessments for one hid*/
		reset($hids);
		while(list($c3,$hid)=each($hids)){
		  if(array_key_exists($hid,$asshids)){/*any assessments for this hid?*/
			$assnos=$asshids[$hid];/*all of the entries in Assessments for this hid*/
			$gradesum=0;
			$gradecount=0;
			/*average over all possible Assessments, indexed by assnos*/
			for($c=0;$c<sizeof($assnos);$c++){
				$assno=$assnos[$c];
				$Assessment=$Assessments[$assno];
				if($breakdown=='subject'){$aid=$Assessment['id_db'];}
				else{$aid=$Assessment['Subject']['value'].$Assessment['SubjectComponent']['value'];}
				/*if this matches one of the chosen aids then include in average*/
				if(in_array($aid,$aids)){
					/*get the marktype for this cell  based on one of the
					Assessments, this simplistically assumes all assessments have
					the same marktype - can't do much else until there is the ability
					to translate between grading schemes*/
			   		$crid=$Assessment['Course']['value'];
					$result=$Assessment['Result']['value'];
			   		$gena=$Assessment['GradingScheme']['value'];
					if($gena!=''){
						$d_grading=mysql_query("SELECT grades FROM grading WHERE name='$gena'");
						$grading_grades=mysql_result($d_grading,0);
						}
					else{$grading_grades='';}
					if($grading_grades!=''){
						$score=gradeToScore($result,$grading_grades);
	   					$gradesum=$gradesum+$score;
		   				$gradecount++;
						}
					else{
						$score=$result;
	   					$gradesum=$gradesum+$score;
		   				$gradecount++;
						}
					}
				}
			  }
			  else{$assnos=array(); $gradecount=0;}

		  /*display the assessment average*/
		  if($gradecount>0){
					$scoreaverage=$gradesum/$gradecount;
					$scoreaverage=round($scoreaverage,1);
					$score=round($scoreaverage);
					$grade=scoreToGrade($score,$grading_grades);
					}
		  else{$score='';$scoreaverage='';$grade='';}
		  if(isset($gradestats[$grade])){$gradestats[$grade]=$gradestats[$grade]+1;/*sum for stats*/}
		  else{$gradestats[$grade]=0;$gradestats[$grade]=$gradestats[$grade]+1;}
		  if($gradecount==1){$viewtable[$rowno]['out'][]=$grade;}
		  else{$viewtable[$rowno]['out'][]=$grade.' ('.$scoreaverage.')';}
		  }

		/*end of row average needed here*/
		}
?>
<script>					
<?php 
	/*write the grade statistics for display in the javascripts*/
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
?>
</script>
<div id="viewcontent" class="content">
<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>"> 
	<input type="hidden" name="current" value="<?php print $action;?>" />
 	<input type="hidden" name="choice" value="<?php print $choice;?>" />
 	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
 	<input type="hidden" name="newyid" value="<?php print $yid;?>" />
 	<input type="hidden" name="newfid" value="<?php print $fid;?>" />
<?php for ($c=0;$c<sizeof($bids);$c++){ 
?>
 	<input type="hidden" name="bids[]" value="<?php print $bids[$c];?>" />
<?php } 
 for ($c=0;$c<sizeof($eids);$c++){
?>
 	<input type="hidden" name="eids[]" value="<?php print $eids[$c];?>" />
<?php } 
?>


<div id="assessmentsview">

<table class="listmenu" border="1">
<?php
	print $viewtable[0];/*  display the column headers*/
	for($rowno=1;$rowno<sizeof($viewtable);$rowno++){
		$row=$viewtable[$rowno];
?>
<tr>
	<td>
		<?php print $row['student']['surname']; ?>, <?php print $row['student']['forename']; ?>
		 (<?php print $row['student']['form_id']; ?>)
	</td>
<?php 
	for($c2=0;$c2<sizeof($row['out']);$c2++){
?>
	<td style="text-align:center">
<?php 
	if($row['out'][$c2]!=' ()'){
		print $row['out'][$c2]; 
		}
?>
	</td>
<?php 
		}
?>
<td></td></tr>	
<?php	
		}
?>
</table>
</div>
 	<input type="hidden" name="date" value="<?php print $date;?>" />
 	<input type="hidden" name="choice" value="<?php print $choice;?>" />
 	<input type="hidden" name="current" value="<?php print $action;?>" />
</form>

</div>

<div class="buttonmenu">
	<button onClick="processContent(this);" name="breakdown" value="subject"><?php print_string('displaybysubject',$book);?></button>
	<button onClick="processContent(this);" name="breakdown" value="assessment"><?php print_string('displaybyassessment',$book);?></button>
	<button  type="button" onClick="stats(grades,gradestats,percents)" name="stats"><?php print_string('showstatistics',$book);?></button>
	<button onClick="processContent(this);" name="sub" value="Cancel"><?php print_string('cancel',$book);?></button>
	<button onClick="processContent(this);" name="sub"
	value="Reset"><?php print_string('reset',$book);?></button>
	<button style="visibility:hidden;" name="" value=""></button>
</div>
