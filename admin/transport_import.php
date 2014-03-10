<?php
/**								transport_import.php
 *
 */

$action='transport_import_action.php';
$choice='transport.php';

include('scripts/sub_action.php');

three_buttonmenu();
?>
    <div id="heading">
        <?php print get_string('import',$book);?>
    </div>
    <div class="content">
        <form id="formtoprocess" name="formtoprocess" enctype="multipart/form-data" method="post" action="<?php print $host;?>">
            <div class="left">
                <fieldset class="divgroup">
                    <h5><?php print_string('firstcolumnidentifier','reportbook');?></h5>
                    <label for="enrolno"><?php print_string('enrolmentnumber','infobook');?></label>
                    <input type="radio" name="firstcol" tabindex="<?php print $tab++;?>" eitheror="sid"  class="requiredor" checked="checked" title="" id="enrolno" value="enrolno" />
                    
                    <label for="sid"><?php print_string('studentdbid','reportbook');?></label>
                    <input type="radio" name="firstcol" tabindex="<?php print $tab++;?>" eitheror="enrolno"  class="requiredor" id="sid" title="" value="sid" />
                </fieldset>
            </div>
            <div class="right">
                <fieldset class="divgroup">
                    <h5><?php print_string('selectfiletoimportfrom');?></h5>
                    <label for="File name"><?php print_string('filename');?></label>
                    <input type="file" id="File name" tabindex="<?php print $tab++;?>" class="required" name="importfile" />
            		<input type="hidden" name="MAX_FILE_SIZE" value="800000">	
                </fieldset> 
            </div>
            <div class="left">
                <fieldset class="divgroup">
                    <h5><?php print_string('fieldseparator',$book);?></h5>		  
                    <label for="sid"><?php print_string('comma',$book);?></label>
                    <input type="radio" name="separator" tabindex="<?php print $tab++;?>" eitheror="enrolno"  class="requiredor" checked="checked" id="comma" title="" value="comma" />
                    <label for="enrolno"><?php print_string('semicolon','infobook');?></label>
                    <input type="radio" name="separator" tabindex="<?php print $tab++;?>" eitheror="sid"  class="requiredor" title="" id="semicolon" value="semicolon" />
                </fieldset>
            </div>
            <div class="right">
                <fieldset class="divgroup">
                    <h5><?php print_string('columnstart',$book);?></h5>		  
                    <?php
                        $listname='colstart';
                        $listlabel='columnno';
                        $selcolstart='4';
                        $grades=array('2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7');
                        include('scripts/set_list_vars.php');
                        list_select_list($grades,$listoptions,$book);
                    ?>
                </fieldset>
            </div>
         	<input type="hidden" name="cancel" value="<?php print $choice; ?>">
         	<input type="hidden" name="current" value="<?php print $action; ?>">
         	<input type="hidden" name="choice" value="<?php print $choice; ?>">
    	</form>
    </div>

