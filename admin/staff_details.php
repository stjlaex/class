<?php 
/**								staff_details.php
 *
 */

$choice='staff_details.php';
$action='staff_details_action.php';

if(isset($_POST['seluid'])){$seluid=$_POST['seluid'];}
else{$seluid=$_SESSION['uid'];/* By default display logged in user. */}

$users=array();
$users=list_responsible_users($tid,$respons,$r);
if($_SESSION['role']=='admin' and sizeof($users)==0){
	$users=list_all_users('0');
	$nologin_users=list_all_users('1');
	}
elseif(sizeof($users)==0){
	$user=get_user($tid);
	$uid=$user['uid'];
	$users[$uid]=$user;
	}

/*This is the record being edited.*/
$edituser=get_user($seluid,'uid');

three_buttonmenu();
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host; ?>">

	  <fieldset class="left divgroup">
		<legend><?php print_string('selectstafftoedit',$book);?></legend>

		<div class="center">
		<label><?php print_string('username');?></label>
		  <select tabindex="<?php print $tab++;?>" 
			name="newuid0" id="Staff" size="1" onChange="processContent(this);">
			<option value="" selected="selected"></option>
<?php
   $sort_array[0]['name']='username';
   $sort_array[0]['sort']='ASC';
   $sort_array[0]['case']=TRUE;
   sortx($users,$sort_array);
   reset($users);
   foreach($users as $uid => $user){
	   if($user['username']!='administrator'){
			print '<option ';
			print	' value="'.$user['uid'].'">'.$user['username'].'  ('.$user['surname'].')</option>';
			}
	   }
?>
		  </select>
		</div>


		<div class="center">
		<label><?php print_string('surname');?></label>
		  <select tabindex="<?php print $tab++;?>" 
			name="newuid1" size="1" onChange="processContent(this);">
			<option value=""></option>
<?php
   $sort_array[0]['name']='surname';
   $sort_array[0]['sort']='ASC';
   $sort_array[0]['case']=TRUE;
   sortx($users,$sort_array);
   reset($users);
   foreach($users as $uid => $user){
	   if($user['username']!='administrator'){
			print '<option ';
			print	' value="'.$user['uid'].'">'.$user['surname'].'  ('.$user['username'].')</option>';
			}
	   }
?>
		  </select>
		</div>

<?php
	if(isset($nologin_users)){
?>
		<br />
		  <br />
		<div class="center">
		<label><?php print get_string('disablelogin',$book).' '.get_string('username');?></label>
		  <select tabindex="<?php print $tab++;?>" 
			name="newuid2" size="1" onChange="processContent(this);">
			<option value=""></option>
<?php
		foreach($nologin_users as $uid => $user){
			print '<option ';
			print ' value="'.$user['uid'].'">' .$user['username']. 
							'  ('.$user['surname'].')</option>';
			}
?>
		  </select>
		</div>
<?php
		}
?>
	  </fieldset>


	  <fieldset class="right">
		<legend><?php print_string('changedetails',$book);?></legend>

		<div class="center">
		  <label for="ID"><?php print_string('username');?></label>
		  <input pattern="truealphanumeric" readonly="readonly"  
				type="text" id="ID" name="username"  
				maxlength="14" value="<?php print $edituser['username'];?>" />
		</div>

		<div class="center">
		  <label for="Surname"><?php print_string('surname');?></label>
		  <input class="required" 
			type="text" id="Surname" name="surname" maxlength="30"
		  value="<?php print $edituser['surname'];?>" tabindex="<?php print $tab++;?>" />  

			<label for="Forename"><?php print_string('forename');?></label>
			<input class="required" 
			  type="text" id="Forename" name="forename" 
			  maxlength="30" value="<?php print $edituser['forename'];?>" 
			  tabindex="<?php print $tab++;?>" />

<?php
$listname='title';
$listlabel='title';
$seltitle=$edituser['title'];
include('scripts/set_list_vars.php');
$tab=list_select_enum('title',$listoptions,'infobook');
unset($listoptions);
?>

			  <label for="Email"><?php print_string('email');?></label>
			  <input pattern="email"
				type="text" id="Email" name="email" 
				maxlength="190" style="width:90%;" 
				tabindex="<?php print $tab++;?>" 
				value="<?php print $edituser['email'];?>" />

<?php 

			  /*this is stored encrypted and need to decrypt before
				posting back the value*/
				$emailpasswd=endecrypt($CFG->webmailshare,$edituser['emailpasswd'],'de');

?>
			  <label for="Emailpasswd"><?php print_string('emailpassword',$book);?></label>
			  <input pattern="truealphanumeric"
				type="password" id="Emailpasswd" name="emailpasswd" 
				maxlength="32" style="width:20%;" 
				tabindex="<?php print $tab++;?>" 
				value="<?php print $emailpasswd;?>" />

			  <label><?php print_string('firstbookpref',$book);?></label>
				<?php $selbook=$edituser['firstbookpref'];?>
				<?php include('scripts/list_books.php'); ?>
		</div>

<?php
if($tid=='administrator' or $_SESSION['role']=='admin'){
?>
		<div class="center">
		  <label for="Work level"><?php print_string('workexperiencelevel',$book);?></label>
		  <select name="worklevel" id="Worklevel" size="1" tabindex="<?php print $tab++;?>" 
			class="required" >
			<option value=""></option>
<?php
		$worklevels=array('-1'=>'useless','0'=>'tryharder','1'=>'good', 
					  '2'=>'verygood','3'=>'teacherspet');
		foreach($worklevels as $index => $worklevel){
			print '<option ';
			if(isset($edituser['worklevel'])){if($edituser['worklevel']==$index){print 'selected="selected"';}}
			print	' value="'.$index.'">'.get_string($worklevel,$book).'</option>';
			}
?>
		  </select>

		  <?php $selrole=$edituser['role']; include('scripts/list_roles.php');?>

<?php
		  unset($key);
		  $listname='senrole';$selsenrole=$edituser['senrole'];$listlabel='senrole';
		  include('scripts/set_list_vars.php');
		  $list[]=array('id'=>'0','name'=>get_string('no'));
		  $list[]=array('id'=>'1','name'=>get_string('yes'));
		  list_select_list($list,$listoptions);
?>

		  <label for="Number1"><?php print_string('newstaffpin',$book);?></label>
		  <input pattern="integer" tabindex="<?php print $tab++;?>" 
			  type="password" id="Number1" name="pin1" pattern="integer" 
			  maxlength="4" style="width:20%;" />

		  <label for="Number2"><?php print_string('retypenewstaffpin',$book);?></label>
		  <input pattern="integer" tabindex="<?php print $tab++;?>" 
				type="password" id="Number2" name="pin2" pattern="integer" 
				maxlength="4" style="width:20%;" />

		  <label for="Nologin"><?php print_string('disablelogin',$book);?></label>
		  <input type="checkbox" id="Nologin"  
				  name="nologin"  tabindex="<?php print $tab++;?>" 
				  <?php if($edituser['nologin']=='1'){print 'checked="checked"';}?> value="1"/>

		</div>
<?php
		}
	else{
?>
	  <input type="hidden" name="role" value="<?php print $edituser['role']; ?>">
	  <input type="hidden" name="senrole" value="<?php print $edituser['senrole']; ?>">
	  <input type="hidden" name="worklevel" value="<?php print $edituser['worklevel']; ?>">
	  <input type="hidden" name="nologin" value="<?php print $edituser['nologin']; ?>">
<?php
		}
?>

	  </fieldset>

	  <input type="hidden" name="seluid" value="<?php print $seluid; ?>">
	  <input type="hidden" name="current" value="<?php print $action; ?>">
	  <input type="hidden" name="choice" value="<?php print $choice; ?>">
	  <input type="hidden" name="cancel" value="<?php print ''; ?>">
	</form>
  </div>
