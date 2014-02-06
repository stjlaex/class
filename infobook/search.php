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
		<form id="infobookchoice" name="infobookchoice" method="post" action="infobook.php" target="viewinfobook">
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
		  <form id="groupchoice" name="groupchoice"  method="post" action="infobook.php" target="viewinfobook">
			<button type="submit" name="submit">
			<?php print get_string('custom',$book).' '.get_string('groups');?>
			</button>
			<input type="hidden" name="current" value="group_search.php"/>
		  </form>
<?php
		if($_SESSION['role']=='admin' or $_SESSION['role']=='district'){
?>
		  <form id="updatesfile" name="updatesfile"  method="post" action="infobook.php" target="viewinfobook">
			<button type="submit" name="submit">
			<?php print get_string('updatesfile',$book);?>
			</button>
			<input type="hidden" name="current" value="updates_file.php"/>
		  </form>
<?php
			}
?>
<?php
		}
?>
	</fieldset>

   	<form id="quicksearch" name="quicksearch" method="post" action="infobook.php" target="viewinfobook">
<?php
	   if($_SESSION['role']=='office' or $_SESSION['role']=='admin'){
?>
	  <fieldset class="infobook">
		<legend><?php print_string('contactsearch',$book);?></legend>

		<!--select class="switcher" type="text" id="contact" tabindex="<?php print $tab++;?>" name="gfield" size="1">
<?php
		$selgfield='surname';
		/*only used for the infobook search options, not an enumarray at all!*/
		$contactfield=array(
							'surname' => 'surname',
							'forename' => 'forename',
							'country' => 'country',
							'postcode' => 'postcode',
							'email' => 'email',
							'epfusername' => 'epfusername'
							);
		$studentfield=array(
							'surname' => 'surname', 
							'forename' => 'forename', 
							'preferredforename' => 'preferredforename', 
							'nationality' => 'nationality',
							'gender' => 'gender',
							'formerupn' => 'enrolmentnumber',
							'epfusername' => 'epfusername'
							);
		if(isset($CFG->enrol_boarders) and $CFG->enrol_boarders=='yes'){
			$studentfield['boarder']='boarder';
			}
		/***/
		$enum=$contactfield;
		while(list($val,$description)=each($enum)){	
				print '<option ';
				if(($selgfield==$val)){print ' selected="selected" ';}
				print ' value="'.$val.'">'.get_string($description,'infobook').'</option>';
				}
?>
		</select-->

		<div id="switchcontact">
		  <input tabindex="<?php print $tab++;?>" type="text" id="Contactsurname" name="contactsurname" value="" maxlength="30"/>
		</div>
	  </fieldset>
<?php
		   }

if($_SESSION['worklevel']>-1){
?>
	  <fieldset class="infobook">
		<legend><?php print_string('studentsearch');?></legend>
		<!--select class="switcher" type="text" id="student" tabindex="<?php print $tab++;?>" name="sfield" size="1">
<?php
		$selsfield='surname';
		$enum=array(
					'surname' => 'surname', 
					'forename' => 'forename', 
					'preferredforename' => 'preferredforename',
					'formerupn' => 'enrolmentnumber'
					);
		while(list($val,$description)=each($enum)){	
				print '<option ';
				if(($selsfield==$val)){print ' selected="selected" ';}
				print ' value="'.$val.'">'.get_string($description,'infobook').'</option>';
				}
?>
		</select-->

		<div id="switchstudent">
		  <input tabindex="<?php print $tab++;?>" type="text" name="studentsurname" value="" maxlength="30"/>
		</div>
		<button type="submit" name="submit">
		  <?php print_string('search');?>
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
		<div id="switchcontactforename" class="hidden">
		  <input tabindex="<?php print $tab++;?>" 
			type="text" id="Contactforename" name="contactforename" value="" maxlength="30"/>
		</div>
		<div id="switchcontactcountry" class="hidden">
<?php 
	    $listname='contactcountry';$listlabel='';$listfilter='address';
		include('scripts/set_list_vars.php');
		list_select_enum('country',$listoptions,$book);
?>
		</div>
		<div id="switchcontactpostcode" class="hidden">
<?php 
		$d_p=mysql_query("SELECT DISTINCT postcode AS id, postcode AS name FROM address ORDER BY postcode;");
	    $listname='contactpostcode';$listlabel='';$listfilter='address';
		include('scripts/set_list_vars.php');
		list_select_db($d_p,$listoptions,$book);
?>
		</div>
		<div id="switchcontactemail" class="hidden">
		  <input tabindex="<?php print $tab++;?>" 
			type="text" name="contactemail" value="" maxlength="30"/>
		</div>
		<div id="switchcontactepfusername" class="hidden">
		  <input tabindex="<?php print $tab++;?>" 
			type="text" name="contactepfusername" value="" maxlength="30"/>
		</div>
		<div id="switchstudentsurname" class="hidden">
		  <input tabindex="<?php print $tab++;?>" 
			type="text" name="studentsurname" value="" maxlength="30"/>
		</div>
		<div id="switchstudentforename" class="hidden">
		  <input tabindex="<?php print $tab;?>" 
			type="text" name="studentforename" value="" maxlength="30"/>
		</div>
		<div id="switchstudentpreferredforename" class="hidden">
		  <input tabindex="<?php print $tab;?>" 
			type="text" name="studentpreferredforename" value="" maxlength="30"/>
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
		<div id="switchstudentformerupn" class="hidden">
		  <input tabindex="<?php print $tab++;?>" 
			type="text" name="studentformerupn" value="" maxlength="30"/>
		</div>
		<div id="switchstudentepfusername" class="hidden">
		  <input tabindex="<?php print $tab++;?>" 
			type="text" name="studentepfusername" value="" maxlength="30"/>
		</div>
<?php
	if(isset($CFG->enrol_boarders) and $CFG->enrol_boarders=='yes'){
?>
		<div id="switchstudentboarder"  class="hidden">
<?php
		$listname='studentboarder';$listlabel='';$listfilter='info';
		include('scripts/set_list_vars.php');
		list_select_enum('boarder',$listoptions,$book);
?>
		</div>
<?php
		}
?>
  </div>
