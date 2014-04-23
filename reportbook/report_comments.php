<?php
/**			       		report_comments.php
 */
$action='report_comments_list.php';
$choice='report_comments.php';

//last two weeks by default
$todate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-14,date('Y')));

$extrabuttons['summary']=array('name'=>'current','value'=>'report_comments_summary.php');
three_buttonmenu($extrabuttons);
?>
    <div id="heading">
        <h4><?php print_string('search',$book);?> <?php    print_string('comments',$book);?></h4>
    </div>
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
		<div class="left">
                    <h5><?php print_string('collatesince',$book);?></h5>
                    <?php include('scripts/jsdate-form.php'); ?>
		</div>
		<div class="right">
                    <h5><?php print_string('collateuntil',$book);?></h5>
                    <?php $required='no'; unset($todate); include('scripts/jsdate-form.php'); ?>
		</div>
                </fieldset>
            </div>

            <div class="row">
            <div class="right">
                <fieldset class="divgroup" >
                    <h5><?php print_string('limittoonetype',$book);?></h5>
                    <?php
                    $listlabel='category'; $listid='category';
                    $required='no';
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
            </div>
            <input type="hidden" name="current" value="<?php print $action; ?>">
            <input type="hidden" name="choice" value="<?php print $choice; ?>">
            <input type="hidden" name="cancel" value="<?php print ''; ?>">
        </form>
    </div