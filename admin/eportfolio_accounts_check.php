<?php
/**		   					eportfolio_accounts_check.php
 *
 */

$action='eportfolio_accounts_action.php';

include('scripts/answer_action.php');

three_buttonmenu();
?>

    <div id="heading">
        <h4><?php print get_string('eportfolios',$book).' ';?></h4>
    </div>
    <div class="content">
        <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
            <div class="left">
                <fieldset class="divgroup">
                    <h5><?php print_string('eportfolios',$book); ?></h5> 
                    <?php print_string('eportfoliowarning',$book); ?>
                </fieldset>
            </div>
            <div class="right">
                <fieldset class="divgroup">
                    <h5><?php print get_string('staff',$book).' / '.
                    get_string('communities',$book) ;?></h5> 
                    <p> Refreshing deletes all communities and all of their associated content (blogs, files, everything all get wiped!). This is probably only needed at the start of a new academic year. Existing users' accounts and their content are left intact.
                    </p>
                    <?php
                      $checkcaption=get_string('blank',$book); $checkname='refresh'; 
                      include('scripts/check_yesno.php');
                    ?>
                </fieldset>
            </div>
            <div class="left">
                <fieldset class="divgroup">
                    <h5><?php print_string('students','infobook');?></h5>
                    <p>
                      Only use this blank option if you want to completely delete all student accounts and associated content. When the next synchronisation runs the students will be issued new logins.  
                    </p>
                    <?php 
                    $checkcaption=get_string('blank',$book); 
                    $checkname='studentblank'; include('scripts/check_yesno.php');
                    ?>
                </fieldset>
            </div>
            <div class="right">
                <fieldset class="divgroup">
                    <h5><?php print_string('contacts','infobook');?></h5>
                    <p>
                      Only use this blank option if you want to completely delete all contacts. When the next synchronisation runs the contacts will be issued new logins.		  
                    </p>
                    <?php 
                      $checkcaption=get_string('blank',$book); $checkname='contactblank'; 
                      include('scripts/check_yesno.php');
                    ?>
                </fieldset>
            </div>
        <input type="hidden" name="cancel" value="<?php print ''; ?>" />
        <input type="hidden" name="current" value="<?php print $action;?>" />
        <input type="hidden" name="choice" value="<?php print $choice;?>" />
        </form> 
    </div>
