<?php
/**												report_assessments.php
 */

$action='report_assessments_action.php';
$choice='report_assessments.php';

if(isset($_POST['yid'])){$yid=$_POST['yid'];}
if(isset($_POST['comid']) and $_POST['comid']!=''){$comid=$_POST['comid'];}else{$comid='';}
if(isset($_POST['profid'])){$profid=$_POST['profid'];}else{$profid='%';}
if(isset($_POST['gender'])){$gender=$_POST['gender'];}else{$gender='';}
if(isset($_POST['limitbid'])){$limitbid=$_POST['limitbid'];}
if(isset($_POST['year'])){$year=$_POST['year'];}
if(isset($_POST['stage'])){$stage=$_POST['stage'];}
if(isset($_POST['gender'])){$gender=$_POST['gender'];}
if(isset($_POST['eids'])){$eids=(array)$_POST['eids'];}else{$eids=array();}
if(isset($_POST['cids'])){$cids=(array)$_POST['cids'];}else{$cids=array();}

if($yid!=''){
	$cohorts=(array)list_community_cohorts(array('id'=>'','type'=>'year','name'=>$yid));
	}
elseif($comid!=''){
	$com=(array)get_community($comid);
	if($com['type']=='form'){$formid=$comid;}
	elseif($com['type']=='house'){$houseid=$comid;}
	$cohorts=(array)list_community_cohorts($com);
	}
elseif(!empty($year) and !empty($stage)){
	$cohorts=array();
	$cohorts[]=array('id'=>'',
					 'course_id'=>$rcrid,
					 'stage'=>$stage,
					 'year'=>$year
					 );
	}
three_buttonmenu();
?>
    <div class="content">
    <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
        <?php
            if(!isset($cohorts)){$collateclass="";}else{$collateclass="left";}
        ?>
        <div class="<?php echo $collateclass;?>">
            <fieldset class="divgroup">
                <h5><?php print_string('collateforstudentsfrom',$book);?></h5>
                <?php $onchange='yes'; $required='no'; include('scripts/'.$listgroup);?>
            </fieldset>
        </div>
        <?php
            if(isset($cohorts)){
        ?>
        <div class="right">
            <fieldset class="divgroup">
                <h5><?php print_string('choosetoinclude',$book);?></h5>
                <?php
                    $listname='gender';$listlabel='gender';$required='no';
                    include('scripts/set_list_vars.php');
                    list_select_enum('gender',$listoptions,$book);
                    unset($listoptions);
                ?>
            </fieldset>
        </div>
        <div class="left">
            <fieldset class="divgroup">
                <h5><?php print_string('choosetoinclude',$book);?></h5>
                <?php
                    $classes=array();
                    foreach($cohorts as $cohort){
                    	trigger_error(':'.$cohort['course_id'],E_USER_WARNING);
                    $classes=array_merge($classes,list_course_classes($cohort['course_id']));
                    	}
                    $listname='cid';$listlabel='classes';$required='no';$multi=5;
                    include('scripts/set_list_vars.php');
                    list_select_list($classes,$listoptions,$book);
                    unset($listoptions);
                ?>
            </fieldset>
        </div>
        <div class="right">
            <fieldset class="divgroup">
                <h5><?php print_string('assessments',$book);?></h5>
                <?php 
                    $profiles=array();
                    foreach($cohorts as $cohort){
                    	$profiles=array_merge($profiles,list_assessment_profiles($cohort['course_id']));
                    	}
                    $onchange='yes';$required='no';
                    include('scripts/list_assessment_profile.php');
                ?>
                <?php 
                    $subjects=array();
                    foreach($cohorts as $cohort){
                    	$subjects=array_merge($subjects,list_course_subjects($cohort['course_id']));
                    	}
                    $listname='limitbid';$listlabel='subject';$required='no';$multi=1;
                    include('scripts/set_list_vars.php');
                    list_select_list($subjects,$listoptions,$book);
                    unset($listoptions);
                ?>
                <?php
                    if($yid!=''){$ryids=array('0'=>$yid);$rforms=array();}
                    elseif($comid!='' and $com['type']=='form'){$rforms[0]=$com;$ryids=array();}
                    elseif($comid!='' and $com['type']=='houses'){$rhouses[0]=$com;$ryids=array();}
                    if($profid==''){$selprofid='%';}
                    else{$selprofid=$profid;}
                    $required='yes';$multi=15;
                    include('scripts/list_assessment.php');
                ?>
            </fieldset>
        </div>
        <?php
            }
        ?>
        <input type="hidden" name="cancel" value="<?php print '';?>" />
        <input type="hidden" name="current" value="<?php print $action;?>" />
        <input type="hidden" name="choice" value="<?php print $choice;?>" />
    </form>
</div>
