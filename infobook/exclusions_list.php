<?php
/**                                  exclusions_list.php    
 *
 */

$action='exclusions_list_action.php';

if(isset($_GET['bid'])){$bid=$_GET['bid'];}
$Exclusions=$Student['Exclusions'];

three_buttonmenu();

	/*Check user has permission to view*/
	$yid=$Student['YearGroup']['value'];
	$perm=getYearPerm($yid,$respons);
	include('scripts/perm_action.php');

?>

  <div id="heading">
	<label><?php print_string('exclusions',$book);?></label>
<?php
	print $Student['Forename']['value'].' '.$Student['Surname']['value'];
	print '('.$Student['RegistrationGroup']['value'].')';
?>
  </div>

  <div class="topform">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <div class="left">
		<label for="Reason"><?php print_string('reason',$book);?></label>
		<textarea name="detail" class="required" id="Reason"  maxlength="250"  
		  tabindex="<?php print $tab++;?>" rows="5" cols="30"></textarea>
	  </div>

	  <div class="right">
		<label for="Category"><?php print_string('category',$book);?></label>
		<select id="Category" name="category" class="required" tabindex="<?php print $tab++;?>">
		  <option value=''></option	
<?php
		$enum=getEnumArray('exclusionscategory');
		while(list($inval,$description)=each($enum)){	
			print "<option value='".$inval."'>".$description."</option>";
			}
?>
		</select>
	  </div>

	  <div class="right">
		<label>Start</label>
		<?php $xmldate='Startdate'; $required='yes'; include('scripts/jsdate-form.php'); ?>
	  </div>

	  <div class="right">
		<label>End</label>
		<?php $xmldate='Enddate'; $required='yes'; include('scripts/jsdate-form.php'); ?>
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
			<th><?php print_string('start');?></th>
			<th><?php print_string('end');?></th>
			<th colspan="2"><?php print_string('category');?></th>
		  </tr>
		</thead>
<?php
	$yid=$Student['NCyearActual']['id_db'];
	$perm=getYearPerm($yid, $respons);
	if(is_array($Student['Exclusions'])){
		reset($Student['Exclusions']);
		while(list($key,$entry)=each($Student['Exclusions'])){
			if(is_array($entry)){
				$rown=0;
				$entryno=$entry['id_db'];
?>
		<tbody id="<?php print $entryno;?>">
		  <tr class="rowplus" onClick="clickToReveal(this)" id="<?php print $entryno.'-'.$rown++;?>">
			<th>&nbsp</th>
<?php 
		   if(isset($entry['StartDate']['value'])){print '<td>'.$entry['StartDate']['value'].'</td>';}
		   else{print'<td></td>';}
		   if(isset($entry['EndDate']['value'])){print '<td>'.$entry['EndDate']['value'].'</td>';}
		   else{print'<td></td>';}
?>
			 <td>
<?php
			   print'<div style="float:left;padding:0 6px 0 6px;" class="negative">';
			   print $entry['Category']['value'].'</div>';
?>
			</td>
		  </tr>
		  <tr class="hidden" id="<?php print $entryno.'-'.$rown++;?>">
			<td colspan="6">
			  <p>
<?php		   if(isset($entry['Reason']['value'])){
					print $entry['Reason']['value'];}
?>
			  </p>
			  <button class="rowaction" title="Delete this comment"
				name="current" value="delete_comment.php" onClick="clickToAction(this)">
				<img class="clicktodelete" />
			  </button>
			  <button class="rowaction" title="Edit" name="Edit" onClick="clickToAction(this)">
				<img class="clicktoedit" />
			  </button>
			</td>
		  </tr>
		  <div id="<?php print 'xml-'.$entryno;?>" style="display:none;">
<?php
				xmlechoer('Comment',$entry);
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


