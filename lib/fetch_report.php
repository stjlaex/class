<?php	
/**											fetch_report.php
 */

function fetchSubjectReports($sid,$reportdefs){
		$Assessments=fetchAssessments_short($sid);
		$Reports=array();
		$Summaries=array();

		/*generate an index to lookup values from the assessments array*/
		$asseids=array();
		$assbids=array();
		while(list($assno,$Assessment)=each($Assessments)){
		   	$eid=$Assessment['id_db'];
		   	$asseids[$eid][]=$assno;
		   	}

		/*collect all subject reports for each course report chosen*/
		while(list($index,$reportdef)=each($reportdefs)){

			$rid=$reportdef['rid'];
			//trigger_error('reportdef: '.$rid,E_USER_WARNING);
			/*index the assessments by subject and component*/
			$repbids=array();
			while(list($index,$eid)=each($reportdef['eids'])){
			  if(isset($asseids[$eid])){
				reset($asseids[$eid]);
				while(list($index,$assno)=each($asseids[$eid])){
					$bid=$Assessments[$assno]['Subject']['value'];
					$pid=$Assessments[$assno]['SubjectComponent']['value'];
					if($pid==' '){$pid='';}
					$repbids[$bid][$pid][]=$assno;
					}
			    }
			  }
 			ksort($repbids);

			/*now loop through all subjects with a report assessment for this student*/
			/*and generate a subject report for each*/
			while(list($index,$subject)=each($reportdef['bids'])){
			  $bid=$subject['id'];
			  if(isset($repbids[$bid])){$reppids=$repbids[$bid];}
			  else{$reppids=array();}
//			while(list($bid,$reppids)=each($repbids)){

			  while(list($pid,$assnos)=each($reppids)){
				$Report=array();
				if($pid!=''){
					$d_subject=mysql_query("SELECT name FROM subject WHERE id='$pid'");
					$componentname=mysql_result($d_subject,0);
					}
				else{$componentname=' ';}

				$Report['Course']=nullCorrect(array('id'=>$reportdef['report']['course_id'], 
					'value'=>$reportdef['report']['course_name']));
				$Report['Subject']=nullCorrect(array('id'=>$bid, 'value'=>$subject['name']));
				$Report['Component']=nullCorrect(array('id'=>$pid, 'value'=>$componentname));

				$repasses=array();
				for($c8=0;$c8<sizeof($assnos);$c8++){
					$repasses['Assessment'][]=nullCorrect($Assessments[$assnos[$c8]]);
					}
				$Report['Assessments']=nullCorrect($repasses);
				$Report['Comments']=nullCorrect(fetchReportEntry($reportdef,$sid,$bid,$pid));
				$Reports['Report'][]=nullCorrect($Report);
			    }
			  }

			while(list($index,$repsummary)=each($reportdef['summaries'])){
				$summaryid=$repsummary['subtype'];
				$Summary=array();
				$Summary['Description']=array('id'=>$summaryid,
							 'type'=>$repsummary['type'], 'value'=>$repsummary['name']);
				if($repsummary['type']=='com'){
					$Summary['Comments']=nullCorrect(fetchReportEntry($reportdef,$sid,'summary',$summaryid));
					}
				$Summaries['Summary'][]=nullCorrect($Summary);
				}
			$Reports['Summaries']=nullCorrect($Summaries);
			/* when combining reports, for now this only works if each has the*/
			/*	same properties!!!*/
		   	$Reports['asstable']=$reportdef['asstable'];
		   	if(isset($reportdef['cattable'])){$Reports['cattable']=$reportdef['cattable'];}
		   	$Reports['publishdate']=date('jS M Y',strtotime($reportdef['report']['date']));
		   	$transform=$reportdef['report']['transform'];
			}
	return array($Reports,$transform);
	}

function fetchReportDefinition($rid,$selbid='%'){
	/*this is NOT an xml array and needs to be rewritten*/
	$reportdef=array();
	$reportdef['id_db']=$rid;
	$reportdef['rid']=$rid;
	$d_report=mysql_query("SELECT * FROM report WHERE id='$rid'");
	if(mysql_numrows($d_report)==0){$reportdef['exists']='false';}
	else{$reportdef['exists']='true';}
	$report=mysql_fetch_array($d_report,MYSQL_ASSOC);
	$crid=$report['course_id'];
	if($crid!='wrapper'){
		$d_course=mysql_query("SELECT name FROM course WHERE id='$crid'");
		$report['course_name']=mysql_result($d_course,0);
		$d_mid=mysql_query("SELECT id FROM mark WHERE midlist='$rid' and marktype='report'");
		$markcount=mysql_numrows($d_mid);
		$reportdef['MarkCount']=array('label' => 'Mark Columns', 
								  'value' => ''.$markcount);
		}
	else{
		$report['course_name']='';
		$d_report=mysql_query("SELECT id,title,stage,course_id FROM report JOIN
				ridcatid ON ridcatid.categorydef_id=report.id 
				WHERE ridcatid.report_id='$rid' AND
				ridcatid.subject_id='wrapper'");
		$reptable=array();
		while($rep=mysql_fetch_array($d_report,MYSQL_ASSOC)){
			$reptable['rep'][]=array('name' => $rep['title'],'course_id'=>$rep['course_id'],
				'stage'=>$rep['stage'],'id_db' => $rep['id']);
			}
		$reportdef['reptable']=nullCorrect($reptable);
		}

	$reportdef['report']=nullCorrect($report);

	$d_cridbid=mysql_query("SELECT DISTINCT subject_id FROM
			cridbid WHERE course_id='$crid' AND subject_id LIKE '$selbid'");
	$reportdef['bids']=array();
	while($bid=mysql_fetch_array($d_cridbid,MYSQL_NUM)){
			$d_subject=mysql_query("SELECT name FROM subject WHERE id='$bid[0]'");
			$subjectname=mysql_result($d_subject,0);
			$reportdef['bids'][]=array('id'=>$bid[0], 'name'=>''.$subjectname);
			}

	$d_assessment=mysql_query("SELECT * FROM assessment JOIN
				rideid ON rideid.assessment_id=assessment.id 
				WHERE report_id='$rid' ORDER BY rideid.priority, assessment.label");
	$reportdef['eids']=array();
	$asstable=array();
	$asselements=array();
	while($ass=mysql_fetch_array($d_assessment,MYSQL_ASSOC)){
		$reportdef['eids'][]=$ass['id'];
		if(!in_array($ass['element'],$asselements) or $ass['element']==''){
			/*this is only used by the xslt... 
				an element can only apear once on the printed report!*/
			$asselements[]=$ass['element'];
			$asstable['ass'][]=array('name' => $ass['description'],
				'label' => $ass['label'], 'element' => ''.$ass['element']);
			}
		}
	$reportdef['asstable']=nullCorrect($asstable);

	if($reportdef['report']['addcategory']=='yes'){
		/*find the categories for this report*/
		list($ratingnames, $catdefs)=fetchReportCategories($rid);
		$reportdef['ratingnames']=$ratingnames;
		$reportdef['catdefs']=$catdefs;
		$cattable=array();
		while(list($index,$cat)=each($catdefs)){
			$cattable['cat'][]=array('name' => ''.$cat['name']);
			}
		while(list($index,$ratings)=each($ratingnames)){
			while(list($value,$rat)=each($ratings)){
				$cattable['rat'][]=array('name' => ''.$rat, 'value' => ''.$value);
				}
			}
		$reportdef['cattable']=nullCorrect($cattable);
		}
	$reportdef['summaries']=(array)fetchReportSummaries($rid);

	return $reportdef;
	}


function fetchReportCategories($rid,$bid='%'){
	/*returns two arrays containing the ratingnames and catdefs for all
		categories for this report*/

	/*Needs to add subject specific ones IN FUTURE!*/
	$d_categorydef=mysql_query("SELECT * FROM categorydef LEFT
				JOIN ridcatid ON ridcatid.categorydef_id=categorydef.id 
				WHERE ridcatid.report_id='$rid' AND
				(ridcatid.subject_id LIKE '$bid' OR
				ridcatid.subject_id='%') AND ridcatid.subject_id!='summary'");
   	$catdefs=array();
	$ratingnames=array();
	while($catdef=mysql_fetch_array($d_categorydef,MYSQL_ASSOC)){
	   	$catdefs[]=$catdef;
	   	if(!array_key_exists($catdef['rating_name'],
				$ratingnames)){
				$ratingname=$catdef['rating_name'];
				$d_rating=mysql_query("SELECT * FROM rating 
						WHERE name='$ratingname' ORDER BY value");
				$ratings=array();
				while($rating=mysql_fetch_array($d_rating,MYSQL_ASSOC)){
					$ratings[$rating['value']]=$rating['descriptor'];
					}
				$ratingnames[$ratingname]=$ratings;
				}
	   	}
	return array($ratingnames, $catdefs);
	}

function fetchReportSummaries($rid){
	/*returns one array containing the catdefs for all summaries for this report*/

	$d_categorydef=mysql_query("SELECT categorydef.id,
				categorydef.name, categorydef.type, categorydef.subtype, categorydef.subject_id,
				categorydef.rating FROM categorydef LEFT
				JOIN ridcatid ON ridcatid.categorydef_id=categorydef.id 
				WHERE ridcatid.report_id='$rid' AND
				ridcatid.subject_id='summary' ORDER BY
				categorydef.type, categorydef.rating");
   	$catdefs=array();
	while($catdef=mysql_fetch_array($d_categorydef,MYSQL_ASSOC)){
	   	$catdefs[]=$catdef;
	   	}
	return $catdefs;
	}


function checkReportEntry($rid, $sid, $bid, $pid){
	$d_reportentry=mysql_query("SELECT entryn
					FROM reportentry WHERE report_id='$rid' AND
					student_id='$sid' AND subject_id='$bid' AND
					component_id='$pid'");
	return mysql_numrows($d_reportentry);
	}

function fetchReportEntry($reportdef, $sid, $bid, $pid){
/*		Retrieves all report entries for one student in one subject*/
/*		All report info is prefetched in $reportdef['report'], */  
/*				$reportdef['catdefs'] and $reportdef['ratingnames']*/

	$Comments=array();
	$rid=$reportdef['report']['id'];
  	$d_reportentry=mysql_query("SELECT * FROM reportentry WHERE
		  report_id='$rid' AND student_id='$sid' AND subject_id='$bid'
		  AND component_id='$pid' ORDER BY entryn");
	while($entry=mysql_fetch_array($d_reportentry)){
	   $Comment=array();
	   $Comment['id_db']=$entry['entryn'];
	   if($reportdef['report']['addcomment']=='yes' or $bid=='summary'){
		   $enttid=$entry['teacher_id'];
		   $d_teacher=mysql_query("SELECT forename, surname 
							FROM users WHERE username='$enttid'");
		   $teachername=mysql_fetch_array($d_teacher,MYSQL_ASSOC);	      
		   $Comment['Teacher']=nullCorrect(array('id_db' => 
				   $enttid, 'value'=>$teachername['forename'].' '.$teachername['surname']));
		   $Comment['Text']=nullCorrect(array('value' => $entry['comment']));
		   }
	   if($reportdef['report']['addcategory']=='yes' and $bid!='summary'){
    		$pairs=explode(";",$entry['category']);
	    	for($c3=0; $c3<sizeof($pairs)-1; $c3++){
   				list($id, $rank)=split(":",$pairs[$c3]);
		   		$entry['ratings'][$id]=$rank;
		   		}
		    $ratingnames=$reportdef['ratingnames'];
			$catdefs=$reportdef['catdefs'];
		   	$Categories=array();
		  	for($c4=0;$c4<sizeof($catdefs);$c4++){
		   		$Category=array();
	   			$catid=$catdefs[$c4]['id'];
   				$catname=$catdefs[$c4]['name'];
			   	$ratings=$ratingnames[$catdefs[$c4]['rating_name']];
			  	$Category=array('label' => $catname, 'id_db' => $catid);
				if(isset($entry['ratings'][$catid])){
					while(list($value,$descriptor)=each($ratings)){
				   		if($entry['ratings'][$catid]==$value){$Category['value']=$value;}
						}
				   	}
				else{$Category['value']='';}
			   	$Categories['Category'][]=nullCorrect($Category);
		   		}
		   $Comment['Categories']=nullCorrect($Categories);
		   }
	   $Comments['Comment'][]=nullCorrect($Comment);
	   }
	return $Comments;
}
?>
