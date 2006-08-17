<?php 
/**    								year_end_action2.php
 */

$action='';
$choice='';

include('scripts/sub_action.php');

	/**/
	$years=array();
	$c=0;
	$d_yeargroup=mysql_query("SELECT id, ncyear, section_id, name FROM
							yeargroup ORDER BY ncyear ASC");
	while($years[]=mysql_fetch_array($d_yeargroup,MYSQL_ASSOC)){
		$yid=$years[$c]['id'];
		$d_form=mysql_query("SELECT id FROM form WHERE
							yeargroup_id='$yid' ORDER BY id DESC");
		$years[$c]['fids']=array();
		while($form=mysql_fetch_array($d_form,MYSQL_ASSOC)){
			$years[$c]['fids'][]=$form['id'];
			}
		$c++;
		}

	for($c=(sizeof($years)-2);$c>-1;$c--){
		$yid=$years[$c]['id'];
		$nextpostyid=$_POST["$yid"];
		if($nextpostyid=='1000'){
			$nextyid=$yid.'-alumni-'.date('Y').'-'.date('m');
			}
		else{
			$nextyid=$nextpostyid;
			}
		mysql_query("UPDATE community SET name='$nextyid' WHERE
				type='year' AND name='$yid';");

		while(list($index,$fid)=each($years[$c]['fids'])){
			if($nextpostyid!='1000'){
				$nextfid=$years[$c+1]['fids'][$index];
				}
			else{
				$nextfid=$fid.'-alumni-'.date('Y').'-'.date('m');
				}
			if($nextfid==''){$nextfid=$fid.'-'.date('Y').'-'.date('m');}
			mysql_query("UPDATE community SET name='$nextfid' WHERE
								type='form' AND name='$fid';");
			mysql_query("UPDATE student SET form_id='$nextfid' WHERE form_id='$fid';");
			//$result[]='Promoted form '.$fid.' to '.$nextfid;
			}

		mysql_query("UPDATE student SET yeargroup_id='$nextyid' WHERE yeargroup_id='$yid';");
		//$result[]='Promoted year '.$yid.' to '.$nextyid;
		}

	/**/
	$courses=array();
	$c=0;
	$d_course=mysql_query("SELECT id, sequence, section_id, name FROM
							course ORDER BY sequence ASC");
	while($courses[]=mysql_fetch_array($d_course,MYSQL_ASSOC)){
		$crid=$courses[$c]['id'];
		/*currently sequence of the stages for a course depends solely
			upon their alphanumeric order - means they require a numeric ending*/
		$d_stage=mysql_query("SELECT stage FROM cohort WHERE
				course_id='$crid' AND status='C' ORDER BY stage DESC");
		$courses[$c]['stages']=array();
		while($stage=mysql_fetch_array($d_stage,MYSQL_ASSOC)){
			$courses[$c]['stages'][]=$stage['stage'];
			}
		$c++;
		}

	for($c=(sizeof($courses)-2);$c>-1;$c--){
		$crid=$courses[$c]['id'];
		$nextpostcrid=$_POST["$crid"];

		$season='S';/*currently restricted to a single season value*/
		$yearnow=getCurriculumYear($crid);
		$yeargone=$yearnow-1;
		$stages=$courses[$c]['stages'];
		for($c2=0;$c2<sizeof($stages);$c2++){
			$stage=$stages[$c2];
			$cohort=array('course_id'=>$crid,'stage'=>$stage,'year'=>$yeargone);
			$cohidgone=updateCohort($cohort);
			$cohort=array('course_id'=>$crid,'stage'=>$stage,'year'=>$yearnow);
			$newcohid=updateCohort($cohort);
			$d_cohidcomid=mysql_query("SELECT community_id FROM cohidcomid 
											WHERE cohort_id='$cohidgone'");
			$comids=array();

			if($c2==0 and $nextpostcrid!='1000'){
				/*last stage of course is being promoted a course*/
				$d_cohort=mysql_query("SELECT id FROM cohort WHERE
						course_id='$nextpostcrid' AND year='C' ORDER BY stage ASC");
				$nextcohid=mysql_result($d_cohort,0,0);
				}
			elseif($nextpostcrid!='1000'){
				/*just move up to next stage*/
				$nextcohid=$newcohid;
				}
			else{
				/*last stage is graduating*/
				$nextcohid='';
				}

			while($cohidcomid=mysql_fetch_array($d_cohidcomid,MYSQL_ASSOC)){
				$comid=$cohidcomid['community_id'];
				$result[]=$crid.$stage.$yeargone.' '.$comid.' '.$nextcohid;
				if($nextcohid!=''){
					mysql_query("INSERT INTO cohidcomid SET
								cohort_id='$nextcohid', community_id='$comid'");
					}
				}
			}
		}

	mysql_query("DELETE FROM cidsid");
	mysql_query("DELETE FROM score");
	mysql_query("DELETE FROM mark");
	mysql_query("DELETE FROM midcid");
	mysql_query("DELETE FROM eidmid");

	include('scripts/results.php');
//	include('scripts/redirect.php');
?>
