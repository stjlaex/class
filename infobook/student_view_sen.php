<?php
/**                                  student_view_sen.php
 */
$action='student_view_sen1.php';

$SEN=$Student['SEN'];
?>
  <div id="heading"><label><?php print_string('',$book);?>SEN Profile</label>
  <?php print $Student['Forename']['value'].' '.$Student['Surname']['value'];?>
  </div>
<?php 

if($Student['SENFlag']['value']=='N'){
	two_buttonmenu();
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <fieldset class="center">	
		<legend><?php print_string('',$book);?>Not classed as an SEN student</legend>
	<button onClick="processContent(this);" name="sub" 
		value="SENStatus"><?php print_string('',$book);?>Change SEN status</button>
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
		<legend><?php print_string('',$book);?>SEN Profile</legend>

		<div class="left">
		  <label for="Start Date">
			<?php print_string($SEN['SENhistory']['StartDate']['label'],$book);?>
		  </label>
<?php
		$todate=$SEN['SENhistory']['StartDate']['value'];
		include('scripts/jsdate-form.php');
?>
		</div>

		<div class="right">
		  <label>
			<?php print_string($SEN['SENhistory']['NextReviewDate']['label'],$book);?>
		  </label>
<?php
		$todate=$SEN['SENhistory']['NextReviewDate']['value'];
		include('scripts/jsdate-form.php');
?>
		</div>

		<div class="left">
		  <label for="Type">
			<?php print_string($SEN['SENtypes'][0]['SENtype']['label'],$book);?>
		  </label>
<?php
	$key=0;
	$enum=getEnumArray($SEN['SENtypes'][0]['SENtype']['field_db']);
	print '<select id="Type"
			name="'.$SEN['SENtypes'][0]['SENtype']['field_db'].$key.'">';
	print '<option value=""></option>';
	while(list($inval,$description)=each($enum)){	
		print '<option ';
		if($SEN['SENtypes'][0]['SENtype']['value']==$inval){print 'selected="selected" ';}
		print ' value="'.$inval.'">'.get_string($description,$book).'</option>';
		}
    print '</select>';				
?>
		</div>

		<div class="right">
		  <label for="Rank">
			<?php print_string($SEN['SENtypes'][0]['SENtypeRank']['label'],$book);?>
		  </label>
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
		  <label for="Strengths">
			<?php print_string($Subject['Strengths']['label'],$book); ?>
		  </label>
		  <textarea id="Stengths" wrap="on" rows="4" cols="28" 
			name="<?php print $Subject['Strengths']['field_db'].$key;?>" 
			><?php print $Subject['Strengths']['value']; ?></textarea>
		</div>

		<div class="right">
		  <label for="Weaknesses">
			<?php print_string($Subject['Weaknesses']['label'],$book); ?>
		  </label>
		  <textarea id="Weaknesses" 
			wrap="on" rows="4" cols="28"  
			name="<?php print $Subject['Weaknesses']['field_db'].$key;?>" 
			><?php print $Subject['Weaknesses']['value']; ?></textarea>
		</div>

		<div class="left">
		  <label for="Strategies">
			<?php print_string($Subject['Strategies']['label'],$book); ?> 
		  </label>
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
