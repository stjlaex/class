<?php
if(!isset($tracking_extra_transform)){$tracking_extra_transform='tracking_student';}
?>
<div class="hidden" id="add-merit">
    <div title="<?php print_string('tracking','markbook'); ?>" name="current" value="student_grades_print.php" onclick="clickToPresentSid(this,'student_grades_print.php','<?php print $tracking_extra_transform; ?>')" >
        <span class="fa fa-bar-chart-o"></span>
    </div>
    <div title="<?php print_string('merits','infobook'); ?>" name="current" value="merit_adder.php" onclick="clickToAddMerit(this,'','','merit')" >
        <span class="fa fa-star"></span>
    </div>
    <div title="<?php print_string('targets','infobook'); ?>" name="current" value="student_targets_print.php" onclick="clickToPresentSid(this,'student_targets_print.php','student_targets_print')" >
        <span class="fa fa-bullseye"></span>
    </div>
</div>