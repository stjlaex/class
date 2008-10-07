<?php 
/**						   			  enrolments_edit.php
 *
 */

$action='enrolments_edit_action.php';

if(isset($_GET['enrolstatus'])){$enrolstatus=$_GET['enrolstatus'];}
if(isset($_GET['enrolyear'])){$enrolyear=$_GET['enrolyear'];}
if(isset($_POST['enrolstatus'])){$enrolstatus=$_POST['enrolstatus'];}
if(isset($_POST['enrolyear'])){$enrolyear=$_POST['enrolyear'];}


three_buttonmenu();
?>
  <div id="heading">
	<label><?php print_string('academicyear'); ?></label>
	<?php  print display_curriculumyear($enrolyear);?>
  </div>

  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post"
	  action="<?php print $host; ?>">

	  <table class="center listmenu">
		<tr>
		  <th style="width:50%;">
			<?php print_string('yeargroup',$book); ?>
		  </th>
		  <th style="width:50%;">
			<?php print_string(displayEnum($enrolstatus,'enrolstatus'),$book); ?>
		  </th>
		</tr>
		<tr>
<?php 
	$yeargroups=list_yeargroups();
	while(list($yindex,$year)=each($yeargroups)){
		$yid=$year['id'];
		if($enrolstatus=='EN'){$comtype='enquired';}
		elseif($enrolstatus=='AC'){
			$comtype='accepted';
			$value=$newcurrentsids;
			}
		else{$comtype='applied';}
		$com=array('id'=>'','type'=>$comtype, 
				   'name'=>$enrolstatus.':'.$yid,'year'=>$enrolyear);
		$comid=update_community($com);
		$com['id']=$comid;
		/* MUST BE true to read static values from table which are being edited */
		$value=countin_community($com,'','',true);
?>
		  <td>
			<?php print $year['name'];?>
		  </td>
		  <td>
			<input type="hidden" name="comids[]" value="<?php print $comid;?>" />
			<input pattern="decimal" type="text" 
				tabindex="<?php print $tab++;?>" 
				name="values[]" maxlength="8" value="<?php print $value;?>" />
		  </td>
		</tr>
<?php
		}
?>
	  </table>

	    <input type="hidden" name="enrolyear" value="<?php print $enrolyear;?>" /> 
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>
