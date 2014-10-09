	<div style="float:right;">
		<button onClick="processContent(this);" name="current" value="staff_attendance_action.php"><?php print_string("absent",$book);?></button>
	</div>
<?php
foreach($users as $user){
	$User=(array)fetchUser($user['uid']);
	if((in_array($user['role'],$listroles) or sizeof($listroles)==0) and $user['username']!='administrator'){

		$d_ua=mysql_query("SELECT * FROM user_attendance WHERE username='".$user['username']."' AND date='$today';");
		$attendancecomment=mysql_result($d_ua,0,'comment');
		$attendancestatus=mysql_result($d_ua,0,'status');

		if($attendancestatus=='a'){$rowclass='staffabsent';}
		else{$rowclass='';}

		if($aperm==1 or $user['uid']==$_SESSION['uid'] or $_SESSION['role']=='office'){
			print '<tr class="clickrow '.$rowclass.'" onclick="window.location.href=\'admin.php?current=staff_details.php&cancel='.$choice.'&choice='.$choice.'&seluid='.$user['uid'].'\';">';
			}
		else{
			print '<tr class="'.$rowclass.'">';
			}
?>
      <td>
		<input type="checkbox" name="uids[]" value="<?php print $user['uid'];?>" onclick="event.stopPropagation()"/>
      </td>
      <td><?php print $User['Surname']['value'];?></td>
      <td><?php print $User['Forename']['value'];?></td>
      <td><?php print $User['Username']['value'];?></td>
      <td><?php print $User['EmailAddress']['value'];?></td>
    </tr>
<?php
		}
	}
?>
