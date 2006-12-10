<?php
/**									student_list.php
 *   	Lists students identified by their ids in array sids.
 */

$action='student_list.php';
$choice='student_list.php';

include('scripts/sub_action.php');

$displayfields=array();
$displayfields[]='RegistrationGroup';$displayfields[]='Gender';$displayfields[]='DOB';
if(isset($_POST['displayfield'])){$displayfields[0]=$_POST['displayfield'];}
if(isset($_POST['displayfield1'])){$displayfields[1]=$_POST['displayfield1'];}
if(isset($_POST['displayfield2'])){$displayfields[2]=$_POST['displayfield2'];}

two_buttonmenu();

?>

<div id="viewcontent" class="content">
<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
<table class="listmenu sidtable">
	<th colspan="2"><?php print_string('checkall'); ?><input type="checkbox" name="checkall" 
				value="yes" onChange="checkAll(this);" /></th>
	<th><?php print_string('student'); ?></th>
<?php
	while(list($index,$displayfield)=each($displayfields)){
?>
		<th><?php include('scripts/list_studentfield.php');?></th>
<?php
		}

	$rown=1;
	while(list($index,$sid)=each($sids)){
		$Student=fetchStudent_short($sid);
		$comment=commentDisplay($sid);
?>
		<tr>
		  <td>
			<input type="checkbox" name="sids[]" value="<?php print $sid;?>" />
			<?php print $rown++;?>
		  </td>
		  <td>
			<a href='infobook.php?current=student_scores.php&sid=<?php print $sid;?>'>T</a> 
			<span <?php print ' title="'.$comment['body'].'"';?>>
			  <a href='infobook.php?current=comments_list.php&sid=<?php print $sid;?>'
				<?php print ' class="'.$comment['class'].'" ';?>>C</a> 
			</span>
			<a href='infobook.php?current=incidents_list.php&sid=<?php print $sid;?>'>I</a>
		  </td>
		  <td>
			<a href='infobook.php?current=student_view.php&sid=<?php print $sid;?>'>
			  <?php print $Student['DisplayFullName']['value']; ?>
			</a>
		  </td>
<?php
	reset($displayfields);
	while(list($index,$displayfield)=each($displayfields)){
		if(array_key_exists($displayfield,$Student)){
			print '<td>'.$Student[$displayfield]['value'].'</td>';
			}
		else{
			$field=fetchStudent_singlefield($sid,$displayfield);
			print '<td>'.$field[$displayfield]['value'].'</td>';
			}
		}
?>
		</tr>
<?php
		}
	reset($sids);
?>
	  </table>
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
  </div>