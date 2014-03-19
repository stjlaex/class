<?php 
/**				   							responsables.php
 */

$choice='responsables.php';
$action='responsables_action.php';

$users=list_all_users(0);
three_buttonmenu();
?>
    <div class="content">
        <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>"> 
            <div class="left">
                <fieldset class="divgroup">
                    <h5><?php print_string('staff',$book);?></h5>
    	           
                    <div class="center">
                        <label for="User"> <?php print_string('username',$book);?> </label>
                        <select class="required"  tabindex="<?php print $tab++;?>" name="user" id="User" size="1">	  	
                            <?php
                                print '<option value="" selected="selected"></option>';
                                foreach($users as $uid => $user){
                                	if($user['username']!='administrator'){
                                		print '<option ';
                                		print	' value="'.$uid.'">'.$user['username'].'  ('.$user['surname'].')</option>';
                                		}
                                	}
                            ?>
                        </select>
                    </div>
                    <div class="center">
        	            <label for="Permissions"><?php print_string('permissions',$book);?></label>
        	            <select class="required"   tabindex="<?php print $tab++;?>" name="privilege" id="Permissions" size="1">
                            <option value="" selected="selected"></option>
                		    <option value="r" ><?php print_string('canview',$book);?></option>
                		    <option value="w" ><?php print_string('canedit',$book);?></option>
                		    <option value="x" ><?php print_string('canconfigure',$book);?></option>
        	            </select>
    	            </div>
                    <div class="center">
                        <label for="email"><?php print_string('receiveemailalerts',$book);?></label>
                        <input type="checkbox"  tabindex="<?php print $tab++;?>" id="email" name="email" value="yes"/>
                    </div>
                </fieldset>
            </div>

	  <div class="right">
		<fieldset class="divgroup">
		  <h5><?php print_string('academicresponsibility',$book);?></h5>
		  <div class="left">
			<label for="Course"><?php print_string('course');?></label>
			<select id="Course"  tabindex="<?php print $tab++;?>" name="crid" size="1">
<?php
  	$d_group=mysql_query("SELECT id AS course_id FROM course ORDER BY course_id;"); 
	print '<option value="" selected="selected"></option>';
	print '<option value="%">';
	print_string('all',$book);
	print '</option>';
	while($group=mysql_fetch_array($d_group,MYSQL_ASSOC)){
			print '<option ';
			print	' value="'.$group['course_id'].'">'.$group['course_id'].'</option>';
			}
?>
			</select>
		  </div>
		  <div class="right">
			<label for="Subject"><?php print_string('subject');?></label>
			<select id="Subject"  tabindex="<?php print $tab++;?>" name="bid" size="1">
<?php
  	$d_group=mysql_query("SELECT DISTINCT subject_id FROM component WHERE id='' ORDER BY subject_id;"); 
	print '<option value="" selected="selected"></option>';
	print '<option value="%">';
	print_string('all');
	print '</option>';
	while($group=mysql_fetch_array($d_group,MYSQL_ASSOC)) {
			print '<option ';
			print	' value="'.$group['subject_id'].'">'.$group['subject_id'].'</option>';
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

<?php
	foreach($users as $uid => $user){
		if($user['username']!='administrator'){
			$d_g=mysql_query("SELECT DISTINCT groups.gid, groups.subject_id, groups.course_id FROM groups JOIN perms ON
				perms.gid=groups.gid WHERE perms.uid='$uid' AND groups.type='a' AND groups.yeargroup_id IS NULL;");
			$user['academic'][]=array();
			while($g=mysql_fetch_array($d_g,MYSQL_ASSOC)){
				$user['academic'][]=$g;
				}
			}
		$users[$uid]=$user;
		}
?>

	<div class="center" id="viewcontent">
	  <table class="listmenu">
	  <tr>
		<th><?php print_string('users',$book);?></th>
		<th><?php print_string('academicresponsibilities',$book);?></th>
	  </tr>
<?php
	foreach($users as $uid => $user){
		if($user['username']!='administrator' and sizeof($user['academic'])>1){
?>
	  <tr>
		<td>
		  <?php print $user['username'].' ('.$user['surname'].')';?>
		</td>
		  <td>
<?php
			foreach($user['academic'] as $group){
				if(isset($group['gid']) and $group['gid']>0){
					$gid=$group['gid'];
					$crid=$group['course_id'];
					$bid=$group['subject_id'];
					$Responsible=array('id_db'=>$crid.'-'.$bid.'-'.$uid);
					$perms=getCoursePerm($crid, $respons);
					if($crid=='%'){$name=$bid;}
					elseif($bid=='%'){$name=$crid;}
					else{$name=$crid.'/'.$bid;}

					if($perms['x']==1){
?>
			<div id="<?php print $crid.'-'.$bid.'-'.$uid;?>" class="rowaction" >
			  <button title="Remove this responsibility"
				name="current"
				value="responsables_edit_course.php" 
				onClick="clickToAction(this)">
					 <?php print $name;?>
			  </button>
			  <div id="<?php print 'xml-'.$crid.'-'.$bid.'-'.$uid;?>" style="display:none;">
							  <?php xmlechoer('Responsible',$Responsible);?>
			  </div>
			</div>
<?php
						}
					else{
						print $name.' ';
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
