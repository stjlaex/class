<?php 
/**									transport.php
 *
 * This is the entry page to the Transport book - it lives within Admin
 * but has its own lib/fetch_transport.php functions and is essentialy a
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

$extrabuttons['morning']=array('name'=>'current',
							   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
							   'value'=>'transport_print.php',
							   'xmlcontainerid'=>'listin',
							   'onclick'=>'checksidsAction(this)');
$extrabuttons['afternoon']=array('name'=>'current',
								 'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
								 'value'=>'transport_print.php',
								 'xmlcontainerid'=>'listout',
								 'onclick'=>'checksidsAction(this)');
$extrabuttons['changes']=array('name'=>'current',
									   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
									   'value'=>'transport_print.php',
									   'xmlcontainerid'=>'changes',
									   'onclick'=>'checksidsAction(this)');
two_buttonmenu($extrabuttons);
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />

	<fieldset class="center divgroup" id="viewcontent">
	  <legend><?php print_string('transport',$book);?></legend>
		<div>
		  <?php print_string('checkall'); ?>
		  <input type="checkbox" name="checkall" value="yes" onChange="checkAll(this);" />
		  <div style="float:right;">
			<?php $required='no'; include('scripts/jsdate-form.php');?>
		  </div>
		</div>
<?php
		$busnames=list_busnames();

		foreach($busnames as $busname){
?>
	<div style="float:left;width:24%;margin:2px;">
	  <table class="listmenu smalltable">
		<tr>
		  <td>
			<input type="checkbox" name="busnames[]" value="<?php print $busname['name']; ?>" />
		  </td>
		  <td>
<?php
				print '<a  href="admin.php?current=transport_list.php&cancel='.$choice.'&choice='.$choice.'&busname='.$busname['name'].'">'.$busname['name'].'</a>';
?>
		  </td>
		  <td></td>
		</tr>
	  </table>
	</div>
<?php
			}
?>
	</fieldset>
	<div id="xml-listin" style="display:none;">
	  <params>
		<checkname>busnames</checkname>
		<selectname>date0</selectname>
		<length>full</length>
		<transform>transport_list_in</transform>
		<paper>landscape</paper>
	  </params>
	</div>
	<div id="xml-listout" style="display:none;">
	  <params>
		<checkname>busnames</checkname>
		<selectname>date0</selectname>
		<length>full</length>
		<transform>transport_list_out</transform>
		<paper>landscape</paper>
	  </params>
	</div>
	<div id="xml-changes" style="display:none;">
	  <params>
		<checkname>busnames</checkname>
		<selectname>date0</selectname>
		<length>short</length>
		<transform>transport_list_changes</transform>
		<paper>landscape</paper>
	  </params>
	</div>
  </form>


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

