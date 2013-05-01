<?php 
/** 									column_edit_action.php
 */

$action='class_view.php';

$mid=$_POST['mid'];
$oldcids=$_POST['newcids'];
$newcids=$_POST['selcids'];
$topic=clean_text($_POST['topic']);
$comment=clean_text($_POST['comment']);
if(isset($_POST['newpid'])){$newpid=$_POST['newpid'];}else{$newpid='';}
if(isset($_POST['neweid'])){$neweid=$_POST['neweid'];}else{$neweid='';}

include('scripts/sub_action.php');

	if($sub=='Submit'){
		$entrydate=$_POST['date0'];
		mysql_query("UPDATE mark SET entrydate='$entrydate', topic='$topic', 
						comment='$comment', component_id='$newpid' WHERE id='$mid'");

		foreach($oldcids as $oldcid){
			/*check for those cids deselected and delete from midcid*/
			if(!in_array($oldcid,$newcids)){
				mysql_query("DELETE FROM midcid WHERE mark_id='$mid' AND class_id='$oldcid' LIMIT 1;");
				}
			}

		$currentcids=list_mark_cids($mid);
		foreach($newcids as $newcid){
			/*check for those cids newly selected and add to midcid*/
			if(!in_array($newcid,$currentcids)){
				mysql_query("INSERT INTO midcid SET mark_id='$mid', class_id='$newcid';");
				}
			}


   		list($eid,$eidbid,$eidpid)=get_mark_assessment($mid);

		/* TODO: this is a temporary hack.... */
		if($neweid!='' and $eid==''){

			$mark=get_mark($mid);
   			$d_markdef=mysql_query("SELECT markdef.scoretype, markdef.grading_name FROM markdef
											JOIN mark ON markdef.name=mark.def_name WHERE mark.id='$mid';");
   			$scoretype=mysql_result($d_markdef,0,0);
   			$grading_name=mysql_result($d_markdef,0,1);
			mysql_query("INSERT INTO eidmid (assessment_id,mark_id) VALUES ('$neweid','$mid');");
			mysql_query("UPDATE mark SET assessment='yes' WHERE id='$mid';");
			$todate=date('Y-m-d');

	   		list($eid,$eidbid,$eidpid)=get_mark_assessment($mid);
			$d_s=mysql_query("SELECT * FROM score WHERE mark_id='$mid';");
			while($score=mysql_fetch_array($d_s,MYSQL_ASSOC)){
				$res=$score['value'];
				if($scoretype=='grade'){
					if(!isset($grading_grades)){
					  $d_g=mysql_query("SELECT grades FROM grading WHERE name='$grading_name';");
					  $grading_grades=mysql_result($d_g,0);
					  }
					$ingrade=$score['value'];
					$res=scoreToGrade($score['value'],$grading_grades);
					}
				elseif($scoretype=='percentage'){
					list($out,$res,$outrank)=scoreToPercent($score['value'],$score['total']);
					}
				$ass_score=array('result'=>$res,'value'=>$score['value'],'date'=>$todate,'comment'=>$score['comment']);
				update_assessment_score($neweid,$score['student_id'],$eidbid,$eidpid,$ass_score);
				}

			}

		}

	include('scripts/results.php');
	include('scripts/redirect.php');
?>