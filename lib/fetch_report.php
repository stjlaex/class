<?php	
/**									   	fetch_report.php
 */

function fetchSubjectReports($sid,$reportdefs){
	$Reports=array();
	$Reports['SummaryAssessments']=array();
	$Assessments=array();
	$Summaries=array();
	$asseids=array();
	$asselements=array();

	/* Collate all assessment and report entries by subject for each course report chosen*/
	while(list($repindex,$reportdef)=each($reportdefs)){
			$rid=$reportdef['rid'];

			/* Provide a look-up array $assbids which references the $Assessments
			 array by index for every subject and component combination which
			 has an Assessment for this student*/
			$assbids=array();
			while(list($index,$eid)=each($reportdef['eids'])){
				if(!isset($asseids[$eid])){
					/*only need to fetch for each eid once*/
					$asseids[$eid]=(array)fetchAssessments_short($sid,$eid);
					}
				if(sizeof($asseids[$eid])>0){
					$Assessments=array_merge($Assessments,$asseids[$eid]);
					reset($Assessments);
					}
				}
			while(list($index,$Assessment)=each($Assessments)){
				$bid=$Assessment['Subject']['value'];
				$pid=$Assessment['SubjectComponent']['value'];
				if($pid==''){$pid=' ';}/*nullCorrect as usual!*/
				$assbids[$bid][$pid][]=$index;
				}
 			ksort($assbids);

			/* This is for assessments which are really statistics.
			 They have two components: overall averages (sid=0) for
			 every bid-pid possible which are independent of the sid,
			 and the sid specific cross-curricular average. They must
			 not be used to generate indexes for assbids otherwise a
			 reportentry for ALL conceivable bid-pid combinations is
			 included */
			while(list($index,$eid)=each($reportdef['stateids'])){
				$GAssessments=(array)fetchAssessments_short($sid,$eid);
				//trigger_error('GStats: '.$eid.' number '.sizeof($GAssessments),E_USER_WARNING);
				if(sizeof($GAssessments)>0){
					$Reports['SummaryAssessments'][]['Assessment']=nullCorrect($GAssessments);
					/* only take the overall assessments for the statseid
						which is relevant to this sid */
					$StatsAssessments=(array)fetchAssessments_short(0,$eid);
					while(list($index,$Assessment)=each($StatsAssessments)){
						$bid=$Assessment['Subject']['value'];
						$pid=$Assessment['SubjectComponent']['value'];
						if($pid==''){$pid=' ';}/*nullCorrect as usual!*/
						if(isset($assbids[$bid][$pid])){
							$Assessments[]=$Assessment;
							end($Assessments);
							$assbids[$bid][$pid][]=key($Assessments);
							}
						}
					}
				}

			/* Now loop through all possible subjects and generate a
			 Report for each which has at least one assessment or a
			 reportentry - any subjects which have neither will not
			 have a Report*/
			while(list($index,$subject)=each($reportdef['bids'])){
			  $bid=$subject['id'];
			  $pids=array();
			  $pids=(array)$subject['pids'];
			  //if(sizeof($pids)>0){$pids[]=array('id'=>' ','name'=>'');}
			  while(list($index,$component)=each($pids)){
				  $pid=$component['id'];
				  $componentname=$component['name'];
				  $componentstatus=$component['status'];

				  /* Combine assessment indexes for this component and all of its
					strands into a single array $assnos.*/
				  $assnos=array();
				  $component['strands'][]=array('id'=>$pid);
				  while(list($index,$strand)=each($component['strands'])){
					  if(isset($assbids[$bid][$strand['id']])){
						  $assnos=array_merge($assnos,$assbids[$bid][$strand['id']]);
						  }
					  }

				  $Comments=fetchReportEntry($reportdef,$sid,$bid,$pid);
				  if(sizeof($Comments)>0 or sizeof($assnos)>0){
					  $Report=array();
					  $Report['Course']=array('id'=>''.$reportdef['report']['course_id'], 
											  'value'=>''.$reportdef['report']['course_name']);
					  $Report['Subject']=array('id'=>''.$bid, 
											   'value'=>''.$subject['name']);
					  $Report['Component']=array('id'=>''.$pid, 
												 'status'=>''.$componentstatus,
												 'value'=>''.$componentname);
					  $repasses=array();
					  while(list($index,$assno)=each($assnos)){
						  $repasses['Assessment'][]=nullCorrect($Assessments[$assno]);
						  }
					  $Report['Assessments']=nullCorrect($repasses);
					  $Report['Comments']=nullCorrect($Comments);
					  $Reports['Report'][]=nullCorrect($Report);
					  }
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

			/* Add assessments to the asstable, to display using xslt
			in the report. Each element appears only once.*/
			if(is_array($reportdef['asstable']['ass'])){
				while(list($index,$ass)=each($reportdef['asstable']['ass'])){
					if(!in_array($ass['element'],$asselements)){
						$asselements[]=$ass['element'];
						$Reports['asstable']['ass'][]=$ass;
						}
					}
				}				

			/* When combining reports, for now this only works if each
			 has the same properties. Otherwise it will be the properties
			 of the last reportdef in the list which dominate!!!*/
		   	if(isset($reportdef['cattable'])){$Reports['cattable']=$reportdef['cattable'];}
			$Reports['Summaries']=nullCorrect($Summaries);
		   	$Reports['publishdate']=date('jS M Y',strtotime($reportdef['report']['date']));
		   	$transform=$reportdef['report']['transform'];
		   	$style=$reportdef['report']['style'];
			}

	if(sizeof($Reports['SummaryAssessments'])==0){
		$Reports['SummaryAssessments']=nullCorrect($Reports['SummaryAssessments']);
		}

	return array($Reports,$transform);
	}

function fetchReportDefinition($rid,$selbid='%'){
	/* This is NOT an xml-friendly array and needs to be rewritten.*/
	$reportdef=array();
	$reportdef['id_db']=$rid;
	$reportdef['rid']=$rid;
	$d_report=mysql_query("SELECT * FROM report WHERE id='$rid'");
	if(mysql_numrows($d_report)==0){$reportdef['exists']='false';}
	else{$reportdef['exists']='true';}
	$report=mysql_fetch_array($d_report,MYSQL_ASSOC);
	$crid=$report['course_id'];
	if($crid!='wrapper'){
		$report['course_name']=get_coursename($crid);
		$d_mid=mysql_query("SELECT id FROM mark WHERE midlist='$rid' and marktype='report'");
		$markcount=mysql_numrows($d_mid);
		$reportdef['MarkCount']=array('label' => 'Markcolumns', 
									  'value' => ''.$markcount);
		$d_categorydef=mysql_query("SELECT name
				FROM categorydef JOIN ridcatid ON ridcatid.categorydef_id=categorydef.id 
				WHERE ridcatid.report_id='$rid' AND
				ridcatid.subject_id='profile'");
		$report['profile_name']=mysql_result($d_categorydef,0);
		}
	else{
		$report['course_name']='';
		$d_report=mysql_query("SELECT id,title,stage,course_id FROM
				report JOIN ridcatid ON ridcatid.categorydef_id=report.id 
				WHERE ridcatid.report_id='$rid' AND
				ridcatid.subject_id='wrapper'");
		$reptable=array();
		while($rep=mysql_fetch_array($d_report,MYSQL_ASSOC)){
			$reptable['rep'][]=array('name' => $rep['title'],
									 'course_id'=>$rep['course_id'],
									 'stage'=>$rep['stage'],
									 'id_db' => $rep['id']);
			}
		$reportdef['reptable']=nullCorrect($reptable);
		}
	$reportdef['report']=nullCorrect($report);

	/* Build a reference of relevant bids/pids/strands */
	$subjects=array();
	if($selbid=='%'){
		$subjects=list_course_subjects($crid);
		}
	else{
		$subjectname=get_subjectname($selbid);
		$subjects[]=array('id'=>$selbid, 
						'name'=>''.$subjectname);
		}
	while(list($index0,$subject)=each($subjects)){
		$components=(array)list_subject_components($subject['id'],$crid);
		while(list($index1,$component)=each($components)){
			$strands=(array)list_subject_components($component['id'],$crid);
			$components[$index1]['strands']=$strands;
			}
		/* Must always be a blank entry to catch the parent subject itself.*/
		$components[]=array('id'=>' ','name'=>'');
		$subjects[$index0]['pids']=$components;
		}
	$reportdef['bids']=$subjects;

	$d_assessment=mysql_query("SELECT * FROM assessment JOIN
				rideid ON rideid.assessment_id=assessment.id 
				WHERE report_id='$rid' ORDER BY rideid.priority, assessment.label");
	$reportdef['eids']=array();
	$reportdef['stateids']=array();
	$asstable=array();
	$asselements=array();
	while($ass=mysql_fetch_array($d_assessment,MYSQL_ASSOC)){
		if($ass['resultstatus']=='S' or $ass['subject_id']=='G'){
			$reportdef['stateids'][]=$ass['id'];
			}
		else{
			$reportdef['eids'][]=$ass['id'];
			}
		if((!in_array($ass['element'],$asselements) or
				$ass['element']=='') and $ass['subject_id']!='G'){
			/* This $asstable is only used by the xslt to construct
				the grade table, it uses the value of element to
				identify assessments in the xml. Many alternative
				assessments may share the same spot on the report if
				they have the same element. Hence an element
				should be unique, each can only apear once in the report. */
			/* Assessments which are not subject specific (G for
				general) belong in summaryassessments. */
			$asselements[]=$ass['element'];
			$asstable['ass'][]=array('name' => ''.$ass['description'],
									 'label' => ''.$ass['label'],
									 'element' => ''.$ass['element']);
			}
		}
	$reportdef['asstable']=nullCorrect($asstable);

	/* Find any categories for this report. */
	if($reportdef['report']['addcategory']=='yes'){
		list($ratingnames, $catdefs)=fetchReportCategories($rid);
		$reportdef['ratingnames']=$ratingnames;
		$reportdef['catdefs']=$catdefs;
		$cattable=array();
		while(list($index,$cat)=each($catdefs)){
			$cattable['cat'][]=array('name'=>''.$cat['name']);
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
				ridcatid.subject_id='%') AND
				ridcatid.subject_id!='summary' AND
				ridcatid.subject_id!='wrapper' AND ridcatid.subject_id!='profile'");
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


/* Returns one array containing the catdefs for all summaries for this */
/* report. */
function fetchReportSummaries($rid){
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


function checkReportEntry($rid,$sid,$bid,$pid){
	$d_reportentry=mysql_query("SELECT entryn
					FROM reportentry WHERE report_id='$rid' AND
					student_id='$sid' AND subject_id='$bid' AND
					component_id='$pid'");
	return mysql_numrows($d_reportentry);
	}


/**		Retrieves all report entries for one student in one subject
 *		All report info is pre-fetched in $reportdef['report'],
 *				$reportdef['catdefs'] and $reportdef['ratingnames']
 */
function fetchReportEntry($reportdef,$sid,$bid,$pid){
	$Comments=array();
	//$Comments['Comment']=array();
	$rid=$reportdef['report']['id'];
   	$d_reportentry=mysql_query("SELECT * FROM reportentry WHERE
		  report_id='$rid' AND student_id='$sid' AND subject_id='$bid'
		  AND component_id='$pid' ORDER BY entryn");
	while($entry=mysql_fetch_array($d_reportentry)){
	   $Comment=array();
	   $Comment['id_db']=$entry['entryn'];
	   if($reportdef['report']['addcomment']=='yes' or $bid=='summary'){
		   $enttid=$entry['teacher_id'];
		   $teachername=get_teachername($enttid);
		   $Comment['Teacher']=nullCorrect(array('id_db' => ''.$enttid, 
												 'value'=> ''.$teachername));
		   $Comment['Text']=nullCorrect(array('value' => ''.$entry['comment']));
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
