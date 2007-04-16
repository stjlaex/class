<?php 
/**										search.php
 *
 * The options for searching in the InfoBook sidebar
 */
$action='search_action.php'
?>	
  <div style="visibility:hidden;" id="hiddenbookoptions" class="bookoptions">	
	<form id="infobookchoice" name="infobookchoice" method="post"
		action="infobook.php" target="viewinfobook">

	<fieldset class="infobook">
		<legend><?php print_string('studentgroups');?></legend>
<?php
	$onsidechange='yes'; include('scripts/list_year.php');
	$onsidechange='yes'; include('scripts/list_form.php');
	if($_SESSION['role']=='office' or $_SESSION['role']=='admin'){
		$type='admissions'; $onsidechange='yes'; include('scripts/list_community.php');
		}
?>
	</fieldset>

	  <input type="hidden" name="current" value="<?php print $action;?>"/>
	</form>

	<form id="quicksearch" name="quicksearch" method="post"
		action="infobook.php" target="viewinfobook">
<?php
	if($_SESSION['role']=='office' or $_SESSION['role']=='admin'){
?>
	  <fieldset class="infobook">
		<legend><?php print_string('contactsearch',$book);?></legend>
		<select class="searchlabel" type="text" id="Contactfield" 
		  tabindex="<?php print $tab++;?>" name="gfield" size="1">
<?php
		$selgfield='surname';
		/*only used for the infobook search options, not an enumarray at all!*/
		$contactfield=array(
						'surname' => 'name', 
						'country' => 'country'
						);
		$studentfield=array(
						'surname' => 'surname', 
						'forename' => 'forename', 
						'nationality' => 'nationality',
						'gender' => 'gender'
						);
		/***/
		$enum=$contactfield;
		while(list($val,$description)=each($enum)){	
				print '<option ';
				if(($selgfield==$val)){print ' selected="selected" ';}
				print ' value="'.$val.'">'.get_string($description,'infobook').'</option>';
				}
?>
		</select>
		<input tabindex="<?php print $tab++;?>" 
		  type="text" id="Contactname" name="gvalue" value="" maxlength="30"/>
	  </fieldset>

	  <fieldset class="infobook">
		<legend><?php print_string('studentsearch');?></legend>
		<select  class="searchlabel" type="text" id="Studentfield" 
		  tabindex="<?php print $tab++;?>" name="sfield" size="1">
<?php
		$selsfield='surname';
		$enum=$studentfield;
		while(list($val,$description)=each($enum)){	
				print '<option ';
				if(($selsfield==$val)){print ' selected="selected" ';}
				print ' value="'.$val.'">'.get_string($description,'infobook').'</option>';
				}
?>
		</select>
		<input tabindex="<?php print $tab++;?>" 
		  type="text" id="Studentvalue" name="svalue" value="" maxlength="30"/>

			<button type="submit" name="submit">
				<?php print_string('search');?>
			</button>
			<button type="reset" name="reset" value="Reset">
				<?php print_string('reset');?>
			</button>
	  </fieldset>
<?php
		}
	  else{
?>
	  <fieldset class="infobook">
		<legend><?php print_string('studentsearch');?></legend>
		<label for="Surname"><?php print_string('surname');?></label>
		<input tabindex="<?php print $tab++;?>" 
		  type="text" id="Surname" name="surname" value="" maxlength="30"/>
		  <label for="Forename"><?php print_string('forename');?></label>
		  <input tabindex="<?php print $tab++;?>" 
			type="text" id="Forename" name="forename" value="" maxlength="30"/>

			<button type="submit" name="submit">
				<?php print_string('search');?>
			</button>
			<button type="reset" name="reset" value="Reset">
				<?php print_string('reset');?>
			</button>
	  </fieldset>
<?php
		}
?>

	  <input type="hidden" name="current" value="<?php print $action;?>"/>
	</form>
  </div>
