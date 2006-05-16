<?php 
/**		  		       			class_matrix_action1.php
 */

$action='class_matrix.php';

include('scripts/sub_action.php');

$crid=$respons[$r]{'course_id'};
$result=array();
$error=array();

if($sub=='Update'){
	$d_subject=mysql_query("SELECT DISTINCT subject_id FROM cridbid
				WHERE course_id='$crid' ORDER BY subject_id");
	$d_yeargroup=mysql_query("SELECT id, name FROM yeargroup ORDER BY id");

	$c=0;
   	while($subject=mysql_fetch_array($d_subject,MYSQL_ASSOC)){
   		$bids[$c]=$subject{'subject_id'};
		$c++;
		}
	$c=0;
   	while($yeargroup=mysql_fetch_array($d_yeargroup,MYSQL_ASSOC)){
   		$yids[$c]=$yeargroup{'id'};
		$c++;
		}
	for($c=0; $c<sizeof($bids); $c++){
  		$bid=$bids[$c];
		for($c2=0; $c2<sizeof($yids); $c2++){
	  		$yid=$yids[$c2];
			$ing=$bid.$yid.'g';
			$inm=$bid.$yid.'m';
			if(isset($_POST{$ing})){
				$many=$_POST{$inm}; 
				$generate=$_POST{$ing};

				if($many!='' and $generate!='none'){
				$d_classes=mysql_query("SELECT * FROM classes WHERE
						subject_id='$bid' AND yeargroup_id='$yid' AND course_id='$crid'");
				if(mysql_fetch_array($d_classes,MYSQL_ASSOC)){
					if(mysql_query("UPDATE classes SET many='$many',
						generate='$generate' WHERE yeargroup_id='$yid' AND
						subject_id='$bid' AND course_id='$crid'"))
						{$result[]=$bid.$crid.' updated.';}
					else{$error[]=mysql_error();}
					}
				else {
					if(mysql_query("INSERT INTO classes (many, generate,
						yeargroup_id, course_id, subject_id) VALUES ('$many',
						'$generate', '$yid', '$crid', '$bid')"))
						{$result[]=$bid.$crid.' entered.';}
					else{$error[]=mysql_error();}
					}
				}
				else {
 					if(mysql_query("DELETE FROM classes WHERE
						yeargroup_id='$yid' AND  course_id='$crid' AND
						subject_id='$bid' LIMIT 1"))
						{$result[]=$bid.$crid.' removed';}
					else{$error[]=mysql_error();}
					}
				}
			}
		}
	}

elseif($sub=='Generate'){
	$action='class_matrix_action1.php';
?>
  <div class="content">
	<fieldset class="center">
	  <?php print_string('generateclassstructurequestion',$book);?>
	  <form method="post" action="<?php print $host;?>">
		<input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
		<input type="hidden" name="cancel" value="<?php print $choice;?>">
			  <button type="submit" name="sub" value="Yes">
				<?php print_string('yes');?>
			  </button>
			  <button type="submit" name="sub" value="No">
				<?php print_string('no');?>
			  </button>
	  </form>
	</fieldset>
  </div>
<?php
	exit;
	}
elseif($sub=='Yes'){
	$result[]='New class structure generated.';

/********* Should delete all from the cidsid and class tables before attempting this?
*/
//	mysql_query("TRUNCATE TABLE cidsid");
//	mysql_query("TRUNCATE TABLE tidcid");
//	mysql_query("TRUNCATE TABLE class");
	mysql_query("DELETE FROM class WHERE course_id='$crid'");
/*********/

	$d_yeargroup=mysql_query("SELECT id FROM yeargroup ORDER BY id");
	while($yeargroup=mysql_fetch_array($d_yeargroup,MYSQL_ASSOC)){
		$yid=$yeargroup['id'];
 	   	
		$d_classes=mysql_query("SELECT * FROM classes WHERE
										course_id='$crid' AND
										yeargroup_id='$yid' ORDER BY yeargroup_id");   	
		$num=mysql_num_rows($d_classes);
 
   		for($c=0;$c<$num;$c++){
				$classes=mysql_fetch_array($d_classes,MYSQL_ASSOC);
   				$bid=$classes['subject_id'];
				$yid=$classes['yeargroup_id'];
				if($classes['generate']=='forms' & $classes['many']>0){
					$d_form=mysql_query("SELECT id FROM form WHERE yeargroup_id='$yid'");
					while($form=mysql_fetch_array($d_form,MYSQL_ASSOC)) {
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
					$c=1;
					$many=$classes['many'];
					if($classes['naming']==''){$name=$bid.$yid.'/';}
					else{$name=$bid.$classes['naming'];}
					while($c<=$many){
						$newcid=$name.$c;
						if(mysql_query("INSERT INTO class (id,
							subject_id, course_id, yeargroup_id) VALUES ('$newcid', '$bid',
								'$crid', '$yid')")){$result[]=' '.$newcid.' ';} 
						else{$error[]='Already exists '.$newcid.' ';}
						$c++;
						}
					}
				}		
		}
	}
elseif($sub=='No'){
	$result[]='No action taken.';
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
