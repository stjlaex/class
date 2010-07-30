<?php 
/**									transport.php
 *
 * This is the entry page to the Order book - it lives within Admin
 * but has its own lib/fetch_orders.php functions and is essentialy a
 * set of self-contained scripts.
 *
 *
 */

$choice='transport.php';
$action='transport_action.php';

/* TO DO: add a transport admin group */
$aperm=get_admin_perm('b',get_uid($tid));

$extrabuttons=array();
if($_SESSION['role']=='admin' or $aperm==1){
	//$extrabuttons['newbus']=array('name'=>'current','value'=>'new_bus.php');
	}
if($_SESSION['role']=='admin' or $aperm==1 or $_SESSION['role']=='office'){
	//$extrabuttons['export']=array('name'=>'current','value'=>'transport_export.php');
	}

two_buttonmenu($extrabuttons,$book);
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>

	<fieldset class="center divgroup" id="viewcontent">
	  <legend><?php print_string('transport',$book);?></legend>

<?php
		$busnames=list_busnames();

		foreach($busnames as $index => $busname){
?>
	<div style="float:left;width:24%;margin:2px;">
	  <table class="listmenu smalltable">
		<tr>
		  <td>
<?php
				print '<a  href="admin.php?current=transport_list.php&cancel='.$choice.'&choice='.$choice.'&busname='.$busname['name'].'">'.$busname['name'].'</a>';
?>
		  </td>
		  <td></td>
		  <td></td>
		</tr>
	  </table>
	</div>
<?php
			}
?>
	</fieldset>


	<fieldset class="center divgroup" id="viewcontent">
	  <legend><?php print get_string('formgroups',$book);?></legend>

<?php
		$forms=list_formgroups();

		foreach($forms as $index => $form){
?>
	<div style="float:left;width:24%;margin:2px;">
	  <table class="listmenu smalltable">
		<tr>
		  <td>
<?php
				print '<a  href="admin.php?current=transport_list.php&cancel='.$choice.'&choice='.$choice.'&fid='.$form['id'].'">'.$form['name'].'</a>';
?>
		  </td>
		  <td></td>
		  <td></td>
		</tr>
	  </table>
	</div>
<?php
			}
?>
	</fieldset>


  </div>
