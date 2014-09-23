<?php 
/**								passwords.php
 */

$choice='passwords.php';
$action='passwords_action.php';
three_buttonmenu();
?>
<div class="content">
<form name="formtoprocess" id="formtoprocess" enctype="multipart/form-data" method="post" action="<?php print $host; ?>">
  <fieldset class="divgroup">
    <h5><strong><?php print_string('regeneratepasswords',$book);?></strong></h5>
    <div class="center">
		  <p><?php print_string('updatepasswordsdetail',$book);?></p>
      <div style="margin: 20px 0;">
        <?php 
        	$checkname='passwords';
        	$checkchoice='no';
        	$checkcaption=get_string('regeneratepasswords',$book); 
        	include('scripts/check_yesno.php')
        ?>
      </div>
    </div>
		<div class="center">
      <?php 
        if($CFG->emailoff!='yes'){
      ?>
		  <p><?php print_string('emailuserpasswordsdetail',$book);?></p>
      <?php
          }
        else{
      ?>
		  <p><?php print_string('emailtostaffisdisabled',$book);?></p>
      <?php
      	}
      ?>
		</div>
  </fieldset>
  <fieldset class="divgroup">
		<h5><strong><?php print_string('emailuserlist',$book);?></strong></h5>
		<div class="center">
		  <p><?php print_string('emailadminwithuserlist',$book);?></p>
		  <div style="margin: 20px 0;">
        <?php 
        	$checkname='emailadmin';
        	$checkcaption=get_string('emailuserlist',$book); 
        	$checkchoice='no';
        	include('scripts/check_yesno.php')
        ?>
      </div>
    </div>
  </fieldset>
	
 	<input type="hidden" name="current" value="<?php print $action; ?>">
 	<input type="hidden" name="choice" value="<?php print $choice; ?>">
 	<input type="hidden" name="cancel" value="<?php print ''; ?>">
</form>  
</div>
