<?php 
/**								staff_list.php
 *
 */

$choice='staff_list.php';
$action='staff_list_action.php';
if(isset($_POST['listsecids'])){$listsecids=$_POST['listsecids'];}else{$listsecids=array();}
if(isset($_POST['listroles'])){$listroles=$_POST['listroles'];}else{$listroles=array();}
if(isset($_POST['listoption'])){$listoption=$_POST['listoption'];}else{$listoption='current';}

/* Super user perms for user accounts. */ 
$aperm=get_admin_perm('u',$_SESSION['uid']);

?>
  <div class="topcontent divgroup" style="font-size:small;">
	<form name="formtoprocess" id="formtoprocess" method="post" novalidate action="<?php print $host; ?>">
		  <label><?php print get_string('section',$book);?></label>
		  <table class="center">
			<tr>
<?php
	$sections=list_sections();
	foreach($sections as $section){
		if(in_array($section['id'],$listsecids)){$checked=' checked="checked" ';$selsection=$section;}else{$checked='';}
		print '<td><input type="radio" name="listsecids[]" '.$checked.' value="'.$section['id'].'">'.$section['name'].'</input></td>';
		}
	if(sizeof($listsecids)==0){$checked=' checked="checked" ';}else{$checked='';}
?>
			<td><input type="radio" name="listsecids[]" <?php print $checked;?> value="uncheck"><?php print_string('all',$book);?></input></td>
			</tr>
		  </table>

		  <label><?php print get_string('role',$book);?></label>
		  <table class="center">
			<tr>
<?php
	$userroles=$CFG->roles;
	foreach($userroles as $userrole){
		if(in_array($userrole,$listroles)){$checked=' checked="checked" ';}else{$checked='';}
		print '<td><input type="checkbox" name="listroles[]" '.$checked.' value="'.$userrole.'">'.get_string($userrole).'</input></td>';
		}
?>
			</tr>
		  </table>

<?php
	if($aperm==1){
?>
		  <table class="left">
			<tr>
<?php
	$options=array(array('id'=>'current','name'=>'current'),array('id'=>'previous','name'=>'previous'));
	foreach($options as $option){
		if($option['id']==$listoption){$checked=' checked="checked" ';}else{$checked='';}
		print '<td><input type="radio" name="listoption" '.$checked.' value="'.$option['id'].'">'.get_string($option['name'],$book).get_string('staff',$book).'</input></td>';
		}
?>
			</tr>
		  </table>
<?php
		}
?>
		<div style="float:right;">
		  <button style="font-size:small;"  type="submit" name="sub" value="list">
			<?php print_string('filterlist');?>
		  </button>
		</div>
	  <input type="hidden" name="current" value="<?php print $action; ?>">
	  <input type="hidden" name="choice" value="<?php print $choice; ?>">
	  <input type="hidden" name="cancel" value="<?php print ''; ?>">
	</form>
  </div>

  <div class="content" id="viewcontent" style="height:70%;">
	<fieldset class="divgroup">
	  <legend><?php print get_string($listoption,$book).' '.get_string('staff',$book);?></legend>
	  <table class="listmenu center">
		  <tr>
			<th><?php print_string('surname',$book);?></th>
			<th><?php print_string('forename',$book);?></th>
			<th><?php print_string('username');?></th>
			<th><?php print_string('email',$book);?></th>
			<th><?php print_string('role',$book);?></th>
		  </tr>

<?php


	if($aperm==1 and $listoption=='previous'){
		$nologin='1';
		}
	else{
		$nologin='0';
		}

	if(isset($selsection)){
		$users=(array)list_group_users_perms($selsection['gid'],$nologin);
		}
	else{
		$users=(array)list_all_users($nologin);
		//$users=list_responsible_users($tid,$respons,$r);
		}



	foreach($users as $user){
			$User=(array)fetchUser($user['uid']);
			if((in_array($user['role'],$listroles) or sizeof($listroles)==0) and $user['username']!='administrator'){
?>
		<tr>
		  <td>
<?php
			if($aperm==1 or $user['uid']==$_SESSION['uid'] or $role==$_SESSION['office']){
				print '<a href="admin.php?current=staff_details.php&cancel='.$choice.'&choice='.$choice.'&seluid='.$user['uid'].'">'.$User['Surname']['value'].'</a>';
				}
			else{
				print $User['Surname']['value'];
				}
?>
		  </td>
		  <td><?php print $User['Forename']['value'];?></td>
		  <td>
		  <?php print $User['Username']['value'];?>
		  </td>
		  <td><?php print $User['EmailAddress']['value'];?></td>
			<td><?php print get_string($User['Role']['value']);?></td>
		</tr>
<?php
					}
				}
?>

	  </table>
  </fieldset>
  </div>
