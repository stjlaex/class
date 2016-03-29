<?php	
/**												lib/fetch_sen.php
 *
 *	
 *	@package	Classis
 *	@author		stj@laex.org
 *	@copyright	S T Johnson 2004-2016
 *	@version	
 *	@since		
 */	


/**
 * SEN: Special Educational Needs
 *
 *
 * @param string $sid
 * @return array
 */
function fetchSEN($sid=-1,$senhid=-1){
	$SEN=array();

	/* Fetch the most recent senhistory unles a senhid is set and fetch that. */
	if($senhid>0){
		$d_s=mysql_query("SELECT * FROM senhistory WHERE id='$senhid';");
		}
	else{
		$d_s=mysql_query("SELECT * FROM senhistory WHERE 
	   								student_id='$sid' ORDER BY startdate DESC LIMIT 1;");
		}

	$sen=mysql_fetch_array($d_s,MYSQL_ASSOC);

	$senhid=$sen['id'];
	$SEN['id_db']=$senhid;
	$SEN['SENprovision']=array('label' => 'provision', 
								   'table_db' => 'senhistory', 
								   'field_db' => 'senprovision',
								   'type_db' => 'enum', 
								   'value' => ''.$sen['senprovision']);
	$SEN['SpecialProvisionIndicator']=array('label' => 'specialprovisionindicator', 
												'type_db' => 'enum', 
												'value' => '');
	$SEN['StartDate']=array('label' => 'startdate', 
								'table_db' => 'senhistory', 
								'field_db' => 'startdate',
								'type_db' => 'date', 
								'value' => ''.$sen['startdate']);
	$SEN['NextReviewDate']=array('label' => 'nextreviewdate', 
									 'table_db' => 'senhistory', 
									 'field_db' => 'reviewdate',
									 'type_db' => 'date', 
									 'value' => ''.$sen['reviewdate']);
	$SEN['AssessmentDate']=array('label' => 'date', 
									 'table_db' => 'senhistory', 
									 'field_db' => 'assessmentdate',
									 'type_db' => 'date', 
									 'value' => ''.$sen['assessmentdate']);


	$d_s=mysql_query("SELECT * FROM sencurriculum
							   WHERE senhistory_id='$senhid' ORDER BY subject_id;");
	$Curriculum=array();
	while($sencurriculum=mysql_fetch_array($d_s,MYSQL_ASSOC)){
		$Subject=array();
		$bid=$sencurriculum['subject_id'];
		$subjectname=get_subjectname($bid);
		$Subject['Subject']=array('label' => 'subject', 
								  'table_db' => 'sencurriculum', 
								  'field_db' => 'subject_id', 
								  'type_db' => 'varchar(10)', 
								  'value_db' => ''.$bid,
								  'value' => ''.$subjectname);
		$Subject['Modification']=array('label' => 'sencurriculum', 
									   'table_db' => 'sencurriculum', 
									   'field_db' => 'curriculum',
									   'type_db' => 'enum', 
									   'value' => ''.$sencurriculum['curriculum']);
		$catid=$sencurriculum['categorydef_id'];
		if($catid!=0){
			$d_categorydef=mysql_query("SELECT name FROM categorydef WHERE id='$catid'");
			$catname=mysql_result($d_categorydef,0);
			}
		else{
			$catname='';
			}
		$Subject['ExtraSupport']=array('label' => 'extrasupport',
									   'table_db' => 'sencurriculum',
									   'field_db' => 'categorydef_id',
									   'type_db'=> 'int',
									   'value_db' => ''.$catid,
									   'value' => ''.$catname);
		$Subject['Strengths']=array('label' => 'strengths', 
									'table_db' => 'sencurriculum', 
									'field_db' => 'comments', 
									'type_db' => 'text', 
									'value' => ''.$sencurriculum['comments']);
		$Subject['Weaknesses']=array('label' => 'weaknesses', 
									 'table_db' => 'sencurriculum', 
									 'field_db' => 'targets', 
									 'type_db' => 'text', 
									 'value' => ''.$sencurriculum['targets']);
		$Subject['Strategies']=array('label' => 'strategies',
									 'table_db' => 'sencurriculum', 
									 'field_db' => 'outcome', 
									 'type_db' => 'text', 
									 'value' => ''.$sencurriculum['outcome']);
		$Subject['Targets']=array('label' => 'targets',
								  'table_db' => 'sencurriculum', 
								  'field_db' => 'extra', 
								  'type_db' => 'text', 
								  'value' => ''.$sencurriculum['extra']);
		$Curriculum[]=$Subject;
		}

	$SEN['Curriculum']=$Curriculum;

	$d_s=mysql_query("SELECT * FROM sentype WHERE student_id='$sid' AND senassessment='I' ORDER BY entryn;");
	$SENtypes['SENtype']=array();
	while($sentype=mysql_fetch_array($d_s,MYSQL_ASSOC)){
		$SENtypes['SENtype'][]=fetchSENtype($sentype);
		}
	$SEN['SENinternaltypes']=$SENtypes;

	$d_s=mysql_query("SELECT * FROM sentype WHERE student_id='$sid' AND senassessment='E' ORDER BY entryn;");
	$SENtypes['SENtype']=array();
	while($sentype=mysql_fetch_array($d_s,MYSQL_ASSOC)){
		$SENtypes['SENtype'][]=fetchSENtype($sentype);
		}
	$SEN['SENtypes']=$SENtypes;

	return $SEN;
	}


/**
 *
 *
 * @param array $sentype
 * @return array
 */
function fetchSENtype($sentype=array('student_id'=>'-1','senranking'=>'','sentype'=>'','senassessment'=>'')){
	$SENtype=array();
	$SENtype['SENtype']=array('label' => 'sentype', 
							  'table_db' => 'sentype', 
							  'field_db' => 'sentype', 
							  'type_db' => 'enum', 
							  'value' => ''.$sentype['sentype']);
	$SENtype['SENtypeRank']=array('label' => 'ranking',
								  'table_db' => 'sentype', 
								  'field_db' => 'senranking', 
								  'type_db' => 'enum', 
								  'value' => ''.$sentype['senranking']);
	$SENtype['SENtypeAssessment']=array('label' => 'senassessment', 
										'table_db' => 'sentype', 
										'field_db' => 'senassessment', 
										'type_db' => 'enum', 
										'value' => ''.$sentype['senassessment']);
	return $SENtype;
	}




/**
 *
 * Returns an array of all senhistory entries for a sid.
 *
 *
 * @param string $sid
 * @return array
 */
function list_student_senhistories($sid){

	$senhistories=array();
	if($sid!=''){
		$d_s=mysql_query("SELECT id, senprovision, startdate, reviewdate, assessmentdate FROM senhistory
													WHERE student_id='$sid' ORDER BY startdate ASC;");
		while($s=mysql_fetch_array($d_s,MYSQL_ASSOC)){
			$senhistories[]=$s;
			}
		}

	return $senhistories;
	}




/**
 *
 *
 */
function display_student_sentype($sid){
	$d_s=mysql_query("SELECT sentype, senassessment FROM sentype WHERE student_id='$sid' ORDER BY senassessment, entryn;");

	$display='';
	while($sentype=mysql_fetch_array($d_s,MYSQL_ASSOC)){
		if($display!=''){$display.=' / ';}
		if($sentype['senassessment']=='I'){
			$assessment='internal';
			}
		else{
			$assessment='';
			}
		$display.=get_string(displayEnum($sentype['sentype'], 'sentype'.$assessment),'seneeds');
		}

	return $display;
	}



/**
 *
 * Sets the senstatus to specified value for a sid.  Creates new blank
 * senhistory entry if the status is Y (and can be used to just do
 * that even when no change in status is being requested).
 *
 *
 * @param integer $sid
 * @param string $senstatus
 *
 * @return integer $senhid
 *
 */
function set_student_senstatus($sid,$status='Y'){

	$todate=date('Y')."-".date('n')."-".date('j');

	if($status=='N'){

		/* Remove the SEN status. */
		mysql_query("UPDATE info SET sen='N' WHERE student_id='$sid'");
		$senhid=-1;

		}
	elseif($status=='Y'){

		/* If not already done add to SEN register. */
		mysql_query("UPDATE info SET sen='Y' WHERE student_id='$sid'");
		
		$d_s=mysql_query("SELECT id FROM senhistory WHERE startdate='$todate' AND student_id='$sid'");

		if(mysql_num_rows($d_s)>0){
			$senhid=mysql_result();
			}
		else{
			/* Set up first blank record for the profile. */
			mysql_query("INSERT INTO senhistory SET startdate='$todate', student_id='$sid'");
			$senhid=mysql_insert_id();

			/* Creates a blank entry for general comments applicable to all subjects. */
			mysql_query("INSERT INTO sencurriculum SET
					senhistory_id='$senhid', subject_id='General'");
			}
		}

	return $senhid;
	}
?>
