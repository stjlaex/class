<?php
/**                                  med_view.php
 */

$action='med_view_action.php';
?>
  <div id="heading"><label><?php print_string('medicalrecord',$book);?></label>
	<a href="infobook.php?current=student_view.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>"
			  target="viewinfobook" onclick="parent.viewBook('infobook');">
  <?php print $Student['Forename']['value'].' '.$Student['Surname']['value'];?>
	</a>
  </div>
<?php
	three_buttonmenu(array('removemed'=>array('name'=>'sub','value'=>'medstatus')),$book);
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="center">
		<legend><?php print_string('studentdetails','infobook');?></legend>
		<table class="listmenu">
		  <tr>
			<?php xmlelement_display($Student['DisplayFullName'],'infobook');?>
			<?php xmlelement_display($Student['DOB'],'infobook');?>
		  </tr>
		  <tr>
			<?php xmlelement_display($Student['Nationality'],'infobook');?>
			<?php xmlelement_display($Student['RegistrationGroup'],'infobook');?>
		  </tr>
		</table>
		<br />
		  <table class="listmenu">
			<tr>
<?php
			if(isset($Student['Contacts'])
			   and sizeof($Student['Contacts'])>0){
				$Contact=$Student['Contacts'][0];
				xmlelement_display($Contact['Relationship'],'infobook');
				xmlelement_display($Contact['Forename'],'infobook');
				xmlelement_display($Contact['Surname'],'infobook');
				}
?>
			</tr>
			<tr>
			  <td colspan="3">
<?php
				$Phones=$Contact['Phones'];
				while(list($phoneno,$Phone)=each($Phones)){
					print '<label>'.
		get_string(displayEnum($Phone['PhoneType']['value'],$Phone['PhoneType']['field_db']),'infobook').
		'</label>' .$Phone['PhoneNo']['value'].'<br /> ';				
					}
?>
			  </td>
			</tr>
		  </table>
	  </fieldset>

	  <div class="center">
		<div class="tinytabs" id="med">
		  <ul>
<?php
	$Notes=$Medical['Notes'];
			  /*
	$keycatids=array();
	while(list($index,$Note)=each($Notes['Note'])){
		if(is_array($Note)){
			$cattype=$Note['MedicalCategory']['value_db'];
			$keycatids[$cattype]=$index;
			}
		}
			  */

	$selkey=0;
	reset($Notes['Note']);
	while(list($index,$Note)=each($Notes['Note'])){
		if(is_array($Note)){
			$cattype=$Note['MedicalCategory']['value_db'];
?>
			<li id="<?php print 'tinytab-med-'.$cattype;?>"><p 
					 <?php if($index==$selkey){ print ' id="current-tinytab" ';}?>
				class="<?php print $cattype;?>"
				onclick="parent.tinyTabs(this)"><?php print $Note['MedicalCategory']['value'];?></p>
			</li>

			<div class="hidden" id="tinytab-xml-med-<?php print $cattype;?>">
			  <table>
				<tr>
				  <?php xmlelement_display($Note['LastReviewDate'],$book);?>
				</tr>
				<tr>
				  <td>
				  <label for="Details">
					<?php print_string($Note['Detail']['label'],$book); ?>
				  </label>
				  </td>
				</tr>
				<tr>
				  <td>
				  <textarea id="Detail" style="font-wight:600; font-size:large;" 
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
?>
		  </ul>
		</div>
		<div id="tinytab-display-med" class="tinytab-display">
		</div>
	  </div>


	  <fieldset class="center listmenu">
		<legend><?php print_string('medications',$book);?></legend>
		<table class="listmenu">
<?php 
	$MedicalAssDefs=array();
	$MedicalAssDefs=fetch_enrolmentAssessmentDefinitions('','M');
	$input_elements='';
	foreach($MedicalAssDefs as $index => $AssDef){
		$eid=$AssDef['id_db'];
		$input_elements.=' <input type="hidden" name="eids[]" value="'.$eid.'" />';
		$gena=$AssDef['GradingScheme']['value'];
		$label=$AssDef['PrintLabel']['value'];
		$Assessments=(array)fetchAssessments_short($sid,$eid,'G');
		if(sizeof($Assessments)>0){$value=$Assessments[0]['Value']['value'];}
		else{$value='';}
		$extra=$Assessments[0]['Comment']['value'];
?>
		  <tr>
			  <td>
<?php 
		print '<label>'.$AssDef['Description']['value'].'</label>';
		if($gena!='' and $gena!=' '){
			$input_elements.=' <input type="hidden" name="scoretype'.$eid.'" value="grade" />';
			$pairs=explode (';',$AssDef['GradingScheme']['grades']);
?>
				<select tabindex="<?php print $tab++;?>" name="<?php print $eid;?>" 
<?php				if($label=='extra'){
						echo "onchange=\"
							if(this.value=='1'){
								document.getElementById('extra".$eid."').style.display='block';
								}
							else{
								document.getElementById('extra".$eid."').style.display='none';
								}
							\"";
						}
?>
				>
<?php
			print '<option value="" ';
			if($value==''){print 'selected';}	
			print ' ></option>';
			for($c3=0; $c3<sizeof($pairs); $c3++){
				list($level_grade, $level)=explode(':',$pairs[$c3]);
				print '<option value="'.$level.'" ';
				if($value==$level){print 'selected';}
				print '>'.$level_grade.'</option>';
				}
?>
				</select>
<?php
			if($label=='extra'){
?>
			  <div id="extra<?php echo $eid;?>" <?php if($value==0){echo "style='display:none';";}?>>
				<label>Extra info</label>
				<input type="text" tabindex="<?php print $tab++;?>" size="5"
				  name="extra<?php echo $eid;?>" value="<?php print $extra;?>"/>
			  </div>
<?php
				}
			}
		else{
?>
				<input tabindex="<?php print $tab++;?>" 
				  name="<?php print $eid;?>" value="<?php print $value;?>"/>
<?php
			}
?>
			</td>
		  </tr>
<?php 
		}
?>
		</table>

	  </fieldset>

 	<?php print $input_elements;?>
 	<input type="hidden" name="current" value="<?php print $action;?>"/>
 	<input type="hidden" name="choice" value="<?php print $current;?>"/>
 	<input type="hidden" name="cancel" value=""/>
	</form>
  </div>
