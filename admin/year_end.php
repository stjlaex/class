<?php 
/** 									year_end.php
 */

$action='year_end_action.php';
$choice='year_end.php';

three_buttonmenu();
?>

    <div class="content">
        <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
            <div class="left">
                <fieldset class="divgroup"> 
                    <h5><?php print_string('yearend',$book);?></h5> 
                    <?php print_string('yearendwarning',$book);?>
                </fieldset>
            </div>
            <div class="right">              
                <fieldset class="divgroup"> 
                    <h5><?php print_string('confirm',$book);?></h5>
                    <p><?php print_string('confidentwhatyouaredoing',$book);?></p>
                    <?php include('scripts/check_yesno.php');?>
                </fieldset> 
            </div>
            <input type="hidden" name="cancel" value="<?php print ''; ?>">
            <input type="hidden" name="current" value="<?php print $action;?>" />
            <input type="hidden" name="choice" value="<?php print $choice;?>" />
        </form> 
    </div>


