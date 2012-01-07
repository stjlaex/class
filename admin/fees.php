<?php 
/**								  		fees.php
 *
 * This is the entry page to the fees book - it lives within Admin
 * but has its own lib/fetch_fees.php functions and is essentialy a
 * set of self-contained scripts.
 *
 *
 */

$choice='fees.php';
$action='fees_action.php';


$aperm=get_admin_perm('b',get_uid($tid));
/* TODO: only fees for current year at the moment. */
$feeyear=get_curriculumyear();

$extrabuttons=array();
if($_SESSION['role']=='admin' or $aperm==1 or $_SESSION['role']=='office'){
	$extrabuttons['remittances']=array('name'=>'current','value'=>'fees_remittance_list.php');
	$extrabuttons['conceptlist']=array('name'=>'current','value'=>'fees_concept_list.php');
	}
two_buttonmenu($extrabuttons,$book);
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post"
	  action="<?php print $host; ?>" >

		<input type="hidden" name="feeyear" value="<?php print $feeyear;?>" />
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>


	<form id="formtoprocess2" name="formtoprocess2" method="post"
	  action="<?php print $host; ?>" >

	  <fieldset class="left">
		<legend><?php print get_string('payment',$book).' '.get_string('search',$book);?></legend>		
		<div class="center">
		  <button style="float:right;"  type="submit" name="sub" value="search">
			<?php print_string('search');?>
		  </button>
		</div>
		<div class="center">
		  <div class="left">
<?php 
		$payment='0';
		$listlabel='type';
		$listname='paymenttype';
		include('scripts/set_list_vars.php');
		list_select_enum('paymenttype',$listoptions,$book);
?>
		  </div>

		  <div class="left">
<?php 
		$payment='0';
		$listlabel='status';
		$listname='payment';
		include('scripts/set_list_vars.php');
		list_select_enum('payment',$listoptions,$book);
?>
		  </div>

		  <div class="right">
<?php 
		$listlabel='concept';
		$liststyle='width:8em;';
		$listname='conceptid';
		$concepts=list_concepts();
		include('scripts/set_list_vars.php');
		list_select_list($concepts,$listoptions,$book);
?>
		  </div>

		</div>
	  </fieldset>

	  <fieldset class="right">
		<legend><?php print_string('invoicesearch',$book);?></legend>		
		<div class="center">
		  <div class="center">
			<button type="submit" name="sub" value="search">
			  <?php print_string('search');?>
			</button>
			<label for="Invoicenumber"><?php print_string('reference',$book);?></label>
			<input tabindex="<?php print $tab++;?>" 
				   type="text" id="Invoicenumber" name="invoicenumber" maxlength="30"/>
		  </div>
		</div>
	  </fieldset>

<?php

	$remittances=(array)list_remittances($feeyear);

?>
	  <fieldset class="right">
		<legend><?php print get_string('recent',$book).' '.get_string('remittance',$book);?></legend>		
		<div class="center">
		  <ul><li>
<?php
			print '<a  href="admin.php?current=fees_remittance_list.php&cancel='.$choice.'&choice='.$choice.'&remid='.$remittances[0]['id_db'].'">'.$remittances[0]['name'].'</a>';
?>
		  </li></ul>
		</div>
	  </fieldset>


	  <fieldset class="center divgroup" id="viewcontent">
		<legend><?php print get_string('yeargroups',$book);?></legend>
		<div>
		  <?php print_string('checkall'); ?>
		  <input type="checkbox" name="checkall" value="yes" onChange="checkAll(this,'yids[]');" />
		</div>
<?php
		$years=list_yeargroups();
		foreach($years as $year){
			$com=array('id'=>'','type'=>'year','name'=>$year['id']);
			$comid=update_community($com);
?>
	<div style="float:left;width:24%;margin:2px;">
	  <table class="listmenu smalltable">
		<tr>
		  <td>
			<input type="checkbox" name="comids[]" value="<?php print $comid; ?>" />
		  </td>
		  <td>
<?php
				print '<a  href="admin.php?current=fees_charge_list.php&cancel='.$choice.'&choice='.$choice.'&comids[]='.$comid.'">'.$year['name'].'</a>';
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


		<input type="hidden" name="feeyear" value="<?php print $feeyear;?>" />
		<input type="hidden" name="current" value="fees_charge_list.php" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>

  </div>
