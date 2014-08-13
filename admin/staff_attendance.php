<?php 
/**								staff_attendance.php
 *
 */

$choice='staff_list.php';
$action='staff_attendance_action.php';
$cancel='staff_list.php';

$today=date("Y-m-d");

$extrabuttons['export']=array('name'=>'sub','value'=>'export');

three_buttonmenu($extrabuttons);
?>

	<div class="content" id="viewcontent">
		<fieldset class="divgroup">
			<div class="center">
				<form name="formtoprocess" id="formtoprocess" method="post" novalidate action="<?php print $host; ?>">
					<h5><?php print get_string($listoption,$book).' '.get_string('staff',$book);?></h5>
					<div class="right">
						<?php $required='no'; include('scripts/jsdate-form.php');?>
					</div>
						<table id="sidtable" class="listmenu sidtable">
							<thead>
								<tr>
									<!--th style="width:1em;" class="checkall">
										<input type="checkbox" name="checkall" value="yes" onChange="checkAll(this,'uids[]');" />
									</th-->
									<th>
										<div class="div-sortable">
											<span style="display: inline-block; margin-right: 10px;"><?php print_string('surname',$book);?></span>
											<a class="sortable"></a>
										</div>
									</th>
									<th>
										<div class="div-sortable">
											<span style="display: inline-block; margin-right: 10px;"><?php print_string('forename',$book);?></span>
											<a class="sortable"></a>
										</div>
									</th>
									<th>
										<div class="div-sortable">
											<span style="display: inline-block; margin-right: 10px;"><?php print_string('username');?></span>
											<a class="sortable"></a>
										</div>
									</th>
									<th>
										<div class="div-sortable">
											<span style="display: inline-block; margin-right: 10px;"><?php print_string('attendance');?></span>
											<a class="sortable"></a>
										</div>
									</th>
								</tr>
							</thead>
<?php

$users=(array)list_all_users(0);

foreach($users as $user){
	$User=(array)fetchUser($user['uid']);
	$username=$user['username'];
	if((in_array($user['role'],$listroles) or sizeof($listroles)==0) and $user['username']!='administrator'){

		$d_ua=mysql_query("SELECT * FROM user_attendance WHERE username='$username' AND date='$today';");
		$attendancecomment=mysql_result($d_ua,0,'comment');
		$attendancestatus=mysql_result($d_ua,0,'status');

		if($attendancestatus=='a'){$rowclass='staffabsent';}
		else{$rowclass='';}

		if($aperm==1 or $uid==$_SESSION['uid'] or $_SESSION['role']=='office'){
			print '<tr class="clickrow '.$rowclass.'" onclick="window.location.href=\'admin.php?current=staff_details.php&cancel='.$choice.'&choice='.$choice.'&seluid='.$user['uid'].'\';">';
			}
		else{
			print '<tr class="'.$rowclass.'">';
			}
?>
					<!--td>
						<input type="checkbox" name="uids[]" value="<?php print $user['uid'];?>" />
					</td-->
					<td><?php print $User['Surname']['value'];?></td>
					<td><?php print $User['Forename']['value'];?></td>
					<td><?php print $User['Username']['value'];?></td>
					<td>
						<input type="hidden" name="usernames[]" value="<?php echo $username;?>" />
						<select name="attendancestatus-<?php print $username;?>" >
							<option value="p" <?php if($attendancestatus=='' or $attendancestatus=='p'){echo "selected";}?>><?php print_string('present','register');?></option>
							<option value="a" <?php if($attendancestatus=='a'){echo "selected";}?>><?php print_string('absent',$book);?></option>
						</select>
						<input type="text" name="attendancecomment-<?php echo $username;?>" value="<?php echo $attendancecomment;?>"/></td>
				</tr>
<?php
		}
	}
?>

			</table>

			<input type="hidden" name="current" value="<?php print $action; ?>" />
			<input type="hidden" name="choice" value="<?php print $choice; ?>" />
			<input type="hidden" name="cancel" value="<?php print $cancel; ?>" />
		</form>
	</div>


</div>
