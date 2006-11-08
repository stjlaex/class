<?php
/**                                  sen_view.php
 */
$action='sen_view_action.php';

?>
  <div id="heading"><label><?php print_string('senprofile','infobook');?></label>
  <?php print $Student['Forename']['value'].' '.$Student['Surname']['value'];?>
  </div>
<?php 

if($Student['SENFlag']['value']=='N'){
	two_buttonmenu();
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <fieldset class="left">	
		<legend><?php print_string('Not classed as an SEN student','infobook');?></legend>
		<button onClick="processContent(this);" name="sub" 
		  value="SENStatus"><?php print_string('Change SEN status','infobook');?></button>
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

	  <fieldset class="left">
		<legend><?php print_string('senprofile','infobook');?></legend>

		<div class="center">
		  <label for="Start Date">
			<?php print_string($SEN['SENhistory']['StartDate']['label'],'infobook');?>
		  </label>
<?php
		$todate=$SEN['SENhistory']['StartDate']['value'];
		include('scripts/jsdate-form.php');
?>
		</div>

		<div class="center">
		  <label>
			<?php print_string($SEN['SENhistory']['NextReviewDate']['label'],'infobook');?>
		  </label>
<?php
		$todate=$SEN['SENhistory']['NextReviewDate']['value'];
		include('scripts/jsdate-form.php');
?>
		</div>

		<div class="center">
		  <label for="Type">
			<?php print_string($SEN['SENtypes'][0]['SENtype']['label'],'infobook');?>
		  </label>
<?php
	$key=0;
	$enum=getEnumArray($SEN['SENtypes'][0]['SENtype']['field_db']);
	print '<select id="Type"  tabindex="'.$tab++.'"
			name="'.$SEN['SENtypes'][0]['SENtype']['field_db'].$key.'">';
	print '<option value=""></option>';
	while(list($inval,$description)=each($enum)){ 
		print '<option ';
		if($SEN['SENtypes'][0]['SENtype']['value']==$inval){print 'selected="selected" ';}
		print ' value="'.$inval.'">'.get_string($description,'infobook').'</option>';
		}
    print '</select>';				
?>
		</div>

		<div class="center">
		  <label for="Rank">
			<?php print_string($SEN['SENtypes'][0]['SENtypeRank']['label'],'infobook');?>
		  </label>
<?php
	$enum=getEnumArray($SEN['SENtypes'][0]['SENtypeRank']['field_db']);
	print '<select id="Rank"  tabindex="'.$tab++.'" 
			name="'.$SEN['SENtypes'][0]['SENtypeRank']['field_db'].$key.'" size="1">';
	print '<option value="">Select</option>';		
	while(list($inval,$description)=each($enum)){	
		print '<option ';
		if($SEN['SENtypes'][0]['SENtypeRank']['value']==$inval){print "selected='selected'";}
		print " value='".$inval."'>".$description."</option>";
		}
	print '</select>';					
?>
		</div>
	  </fieldset>

	  <fieldset class="right">
<?php
	while(list($key,$Subject)=each($SEN['NationalCurriculum'])){
		if(is_array($Subject)){    
?>
		<legend><?php print $Subject['Subject']['value']; ?></legend>
		<div class="center">
		  <label for="Strengths">
			<?php print_string($Subject['Strengths']['label'],'infobook'); ?>
		  </label>
		  <textarea id="Stengths" 
			wrap="on" rows="4" cols="28" tabindex="<?php print $tab++;?>" 
			name="<?php print $Subject['Strengths']['field_db'].$key;?>" 
			><?php print $Subject['Strengths']['value']; ?></textarea>
		</div>

		<div class="center">
		  <label for="Weaknesses">
			<?php print_string($Subject['Weaknesses']['label'],'infobook'); ?>
		  </label>
		  <textarea id="Weaknesses" 
			wrap="on" rows="4" cols="28" tabindex="<?php print $tab++;?>"
			name="<?php print $Subject['Weaknesses']['field_db'].$key;?>" 
			><?php print $Subject['Weaknesses']['value']; ?></textarea>
		</div>

		<div class="center">
		  <label for="Strategies">
			<?php print_string($Subject['Strategies']['label'],'infobook'); ?> 
		  </label>
		  <textarea id="Strategies" 
			wrap="on" rows="4" cols="28" tabindex="<?php print $tab++;?>"
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
 	<input type="hidden" name="choice" value="<?php print $current;?>"/>
 	<input type="hidden" name="cancel" value=""/>
	</form>
  </div>
