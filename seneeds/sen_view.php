<?php
/**                                  sen_view.php
 */
$action='sen_view_action.php';

?>
  <div id="heading"><label><?php print_string('iep',$book);?></label>
  <?php print $Student['Forename']['value'].' '.$Student['Surname']['value'];?>
  </div>
<?php 

	three_buttonmenu(array('removesen'=>array('name'=>'sub','value'=>'senstatus')));
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="left">
		<legend><?php print_string('senprofile',$book);?></legend>

		<div class="center">
		  <label for="Start Date">
			<?php print_string($SEN['SENhistory']['StartDate']['label'],$book);?>
		  </label>
<?php
		$todate=$SEN['SENhistory']['StartDate']['value'];
		include('scripts/jsdate-form.php');
?>
		</div>

		<div class="center">
		  <label>
			<?php print_string($SEN['SENhistory']['NextReviewDate']['label'],$book);?>
		  </label>
<?php
		$todate=$SEN['SENhistory']['NextReviewDate']['value'];
		include('scripts/jsdate-form.php');
?>
		</div>

		<div class="center">
		  <label for="Type">
			<?php print_string($SEN['SENtypes'][0]['SENtype']['label'],$book);?>
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
		print ' value="'.$inval.'">'.get_string($description,$book).'</option>';
		}
    print '</select>';				
?>
		</div>

		<div class="center">
		  <label for="Rank">
			<?php print_string($SEN['SENtypes'][0]['SENtypeRank']['label'],$book);?>
		  </label>
<?php
	$enum=getEnumArray($SEN['SENtypes'][0]['SENtypeRank']['field_db']);
	print '<select id="Rank"  tabindex="'.$tab++.'" 
			name="'.$SEN['SENtypes'][0]['SENtypeRank']['field_db'].$key.'" size="1">';
	print '<option value=""></option>';		
	while(list($inval,$description)=each($enum)){	
		print '<option ';
		if($SEN['SENtypes'][0]['SENtypeRank']['value']==$inval){print "selected='selected'";}
		print " value='".$inval."'>".$description."</option>";
		}
	print '</select>';			
?>
		</div>
	  </fieldset>

	  <div class="right">
		<div class="tinytabs" id="sen">
		  <ul>
<?php
	$key=-1;
	while(list($key,$Subject)=each($SEN['NCmodifications'])){
		if(is_array($Subject)){
?>
			<li id="<?php print 'tinytab-sen-'.$Subject['Subject']['value'];?>"><p 
					 <?php if($key==0){ print ' id="current-tinytab" ';}?>
				class="<?php print $Subject['Subject']['value'];?>"
				onclick="tinyTabs(this)"><?php print $Subject['Subject']['value'];?></p>
			</li>

			<div class="hidden" id="tinytab-xml-sen-<?php print $Subject['Subject']['value'];?>">
			  <table>
				<tr>
				  <td>
<?php
				$cattype='sen';$required='no';
				$listname='extrasupport';
				$selextrasupport=$Subject['ExtraSupport']['value_db'];
				$listlabel=$Subject['ExtraSupport']['label'];
				include('scripts/list_category.php');
?>
				  </td>
				</tr>
				<tr>
				  <td>
				  <label for="Strengths">
					<?php print_string($Subject['Strengths']['label'],$book); ?>
				  </label>
				  </td>
				</tr>
				<tr>
				  <td>
				  <textarea id="Stengths" 
				  wrap="on" rows="5" tabindex="<?php print $tab++;?>" 
				  name="<?php print $Subject['Strengths']['field_db'].$key;?>" 
				  ><?php print $Subject['Strengths']['value']; ?></textarea>
				  </td>
				</tr>
				<tr>
				  <td>
				  <label for="Weaknesses">
					<?php print_string($Subject['Weaknesses']['label'],$book); ?>
				  </label>
				  </td>
				</tr>
				<tr>
				  <td>
				  <textarea id="Weaknesses" 
				  wrap="on" rows="5" tabindex="<?php print $tab++;?>"
				  name="<?php print $Subject['Weaknesses']['field_db'].$key;?>" 
				  ><?php print $Subject['Weaknesses']['value']; ?></textarea>
				  </td>
				</tr>
				<tr>
				  <td>
				  <label for="Strategies">
				  <?php print_string($Subject['Strategies']['label'],$book); ?> 
				  </label>
				  </td>
				</tr>
				<tr>
				  <td>
				  <textarea id="Strategies" 
				  wrap="on" rows="5" tabindex="<?php print $tab++;?>"
				  name="<?php print $Subject['Strategies']['field_db'].$key;?>" 
				  ><?php print $Subject['Strategies']['value']; ?></textarea>
				  </td>
				</tr>
			  </table>
			</div>
<?php
			}
		}
		$subject='addsubject';
?>
			<li id="<?php print 'tinytab-sen-'.$subject;?>"><p 
					 <?php if($key==-1){ print ' id="current-tinytab" ';}?>
				class="<?php print $subject;?>"
				onclick="tinyTabs(this)"><?php print_string($subject,$book);?></p></li>

			<div class="hidden" id="tinytab-xml-sen-<?php print $subject;?>">
			  <table>
				<tr>
				  <td>
				  </td>
				</tr>
				<tr>
				  <td>
					<button class="rowaction" 
					  name="ncmod" 
					  value="-1" 
					  onClick="processContent(this);">
					  <?php print_string('addsubject',$book);?>
					</button>
					<?php $required='no'; include('scripts/list_studentsubjects.php');?>
				  </td>
				</tr>
				<tr>
				  <td>
				  </td>
				</tr>
			  </table>
			</div>

		  </ul>
		</div>
		<div id="tinytab-display-sen" class="tinytab-display">
		</div>
	  </div>
	
 	<input type="hidden" name="current" value="<?php print $action;?>"/>
 	<input type="hidden" name="choice" value="<?php print $current;?>"/>
 	<input type="hidden" name="cancel" value=""/>
	</form>
  </div>
