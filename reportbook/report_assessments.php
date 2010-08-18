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
		if($r>-1 or isset($cohorts)){

/*	  <fieldset class="center">
		<legend><?php print_string('limitbysubject',$book);?></legend>
		<div class="left" >
		  <?php $multi='4'; include('scripts/list_subjects.php');?>
		</div>
	  </fieldset>
*/
?>

	  <fieldset class="center">
		<legend><?php print_string('choosetoinclude',$book);?></legend>
		<div class="center" >
<?php
	$listname='gender';$listlabel='gender';$required='no';
	include('scripts/set_list_vars.php');
	list_select_enum('gender',$listoptions,$book);
?>

		</div>
	  </fieldset>

	  <fieldset class="left">
		<legend><?php print_string('assessmentprofile',$book);?></legend>
		<div class="center">
<?php 
	$profiles=array();
	foreach($cohorts as $cohort){
		$profiles=array_merge($profiles,list_assessment_profiles($cohort['course_id']));
		}
	$onchange='yes';$required='no';
	include('scripts/list_assessment_profile.php');
?>
		</div>
	  </fieldset>
	  <fieldset class="right">
		<legend><?php print_string('template',$book);?></legend>
		<div class="center">
<?php $onchange='yes';$required='no';
   	$d_catdef=mysql_query("SELECT DISTINCT comment AS id, comment AS name FROM categorydef WHERE
								  type='pro' AND comment!='' ORDER BY course_id;");
	$listname='template';$onchange='no';
	include('scripts/set_list_vars.php');
	list_select_db($d_catdef,$listoptions,$book);
	unset($listoptions);
?>
		</div>
	  </fieldset>



	  <fieldset class="center">
		<legend><?php print_string('assessments',$book);?></legend>
		<div class="center" >
<?php
	if($selyid!=''){$ryids=array('0'=>$selyid);$rfids=array();}
	elseif($selfid!=''){$rfids=array('0'=>$selfid);$ryids=array();}
	if($profid==''){$selprofid='%';}
	else{$selprofid=$profid;}
	$required='no';
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
