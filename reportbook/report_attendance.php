<?php
/**			       		report_attendance.php
 */

$action='report_attendance_action.php';
$choice='report_attendance.php';

if(isset($_POST['yid'])){$yid=$_POST['yid'];$selyid=$yid;}else{$yid='';}
if(isset($_POST['formid'])){$formid=$_POST['formid'];}else{$formid='';}
if(isset($_POST['houseid'])){$houseid=$_POST['houseid'];}else{$houseid='';}
if(isset($_POST['reporttype'])){$reporttype=$_POST['reporttype'];}else{$reporttype='S';}

/* Search across last four weeks by default */
$todate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-28,date('Y')));

three_buttonmenu();
?>
    <div id="heading">
        <h4><?php print_string('search',$book);?> <?php  print_string('attendance',$book);?></h4>
    </div>
    <div class="content">
        <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
            <div class="center"> 
                <fieldset class="divgroup">
                    <h5><?php print_string('collateforstudentsfrom',$book);?></h5>
<?php
				$onchange='yes';
				$required='yes';
				include('scripts/'.$listgroup);
?>
				<div class='left'>
<?php
				$onsidechange='yes';
				$listtype='section';
				$listname='secid';
				$listlabel='section';
				$listlabelstyle='eternal';
				include('scripts/list_section.php');
?>
				</div>
                </fieldset>
            </div>
            <div class="left">
                <fieldset class="divgroup">
	            <div class="left">
                    <h5><?php print_string('collatesince',$book);?></h5>
                    <?php 
                        unset($todate);
                        include('scripts/jsdate-form.php'); 
                    ?>
	            </div>
	            <div class="right">
                        <h5><?php print_string('collateuntil',$book);?></h5>
                        <?php 
                            include('scripts/jsdate-form.php'); 
                        ?>
	            </div>
                </fieldset>
            </div>
            <div class="right">
                <fieldset class="divgroup">
                    <h5><?php print get_string('attendance','register').' '.get_string('type',$book);?></h5>
                    <?php
                    	$types=array('P'=>'classes','S'=>'registrationsession');
                    	foreach($types as $value => $type){
                    		if($value==$reporttype){$checked='checked="checked"';}
                    		else{$checked='';}
                    		print '<input type="radio" name="reporttype" '.$checked .'value="'.$value.'">'.get_string($type,'register').'</input> <br/>';
                        }
                    ?>
                </fieldset>
            </div>
            <input type="hidden" name="current" value="<?php print $action; ?>">
            <input type="hidden" name="choice" value="<?php print $choice; ?>">
            <input type="hidden" name="cancel" value="<?php print ''; ?>">
        </form>
    </div>

