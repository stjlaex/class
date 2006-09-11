<?php 
/**								staff_details.php
 */

$choice='staff_details.php';
$action='staff_details_action.php';

if(isset($_POST['seluid'])){$seluid=$_POST['seluid'];}
else{$seluid=$_SESSION['uid'];}
$users=getResponStaff($tid,$respons,$r);
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
		  <input pattern="alphanumeric" readonly="readonly"  
				type="text" id="ID" name="username" 
				maxlength="14" value="<?php print $user['username'];?>" />
		</div>

		<div class="center">
		  <label for="Surname"><?php print_string('surname');?></label>
		  <input class="required" pattern="alphanumeric"
			type="text" id="Surname" name="surname" maxlength="30"
		  value="<?php print $user['surname'];?>" tabindex="<?php print $tab++;?>" />  

			<label for="Forename"><?php print_string('forename');?></label>
			<input class="required" pattern="alphanumeric"
			  type="text" id="Forename" name="forename" 
			  maxlength="30" value="<?php print $user['forename'];?>" 
			  tabindex="<?php print $tab++;?>" />

			  <label for="Email"><?php print_string('email');?></label>
			  <input pattern="email"
				type="text" id="Email" name="email" 
				maxlength="190" style="width:90%;" 
				tabindex="<?php print $tab++;?>" 
				value="<?php print $user['email'];?>" />

			  <label for="Firstbook"><?php print_string('firstbookpref',$book);?></label>
				<input pattern="alphanumeric" tabindex="<?php print $tab++;?>" 
				  type="text" id="Firstbook" name="firstbookpref" class="required"
				  maxlength="190" style="width:40%;" 
				  value="<?php print $user['firstbookpref'];?>" />
		</div>

<?php
if($tid=="administrator"){
?>
		<div class="center">

		  <label for="Password"><?php print_string('newpassword',$book);?></label>
		  <input pattern="truealphanumeric" tabindex="<?php print $tab++;?>" 
			  type="password" id="Password" name="password1" 
			  maxlength="20" style="width:20%;" />

			<label for="Password2"><?php print_string('retypenewpassword',$book);?></label>
			<input pattern="truealphanumeric" tabindex="<?php print $tab++;?>" 
				type="password" id="Password2" name="password2" 
				maxlength="20" style="width:20%;" />

			  <label for="No Login"><?php print_string('disablelogin',$book);?></label>
			  <input type="checkbox" id="No Login" 
				  name="nologin"  tabindex="<?php print $tab++;?>" 
				  <?php if($user['nologin']=='1'){print 'checked="checked"';}?> value="1"/>

		</div>
<?php
		}
?>

	  </fieldset>

	  <input type="hidden" name="role" value="<?php print $user['role']; ?>">
	  <input type="hidden" name="current" value="<?php print $action; ?>">
	  <input type="hidden" name="choice" value="<?php print $choice; ?>">
	  <input type="hidden" name="cancel" value="<?php print ''; ?>">
	</form>
  </div>
