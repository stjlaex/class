<?php
/**								   column_save.php
 *
 */

$action='column_save_action.php';
$choice='student_list.php';

if(isset($_POST['colno'])){$displayfields_no=$_POST['colno'];}
for($dindex=0;$dindex < ($displayfields_no);$dindex++){
	if(isset($_POST['displayfield'.$dindex])){$displayfields[$dindex]=$_POST['displayfield'.$dindex];}
	}

$extrabuttons['delete']=array('name'=>'current','value'=>'column_save_action.php');

three_buttonmenu($extrabuttons);
?>

  <div id="heading">
	<label><?php print_string('saveview',$book);?></label>
  </div>

  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="divgroup center">
		<legend>Add new view</legend>
		<div class="center">
		  <label for="name"><?php print_string('name',$book);?></label>
		  <input type="text" name="name" id="name" size="60" value="" />
		  <label for="type"><?php print_string('type',$book);?></label>
		  <select name="type">
		  	<option></option>
		  	<option value="full"><?php print_string('full',$book);?></option>
		  	<option value="summary"><?php print_string('summary',$book);?></option>
		  </select>
		</div>
	  </fieldset>



	<div class="center">
	  <table class="listmenu">
		<tr>
		  <th class="checkall">
		  <input type="checkbox" name="checkall" value="yes" onChange="checkAll(this);" />
		  </th>
		  <th>
			Existing views
		  </th>
		</tr>
<?php
	$d_c=mysql_query("SELECT id, name, comment FROM categorydef WHERE type='col';");
	while($cat=mysql_fetch_array($d_c,MYSQL_ASSOC)){
?>
		<tr>
		  <td>
			<input type="checkbox" name="catids[]" value="<?php print $cat['id'];?>" />
		  </td>
<?php
		print '<td>'.$cat['name'].'</td>';
		print '</tr>';
		}
?>
	  </table>
	</div>



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
