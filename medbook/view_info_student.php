<?php
/**                                  view_info_student.php
 */
$med=fetchMedical($sid);
?>
<fieldset class="center">
		<legend><?php print_string('studentdetails','infobook');?></legend>
		<table class="listmenu">
		  <tr>
			<?php xmlelement_display($Student['DisplayFullName'],'infobook');?>
			<?php xmlelement_display($Student['DOB'],'infobook');?>
			<?php xmlelement_display($Student['RegistrationGroup'],'infobook');?>
			<td rowspan="3">
				<div class="icon">
					<a href="infobook.php?current=student_view.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>"
							target="viewinfobook" onclick="parent.viewBook('infobook');">
						<img src="<?php print 'scripts/photo_display.php?epfu='.$Student['EPFUsername']['value'].'&enrolno='.$Student['EnrolmentNumber']['value'].'&size=maxi';?>" 
							style="display:block;margin-left:auto;margin-right:auto;height:auto;width:auto;float:none;"/>
					</a>
				</div>
			</td>
		  </tr>
		  <tr>

			  <td colspan="3">
			  	<span title="<?php echo $title;?>">
		  			<label><?php print_string("contactinfo","infobook");?></label>
<?php
		  	if(isset($Student['Contacts'])
			   and sizeof($Student['Contacts'])>0){
				$Contact=$Student['Contacts'][0];
				echo $Contact['Forename']['value'].' '.$Contact['Surname']['value'].' ('.$Contact['Relationship']['value'].') - ';
				}
				$Phones=$Contact['Phones'];
				while(list($phoneno,$Phone)=each($Phones)){
					print get_string(displayEnum($Phone['PhoneType']['value'],$Phone['PhoneType']['field_db']),'infobook').
					': '.$Phone['PhoneNo']['value'].'; ';
					}
?>
		  </tr>
		  <tr>
		  	<td colspan="3">
		  			<label><?php print_string("medicalinformation","infobook");?></label>
<?php
		  			foreach($med['Notes']['Note'] as $note){
		  				$title='';
						if($note['Detail']['value']!=''){
							$title.='<label>'.$note['MedicalCategory']['value'].'</label> '.$note['Detail']['value'].";";
		  					echo '<span title="'.$title.'" style="margin:2%;padding:2px 4px 2px 4px;border:0 none;border-radius: 3px 3px 3px 3px;cursor: pointer;background-color:#554466;color:#FFFFEE;">'.$note['MedicalCategory']['value'].'</span>';
		  					}
		  				}
?>
		  	</td>
		  </tr>
		</table>
</fieldset>

<input type="hidden" name="studentid" value="<?php print $sid;?>"/>
