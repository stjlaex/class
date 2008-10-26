<?php
/**
 *                                  student_view_medical.php
 */

$action='student_view_medical1.php';
$cancel='student_view.php';

two_buttonmenu();

	/*Anyone who gets to this page can read it but not edit*/
	$yid=$Student['YearGroup']['value'];
	$perm=getMedicalPerm($yid,$respons);

?>
  <div id="heading">
	<?php print $Student['Forename']['value'].' '.$Student['Surname']['value'];?>
  </div>

  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="center">	
		<legend><?php print_string('medicalcaption',$book);?></legend>

<?php 
if($Student['MedicalFlag']['value']=='N' and $perm['w']==1){
?>

		<p><?php print_string('nomedicalinfo',$book);?></p>
		<button name="sub" value="MedicalStatus">
		  <?php print_string('medicalbutton',$book);?></button>

<?php
	}
else{
	$Medical=fetchMedical($sid);
?>
		<div class="center">
		  <p><?php print_String('furtherinformationonfile',$book);?></p>
		</div>
<?php
		$Notes=$Medical['Notes'];
		while(list($index,$Note)=each($Notes['Note'])){
			if(is_array($Note) and $Note['MedicalRating']['value']==1){
				$cattype=$Note['MedicalCategory']['value_db'];
?>
			<div class="center">
			  <table>
				<tr>
				  <?php xmlelement_display($Note['MedicalCategory'],'medbook');?>
				  <?php xmlelement_display($Note['LastReviewDate'],'medbook');?>
				</tr>
				<tr>
				  <td colspan="2">
				  <textarea id="Detail" 
				  wrap="on" rows="5" tabindex="<?php print $tab++;?>"
				  name="<?php print $Note['Detail']['field_db'].$index;?>" 
				  ><?php print $Note['Detail']['value'];?></textarea>
				  </td>
				</tr>
			  </table>
			</div>
<?php

				}
			}

		}
?>
	  </fieldset>
 	<input type="hidden" name="current" value="<?php print $action;?>" />
 	<input type="hidden" name="cancel" value="<?php print $cancel;?>">
 	<input type="hidden" name="choice" value="<?php print $choice;?>">
</form>
</div>
