<?php
/**								   updates_file.php
 *
 */

$action='updates_file_action.php';
$choice='student_list.php';


three_buttonmenu();
?>

  <div id="heading">
	<label><?php print_string('updatesfile',$book);?></label>
  </div>

  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="divgroup center">
		<legend>
			<?php print_string('update','admin'); ?>
		</legend>
		<div class="left">
		  <label for="name"><?php print_string('current',$book);?></label>
		  <input type="radio" name="update" id="current" value="1"  checked="yes" />
		</div>
		<div class="right">
		  <label for="name"><?php print_string('previous',$book);?></label>
		  <input type="radio" name="update" id="previous" value="2" />
		</div>   
	  </fieldset>



	<div class="center">
	  <table class="listmenu">
		<tr>
		  <th colspan="2">
			<?php print_string('selectview',$book); ?>
		  </th>
		</tr>
<?php
	$d_c=mysql_query("SELECT id, name, comment FROM categorydef WHERE type='col';");
	while($cat=mysql_fetch_array($d_c,MYSQL_ASSOC)){
?>
		<tr>
		  <td>
			<input type="radio" name="catid" value="<?php print $cat['id'];?>" />
		  </td>
<?php
		print '<td>'.$cat['name'].'</td>';
		print '</tr>';
		}
?>
		<tr>
		  <td colspan="2">
			<input type="radio" name="catid" value="uncheck" checked="yes"><?php print_string('uncheck',$book);?></input>
		  </td>
		</tr>
	  </table>
	</div>


<?php
if($tid=='administrator' or $tid=='classadmin'){
?>
	<br/>
	<div class="center">
	  <table class="listmenu">
		<tr>
		  <th colspan="2">
			<?php print_string('selectformat',$book); ?>
		  </th>
		</tr>
		<tr>
		  <td>
			<input type="radio" name="format" value="xls" checked="yes"/>
		  </td>
		  <td>
			<?php print_string('xls',$book); ?>
		  </td>
		</tr>
		<tr>
		  <td>
			<input type="radio" name="format" value="xml" />
		  </td>
		  <td>
			<?php print_string('xml',$book); ?>
		  </td>
		</tr>
<?php
	if(isset($CFG->ppod_api) and $CFG->ppod_api!=''){
?>
		<tr>
		  <td>
			<input type="radio" name="format" value="ppod" />
		  </td>
		  <td>
			PPOD
		  </td>
		</tr>
<?php
		}
?>
	  </table>
	</div>

<?php
	}
?>

	<input type="hidden" name="colno" value="<?php print $displayfields_no;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print 'student_list.php';?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
  </form>
  </div>
