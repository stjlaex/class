<?php 
/**				   							responsables.php
 */

$choice='responsables.php';
$action='responsables_action.php';

$users=list_all_users(0);
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

<?php
	foreach($users as $uid => $user){
		if($user['username']!='administrator'){
			$uid=$user['uid'];
			$d_group=mysql_query("SELECT * FROM groups LEFT JOIN perms ON
			perms.gid=groups.gid WHERE perms.uid='$uid' AND
			groups.yeargroup_id IS NOT NULL");
			$user['pastoral'][]=array();
			while($group=mysql_fetch_array($d_group,MYSQL_ASSOC)){
				if($group['gid']>0){$user['pastoral'][]=$group;}
				}
			$d_group=mysql_query("SELECT * FROM groups LEFT JOIN perms ON
				perms.gid=groups.gid WHERE perms.uid='$uid' AND
				groups.yeargroup_id IS NULL");
			$user['academic'][]=array();
			while($group=mysql_fetch_array($d_group,MYSQL_ASSOC)){
				if($group['gid']>0){$user['academic'][]=$group;}
				}
			}
		$users[$uid]=$user;
		}
?>
  <div class="content">
	<div class="center">
	  <table class="listmenu">
	  <tr>
		<th><?php print_string('users',$book);?></th>
		<th style="width:25%;"><?php print_string('pastoralresponsibilities',$book);?></th>
		<th><?php print_string('academicresponsibilities',$book);?></th>
	  </tr>
<?php
	foreach($users as $uid => $user){
		if($user['username']!='administrator' and 
		   (sizeof($user['pastoral'])>1 or sizeof($user['academic'])>1)){
			$uid=$user['uid'];
?>
	  <tr>
		<td>
		  <?php print $user['username'].' ('.$user['surname'].')';?>
		</td>
		<td>
<?php
			foreach($user['pastoral'] as $index=>$group){
				$gid=$group['gid'];
				if($gid>0){
					$yid=$group['yeargroup_id'];
					$Responsible=array('id_db'=>$yid.'-'.$uid);
					$perms=getYearPerm($yid, $respons);
					if($perms['x']==1){
?>
			<div id="<?php print $yid.'-'.$uid;?>" class="rowaction" >
			  <button class="rowaction" title="Remove this responsibility"
				name="current" 
				value="responsables_edit_yeargroup.php" 
				onClick="clickToAction(this)">
				<?php print $group['name'];?>
			  </button>
			  <div id="<?php print 'xml-'.$yid.'-'.$uid;?>" style="display:none;">
							  <?php xmlechoer('Responsible',$Responsible);?>
			  </div>
			</div>
<?php
					  }
					else{
						print $group['name'].' ';
						}
					}
				}
?>
		</td>
		<td>
<?php
			foreach($user['academic'] as $index=>$group){
				$gid=$group['gid'];
				if($gid>0){
					$crid=$group['course_id'];
					$bid=$group['subject_id'];
					$Responsible=array('id_db'=>$crid.'-'.$bid.'-'.$uid);
					$perms=getCoursePerm($crid, $respons);
					if($perms['x']==1){
?>
			<div id="<?php print $crid.'-'.$bid.'-'.$uid;?>" class="rowaction" >
			  <button title="Remove this responsibility"
				name="current"
				value="responsables_edit_course.php" 
				onClick="clickToAction(this)">
					 <?php print $group['name'];?>
			  </button>
			  <div id="<?php print 'xml-'.$crid.'-'.$bid.'-'.$uid;?>" style="display:none;">
							  <?php xmlechoer('Responsible',$Responsible);?>
			  </div>
			</div>
<?php
						}
					else{
						print $group['name'].' ';
						}
					}
				}
?>
		</td>
	  </tr>
<?php
			}
		}
?>
	</table>
	</div>
  </div>
</div>
