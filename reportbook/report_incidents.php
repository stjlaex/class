<?php
/**			       		report_incidents.php
 */

$action='report_incidents_list.php';
$choice='report_incidents.php';

//last week by default
$todate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-7,date('Y')));

three_buttonmenu();
?>
    <div class="content">
        <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
            <div class="left">
                <fieldset class="divgroup">
                    <h5><?php print_string('collateforstudentsfrom',$book);?></h5>
                    <?php $required='yes'; include('scripts/'.$listgroup);?>
                </fieldset>
            </div>
            <div class="right">
                <fieldset class="divgroup">
                    <h5><?php print_string('collatesince',$book);?></h5>
                    <?php include('scripts/jsdate-form.php'); ?>
                </fieldset>
            </div>
            <div class="left">
                <fieldset class="divgroup">
                    <h5><?php print_string('collateuntil',$book);?></h5>
                    <?php $required='no'; unset($todate); include('scripts/jsdate-form.php'); ?>
                </fieldset>
            </div>
            <div class="right">
                <fieldset class="divgroup">
                    <h5><?php print_string('sanction');?></h5>
                    <?php 
                        $listlabel='sanction'; $required='no';
                        $listid='sanction';$cattype='inc';
                        include('scripts/list_category.php');
                    ?>
                </fieldset>
            </div>
            <div class="left">
            <fieldset class="divgroup" >
                <h5><?php print_string('limittoonesubject');?></h5>
                <?php
                  $required='no';
                  include('scripts/list_subjects.php');
                ?>
            </fieldset>
            </div>
            <input type="hidden" name="cancel" value="<?php print ''; ?>">
            <input type="hidden" name="current" value="<?php print $action; ?>">
            <input type="hidden" name="choice" value="<?php print $choice; ?>">
        </form>  
    </div>