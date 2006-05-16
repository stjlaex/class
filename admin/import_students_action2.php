<?php 
/**									   		import_students_action2.php
 *
 *	Verify the selected import structure.	
 */
	
$action='import_students_action3.php';

include('scripts/sub_action.php');

$nofields=$_SESSION{'nofields'};
$idef=$_SESSION{'idef'};
$instudents=$_SESSION{'instudents'};

if($sub=='Submit'){
	$extrabuttons['savedefinition']=array('name'=>'sub','value'=>'Save');
	three_buttonmenu($extrabuttons);
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host;?>">

	  <div class="center">
		<table class="listmenu">
		  <caption><?php print_string('resultofdatafile',$book);?></caption>
		  <tr>
			<th><?php print_string('fieldnumber',$book);?></th>
			<th><?php print_string('examplevalue',$book);?></th> 
			<th><?php print_string('presetvalue',$book);?></th>
			<th><?php print_string('fieldname',$book);?></th>
		  </tr>
<?php
		$egstudent=$instudents[0];
		for($c=0;$c<$nofields;$c++){
?>
		<tr>
		  <td><?php print $c;?></td>
		  <td><?php print $egstudent[$c];?></td>
		  <td>
<?php
	if($_POST{"preset$c"}!=''){
		print $_POST{"preset$c"};
?>		<input type='hidden' name='<?php print "preset$c"; ?>' value='<?php print $_POST{"preset$c"}; ?>'>
<?php
		}	
	
?>
		  </td>
		  <td>
<?php
	if($_POST{"sidfield$c"}!=''){
		print "Student: ".$_POST{"sidfield$c"};
?>		<input type='hidden' name='<?php print "field$c"; ?>' value='<?php print $_POST{"sidfield$c"}; ?>'>
		<input type='hidden' name='<?php print "table$c"; ?>' value='sid'>
<?php
		}
	elseif($_POST{"gid1field$c"}!=''){
		print "Guardian One: ".$_POST{"gid1field$c"};
?>		<input type='hidden' name='<?php print "field$c"; ?>' value='<?php print $_POST{"gid1field$c"}; ?>'>
		<input type='hidden' name='<?php print "table$c"; ?>' value='gid1'>
<?php
		}	
	elseif($_POST{"gid2field$c"}!=''){
		print "Guardian Two: ".$_POST{"gid2field$c"};
?>		<input type='hidden' name='<?php print "field$c"; ?>' value='<?php print $_POST{"gid2field$c"}; ?>'>
		<input type='hidden' name='<?php print "table$c"; ?>' value='gid2'>
<?php
		}	
	elseif($_POST{"gid3field$c"}!=''){
		print "Guardian Three: ".$_POST{"gid3field$c"};
?>		<input type='hidden' name='<?php print "field$c"; ?>' value='<?php print $_POST{"gid3field$c"}; ?>'>
		<input type='hidden' name='<?php print "table$c"; ?>' value='gid3'>
<?php
		}	
?>
		  </td>
		</tr>
<?php
				}
?>
		</table>
	  </div>
	  <input type="hidden" name="nofields" value="<?php print $nofields;?>"/>
		<input type="hidden" name="current" value="<?php print $action;?>"/>
		  <input type="hidden" name="choice" value="<?php print $choice;?>"/>
			<input type="hidden" name="cancel" value="<?php print 'import_students_action1.php';?>"/>
	</form>
  </div>
<?php
}
elseif($sub=='Load'){
	$action="import_students_cidef.php";

	three_buttonmenu();
?>
  <div class="content">
	<fieldset class="center">
	  <legend><?php print_string('loadpreviousdefinition',$book);?></legend>
	  <form  name="formtoprocess" id="formtoprocess" 
		method="post" enctype="multipart/form-data" action="<?php print $host;?>">
		<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
		  <label><?php print_string('filename');?></label>
		  <input class="required" type="file" id="importfile" name="importfile" />
			<input type="hidden" name="current" value="<?php print $action;?>"/>
			<input type="hidden" name="choice" value="<?php print $choice;?>"/>
			<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	  </form>
	</fieldset>
  </div>
<?php
				}
?>















