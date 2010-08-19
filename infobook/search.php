<?php 
/**										search.php
 *
 * The options for searching in the InfoBook sidebar
 */
$action='search_action.php'
?>	
  <div style="visibility:hidden;" id="hiddenbookoptions" class="bookoptions">	


	<fieldset class="infobook">
		<legend><?php print_string('studentgroups');?></legend>
		<form id="infobookchoice" name="infobookchoice" method="post"
			action="infobook.php" target="viewinfobook">
<?php
	$onsidechange='yes'; include('scripts/list_year.php');
	$onsidechange='yes'; include('scripts/list_form.php');

	$listtype='infosearch';
	$onsidechange='yes';
	include('scripts/list_community.php');

if($_SESSION['role']=='office' or $_SESSION['role']=='admin' or $_SESSION['role']=='sen'){
		$listtype='admissions';
		$listlabel='enrolments';
		$onsidechange='yes'; 
		include('scripts/list_community.php');
		}
?>
		<input type="hidden" name="current" value="<?php print $action;?>"/>
		</form>

<?php
	   if($_SESSION['role']=='office' or $_SESSION['role']=='admin'){
?>
		<div>
		  <form id="groupchoice" name="groupchoice"  method="post" 
			action="infobook.php" target="viewinfobook">
			<button type="submit" name="submit">
			<?php print get_string('custom',$book).' '.get_string('groups');?>
			</button>
			<input type="hidden" name="current" value="group_search.php"/>
		</form>
		</div>
<?php
		}
?>

	</fieldset>

	<div style="margin-top:20px;">
	</div>

   	<form id="quicksearch" name="quicksearch" method="post"
				action="infobook.php" target="viewinfobook">
<?php
	   if($_SESSION['role']=='office' or $_SESSION['role']=='admin'){
?>
	  <fieldset class="infobook">
		<legend><?php print_string('contactsearch',$book);?></legend>

		<select class="switcher" type="text" id="contact" 
		  onChange="selerySwitch('contact',this.value)"  
		  tabindex="<?php print $tab++;?>" name="gfield" size="1">
<?php
		$selgfield='surname';
		/*only used for the infobook search options, not an enumarray at all!*/
		$contactfield=array(
							'country' => 'country',
							'surname' => 'name'
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

		<div id="switchcontact">
		</div>
	  </fieldset>

	  <fieldset class="infobook">
		<legend><?php print_string('studentsearch');?></legend>
		<select class="switcher" type="text" id="student" 
		  onChange="selerySwitch('student',this.value)" 
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

		<div id="switchstudent">
		</div>

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

		<div id="switchcontactsurname" class="hidden">
		  <input tabindex="<?php print $tab++;?>" 
			type="text" id="Contactsurname" name="contactsurname" value="" maxlength="30"/>
		</div>
		<div id="switchcontactcountry" class="hidden">
<?php 
	    $listname='contactcountry';$listlabel='';$listfilter='address';
		include('scripts/set_list_vars.php');
		list_select_enum('country',$listoptions,$book);
?>
		</div>
		<div id="switchstudentsurname" class="hidden">
		  <input tabindex="<?php print $tab++;?>" 
			type="text" name="studentsurname" value="" maxlength="30"/>
		</div>
		<div id="switchstudentforename" class="hidden">
		  <input tabindex="<?php print $tab;?>" 
			type="text" name="studentforename" value="" maxlength="30"/>
		</div>
		<div id="switchstudentgender"  class="hidden">
<?php 
		$listname='studentgender';$listlabel='';
		include('scripts/set_list_vars.php');
		list_select_enum('gender',$listoptions,$book);
?>
		</div>
		<div id="switchstudentnationality"  class="hidden">
<?php
		$listname='studentnationality';$listlabel='';$listfilter='info';
		include('scripts/set_list_vars.php');
		list_select_enum('nationality',$listoptions,$book);
?>
		</div>
  </div>

