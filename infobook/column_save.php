<?php
/**								   column_save.php
 *
 */

$action='column_save_action.php';
$choice='student_list.php';

//if(isset($_POST['savedview'])){$savedview=$_POST['savedview'];}else{$savedview='';}
if(isset($_POST['colno'])){$displayfields_no=$_POST['colno'];}
for($dindex=0;$dindex < ($displayfields_no);$dindex++){
	if(isset($_POST['displayfield'.$dindex])){$displayfields[$dindex]=$_POST['displayfield'.$dindex];}
	}


three_buttonmenu();
?>

  <div id="heading">
	<label><?php print_string('saveview',$book);?></label>
  </div>

  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	<fieldset class="divgroup center">
	  <div class="center">
		<label for="name"><?php print_string('name',$book);?></label>
		<input type="text" name="name" id="name" size="60" value="" />
	  </div>
	</fieldset>

<?php
foreach($displayfields as $dindex => $displayfield){
	print '<input type="hidden" name="displayfield'.$dindex.'" value="'.$displayfield.'" />';
	}
?>
	<input type="hidden" name="colno" value="<?php print $displayfields_no;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print 'student_list.php';?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
  </form>
  </div>
