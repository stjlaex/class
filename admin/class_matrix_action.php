<?php 
/**		  		       			class_matrix_action.php
 */

$action='class_matrix.php';

include('scripts/sub_action.php');

list($crid,$bid,$error)=checkCurrentRespon($r,$respons,'course');
if($error!=''){include('scripts/results.php');exit;}

if($sub=='Update'){
	$d_subject=mysql_query("SELECT DISTINCT subject_id FROM cridbid
				WHERE course_id='$crid' ORDER BY subject_id");
	$d_classes=mysql_query("SELECT DISTINCT stage FROM classes WHERE
							course_id='$crid'");
	$bids=array();
   	while($subject=mysql_fetch_array($d_subject,MYSQL_ASSOC)){
   		$bids[]=$subject['subject_id'];
		}
	$stages=array();
	while($stage=mysql_fetch_array($d_classes,MYSQL_ASSOC)){
   		$stages[]=$stage['stage'];
		}
	for($c=0;$c<sizeof($bids);$c++){
  		$bid=$bids[$c];
		for($c2=0;$c2<sizeof($stages);$c2++){
	  		$stage=$stages[$c2];
			$ing=$bid.$stage.'g';
			$inm=$bid.$stage.'m';
			if(isset($_POST[$ing])){
				$many=$_POST[$inm]; 
				$generate=$_POST[$ing];

				if($many!='' and $generate!='none'){
					$d_classes=mysql_query("SELECT * FROM classes WHERE
						subject_id='$bid' AND stage='$stage' AND course_id='$crid'");
					if(mysql_fetch_array($d_classes,MYSQL_ASSOC)){
						mysql_query("UPDATE classes SET many='$many',
						generate='$generate' WHERE stage='$stage' AND
						subject_id='$bid' AND course_id='$crid'");
						}
					else{
						mysql_query("INSERT INTO classes (many, generate,
						yeargroup_id, course_id, subject_id) VALUES ('$many',
						'$generate', '$stage', '$crid', '$bid')");
						}
					}
				else{
 					mysql_query("DELETE FROM classes WHERE
						stage='$stage' AND  course_id='$crid' AND
						subject_id='$bid' LIMIT 1");
					}
				}
			}
		}
	}

elseif($sub=='Generate'){
	$action='class_matrix_action.php';
	three_buttonmenu();
?>
  <div class="content">
	<fieldset class="center">
		<legend><?php print_string('confirm',$book);?></legend>
	  <?php print_string('generateclassstructurequestion',$book);?>
	  <form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host; ?>">
		<input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
		<input type="hidden" name="cancel" value="<?php print $choice;?>">

			  <div class="right">
				<?php include('scripts/check_yesno.php');?>
			  </div>

	  </form>
	</fieldset>
  </div>
<?php
	exit;
	}

elseif($sub=='Submit'){

	include('scripts/answer_action.php');

	$result[]=get_string('newclassstructure',$book);

	mysql_query("DELETE cidsid.* FROM cidsid, class WHERE
		class.id=cidsid.class_id AND class.course_id='$crid'");
	mysql_query("DELETE tidcid.* FROM tidcid, class WHERE 
		class.id=tidcid.class_id AND class.course_id='$crid'");
	mysql_query("DELETE midcid.* FROM midcid, class WHERE 
		class.id=midcid.class_id AND class.course_id='$crid'");
	mysql_query("DELETE FROM class WHERE course_id='$crid'");

	$d_classes=mysql_query("SELECT * FROM classes WHERE
										course_id='$crid' ORDER BY
										subject_id, stage");   	

	/*keeping things simple by fixing season and year to a single value*/
	/*to sophisticate in the future*/
	$currentseason='S';
	$currentyear=get_curriculumyear($crid);
	while($classes=mysql_fetch_array($d_classes,MYSQL_ASSOC)){
		$bid=$classes['subject_id'];
		$stage=$classes['stage'];
		$d_cohidcomid=mysql_query("SELECT cohidcomid.community_id FROM
			cohidcomid JOIN cohort ON cohidcomid.cohort_id=cohort.id 
			WHERE cohort.course_id='$crid' AND cohort.year='$currentyear'
			AND cohort.season='$currentseason' AND cohort.stage='$stage'");
		$communities=array();
		$name=array();
		$name_counter='';
		while($cohidcomid=mysql_fetch_array($d_cohidcomid,MYSQL_ASSOC)){
			$comid=$cohidcomid['community_id'];
			$d_community=mysql_query("SELECT * FROM community WHERE id='$comid'");
			$communities[$comid]=mysql_fetch_array($d_community,MYSQL_ASSOC);
			if($communities[$comid]['type']=='year'){$yid=$communities[$comid]['name'];}
			}

		if($classes['naming']=='' and $classes['generate']=='forms'){
			$name['root']=$bid;
			$name['stem']='-';
			$name['branch']='';
			}
		elseif($classes['naming']=='' and $classes['generate']=='sets'){
			$name['root']=$bid;
			$name['stem']=$stage;
			$name['branch']='/';
			}
		else{
			list($name['root'],$name['stem'],$name['branch'],$name_counter)=split(';',$classes['naming'],4);
			while(list($index,$namecheck)=each($name)){
				if($namecheck=='subject'){$name["$index"]=$bid;}
				if($namecheck=='stage'){$name["$index"]=$stage;}
				if($namecheck=='course'){$name["$index"]=$crid;}
				if($namecheck=='year'){$name["$index"]=$yid;}
				}
			}

		$class_counters=array();
		if($classes['generate']=='forms' & $classes['many']>0){
			while(list($comid,$community)=each($communities)){
				if($community['type']=='year'){
					$yid=$community['name'];
					$d_form=mysql_query("SELECT id FROM form
								WHERE yeargroup_id='$yid'");
					}
				while($form=mysql_fetch_array($d_form,MYSQL_ASSOC)){
					$class_counters[]=$form['id'];
					}
				}
			}
		elseif($classes['many']>0){
			if($name_counter!=''){
				for($c=0;$c<$classes['many'];$c++){
					$class_counters[]=$name_counter[$c];
					}
				}
			else{
				$class_counters=range('1',$classes['many']);
				}
			}
		else{
			$class_counters=array();
			}

		foreach($class_counters as $counter){
			$newcid=$name['root'].$name['stem'].$name['branch'].$counter;
			mysql_query("INSERT INTO class (id,
							subject_id, course_id, stage) VALUES ('$newcid', '$bid',
								'$crid', '$stage')");
			if($classes['generate']=='forms'){
   				$d_sids=mysql_query("SELECT id FROM student WHERE form_id='$counter'");
				while($sids=mysql_fetch_array($d_sids, MYSQL_ASSOC)){
					$sid=$sids['id'];
					mysql_query("INSERT INTO cidsid
								(class_id, student_id) VALUES ('$newcid','$sid')");
					}
				}
			}
		}
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
