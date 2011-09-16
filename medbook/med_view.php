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
				onclick="tinyTabs(this)"><?php print $Note['MedicalCategory']['value'];?></p>
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
	
 	<input type="hidden" name="current" value="<?php print $action;?>"/>
 	<input type="hidden" name="choice" value="<?php print $current;?>"/>
 	<input type="hidden" name="cancel" value=""/>
	</form>
  </div>
