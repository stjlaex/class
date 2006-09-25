<?php
/**                                  student_view_sen.php
 */
$action='student_view_sen1.php';

$SEN=$Student['SEN'];
?>
  <div id="heading"><label>SEN Profile</label> 
  <?php print $Student['Forename']['value'].' '.$Student['Surname']['value'];?>
  </div>
<?php 

if($Student['SENFlag']['value']=='N'){
	two_buttonmenu();
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <fieldset class="center">	
		<legend>Not classed as an SEN student</legend>
	<button onClick="processContent(this);" name="sub" 
		value="SENStatus">Change SEN status</button>
	  </fieldset>
	<input type="hidden" name="current" value="<?php print $action;?>"/>
	<input type="hidden" name="cancel" value="<?php print $cancel;?>"/>
	<input type="hidden" name="choice" value="<?php print $choice;?>"/>
	</form>
  </div>
<?php
	}
else{
	three_buttonmenu();
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="center">
		<legend>Current SEN Profile</legend>

		<div class="left">
		  <label for="Start Date"><?php print $SEN['SENhistory']['StartDate']['label'];?></label>
		  <p id="Start Date"><?php  print $SEN['SENhistory']['StartDate']['value']; ?></p>
		</div>

		<div class="right">
		  <label><?php print_string($SEN['SENhistory']['NextReviewDate']['label']);?></label>
<?php
		$entrydate=$SEN['SENhistory']['NextReviewDate']['value'];
		include('scripts/jsdate-form.php');
?>
		</div>

		<div class="left">
		  <label for="Type"><?php print $SEN['SENtypes'][0]['SENtype']['label'];?></label>
<?php
	$key=0;
	$enum=getEnumArray($SEN['SENtypes'][0]['SENtype']['field_db']);
	print "<select id='Type'
			name='".$SEN['SENtypes'][0]['SENtype']['field_db'].$key."'>";
	print "<option value=''>Select</option>";
	while(list($inval,$description)=each($enum)){	
		print "<option ";
		if($SEN['SENtypes'][0]['SENtype']['value']==$inval){print "selected='selected'";}
		print " value='".$inval."'>".$description."</option>";
		}
    print '</select>';					
?>
		</div>

		<div class="right">
		  <label for="Rank"><?php print  $SEN['SENtypes'][0]['SENtypeRank']['label'];?></label>
<?php
	$enum=getEnumArray($SEN['SENtypes'][0]['SENtypeRank']['field_db']);
	print "<select id='Rank' name='".$SEN['SENtypes'][0]['SENtypeRank']['field_db'].$key."' size='1'>";
	print "<option value=''>Select</option>";		
	while(list($inval,$description)=each($enum)){	
		print '<option ';
		if($SEN['SENtypes'][0]['SENtypeRank']['value']==$inval){print "selected='selected'";}
		print " value='".$inval."'>".$description."</option>";
		}			
	print '</select>';					
?>
		</div>
	  </fieldset>

	  <fieldset class="center">
<?php
	while(list($key,$Subject)=each($SEN['NationalCurriculum'])){
		if(is_array($Subject)){    
?>
		<legend><?php print $Subject['Subject']['value']; ?></legend>
		<div class="left">
		  <label for="Strengths"><?php print $Subject['Strengths']['label']; ?>:</label>
		  <textarea id="Stengths" wrap="on" rows="4" cols="28"  name="<?php print $Subject['Strengths']['field_db'].$key; ?>"><?php print $Subject['Strengths']['value']; ?></textarea>
		</div>

		<div class="right">
		  <label for="Weaknesses"><?php print $Subject['Weaknesses']['label']; ?>:</label>
		  <textarea id="Weaknesses" 
			wrap="on" rows="4" cols="28"  
			name="<?php print $Subject['Weaknesses']['field_db'].$key;?>" 
			><?php print $Subject['Weaknesses']['value']; ?></textarea>
		</div>

		<div class="left">
		  <label for="Strategies"><?php print $Subject['Strategies']['label']; ?>:</label>
		  <textarea id="Strategies" wrap="on" rows="4" cols="28"  
			name="<?php print $Subject['Strategies']['field_db'].$key;?>" 
			><?php print $Subject['Strategies']['value']; ?></textarea>
		</div>
<?php
			}
		}
	}
?>
	  </fieldset>	
	
 	<input type="hidden" name="current" value="<?php print $action;?>"/>
 	<input type="hidden" name="cancel" value="<?php print $cancel;?>"/>
 	<input type="hidden" name="choice" value="<?php print $choice;?>"/>
	</form>
  </div>
