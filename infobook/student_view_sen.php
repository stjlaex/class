<?php
/**                                  student_view_sen.php
 */

$cancel='student_view.php';
$action='student_view_sen1.php';
if(isset($_POST['bid'])){$selbid=$_POST['bid'];}else{$selbid='';}
if(isset($_GET['bid'])){$selbid=$_GET['bid'];}

if($Student['SENFlag']['value']=='N'){
	two_buttonmenu();

	/*Check user has permission to view*/
	$yid=$Student['YearGroup']['value'];
	$perm=getSENPerm($yid);
	include('scripts/perm_action.php');

?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <fieldset class="center">	
		<legend><?php print_string('notsenstudent','seneeds');?></legend>
		<button onClick="processContent(this);" name="sub" 
				value="senstatus"><?php print_string('changesenstatus','seneeds');?></button>
	  </fieldset>
	<input type="hidden" name="current" value="<?php print $action;?>"/>
	<input type="hidden" name="cancel" value="<?php print $cancel;?>"/>
	<input type="hidden" name="choice" value="<?php print $choice;?>"/>
	</form>
  </div>
<?php
	}
else{

	/* Get the most recent SEN record if one exists. */
	$SEN=fetchSEN($sid);
	$senhid=$SEN['id_db'];

	/* Careful with the setting of book: it needs to be seneeds to get
	 * all of the lang options but tinytabs won't find the tab to load if
	 * we are in infobook; 
	 */
	$book='seneeds';
	include('seneeds/sen_view.php'); $book='infobook'; } ?>
