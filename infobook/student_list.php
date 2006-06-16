<?php
/**									student_list.php
 *   	Lists students identified by their ids in array sids.
 */

$action='student_view.php';
$choice='student_list.php';

two_buttonmenu(); 
?>

<div id="viewcontent" class="content">
<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
<table class="listmenu">
	<th><?php print_string('checkall'); ?><input type="checkbox" name="checkall" 
				value="yes" onChange="checkAll(this);" /></th>
	<th><?php print_string('student'); ?></th>
	<th><?php print_string('gender'); ?></th>
	<th><?php print_string('dateofbirth'); ?></th>
	<th><?php print_string('formgroup'); ?></th>
<?php
	$students=array();
	$tomonth=date('n')-1;/*highlights comments for past month, needs sohpisticating!!!*/
	$commentdate=date('Y')."-".$tomonth."-".date('j');
	
	while(list($index,$sid)=each($sids)){
		$d_student=mysql_query("SELECT * FROM student WHERE id='$sid'");
		$student=mysql_fetch_array($d_student,MYSQL_ASSOC);
		$students[$sid]=$student;
		$comment=commentDisplay($sid,$commentdate);
?>
		<tr>
		  <td>
			<input type="checkbox" name="sids[]" value="<?php print $sid; ?>" />
			&nbsp
			<a href='infobook.php?current=student_scores.php&sid=<?php print $sid;?>'>T</a> 
			<a href='infobook.php?current=comments_list.php&sid=<?php print $sid;?>'
			  <?php print " class='".$comment['class']."' title='".$comment['body']."'"; ?>>C</a> 
			<a href='infobook.php?current=incidents_list.php&sid=<?php print $sid;?>'>I</a>
		  </td>
		  <td>
			<a href='infobook.php?current=student_view.php&sid=<?php print $sid;?>'>
			  <?php print $student['surname']; ?>, 
			  <?php print $student['forename']; ?>
			  <?php if($student['preferredforename']!=''){print ' ('.$student['preferredforename'].')';}; ?>
			</a>
		  </td>
		  <td><?php print $student['gender']; ?></td>	
		  <td><?php print $student['dob']; ?></td>	
		  <td><?php print $student['form_id']; ?></td>
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