<?php 
/**						   			  orders_limit.php
 *
 */

$action='orders_limit_action.php';

if(isset($_GET['budid'])){$budid=$_GET['budid'];}else{$budid='';}
if(isset($_POST['budid'])){$budid=$_POST['budid'];}

$Budget=fetchBudget($budid);
$users=list_all_users('0');
$perms=array('r'=>1,'w'=>1,'x'=>1);
$xusers=(array)list_budget_users($budid,$perms);
$perms=array('r'=>1,'w'=>1,'x'=>0);
$wusers=(array)list_budget_users($budid,$perms);
$tab=1;

three_buttonmenu();
?>
  <div id="heading">
	<label><?php print_string('budget',$book); ?></label>
	<?php print $Budget['Name']['value'];?>
  </div>

  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post"
	  action="<?php print $host; ?>">

	  <fieldset class="left">
		<div class="center">
		  <label for="<?php print $Budget['Limit']['label'];?>">
			<?php print_string($Budget['Limit']['label'],$book);?>
		  </label>
		  <?php $tab=xmlelement_input($Budget['Limit'],'',$tab,'admin');?>
		</div>
	  </fieldset>


	  <table class="center listmenu">
		<tr>
		  <th style="width:50%;"><?php print_string('authorise',$book); ?></th>
		  <th style="width:50%;"><?php print_string('order',$book); ?></th>
		</tr>
		<tr>
		  <td>
			&nbsp;
<?php 
		while(list($uid,$user)=each($xusers)){
			$Responsible=array('id_db'=>$budid.'-'.$uid);
?>
			<div  id="<?php print $budid.'-'.$uid;?>" class="rowaction" >
			  <button title="Remove this responsibility"
				name="current" 
				value="responsables_edit_budget.php" 
				onClick="clickToAction(this)">
					 <?php print $user['username'].' ('.$user['surname'].')';?>
			  </button>
			  <div id="<?php print 'xml-'.$budid.'-'.$uid;?>" style="display:none;">
							  <?php xmlechoer('Responsible',$Responsible);?>
			  </div>
			</div>
<?php
			}
?>
		  </td>
		  <td>
			&nbsp;
<?php 
		while(list($uid,$user)=each($wusers)){
			$Responsible=array('id_db'=>$budid.'-'.$uid);
?>
			<div  id="<?php print $budid.'-'.$uid;?>" class="rowaction" >
			  <button title="Remove this responsibility"
				name="current" 
				value="responsables_edit_budget.php" 
				onClick="clickToAction(this)">
					 <?php print $user['username'].' ('.$user['surname'].')';?>
			  </button>
			  <div id="<?php print 'xml-'.$budid.'-'.$uid;?>" style="display:none;">
							  <?php xmlechoer('Responsible',$Responsible);?>
			  </div>
			</div>
<?php
			}
?>
		  </td>
		</tr>		  
		<tr>
		  <td>
			<div class="center">
<?php 
		$listlabel='add';
		$listname='xuid';
		$liststyle='width:65%;';
		$listvaluefield='uid';
		$listdescriptionfield='username';
		$onchange='yes';
		include('scripts/set_list_vars.php');
		list_select_list($users,$listoptions,$book);
		unset($listoptions);
?>
			</div>
		  </td>
		  <td>
			<div class="center">
<?php 
		$listlabel='add';
		$listname='wuid';
		$liststyle='width:65%;';
		$listvaluefield='uid';
		$listdescriptionfield='username';
		$onchange='yes';
		include('scripts/set_list_vars.php');
		list_select_list($users,$listoptions,$book);
		unset($listoptions);
?>

			</div>
		  </td>
		</tr>


	  </table>


	    <input type="hidden" name="budid" value="<?php print $budid;?>" /> 
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>
