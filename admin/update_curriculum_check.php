<?php
/**								update_curriculum_check.php
 *
 */

$action='update_curriculum_action.php';

include('scripts/answer_action.php');

three_buttonmenu();
?>

<div class="content">
    <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
        <div class="left">
            <fieldset class="divgroup">
                <h5><?php print_string('updatecurriculum',$book); ?></h5> 
                <?php print_string('updatecurriculumwarning',$book); ?>
            </fieldset>
        </div>
        <div class="right">
            <fieldset class="divgroup">
                <h5><?php print_string('assessmentmethods',$book);?></h5>
                <p><?php print_string('deletesallmarksetc',$book);?></p>
                <?php $checkname='asscheck'; include('scripts/check_yesno.php');?>
            </fieldset>
      </div>
        <div class="left">      
            <fieldset class="divgroup">
                <h5><?php print_string('curriculum',$book);?></h5>
                <p><?php print_string('deletesallclassesetc',$book);?></p>
                <?php $checkname='coursecheck'; include('scripts/check_yesno.php');?>
            </fieldset>
        </div>
        <div class="right">
            <fieldset class="divgroup">
                <h5><?php print_string('pastoralgroups',$book);?></h5>
                <p><?php print_string('deletesallyeargroupsetc',$book);?></p>
                <?php $checkname='groupcheck'; include('scripts/check_yesno.php');?>
            </fieldset>
        </div>
        <input type="hidden" name="cancel" value="<?php print ''; ?>" />
        <input type="hidden" name="current" value="<?php print $action;?>" />
        <input type="hidden" name="choice" value="<?php print $choice;?>" />
    </form> 
</div>
