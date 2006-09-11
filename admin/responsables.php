<?php 
/**				   							responsables.php
 */

$choice='responsables.php';
$action='responsables_action.php';

$users=getAllStaff(0);
three_buttonmenu();
?>
  <div class="topform">
	<form id="formtoprocess" name="formtoprocess"  
		method="post" action="<?php print $host;?>"> 

	  <div class="left">
		<p><?php print_string('chooseeitherresponsibility',$book);?></p>
		<label for="User">
		  <?php print_string('username',$book);?>
		</label>
		<select class="required"  tabindex="<?php print $tab++;?>" 
		  name="user" id="User" size="1">	  	
<?php
	print '<option value="" selected="selected"></option>';
	foreach($users as $uid => $user){
		if($user['username']!='administrator'){
			print '<option ';
			if($uid==$seluid){print 'selected="selected"';}
			print	' value="'.$uid.'">'.$user['username'].'  ('.$user['surname'].')</option>';
			}
		}
?>
		</select>

		<label for="Permissions">
		  <?php print_string('permissions',$book);?>
		</label>
		<select class="required"   tabindex="<?php print $tab++;?>" 
		  name="privilege" id="Permissions" size="1">
			  <option value="" selected="selected"></option>
			  <option value="r" ><?php print_string('canview',$book);?></option>
			  <option value="w" ><?php print_string('canedit',$book);?></option>
			  <option value="x" ><?php print_string('canconfigure',$book);?></option>
		</select>

		<label for="email"><?php print_string('receiveemailalerts',$book);?></label>
		<input type="checkbox"  tabindex="<?php print $tab++;?>" 
		  id="email" name="email" value="yes"/>
	  </div>

	  <div class="right">
		<fieldset class="center">
		  <legend><?php print_string('pastoralresponsibility',$book);?></legend>
		  <?php include('scripts/list_year.php');?>
		</fieldset>
		<fieldset class="center">
		  <legend><?php print_string('academicresponsibility',$book);?></legend>
		  <div class="left">
			<label for="Course"><?php print_string('course');?></label>
			<select id="Course"  tabindex="<?php print $tab++;?>" name="crid" size="1">
<?php
  	$d_group=mysql_query("SELECT * FROM groups WHERE subject_id='%' ORDER BY course_id"); 
	print '<option value="" selected="selected"></option>';
	print '<option value="%">';
	print_string('all',$book);
	print '</option>';
	while($group=mysql_fetch_array($d_group,MYSQL_ASSOC)){
			print '<option ';
			print	' value="'.$group{'course_id'}.'">'.$group{'course_id'}.'</option>';
			}
?>
			</select>
		  </div>
		  <div class="right">
			<label for="Subject"><?php print_string('subject');?></label>
			<select id="Subject"  tabindex="<?php print $tab++;?>" name="bid" size="1">
<?php
  	$d_group=mysql_query("SELECT * FROM groups WHERE course_id='%' ORDER BY subject_id"); 
	print '<option value="" selected="selected"></option>';
	print '<option value="%">';
	print_string('all');
	print '</option>';
	while($group=mysql_fetch_array($d_group,MYSQL_ASSOC)) {
			print '<option ';
			print	' value="'.$group['subject_id'].'">'.$group{'subject_id'}.'</option>';
			}
?>
			</select>
		  </div>
		</fieldset>
	  </div>
	    <input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
		<input type="hidden" name="cancel" value="<?php print '';?>">
	</form>
  </div>

  <div class="content">
	<table class="listmenu">
	  <tr>
		<th><?php print_string('users',$book);?></th>
		<th style="width:30%;"><?php print_string('pastoralresponsibilities',$book);?></th>
		<th><?php print_string('academicresponsibilities',$book);?></th>
	  </tr>
<?php
	foreach($users as $uid => $user){
		if($user['username']!='administrator'){
			$uid=$user['uid'];
			print '<tr>';
			print '<td>'.$user['username'].' ('.$user['surname'].')';
			print '</td><td>';
			$d_group=mysql_query("SELECT * FROM groups LEFT JOIN perms ON
			perms.gid=groups.gid WHERE perms.uid='$uid' AND
			groups.yeargroup_id IS NOT NULL");
			while($group=mysql_fetch_array($d_group,MYSQL_ASSOC)){
				$gid=$group['gid'];
				print '<a href="admin.php?current=responsables_edit_pastoral.php&uid='. 
					$uid.'&gid='.$gid.'&yid='.$group['yeargroup_id']. 
					'" >'.$group['name'].'</a>'; 
				}
			print '</td><td>';

			$d_group=mysql_query("SELECT * FROM groups LEFT JOIN perms ON
				perms.gid=groups.gid WHERE perms.uid='$uid' AND
				groups.yeargroup_id IS NULL");
			while($group=mysql_fetch_array($d_group,MYSQL_ASSOC)){
				$gid=$group['gid'];
				print '<a href="admin.php?current=responsables_edit.php&uid='. 
						$uid.'&gid='.$gid.'&crid='.$group['course_id'].'&bid='. 
						$group['subject_id'].'">'.$group['name'].'</a>'; 
				}
			print '</td>';
			print '</tr>';
			}
		}
?>
	</table>
  </div>
</div>
