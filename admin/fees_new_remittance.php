<?php
/**									fees_new_remittance.php
 */

$action='fees_new_remittance_action.php';

if(isset($_GET['remid'])){$remid=$_GET['remid'];}else{$remid=-1;}
if(isset($_POST['remid'])){$remid=$_POST['remid'];}

if(isset($_GET['feeyear'])){$feeyear=$_GET['feeyear'];}else{$feeyear='';}
if(isset($_POST['feeyear'])){$feeyear=$_POST['feeyear'];}

three_buttonmenu();

$Remittance=fetchRemittance($remid);

?>

  <div id="heading">
	<label><?php print_string('newremittance',$book);?></label>
  </div>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">


	  <fieldset class="center">
		<div class="center">
		  <?php xmlelement_div($Remittance['Name'],'',$tab,'center','admin');?>
		</div>
	  </fieldset>

	  <fieldset class="center">
		<div class="center">

<?php 
		$listlabel='concept';
		$liststyle='width:400px;';
		$multi=10;
		$listname='conid';
		$concepts=list_concepts();
		include('scripts/set_list_vars.php');
		list_select_list($concepts,$listoptions,$book);
?>
		</div>
	  </fieldset>



	    <input type="hidden" name="remid" value="<?php print $remid;?>">
	    <input type="hidden" name="feeyear" value="<?php print $feeyear;?>">
	    <input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="cancel" value="<?php print $choice;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>
