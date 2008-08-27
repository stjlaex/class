<?php 
/**								  		formgroup_matrix.php
 */

$choice='formgroup_matrix.php';
$action='formgroup_matrix_action.php';

three_buttonmenu();
?>
  <div class="content">
	  <form id="formtoprocess" name="formtoprocess" method="post"
		action="<?php print $host; ?>" >

	  <fieldset class="right">
		  <legend><?php print_string('assignformtoteacher',$book);?></legend>

		<div class="center">
<?php $required='yes'; include('scripts/list_teacher.php');?>
		</div>

		<div class="center">
		  <label for="Forms" ><?php print_string('unassignedformgroups',$book);?></label>
		  <select id="Forms" name="newfid" size="1" class="required" 
			tabindex="<?php print $tab++;?>" 
			style="width:95%;">
<?php
  	$d_form=mysql_query("SELECT id FROM form WHERE teacher_id='' OR
					teacher_id IS NULL ORDER BY yeargroup_id"); 
   	while($form=mysql_fetch_array($d_form,MYSQL_ASSOC)){
   		print '<option ';
		print	' value="'.$form['id'].'">'.$form['id'].'</option>';
		}
?>		
		  </select>
		</div>
	  </fieldset>
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
      <input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>

	<div class="left"  id="viewcontent">
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('formgroup');?></th>
		  <th><?php print_string('numberofstudents',$book);?></th>
		  <th><?php print_string('formtutor',$book);?></th>
		</tr>
<?php
	$d_form=mysql_query("SELECT * FROM form ORDER BY yeargroup_id, id");
	while($form=mysql_fetch_array($d_form,MYSQL_ASSOC)){
		$fid=$form['id'];
		$yid=$form['yeargroup_id'];
		$tid=$form['teacher_id'];
		$d_groups=mysql_query("SELECT gid FROM groups WHERE
				yeargroup_id='$yid' AND course_id=''");
		$gid=mysql_result($d_groups,0);
		$perms=getFormPerm($fid, $respons);
		$nosids=countin_community(array('type'=>'form','name'=>$fid));
?>
		<tr>
		  <td>
<?php
		if($perms['r']==1){
			print '<a href="admin.php?current=form_edit.php&cancel='.$choice.'&choice='.$choice.'&newtid='.$tid.'&newfid='.$fid.'">'.$fid.'</a>';
			}
		else{
	   		print $fid;
	   		}
?>
		  </td>
		  <td><?php print $nosids;?></td>
		  <td>
<?php
		if($perms['w']==1 and $tid!=''){
			$uid=get_uid($tid);
			$Responsible=array('id_db'=>$fid.'-'.$uid);
?>
			<div  id="<?php print $fid.'-'.$uid;?>" class="rowaction" >
			  <button title="Remove this responsibility"
				name="current"
				value="responsables_edit_formgroup.php" 
				onClick="clickToAction(this)">
					 <?php print $tid;?>
			  </button>
			  <div id="<?php print 'xml-'.$fid.'-'.$uid;?>" style="display:none;">
							  <?php xmlechoer('Responsible',$Responsible);?>
			  </div>
			</div>
<?php
			}
		else{
			print $tid;
			}
?>
		  </td>
		</tr>
<?php
		}
?>

	  </table>
	</div>
  </div>