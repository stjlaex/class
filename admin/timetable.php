<?php
/**								   timetable.php
 *
 */

$choice='timetable.php';
$action='timetable_export.php';

include('scripts/sub_action.php');

three_buttonmenu();
?>
<div id="viewcontent" class="content">
    <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
        <div class="left">
            <fieldset class="divgroup"> 
                <h5><?php print_string('timetable',$book);?></h5> 
                <?php print_string('timetable',$book);?>
            </fieldset>
        </div>
        <div class="right">
            <fieldset class="divgroup"> 
                <h5><?php print_string('confirm',$book);?></h5>
                <p><?php print_string('confidentwhatyouaredoing',$book);?></p>
                <?php include('scripts/check_yesno.php');?>
            </fieldset> 
        </div>
<input type="hidden" name="cancel" value="" />
<input type="hidden" name="choice" value="<?php print $choice;?>" />
<input type="hidden" name="current" value="<?php print $action;?>" />
</form>
</div>
