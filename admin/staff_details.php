<?php 
/**								staff_details.php
 *
 */

$action='staff_details_action.php';

if(isset($_POST['seluid'])){$seluid=$_POST['seluid'];}
elseif(isset($_GET['seluid'])){$seluid=$_GET['seluid'];}
else{$seluid='';}

/*This is the record being edited.*/
$User=fetchUser($seluid);

/* Super user perms for user accounts. */ 
$aperm=get_admin_perm('u',$_SESSION['uid']);

three_buttonmenu();
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post" novalidate action="<?php print $host; ?>">

	  <div class="left">
		<?php $tab=xmlarray_form($User,'','details',$tab,'infobook'); ?>
	  </div>

	  <fieldset class="right">
		<legend><?php print_string('account',$book);?></legend>

		<div class="center">
		  <label for="ID"><?php print_string('username');?></label>
		  <input pattern="truealphanumeric" readonly="readonly"  
				type="text" id="ID" name="username"  
				maxlength="14" value="<?php print $User['Username']['value'];?>" />
		</div>
		<div class="center">
<?php 
		$selbook=$User['FirstBook']['value'];
		include('scripts/list_books.php');
?>
		</div>
<?php
if($_SESSION['role']=='admin' or $aperm==1){
?>
		<div class="center">
		  <label for="Work level"><?php print_string('workexperiencelevel',$book);?></label>
		  <select name="worklevel" id="Worklevel" size="1" tabindex="<?php print $tab++;?>" 
			class="required" >
			<option value=""></option>
<?php
		$worklevels=array('-1'=>'useless','0'=>'tryharder','1'=>'good','2'=>'verygood');
		foreach($worklevels as $index => $worklevel){
			print '<option ';
			if(isset($User['Worklevel']['value'])){if($User['Worklevel']['value']==$index){print 'selected="selected"';}}
			print	' value="'.$index.'">'.get_string($worklevel,$book).'</option>';
			}
?>
		  </select>

		  <?php $selrole=$User['Role']['value']; include('scripts/list_roles.php');?>

<?php
		  unset($key);
		  $listname='senrole';$selsenrole=$User['SENRole']['value'];$listlabel='senrole';
		  include('scripts/set_list_vars.php');
		  $list[]=array('id'=>'0','name'=>get_string('no'));
		  $list[]=array('id'=>'1','name'=>get_string('yes'));
		  list_select_list($list,$listoptions);

		  unset($key);
		  $listname='medrole';$selmedrole=$User['MedRole']['value'];$listlabel='medrole';$required='yes';
		  include('scripts/set_list_vars.php');
		  list_select_list($list,$listoptions);

?>
		  </div>
<?php
		}
	else{
?>
	  <input type="hidden" name="role" value="<?php print $User['Role']['value']; ?>">
	  <input type="hidden" name="senrole" value="<?php print $User['SENRole']['value']; ?>">
	  <input type="hidden" name="medrole" value="<?php print $User['MedRole']['value']; ?>">
	  <input type="hidden" name="worklevel" value="<?php print $User['Worklevel']['value']; ?>">
	  <input type="hidden" name="nologin" value="<?php print $User['NoLogin']['value']; ?>">
<?php
		}
?>
		</fieldset>


<?php
	if($_SESSION['role']=='admin'  or $aperm==1){
?>
	  <fieldset class="right">
		<legend><?php print_string('password',$book);?></legend>
		<div class="center">

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
				  <?php if($User['NoLogin']['value']=='1'){print 'checked="checked"';}?> value="1"/>

		</div>
	  </fieldset>
<?php
		}

?>
<div class="right">

<?php
	if($_SESSION['role']=='admin'){
?>
	  <fieldset class="left">
		<legend><?php print_string('specialadminpermissions',$book);?></legend>
<?php
		$agroups=(array)list_admin_groups();
		foreach($agroups as $type=>$agroup){
			$editaperm=get_admin_perm($type,$seluid);
?>
		  <label for="<?php print $agroup['name'];?>"><?php print_string($agroup['name'],$book);?></label>
		  <input type="checkbox" id="a<?php print $agroup['gid'];?>"  
				  name="a<?php print $agroup['gid'];?>"  tabindex="<?php print $tab++;?>" 
				  <?php if($editaperm){print 'checked="checked"';}?> value="1"/>
<?php
			}
?>
	  </fieldset>
<?php
		}

?>

	  <fieldset class="right">
		<legend><?php print_string('section',$book);?></legend>
<?php
		$sections=(array)list_sections();
		$access_groups=(array)list_user_groups($seluid,'s');
		foreach($sections as $section){
			if(in_array($section['gid'],$access_groups)){$editaperm=true;}
			else{$editaperm=false;}
?>
		  <label for="<?php print $section['name'];?>"><?php print $section['name'];?></label>
		  <input type="checkbox" id="a<?php print $section['gid'];?>"  
				  name="a<?php print $section['gid'];?>"  tabindex="<?php print $tab++;?>" 
				  <?php if($editaperm){print 'checked="checked"';}?> value="1"/>
<?php
			}
?>
		</fieldset>
	  </div>


	  <div class="left">
<?php
	$addressno='0';/*Only doing one address.*/
	$tab=xmlarray_form($User['Address'],$addressno,'contactaddress',$tab,'infobook'); 
?>
		<input type="hidden" name="addid" value="<?php print $User['Address']['id_db']; ?>">
	  </div>

	  <input type="hidden" name="seluid" value="<?php print $seluid; ?>">
	  <input type="hidden" name="current" value="<?php print $action; ?>">
	  <input type="hidden" name="choice" value="<?php print $choice; ?>">
	  <input type="hidden" name="cancel" value="<?php print $choice; ?>">
	</form>
  </div>
