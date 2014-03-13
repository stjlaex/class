<?php
/**								update_curriculum.php
 *
 * Update the database tables to match with entries from the curriculum
 * files. It does not (as yet) remove any data fro mthe database even if 
 * it has been removed from the curriculum files.
 */

$action='update_curriculum_check.php';
$choice='update_curriculum.php';

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
                <h5><?php print_string('confirm',$book);?></h5>
                <p><?php print_string('confidentwhatyouaredoing',$book);?></p>
                  <?php include('scripts/check_yesno.php');?>
            </fieldset>
        </div>
        <input type="hidden" name="cancel" value="<?php print ''; ?>" />
        <input type="hidden" name="current" value="<?php print $action;?>" />
        <input type="hidden" name="choice" value="<?php print $choice;?>" />
    </form>
</div>
