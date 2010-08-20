<?php 
/**		  		       			class_matrix_action.php
 */

$action='class_matrix.php';

include('scripts/sub_action.php');

list($crid,$bid,$error)=checkCurrentRespon($r,$respons,'course');
if(sizeof($error)>0){include('scripts/results.php');exit;}

if($sub=='Update'){

	$subjects=(array)list_course_subjects($crid);
	$stages=(array)list_course_stages($crid);

	for($c=0;$c<sizeof($subjects);$c++){
  		$bid=$subjects[$c]['id'];
		for($c2=0;$c2<sizeof($stages);$c2++){
	  		$stage=$stages[$c2]['id'];
			$ing=$bid. $stage.'g';
			$inm=$bid. $stage.'m';
			$ins=$bid. $stage.'s';
			$ind=$bid. $stage.'d';
			$inblock=$bid. $stage.'block';
			$classdef=array('crid'=>$crid,'bid'=>$bid,'stage'=>$stage);
			if(isset($_POST[$ing])){
				if($_POST[$ing]=='forms'){
					$classdef['generate']=$_POST[$ing];
					$classdef['many']=''; 
					}
				else{
					$classdef['generate']='sets';
					$classdef['many']=$_POST[$ing]; 
					}
				$classdef['sp']=$_POST[$ins];
				$classdef['dp']=$_POST[$ind];
				$classdef['block']=$_POST[$inblock];
				update_subjectclassdef($classdef);
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
		class.id=cidsid.class_id AND class.course_id='$crid';");
	mysql_query("DELETE tidcid.* FROM tidcid, class WHERE 
		class.id=tidcid.class_id AND class.course_id='$crid';");
	mysql_query("DELETE midcid.* FROM midcid, class WHERE 
		class.id=midcid.class_id AND class.course_id='$crid';");
	mysql_query("DELETE FROM class WHERE course_id='$crid';");

	$d_classes=mysql_query("SELECT * FROM classes WHERE
										course_id='$crid' ORDER BY
										subject_id, stage;");   	

	while($classes=mysql_fetch_array($d_classes,MYSQL_ASSOC)){
		$bid=$classes['subject_id'];
		$stage=$classes['stage'];
		$classdef=get_subjectclassdef($crid,$bid,$stage);
		populate_subjectclassdef($classdef);
		}
	}

if(isset($result)){include('scripts/results.php');}
include('scripts/redirect.php');
?>
