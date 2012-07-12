<?php 
/**								staff_list.php
 *
 */

$choice='staff_list.php';
$action='staff_list_action.php';
if(isset($_POST['listsecids'])){$listsecids=$_POST['listsecids'];}else{$listsecids=array();}
if(isset($_POST['listroles'])){$listroles=$_POST['listroles'];}else{$listroles=array();}


$users=(array)list_all_users('0');
//$users=list_responsible_users($tid,$respons,$r);
$aperm=get_admin_perm('u',$_SESSION['uid']);

if(($_SESSION['role']=='admin' or $aperm==1)){
	$nologin_users=list_all_users('1');
	$aperm=1;
	}

?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post" novalidate action="<?php print $host; ?>">

	  <fieldset class="center divgroup">
		<legend><?php print get_string('section',$book);?></legend>
		  <button style="float:right;"  type="submit" name="sub" value="list">
			<?php print_string('list');?>
		  </button>
		<div class="center">
		  <table>
			<tr>
<?php
	$sections=list_sections();
	foreach($sections as $section){
		if(in_array($section['id'],$listsecids)){$checked=' checked="checked" ';}else{$checked='';}
		print '<td><input type="checkbox" name="listsecids[]" '.$checked.' value="'.$section['id'].'">'.$section['name'].'</input></td>';
		}
?>
			</tr>
		  </table>
		</div>
		<div class="center">
		  <table>
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
		</div>

	  </fieldset>


	  <fieldset class="center divgroup" id="viewcontent">
		<legend><?php print get_string('staff',$book);?></legend>
		<table class="listmenu">
		  <tr>
			<th><?php print_string('surname',$book);?></th>
			<th><?php print_string('forename',$book);?></th>
			<th><?php print_string('username');?></th>
			<th><?php print_string('email',$book);?></th>
			<th><?php print_string('role',$book);?></th>
		  </tr>

<?php
		foreach($users as $user){
			if(in_array($user['role'],$listroles) or sizeof($listroles)==0){
?>
		<tr>
		  <td><?php print $user['surname'];?></td>
		  <td><?php print $user['forename'];?></td>
		  <td>
<?php
			if($aperm==1 or $user['uid']==$_SESSION['uid']){
				print '<a href="admin.php?current=staff_details.php&cancel='.$choice.'&choice='.$choice.'&seluid='.$user['uid'].'">'.$user['username'].'</a>';
				}
			else{
				print $user['username'];
				}
?>
		  </td>
		  <td><?php print $user['email'];?></td>
			<td><?php print get_string($user['role']);?></td>
		</tr>
<?php
					}
				}
?>

	  </table>
	</fieldset>


	  <input type="hidden" name="current" value="<?php print $action; ?>">
	  <input type="hidden" name="choice" value="<?php print $choice; ?>">
	  <input type="hidden" name="cancel" value="<?php print ''; ?>">
	</form>
  </div>
