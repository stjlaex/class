<?php	
/**									   	fetch_report.php
 *
 */


/**
 *
 */
function fetchSubjectReports($sid,$reportdefs){
	$Reports=array();
	$Reports['SummaryAssessments']=array();
	$Assessments=array();
	$Summaries=array();
	$asseids=array();
	$asselements=array();

	/* Take care first to only apply reports for which this sid is a
	 * member of the relevant cohort.
	 */
	foreach($reportdefs as $reportdef){
		if($reportdef['report']['course_id']!='wrapper' and $reportdef['report']['stage']!='%'){
			$report_cohort=array('id'=>'',
								 'course_id'=>$reportdef['report']['course_id'],
								 'year'=>$reportdef['report']['year'],
								 'stage'=>$reportdef['report']['stage']);
			$status=check_student_cohort($sid,$report_cohort,$todate='');
			if($status){$relevant_reportdefs[]=$reportdef;}
			}
		else{$relevant_reportdefs[]=$reportdef;}
		}

	/* Collate all assessment and report entries by subject for each
	 * course report chosen.
	 */
	foreach($relevant_reportdefs as $reportdef){
		$rid=$reportdef['rid'];
			/* Provide a look-up array $assbids which references the $Assessments
			 * array by index for every subject and component combination which
			 * has an Assessment for this student. 
			 */
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

			/**
			 * List the assessments for any linked profile
			 */
			if(isset($reportdef['report']['profile_name']) 
			   and $reportdef['report']['profile_name']!='' and $reportdef['report']['profile_name']!=' '){
				$profile_name=$reportdef['report']['profile_name'];
				$profile_crid=$reportdef['report']['course_id'];
				$profile_enddate=$reportdef['report']['date'];
				/* Include only those that are results and that were recorded prior 
				 * to this report (otherwise the report is going to change and it 
				 * should be fixed at the publication data). 
				 */
				$d_a=mysql_query("SELECT id FROM assessment WHERE course_id='$profile_crid' AND
					   profile_name='$profile_name' AND resultstatus='R' AND deadline<='$profile_enddate';");

				$profile_asseids=array();
				while($a=mysql_fetch_array($d_a,MYSQL_ASSOC)){
					/* Do not include any eid that is linked explicity
					 * with the report - probably current attainment -
					 * the profile only deals with the history
					 */
					if(!array_key_exists($a['id'],$asseids)){$profile_asseids[]=$a['id'];}
					}
				if(sizeof($profile_asseids)==0){$profile_name='';}
				}
			else{$profile_name='';}

			/* This is for assessments which are really statistics.
			 * They have two components: overall averages (sid=0) for
			 * every bid-pid possible which are independent of the sid,
			 * and the sid specific cross-curricular average. They must
			 * not be used to generate indexes for assbids otherwise a
			 * reportentry for ALL conceivable bid-pid combinations is
			 * included 
			 */
			while(list($index,$eid)=each($reportdef['stateids'])){
				$GAssessments=(array)fetchAssessments_short($sid,$eid);
		//trigger_error('GStats: '.$eid.' number '.sizeof($GAssessments),E_USER_WARNING);
				if(sizeof($GAssessments)>0){
					$Reports['SummaryAssessments'][]['Assessment']=nullCorrect($GAssessments);
					/* Only take the overall assessments for the statseid
					 * which is relevant to this sid 
					 */
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
			 * Report for each which has at least one assessment or a
			 * reportentry - any subjects which have neither will not
			 * have a Report
			 */
			while(list($index,$subject)=each($reportdef['bids'])){
			  $bid=$subject['id'];
			  $pids=array();
			  $pids=(array)$subject['pids'];
			  //if(sizeof($pids)>0){$pids[]=array('id'=>' ','name'=>'');}
			  reset($pids);
			  while(list($index,$component)=each($pids)){
				  $pid=$component['id'];
				  $componentname=$component['name'];
				  if(isset($component['status'])){$componentstatus=$component['status'];}
				  else{
					  $componentstatus='';
					  }
				  if(isset($component['sequence'])){
					  $componentseq=$component['sequence'];
					  }
				  else{
					  /* TODO: combine sequences for bid and pid consistently
					   * but until cridbid has a sequence is it possible or even cridbid 
					   * is subsumed into component?
					   */ 
					  if($bid=='Mat' or $bid=='Jun'){$componentseq=1;}
					  else{$componentseq=10;}
					  }
				  /* Combine assessment indexes for this component and all of its
				   * strands into a single array $assnos. 
				   */
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
												 'sequence'=>''.$componentseq,
												 'value'=>''.$componentname);
					  $repasses=array();
					  while(list($index,$assno)=each($assnos)){
						  $repasses['Assessment'][]=nullCorrect($Assessments[$assno]);
  						  }

					  /* An additional section if the report is linked to an assessment profile. */
					  if($profile_name!='' and $profile_name!=' '){
						  $ProfileAssessments['Assessment']=array();
						  foreach($profile_asseids as $index=>$eid){
							  $PAsses=(array)fetchAssessments_short($sid,$eid,$bid,$pid);
							  if(sizeof($PAsses)>0){
								  $PAsses=nullCorrect($PAsses);
								  $ProfileAssessments['Assessment']=array_merge($ProfileAssessments['Assessment'],$PAsses);
								  }
							  }
						  if(sizeof($ProfileAssessments['Assessment'])==0){
							  $ProfileAssessments['Assessment']=nullCorrect($ProfileAssessments['Assessment']);
							  }
						  $Report['ProfileAssessments']=$ProfileAssessments;
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


/**
 *
 */
function fetchReportDefinition($rid,$selbid='%'){
	$RepDef=array();
	$RepDef['id_db']=$rid;
	$d_report=mysql_query("SELECT * FROM report WHERE id='$rid';");
	if(mysql_numrows($d_report)==0){$RepDef['exists']='false';}
	else{$RepDef['exists']='true';}
	$report=mysql_fetch_array($d_report,MYSQL_ASSOC);
	$crid=$report['course_id'];
   	$RepDef['Course']=array('label'=>'course',
							'table_db'=>'report', 
							'field_db'=>'course_id',
							'type_db'=>'varchar(10)', 
							'value'=>''.$report['course_id']);
   	$RepDef['Stage']=array('label'=>'stage',
						   'table_db'=>'report', 
						   'field_db'=>'stage',
						   'type_db'=>'char(3)', 
						   'value'=>''.$report['stage']);
   	$RepDef['Title']=array('label'=>'title',
						   'inputtype'=> 'required',
						   'table_db'=>'report', 
						   'field_db'=>'title',
						   'type_db'=>'char(60)', 
						   'value'=>''.$report['title']);
   	$RepDef['PublishedDate']=array('label'=>'publisheddate', 
								   'inputtype'=> 'required',
								   'table_db'=>'report', 
								   'field_db'=>'date',
								   'type_db'=>'date', 
								   'value'=>''.$report['date']);
   	$RepDef['Deadline']=array('label'=>'deadlineforcompletion', 
							  'inputtype'=>'required',
							  'table_db'=>'report', 
							  'field_db'=>'deadline',
							  'type_db'=>'date', 
							  'value'=>''.$report['deadline']);
   	$RepDef['Year']=array('label'=>'Year', 
						  'inputtype'=>'required',
						  'table_db'=>'report', 
						  'field_db'=>'year',
						  'type_db'=>'year', 
						  'value'=>''.$report['year']);
   	$RepDef['ComponentStatus']=array('label'=>'componentstatus', 
									 'table_db'=>'report', 
									 'field_db'=>'component_status',
									 'type_db'=>'enum', 
									 'value'=>''.$report['component_status']);
   	$RepDef['CommentsOn']=array('label'=>'allowsubjectcomments', 
								'table_db'=>'report', 
								'field_db'=>'addcomment',
								'type_db'=>'enum', 
								'value'=>''.$report['addcomment']);
   	$RepDef['CommentsCompulsory']=array('label'=>'commentsarecompulsory', 
										'table_db'=>'report', 
										'field_db'=>'commentcomp',
										'type_db'=>'enum', 
										'value'=>''.$report['commentcomp']);
   	$RepDef['CommentsLength']=array('label'=>'restrictcommentscharacterlength', 
									'table_db'=>'report', 
									'field_db'=>'commentlength',
									'type_db'=>'smallint', 
									'value'=>''.$report['commentlength']);
   	$RepDef['CategoriesOn']=array('label'=>'addcategories', 
								  'table_db'=>'report', 
								  'field_db'=>'addcategory',
								  'type_db'=>'enum', 
								  'value'=>''.$report['addcategory']);
   	$RepDef['CategoriesRating']=array('label'=>'ratingname', 
									  'table_db'=>'report', 
									  'field_db'=>'rating_name',
									  'type_db'=>'varchar(30)', 
									  'ratings'=>'', 
									  'value'=>''.$report['rating_name']);
   	$RepDef['Style']=array('label'=>'pagestyle', 
						   'table_db'=>'report', 
						   'field_db'=>'style',
						   'type_db'=>'varchar(60)', 
						   'value'=>''.$report['style']);
   	$RepDef['Template']=array('label'=>'nameoftemplate', 
							  'table_db'=>'report', 
							  'field_db'=>'template',
							  'type_db'=>'varchar(60)', 
							  'value'=>''.$report['transform']);
	
	if($crid!='wrapper'){
		$report['course_name']=get_coursename($crid);
		$d_mid=mysql_query("SELECT id FROM mark WHERE midlist='$rid' 
												AND marktype='report';");
		$markcount=mysql_numrows($d_mid);
		$RepDef['MarkCount']=array('label' => 'markcolumns', 
								   'value' => ''.$markcount);

		/* This identifies any assessment profiles the report is linked to. */
		$d_catdef=mysql_query("SELECT id,name
				FROM categorydef JOIN ridcatid ON ridcatid.categorydef_id=categorydef.id 
				WHERE ridcatid.report_id='$rid' AND categorydef.type='pro' AND
				ridcatid.subject_id='profile';");
		if(mysql_num_rows($d_catdef)>0){
			$profile=mysql_fetch_array($d_catdef,MYSQL_ASSOC);
			$RepDef['ProfileLink']=array('id_db'=>''.$profile['id'],'name'=>''.$profile['name']);
			}
		}
	else{
		$report['course_name']='';
		$d_report=mysql_query("SELECT id,title,stage,course_id FROM
				report JOIN ridcatid ON ridcatid.categorydef_id=report.id 
				WHERE ridcatid.report_id='$rid' AND
				ridcatid.subject_id='wrapper';");
		$reptable=array();
		while($rep=mysql_fetch_array($d_report,MYSQL_ASSOC)){
			$reptable['rep'][]=array('name' => $rep['title'],
									 'course_id'=>$rep['course_id'],
									 'stage'=>$rep['stage'],
									 'id_db' => $rep['id']);
			}
		$RepDef['reptable']=nullCorrect($reptable);
		}


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
	$RepDef['bids']=$subjects;

	$d_assessment=mysql_query("SELECT * FROM assessment JOIN
				rideid ON rideid.assessment_id=assessment.id 
				WHERE report_id='$rid' ORDER BY rideid.priority, assessment.label");
	$RepDef['eids']=array();
	$RepDef['stateids']=array();
	$asstable=array();
	$asselements=array();
	while($ass=mysql_fetch_array($d_assessment,MYSQL_ASSOC)){
		if($ass['resultstatus']=='S' or $ass['subject_id']=='G'){
			$RepDef['stateids'][]=$ass['id'];
			}
		else{
			$RepDef['eids'][]=$ass['id'];
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
	$RepDef['asstable']=nullCorrect($asstable);

	if($report['addcategory']=='yes'){
		/* ratings is an array of value=>descriptor pairs. 
		   TODO: make use of the longdescriptor?
		 */
	   	if($RepDef['CategoriesRating']['value']!=''){
			$ratingname=$RepDef['CategoriesRating']['value'];
			$d_rating=mysql_query("SELECT * FROM rating 
						WHERE name='$ratingname' ORDER BY value;");
			$ratings=array();
			/*TODO: bring up to date for bid specific ratings identified using ratingname */
			while($rating=mysql_fetch_array($d_rating,MYSQL_ASSOC)){
				$ratings[$rating['value']]=$rating['descriptor'];
				}
			$RepDef['CategoriesRating']['ratings']=$ratings;
			}

		/*
		list($ratingnames,$catdefs)=get_report_categories($rid,$selbid);
		$RepDef['ratingnames']=$ratingnames;
		$RepDef['catdefs']=$catdefs;
		$cattable=array();
		while(list($index,$cat)=each($catdefs)){
			$cattable['cat'][]=array('name'=>''.$cat['name']);
			}
		while(list($index,$ratings)=each($ratingnames)){
			while(list($value,$rat)=each($ratings)){
				$cattable['rat'][]=array('name' => ''.$rat, 'value' => ''.$value);
				}
			}
		$RepDef['cattable']=nullCorrect($cattable);
		*/
		}
	$RepDef['summaries']=(array)fetchReportSummaries($rid);

	return $RepDef;
	}

/**
 * This OLD and NOT an xml-friendly array and is deprecated
 * for the new version fetchReportDefinition above.
 *
 * TODO: update all scripts which call this to use the new XML
 * compliant version:
 *
 //markbook/new_edit_reports.php
 //markbook/httpscripts/report_summary_preview.php
 //reportbook/report_reports_publish.php
 //reportbook/report_reports_email.php
 //reportbook/report_reports_list.php
 //reportbook/new_report.php
 //reportbook/httpscripts/generate_report_columns.php
 //reportbook/httpscripts/generate_report_columns.php
 //reportbook/httpscripts/report_reports_print.php
 //reportbook/httpscripts/delete_report.php
 //reportbook/httpscripts/delete_report_columns.php
 //reportbook/httpscripts/comment_writer.php
 */
function fetch_reportdefinition($rid,$selbid='%'){
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
		$reportdef['MarkCount']=array('label' => 'markcolumns', 
									  'value' => ''.$markcount);

		/* This identifies any assessment profiles the report is linked to. */
		$d_categorydef=mysql_query("SELECT name
				FROM categorydef JOIN ridcatid ON ridcatid.categorydef_id=categorydef.id 
				WHERE ridcatid.report_id='$rid' AND categorydef.type='pro' AND
				ridcatid.subject_id='profile'");
		if(mysql_num_rows($d_categorydef)>0){
			$report['profile_name']=mysql_result($d_categorydef,0);
			}
		else{
			$report['profile_name']='';
			}
		}
	else{
		$report['course_name']='';
		$d_report=mysql_query("SELECT id,title,stage,course_id FROM
				report JOIN ridcatid ON ridcatid.categorydef_id=report.id 
				WHERE ridcatid.report_id='$rid' AND
				ridcatid.subject_id='wrapper';");
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
			/* Just in case to avoid a nasty recursion if component and subject have the same id */
	   		if($component['id']!=$subject['id']){
				$strands=(array)list_subject_components($component['id'],$crid);
				}
			else{
				$strands=array();
				}
			$components[$index1]['strands']=$strands;
			}
		/* Must always be a blank entry to catch the parent subject itself.*/
		$components[]=array('id'=>' ','name'=>'');
		$subjects[$index0]['pids']=$components;
		}
	$reportdef['bids']=$subjects;

	$d_assessment=mysql_query("SELECT * FROM assessment JOIN
				rideid ON rideid.assessment_id=assessment.id 
				WHERE report_id='$rid' ORDER BY rideid.priority, assessment.label;");
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

	if($reportdef['report']['addcategory']=='yes'){

		$reportdef['ratings']=array();
		$cattable=array();
	   	if($reportdef['report']['rating_name']!=''){
			$reportdef['cattable']=array();
			$pairs=explode(';',$reportdef['report']['rating_name']);

			/* 
			 *  Just in case: for backward compatibility with existing reports. 
			 */
			if(!is_array($pairs) or (is_array($pairs) and sizeof($pairs)==1)){
				$pairs=array('0'=>'%:'.$reportdef['report']['rating_name']);
				}

			for($c=0;$c<sizeof($pairs);$c++){
				$ratings=array();
				$cattable=array();
				list($ratingbid, $ratingname)=split(':',$pairs[$c]);
				/* Only need to do each rating name once. */
				if(!isset($reportdef['ratings'][$ratingname])){
					$cattable['ratingname']=$ratingname;
					$d_rating=mysql_query("SELECT * FROM rating WHERE name='$ratingname' ORDER BY value;");
					while($rating=mysql_fetch_array($d_rating,MYSQL_ASSOC)){
						$ratings[$rating['value']]=$rating['descriptor'];
						$cattable['rat'][]=array('name'=>''.$rating['descriptor'],
												 'value'=>''.$rating['value']);
						}
					$reportdef['cattable'][]=nullCorrect($cattable);
					$reportdef['ratings'][$ratingname]=$ratings;
					}
				}
			}

		/*
		list($ratingnames, $catdefs)=get_report_categories($rid,$selbid);
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
		*/

		}

	$reportdef['summaries']=(array)fetchReportSummaries($rid);

	return $reportdef;
	}



/**
 *
 * Returns an array containing the catdefs for all
 * categories for this report.
 * 
 * The type can be 'cat' (default) or 'sub' if we are looking for
 * subcomment categories.
 *
 */
function get_report_categories($rid,$bid='%',$pid='',$type='cat'){

	/* There is no component_id field in ridcatid, if pid is set then it uses subject_id */
	if($pid!='' and $pid!=' '){$bid=$pid;}

	$d_categorydef=mysql_query("SELECT id, name, type, subtype, rating,  
				 rating_name, comment, ridcatid.subject_id AS bid FROM categorydef LEFT
				JOIN ridcatid ON ridcatid.categorydef_id=categorydef.id 
				WHERE ridcatid.report_id='$rid' AND categorydef.type='$type'  
				AND (ridcatid.subject_id='$bid' OR ridcatid.subject_id='%');");

	/*
	  AND ridcatid.subject_id!='summary' AND
	  ridcatid.subject_id!='wrapper' AND ridcatid.subject_id!='profile' 
	  ORDER BY ridcatid.subject_id;
	*/

   	$catdefs=array();
	while($catdef=mysql_fetch_array($d_categorydef,MYSQL_ASSOC)){
	   	$catdefs[]=$catdef;
	   	}

	return $catdefs;
	}


/**
 *
 * Returns the appropriate ratingname for a bid from the given reportdef
 * 
 *
 */
function get_report_ratingname($reportdef,$bid='%'){
	$linkedname='';
	$pairs=explode(';',$reportdef['report']['rating_name']);
	/* 
	 *  Just in case: for backward compatibility with existing reports. 
	 */
	if(!is_array($pairs) or (is_array($pairs) and sizeof($pairs)==1)){
		$pairs=array('0'=>'%:'.$reportdef['report']['rating_name']);
		}
	/*
	 * Identify the ratingname which applies to this subject.
	 */
	for($c=0;$c<sizeof($pairs);$c++){
		list($ratingbid, $ratingname)=split(':',$pairs[$c]);
		if(($ratingbid=='%' and !isset($Categories['ratingname'])) or ($ratingbid==$bid)){
			$linkedname=$ratingname;
			}
		}
	return $linkedname;
	}



/**
 *
 * Returns one array containing the catdefs for all summaries for this
 * report.
 *
 */
function fetchReportSummaries($rid){
	$d_categorydef=mysql_query("SELECT categorydef.id,
				categorydef.name, categorydef.type, categorydef.subtype, categorydef.subject_id,
				categorydef.rating, categorydef.comment FROM categorydef LEFT
				JOIN ridcatid ON ridcatid.categorydef_id=categorydef.id 
				WHERE ridcatid.report_id='$rid' AND (categorydef.type='sig' OR categorydef.type='com') AND
				ridcatid.subject_id='summary' ORDER BY
				categorydef.type, categorydef.rating;");
   	$catdefs=array();
	while($catdef=mysql_fetch_array($d_categorydef,MYSQL_ASSOC)){
	   	$catdefs[]=$catdef;
	   	}
	return $catdefs;
	}



/**
 *	Simply checks to see if any report entries for a bid/pid combination 
 *  have been made for this sid in this report. The number of report entries 
 *  are returned.
 *
 */
function checkReportEntry($rid,$sid,$bid,$pid){
	$d_reportentry=mysql_query("SELECT entryn
					FROM reportentry WHERE report_id='$rid' AND
					student_id='$sid' AND subject_id='$bid' AND
					component_id='$pid';");
	return mysql_numrows($d_reportentry);
	}


/**
 *
 *		Retrieves all report entries for one student in one subject
 *		All report info is pre-fetched in $reportdef['report'].
 *
 */
function fetchReportEntry($reportdef,$sid,$bid,$pid){

	$Comments=array();
	//$Comments['Comment']=array();
	$rid=$reportdef['report']['id'];
   	$d_reportentry=mysql_query("SELECT * FROM reportentry WHERE
		  report_id='$rid' AND student_id='$sid' AND subject_id='$bid'
		  AND component_id='$pid' ORDER BY entryn;");
	$subcomments=(array)get_report_categories($rid,$bid,$pid,'sub');
	$subcomments_no=sizeof($subcomments);

	while($entry=mysql_fetch_array($d_reportentry)){

	   $Comment=array();
	   $Comment['id_db']=$entry['entryn'];
	   if($reportdef['report']['addcomment']=='yes' or $bid=='summary'){
		   $enttid=$entry['teacher_id'];
		   $teachername=get_teachername($enttid);
		   unset($comment_html);
		   if($subcomments_no>0){
			   /* Each subcomment gets embedded as a html fragment in the xml 
				* for display in xslt using copy-of (and not select!).
				*/
			   $comment_html=array();
			   $comments=split(':::',$entry['comment']);
			   for($c=0;$c<$subcomments_no;$c++){
				   $comment_div=array();
				   $comment_div['label']=''.$subcomments[$c]['name'];
				   $comment_div['p']=''.$comments[$c];
				   $comment_html['div'][]=$comment_div;
				   }

			   }
		   //  elseif($reportdef['report']['date']<'2009-08-11'){
					  elseif($reportdef['report']['date']<'2009-04-01'){
			   /* For backward compatibility with old xslt templates. */
			   $comment_html=$entry['comment'];
			   }
		   else{
			   $comment_html['div'][]=$entry['comment'];
			   }

		   $Comment['Teacher']=nullCorrect(array('id_db'=>''.$enttid, 
												 'value'=>''.$teachername));
		   $Comment['Text']=nullCorrect(array('value'=>$comment_html,
											  'value_db'=>''.$entry['comment']));
		   }

	   /* These are the check box ratings. */
	   if($reportdef['report']['addcategory']=='yes' and $bid!='summary'){
		   $catdefs=get_report_categories($rid,$bid,$pid);
		   $pairs=explode(';',$entry['category']);
		   for($c=0;$c<(sizeof($pairs)-1);$c++){
			   list($catid, $rank)=split(':',$pairs[$c]);
			   $entry['ratings'][$catid]=$rank;
			   }

		   $Categories=array();

		   $Categories['ratingname']=get_report_ratingname($reportdef,$bid);
		   $ratings=$reportdef['ratings'][$Categories['ratingname']];
		   
		   for($c=0;$c<sizeof($catdefs);$c++){
		   		$Category=array();
	   			$catid=$catdefs[$c]['id'];
   				$catname=$catdefs[$c]['name'];
			  	$Category=array('label'=>$catname,'id_db'=>$catid,'value'=>'');
				if(isset($entry['ratings'][$catid])){
					reset($ratings);
					while(list($value,$descriptor)=each($ratings)){
				   		if($entry['ratings'][$catid]==$value){$Category['value']=$value;}
						}
				   	}
				/* Only pass catgories which have had a value set. */
			   	if($Category['value']!='' or $Category['value']=='0'){
					$Categories['Category'][]=nullCorrect($Category);
					}
		   		}
		   $Comment['Categories']=nullCorrect($Categories);
		   }

	   $Comments['Comment'][]=nullCorrect($Comment);
	   }

	return $Comments;
	}
?>
