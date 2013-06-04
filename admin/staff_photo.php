<?php
/**
 *                                  staff_photo.php
 */

$action='staff_details.php';

two_buttonmenu();
$User=fetchUser($_GET['seluid']);
?>

  <div id="heading">
	<?php print $User['Forename']['value'].' '.$User['Surname']['value'];?>
  </div>

  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="seluid" value="<?php print $_GET['seluid'];?>" />
		<input type="hidden" name="cancel" value="<?php print $cancel;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>

<?php
	require_once('lib/eportfolio_functions.php');
	html_document_drop($User['EPFUsername']['value'],'icon','%',$_GET['seluid'],'staff');
?>

  </div>
