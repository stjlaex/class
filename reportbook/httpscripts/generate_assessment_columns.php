<?php
/*                    httpscripts/generate_assessment_columns.php
 */

require_once('common.php');

if(isset($_GET{'eid'})){$eid=$_GET{'eid'};}
elseif(isset($_POST{'eid'})){$eid=$_POST{'eid'};}

$AssDef=fetchAssessmentDefinition($eid);
$resq=$AssDef['ResultQualifier']['value'];
$crid=$AssDef['Course']['value'];
$method=$AssDef['Method']['value'];
$subject=$AssDef['Subject']['value'];
$compstatus=$AssDef['ComponentStatus']['value'];
$description=$AssDef['Description']['value'];
$stage=$AssDef['Stage']['value'];

/*			find the markdef_name based on the resultqualifier and method*/
   			$d_markdef = mysql_query("SELECT * FROM markdef JOIN method ON
						method.markdef_name=markdef.name WHERE
						method.resultqualifier='$resq' AND 
						(method.method='%' OR method.method='$method')
						AND (method.course_id='%' OR method.course_id='$crid')");
	   		$markdef=mysql_fetch_array($d_markdef,MYSQL_ASSOC);
			$markdef_name=$markdef['name'];
			mysql_free_result($d_markdef);
			$result[]=$markdef_name;
		   	if($markdef['scoretype']=='grade'){
		   		$gradingname=$markdef['grading_name'];		
		   		$d_grading = mysql_query("SELECT grades FROM 
								grading WHERE name='$gradingname'");
	   			$grading_grades=mysql_result($d_grading,0);
				mysql_free_result($d_grading);
				}

			$entrydate=date('Y').'-'.date('n').'-'.date('j');

   			/*this only until stage is implemented fully!!!*/
	   		if($stage!='%'){
			  //		   		$d_stage=mysql_query("SELECT stage FROM cohort WHERE
			  //			   			course_id='$crid' AND id='$stage'");
			  //		      	$stage=mysql_result($d_stage,0);
			  //			  	mysql_free_result($d_stage);
		   	   	}

/*			make a list of subjects that will need distinct new marks*/
			$bids=array();
			if($subject!='%'){$bids[]=$subject;}
			else{
				$d_cridbid=mysql_query("SELECT DISTINCT subject_id FROM cridbid
				WHERE course_id LIKE '$crid' ORDER BY subject_id");
				while ($bid=mysql_fetch_array($d_cridbid,MYSQL_NUM)){$bids[]=$bid[0];}
				mysql_free_result($d_cridbid);
				}

/*			generate a mark for each bid/pid combination*/
			while(list($index,$bid)=each($bids)){
				$pids=array();
				if($compstatus=='A'){$compstatus='%';}
				$d_component=mysql_query("SELECT DISTINCT id FROM component
					WHERE course_id='$crid' AND subject_id='$bid'
						AND status LIKE '$compstatus'");
				while($pid=mysql_fetch_array($d_component,MYSQL_NUM)){$pids[]=$pid[0];}
				mysql_free_result($d_component);

				if(sizeof($pids)==0){$pids[0]='';}
				while(list($index,$pid)=each($pids)){
				/*if there is no component for this subject or components are not
					requested then $pid is blank*/
					if(mysql_query("INSERT INTO mark 
					  (entrydate, marktype, topic, comment, author,
						 def_name, assessment, component_id) 
							VALUES ('$entrydate', 'score', '$description', 
						 '', 'ClaSS', '$markdef_name', 'yes', '$pid')"))
					{$result[]='Created mark for '.$bid.'-'.$pid;}
					else{$error[]='Failed on new mark for:'.$bid.$pid.' '.mysql_error();}
					$mid=mysql_insert_id();

/*			        entry in eidmid for this new mark*/
					mysql_query("INSERT INTO eidmid (assessment_id, 
						mark_id) VALUES ('$eid', '$mid')");

/*			   	    entry in midcid for new mark and classes with crid and bid*/
					if($bid==' ' or $bid=='G'){$bid='%';}
					$d_class=mysql_query("SELECT id FROM class WHERE
						course_id='$crid' AND subject_id LIKE '$bid' 
						AND stage LIKE '$stage'");
					while($d_cid=mysql_fetch_array($d_class,MYSQL_NUM)){
						$cid=$d_cid[0];
						mysql_query("INSERT INTO midcid (mark_id,
						class_id) VALUES ('$mid', '$cid')");
						}

					if(mysql_num_rows($d_class)>0){
   		  			/*now enter scores for this mark from matching
						eidsid entries*/
   					$d_eidsids=mysql_query("SELECT student_id,
				   		result, value FROM eidsid WHERE
				   		subject_id LIKE '$bid' AND component_id='$pid' 
						AND assessment_id='$eid'");
					$sids=array();
   					while($eidsid=mysql_fetch_array($d_eidsids,MYSQL_ASSOC)){
						$sids[$eidsid['student_id']]=$eidsid;
						/*ensures if their is a duplicate value only
						the most recent id is used*/
						}
					while(list($sid,$score)=each($sids)){
   						$out=$score['result'];
  						$value=$score['value'];
						if($markdef['scoretype']=='grade'){
							$score=gradeToScore($out,$grading_grades);		
							}
						else{$score='';}
						if(mysql_query("INSERT INTO score (student_id,
							mark_id, grade, value) VALUES ('$sid',
							'$mid', '$score', '$value')")){
						  /*transitional action neded in 0.7.x until eidsid and
   					score duplicate each other fully*/
						  //  			   			mysql_query("DELETE FROM eidsid WHERE
						  //		   					student_id='$sid' AND subject_id='$bid' AND
						  //								component_id='$pid' AND assessment_id='$eid'");
						}
						else{$error[]='Failed on '.$sid.'-'.$bid.'-'.$pid.'-'.$mid.' '.mysql_error();}
   						}
   					mysql_free_result($d_eidsids);
					}
   				mysql_free_result($d_class);


					}
				}


$returnXML=fetchAssessmentDefinition($eid);
$rootName='AssessmentDefinition';
require_once('commonreturn.php');
exit;
?>

















