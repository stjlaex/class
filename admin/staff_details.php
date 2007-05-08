<?php 
/**								staff_details.php
 */

$choice='staff_details.php';
$action='staff_details_action.php';

if(isset($_POST['seluid'])){$seluid=$_POST['seluid'];}
else{$seluid=$_SESSION['uid'];/*by default display logged in user*/}

$users=array();
$users=list_responsible_users($tid,$respons,$r);
if($_SESSION['role']=='admin' and sizeof($users)==0){
	$users=list_all_users();
	}
elseif(sizeof($users)==0){
	$user=get_user($tid);
	$uid=$user['uid'];
	$users[$uid]=$user;
	}

three_buttonmenu();
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host; ?>">

	  <fieldset class="left">
		<legend><?php print_string('selectstafftoedit',$book);?></legend>
		<label for="Staff"><?php print_string('username');?></label>
		<div>
		  <select tabindex="<?php print $tab++;?>" 
			name="newuid" size="20" onChange="processContent(this);">
<?php
   foreach($users as $uid => $user){
	   if($user['username']!='administrator'){
			print '<option ';
			if($uid==$seluid){print 'selected="selected"';}
			print	' value="'.$uid.'">'.$user['username'].'  ('.$user['surname'].')</option>';
			}
	   }
?>
		  </select>
		</div>
	  </fieldset>

<?php $user=$users[$seluid]; ?>

	  <fieldset class="right">
		<legend><?php print_string('changedetails',$book);?></legend>

		<div class="center">
		  <label for="ID"><?php print_string('username');?></label>
		  <input pattern="truealphanumeric" readonly="readonly"  
				type="text" id="ID" name="username"  
				maxlength="14" value="<?php print $user['username'];?>" />
		</div>

		<div class="center">
		  <label for="Surname"><?php print_string('surname');?></label>
		  <input class="required" 
			type="text" id="Surname" name="surname" maxlength="30"
		  value="<?php print $user['surname'];?>" tabindex="<?php print $tab++;?>" />  

			<label for="Forename"><?php print_string('forename');?></label>
			<input class="required" 
			  type="text" id="Forename" name="forename" 
			  maxlength="30" value="<?php print $user['forename'];?>" 
			  tabindex="<?php print $tab++;?>" />

			  <label for="Email"><?php print_string('email');?></label>
			  <input pattern="email"
				type="text" id="Email" name="email" 
				maxlength="190" style="width:90%;" 
				tabindex="<?php print $tab++;?>" 
				value="<?php print $user['email'];?>" />

<?php 

			  /*this is stored encrypted and need to decrypt before
				posting back the value*/
				$emailpasswd=endecrypt($CFG->webmailshare,$user['emailpasswd'],'de');

?>
			  <label for="Emailpasswd"><?php print_string('emailpassword',$book);?></label>
			  <input pattern="truealphanumeric"
				type="password" id="Emailpasswd" name="emailpasswd" 
				maxlength="32" style="width:20%;" 
				tabindex="<?php print $tab++;?>" 
				value="<?php print $emailpasswd;?>" />

			  <label><?php print_string('firstbookpref',$book);?></label>
				<?php $selbook=$user['firstbookpref'];?>
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
	foreach($worklevels as $key => $worklevel){
			print '<option ';
			if(isset($user['worklevel'])){if($user['worklevel']==$key){print 'selected="selected"';}}
			print	' value="'.$key.'">'.get_string($worklevel,$book).'</option>';
		}
?>
		  </select>

		  <?php $selrole=$user['role']; include('scripts/list_roles.php');?>

		  <label for="Number1"><?php print_string('newstaffpin',$book);?></label>
		  <input pattern="integer" tabindex="<?php print $tab++;?>" 
			  type="password" id="Number1" name="pin1" pattern="integer" 
			  maxlength="4" style="width:20%;" />

		  <label for="Number2"><?php print_string('retypenewstaffpin',$book);?></label>
		  <input pattern="integer" tabindex="<?php print $tab++;?>" 
				type="password" id="Number2" name="pin2" pattern="integer" 
				maxlength="4" style="width:20%;" />

		  <label for="Nologin"><?php print_string('disablelogin',$book);?></label>
		  <input type="checkbox" id="Nologin" class="required" 
				  name="nologin"  tabindex="<?php print $tab++;?>" 
				  <?php if($user['nologin']=='1'){print 'checked="checked"';}?> value="1"/>

		</div>
<?php
		}
else{
?>
	  <input type="hidden" name="role" value="<?php print $user['role']; ?>">
	  <input type="hidden" name="worklevel" value="<?php print $user['worklevel']; ?>">
	  <input type="hidden" name="nologin" value="<?php print $user['nologin']; ?>">
<?php
	}
?>

	  </fieldset>

	  <input type="hidden" name="current" value="<?php print $action; ?>">
	  <input type="hidden" name="choice" value="<?php print $choice; ?>">
	  <input type="hidden" name="cancel" value="<?php print ''; ?>">
	</form>
  </div>
