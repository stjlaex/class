<?php
/**                    new_report_action.php
 */

$action='new_report.php';
$rcrid=$respons[$r]['course_id'];

include('scripts/sub_action.php');

if($sub=='Submit'){
		$title=$_POST['title'];
		$comment=$_POST['comment'];
		$compstatus=$_POST['componentstatus'];
		$stage=$_POST['stage'];
		$reptype=$_POST['reptype0'];
		$addcategory=$_POST['addcategory0'];
		$commentcomp=$_POST['commentcomp0'];
		if(isset($_POST['commentlength'])){$commentlength=$_POST['commentlength'];}
		else{$commentlength='0';}
		if(isset($_POST['template'])){$style=$_POST['template'];}else{$style='';}
		if(isset($_POST['template'])){$transform=$_POST['template'];}else{$template='';}
		if(isset($_POST['catdefids'])){$catdefids=(array)$_POST['catdefids'];}
		else{$catdefids=array();}
		$date=$_POST['date0'];
		$deadline=$_POST['date1'];

		if(mysql_query("INSERT INTO report (title, comment, course_id,
				stage, component_status,
				date, deadline, addcomment, commentlength,
					commentcomp, addcategory, style, transform) VALUES
				('$title', '$comment', '$rcrid', '$stage', '$compstatus',
				'$date', '$deadline', '$reptype', '$commentlength',
					'$commentcomp', '$addcategory', '$style', '$transform');"))	
				{$result[]='Successfully created new report.';}
		else {$error[]='Failed to create report!';
				$error[]=mysql_error(); 
				include('scripts/results.php'); 
				exit;
				}

/*		generate mark columns for the new report*/
		$rid=mysql_insert_id();
		/*the rid will be stored in the midlist for each new mark*/

/*		make a list of subjects that will need distinct new marks*/
		$bids=array();
		$d_cridbid=mysql_query("SELECT DISTINCT subject_id FROM cridbid
				WHERE course_id LIKE '$rcrid' ORDER BY subject_id");
		while($bid=mysql_fetch_array($d_cridbid,MYSQL_NUM)){$bids[]=$bid[0];}

/*		generate a mark for each crid and bid combination*/
   	   	while(list($index,$bid)=each($bids)){
			$pids=array();
   			if($compstatus=='A'){$compstatus='%';}
   			$d_component=mysql_query("SELECT DISTINCT id FROM component
					WHERE course_id='$rcrid' AND subject_id='$bid'
						AND status LIKE '$compstatus'");
   			while($pid=mysql_fetch_array($d_component,MYSQL_NUM)){$pids[]=$pid[0];}

			if(sizeof($pids)==0){$pids[0]='';}
		   	while(list($index,$pid)=each($pids)){
				/*if there is no component for this subject or componenets are not
					requested then $pid is blank*/
				if(mysql_query("INSERT INTO mark 
				(entrydate, marktype, topic, comment, author,
				 def_name, assessment, midlist, component_id) 
					VALUES ('$date', 'report', '$title', 
				 'complete by $deadline', '$tid', '', 'no', '$rid', '$pid')"))
				{$result[]='Created report for '.$bid.' - '.$pid;}
				else{$error[]='Failed mark may already exist!'.mysql_error();}
				$mid=mysql_insert_id();

/*		        entry in midcid for new mark and classes with crid and bid*/
				$d_class=mysql_query("SELECT id FROM class WHERE
						course_id LIKE '$rcrid' AND subject_id LIKE
						'$bid' AND stage LIKE '$stage' ORDER BY subject_id");
				while($d_cid=mysql_fetch_array($d_class,MYSQL_NUM)){
						$cid=$d_cid[0];
						mysql_query("INSERT INTO midcid (mark_id,
							class_id) VALUES ('$mid', '$cid')");
						}
				}
			}

/*      entry in rideid to link new report with chosen assessments*/
		$eids=(array)$_POST{'eids'};
		foreach($eids as $eid){ 
  			if(mysql_query("INSERT INTO rideid 
		     (report_id, assessment_id) 
				VALUES ('$rid', '$eid')")){}
			else{$error[]='Failed to link to assessment!'.mysql_error();}
			}

		if($addcategory=='yes'){
			$d_catdef=mysql_query("SELECT id FROM categorydef WHERE
						type='rep' AND (course_id='%' OR course_id
							LIKE '$rcrid')");
			while($d_catid=mysql_fetch_array($d_catdef,MYSQL_NUM)){
				$catid=$d_catid[0];
				mysql_query("INSERT INTO ridcatid (report_id,
							categorydef_id, subject_id) VALUES
							('$rid', '$catid', '%')");
				}
			}

		while(list($index,$catid)=each($catdefids)){
			if($catid!='-100'){
				mysql_query("INSERT INTO ridcatid (report_id,
							categorydef_id, subject_id) VALUES
							('$rid', '$catid', 'summary')");
				}
			}
		}

include('scripts/results.php');
include('scripts/redirect.php');
?>