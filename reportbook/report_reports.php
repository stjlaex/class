<?php 
/**									report_reports.php
 */

$action='report_reports_action.php';
$choice='report_reports.php';



if(isset($_POST['yid'])){$yid=$_POST['yid'];$selyid=$yid;}else{$yid='';}
if(isset($_POST['formid'])){$formid=$_POST['formid'];}else{$formid='';}
if(isset($_POST['houseid'])){$houseid=$_POST['houseid'];}else{$houseid='';}
if(isset($_POST['wrapper_rid'])){$wrapper_rid=$_POST['wrapper_rid'];}else{$wrapper_rid='';}

if(isset($_POST['comid'])  and $_POST['comid']!=''){$comid=$_POST['comid'];}else{$comid='';}

include('scripts/sub_action.php');

three_buttonmenu();
?>

    <div class="content">
        <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>"> 
            <fieldset class="divgroup">
            <h5><?php print_string('collateforstudentsfrom',$book);?></h5>
            <?php $onchange='yes'; $required='yes'; include('scripts/'.$listgroup);?>
            </fieldset>
            <?php
                /* Restrict to the current academic year unles an admin */
                if($_SESSION['role']=='admin'){$current=false;}
                else{$current=true;}
                if($comid!=''){
                $com=(array)get_community($comid);
                if($com['type']=='form'){$formid=$comid;}
                elseif($com['type']=='house'){$houseid=$comid;}
                    $cohorts=(array)list_community_cohorts($com,$current);
                    }
                /* TODO: should the cohorts be listed by community instead???? */
                if($yid!=''){
                    $cohorts=(array)list_community_cohorts(array('id'=>'','type'=>'year','name'=>$yid),$current);
                	}
                if(isset($cohorts)){
            ?>
            <fieldset class="divgroup">
                <h5><?php print_string('choosetoinclude',$book);?></h5>
                <?php		include('scripts/list_report_wrapper.php');?>
            </fieldset>
            <?php
                }
            ?>
            <input type="hidden" name="cancel" value="<?php print '';?>" />
            <input type="hidden" name="comid" value="<?php print $comid;?>" />
            <input type="hidden" name="current" value="<?php print $action; ?>">
            <input type="hidden" name="choice" value="<?php print $choice; ?>">
        </form>
    </div>

