<?php
/**									fees_new_concept.php
 */

$action='fees_new_concept_action.php';

if(isset($_GET['conid'])){$conid=$_GET['conid'];}else{$conid=-1;}
if(isset($_POST['conid'])){$conid=$_POST['conid'];}

if(isset($_GET['feeyear'])){$feeyear=$_GET['feeyear'];}else{$feeyear='';}
if(isset($_POST['feeyear'])){$feeyear=$_POST['feeyear'];}

three_buttonmenu();

$Concept=fetchConcept($conid);

?>

  <div id="heading">
	<label><?php print_string('newconcept',$book);?></label>
  </div>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <div class="center">
		  <?php $tab=xmlarray_form($Concept,'','concept',$tab,'admin'); ?>
	  </div>


	    <input type="hidden" name="conid" value="<?php print $conid;?>">
	    <input type="hidden" name="feeyear" value="<?php print $feeyear;?>">
	    <input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="cancel" value="<?php print $choice;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>
