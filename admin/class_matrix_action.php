<?php 
/**		  		       			class_matrix_action.php
 */

$action='class_matrix.php';

include('scripts/sub_action.php');

$crid=$respons[$r]['course_id'];
$result=array();
$error=array();

if($sub=='Update'){
	$d_subject=mysql_query("SELECT DISTINCT subject_id FROM cridbid
				WHERE course_id='$crid' ORDER BY subject_id");
	$d_classes=mysql_query("SELECT DISTINCT stage FROM classes WHERE
							course_id='$crid'");

	$bids=array();
   	while($subject=mysql_fetch_array($d_subject,MYSQL_ASSOC)){
   		$bids[]=$subject{'subject_id'};
		}
	$stages=array();
	while($stage=mysql_fetch_array($d_classes,MYSQL_ASSOC)){
   		$stages[]=$stage['stage'];
		}
	for($c=0; $c<sizeof($bids); $c++){
  		$bid=$bids[$c];
		for($c2=0; $c2<sizeof($stages); $c2++){
	  		$stage=$stages[$c2];
			$ing=$bid.$stage.'g';
			$inm=$bid.$stage.'m';
			if(isset($_POST{$ing})){
				$many=$_POST{$inm}; 
				$generate=$_POST{$ing};

				if($many!='' and $generate!='none'){
				$d_classes=mysql_query("SELECT * FROM classes WHERE
						subject_id='$bid' AND stage='$stage' AND course_id='$crid'");
				if(mysql_fetch_array($d_classes,MYSQL_ASSOC)){
					if(mysql_query("UPDATE classes SET many='$many',
						generate='$generate' WHERE stage='$stage' AND
						subject_id='$bid' AND course_id='$crid'"))
						{}
					else{$error[]=mysql_error();}
					}
				else {
					if(mysql_query("INSERT INTO classes (many, generate,
						yeargroup_id, course_id, subject_id) VALUES ('$many',
						'$generate', '$stage', '$crid', '$bid')"))
						{}
					else{$error[]=mysql_error();}
					}
				}
				else {
 					if(mysql_query("DELETE FROM classes WHERE
						stage='$stage' AND  course_id='$crid' AND
						subject_id='$bid' LIMIT 1"))
						{}
					else{$error[]=mysql_error();}
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
	  <?php print_string('generateclassstructurequestion',$book);?>
	  <form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host; ?>">
		<input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
		<input type="hidden" name="cancel" value="<?php print $choice;?>">
<?php check_yesno();?>
	  </form>
	</fieldset>
  </div>
<?php
	exit;
	}
elseif($sub=='Submit' and $_POST['answer']=='yes'){
	$result[]=get_string('newclassstructure',$book);

/********* Should delete all from the cidsid and class tables before attempting this?
*/
	//	mysql_query("TRUNCATE TABLE cidsid");
	//	mysql_query("TRUNCATE TABLE tidcid");
	mysql_query("DELETE FROM class WHERE course_id='$crid'");
/*********/

		$d_classes=mysql_query("SELECT * FROM classes WHERE
										course_id='$crid' ORDER BY
										subject_id, stage");   	
		while($classes=mysql_fetch_array($d_classes,MYSQL_ASSOC)){
   				$bid=$classes['subject_id'];
				$stage=$classes['stage'];

				if($classes['generate']=='forms' & $classes['many']>0){
					$d_form=mysql_query("SELECT id FROM form WHERE yeargroup_id='$yid'");
					while($form=mysql_fetch_array($d_form,MYSQL_ASSOC)){
						$fid=$form['id'];
						$newcid=$bid.$fid;
						if(mysql_query("INSERT INTO class (id,
							subject_id, course_id, yeargroup_id) VALUES ('$newcid', '$bid',
								'$crid', '$yid')")){$result[]=' '.$newcid.' ';}
						else {$error[]='Already exists '.$newcid.' ';}

						$d_sids=mysql_query("SELECT id FROM student WHERE form_id='$fid'");
						while($sids=mysql_fetch_array($d_sids, MYSQL_ASSOC)){
							$sid=$sids{'id'};
							if(mysql_query("INSERT INTO cidsid
								(class_id, student_id) VALUES ('$newcid',
									'$sid')")) { } else {}
							}
						}
					}
				elseif($classes['generate']=='sets' & $classes['many']>0){
					if($classes['naming']==''){$name=$bid.$stage.'/';}
					else{$name=$bid.$classes['naming'];}
					for($c=1;$c<=$classes['many'];$c++){
						$newcid=$name.$c;
						if(mysql_query("INSERT INTO class (id,
							subject_id, course_id, stage) VALUES ('$newcid', '$bid',
								'$crid', '$stage')")){$result[]=' '.$newcid.' ';} 
						else{$error[]='Already exists '.$newcid.' ';}
						}
					}
		}
	}
elseif($sub=='Submit' and $_POST['answer']=='no'){
	$result[]=get_string('noactiontaken',$book);
	}
include('scripts/results.php');
include('scripts/redirect.php');
?>
