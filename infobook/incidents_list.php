<?php
/**                                  incidents_list.php    
 */
$current='incidents_list.php';
$action='incidents_list_action.php';

if(isset($_GET['bid'])){$bid=$_GET['bid'];}

three_buttonmenu();
?>
  <div id="heading">
	<label><?php print_string('incidents');?></label>
<?php
	print $Student['Forename']['value'].' '.$Student['Surname']['value'];
	print '('.$Student['RegistrationGroup']['value'].')';
?>
  </div>

  <div class="topform">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <div class="left">
		<label for="Detail"><?php print_string('details',$book);?></label>
		<textarea name="detail"   tabindex="<?php print $tab++;?>" 
		  class="required" id="Detail" rows="5" cols="35"></textarea>
	  </div>
	  <div class="right" >
		<?php $xmldate='Entrydate'; include('scripts/jsdate-form.php'); ?>
	  </div>
	  <div class="right">
		<label for="Subject"><?php print_string('subjectspecific');?></label>
			   <?php $required="no"; include('scripts/list_studentsubjects.php');?>
	  </div>

	  <input type="text" style="display:none;" id="Id_db" name="id_db" value="" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print 'student_view.php';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
  </div>

  <div class="content">
	<div class="center">
	  <table class="listmenu">
		<caption><?php print_string('entries',$book);?></caption>
		<thead>
		  <tr>
			<th></th>
			<th><?php print_string('yeargroup');?></th>
			<th><?php print_string('date');?></th>
			<th><?php print_string('subject');?></th>
			<th><?php print_string('category');?></th>
		  </tr>
		</thead>
<?php
   	$yid=$Student['YearGroup']['value'];
	$perm=getYearPerm($yid, $respons);
	if(is_array($Student['Incidents'])){
		reset($Student['Incidents']);
		while(list($key,$entry)=each($Student['Incidents'])){
			if(is_array($entry)){
				$rown=0;
				$entryno=$entry['id_db'];
?>
		<tbody id="<?php print $entryno;?>">
		  <tr class="rowplus" onClick="clickToReveal(this)" id="<?php print $entryno.'-'.$rown++;?>">
			<th>&nbsp</th>
<?php
		   if(isset($entry['YearGroup']['value'])){print '<td>'.$entry['YearGroup']['value'].'</td>';}
		   else{print'<td></td>';}
		   if(isset($entry['EntryDate']['value'])){print '<td>'.$entry['EntryDate']['value'].'</td>';}
		   else{print'<td></td>';}
		   if(isset($entry['Subject']['value'])){print '<td>'.$entry['Subject']['value'].'</td>';}
		   else{print'<td></td>';}
		   if(isset($entry['Category']['value'])){print '<td>'.$entry['Category']['value'].'</td>';}
		   else{print'<td></td>';}
?>
		  </tr>
		  <tr class="hidden" id="<?php print $entryno.'-'.$rown++;?>">
			<td colspan="5">
			  <p>
<?php		   if(isset($entry['Detail']['value'])){print $entry['Detail']['value'];}?>
			  </p>
			  <p>
<?php		   if(isset($entry['Outcome']['value'])){print $entry['Outcome']['value'];}?>
			  </p>
			  <button class="rowaction" title="Delete this incident"
				name="current" value="delete_incident.php" onClick="clickToAction(this)">
				<img class="clicktodelete" />
			  </button>
			  <button class="rowaction" title="Edit" name="Edit" onClick="clickToAction(this)">
				<img class="clicktoedit" />
			  </button>
			</td>
		  </tr>
		  <div id="<?php print 'xml-'.$entryno;?>" style="display:none;">
<?php
				xmlechoer('Incident',$entry);
?>
		  </div>
		</tbody>
<?php
			}
		}
	}
?>
	  </table>
	</div>
  </div>
