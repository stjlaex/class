<?php
/**												report_assessments.php
 */

$action='report_assessments_action.php';
$choice='report_assessments.php';

if(isset($_GET['selfid'])){$selfid=$_GET['selfid'];}else{$selfid='';}
if(isset($_POST['selfid'])){$selfid=$_POST['selfid'];}
if(isset($_GET['selyid'])){$selyid=$_GET['selyid'];}else{$selyid='';}
if(isset($_POST['selyid'])){$selyid=$_POST['selyid'];}
if(isset($_POST['profid'])){$profid=$_POST['profid'];}else{$profid='%';}
if(isset($_POST['gender'])){$gender=$_POST['gender'];}else{$gender='';}


if(isset($_POST['year'])){$year=$_POST['year'];}
if(isset($_POST['stage'])){$stage=$_POST['stage'];}
if(isset($_POST['cid'])){$cid=$_POST['cid'];}
if(isset($_POST['gender'])){$gender=$_POST['gender'];}
if(isset($_POST['eids'])){$eids=(array)$_POST['eids'];}else{$eids=array();}


if($selfid!=''){
	$cohorts=list_community_cohorts(array('id'=>'','type'=>'form','name'=>$selfid));
	}
elseif($selyid!=''){
	$cohorts=list_community_cohorts(array('id'=>'','type'=>'year','name'=>$selyid));
	}
if(isset($cohorts)){
	$rcrid=$cohorts[1]['course_id'];$onchange='';$required='yes';
	}


three_buttonmenu();
?>
  <div class="content">
	<form id="formtoprocess" 
		name="formtoprocess" method="post" action="<?php print $host;?>"> 

	  <fieldset class="center">
		<legend><?php print_string('collateforstudentsfrom',$book);?></legend>
		<?php $onchange='yes'; $required='no'; include('scripts/'.$listgroup);?>
	  </fieldset>


<?php
		if($r>-1){
?>
	  <fieldset class="right">
		<legend><?php print_string('limitbysubject',$book);?></legend>
		<div class="left" >
<?php
			$classes=(array)list_course_classes($rcrid);
			$listname='cid';$listlabel='class';$required='no';
			include('scripts/set_list_vars.php');
			list_select_list($classes,$listoptions,$book);
?>
		</div>
	  </fieldset>

<?php
			}
		if($r>-1 or isset($cohorts)){
?>

	  <fieldset class="left">
		<legend><?php print_string('choosetoinclude',$book);?></legend>
		<div class="center" >
<?php
	$listname='gender';$listlabel='gender';$required='no';
	include('scripts/set_list_vars.php');
	list_select_enum('gender',$listoptions,$book);
?>
		</div>
	  </fieldset>

	  <fieldset class="center">
		<legend><?php print_string('assessments',$book);?></legend>
		<div class="right">
<?php 
	$profiles=array();
	if(isset($cohorts)){
		foreach($cohorts as $cohort){
			$profiles=array_merge($profiles,list_assessment_profiles($cohort['course_id']));
			}
		}
	else{
		$profiles=(array)list_assessment_profiles($rcrid);
		}
	$onchange='yes';$required='no';
	include('scripts/list_assessment_profile.php');
?>
		</div>

		<div class="left" >
<?php
	if($selyid!=''){$ryids=array('0'=>$selyid);$rforms=array();}
	elseif($selfid!=''){$rforms=array('0'=>array('name'=>$selfid));$ryids=array();}
	if($profid==''){$selprofid='%';}
	else{$selprofid=$profid;}
	$required='yes';$multi=15;
	include('scripts/list_assessment.php');
?>
		</div>
	  </fieldset>


<?php
		}
?>

	  <input type="hidden" name="selfid" value="<?php print $selfid;?>" />
	  <input type="hidden" name="selyid" value="<?php print $selyid;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
  </div>
