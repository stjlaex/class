<?php
/**								import_assessment_scores.php
 *
 */

$action='import_assessment_scores_action.php';
$choice='new_assessment.php';

$curryear=$_POST['curryear'];

/*Check user has permission to configure*/
$perm=getCoursePerm($rcrid,$respons);
$neededperm='x';
include('scripts/perm_action.php');

include('scripts/sub_action.php');

function list_all_subjects(){
	$subjects=array();
	$d_s=mysql_query("SELECT id,name FROM subject ORDER BY id ASC;");
	$subjects['G']=array('id'=>'G','name'=>'General');
	while($subject=mysql_fetch_array($d_s,MYSQL_ASSOC)){
		$subjects[$subject['id']]=$subject;
		}
	return $subjects;
	}
$multiple=array(0=>"Multiple");
$subjects=array_merge($multiple,list_all_subjects());
$profiles=array_merge($multiple,list_assessment_profiles($rcrid));

three_buttonmenu();
?>
<div id="heading">
<?php print get_string('importscores',$book);?>
</div>

<div class="content">
	<form id="formtoprocess" name="formtoprocess" 
	  enctype="multipart/form-data" method="post" action="<?php print $host;?>">

	  <fieldset class="left">
		<legend><?php print_string('firstcolumnidentifier',$book);?></legend>

		<label for="enrolno"><?php print_string('enrolmentnumber','infobook');?></label>
		<input type="radio" name="firstcol" tabindex="<?php print $tab++;?>"
		  eitheror="sid"   checked="checked" 
		  title="" id="enrolno" value="enrolno" />

		<label for="sid"><?php print_string('studentdbid',$book);?></label>
		<input type="radio" name="firstcol" tabindex="<?php print $tab++;?>"
			eitheror="enrolno" 
			id="sid" title="" value="sid" />

		<label for="upn"><?php print_string('upn',$book);?></label>
		<input type="radio" name="firstcol" tabindex="<?php print $tab++;?>"
			eitheror="enrolno" 
			id="upn" title="" value="upn" />
	  </fieldset>


	  <fieldset class="right">
		<legend><?php print_string('selectfiletoimportfrom');?></legend>
		<label for="File name"><?php print_string('filename');?></label>
		<input style="width:20em;" type="file" id="File name" 
		  tabindex="<?php print $tab++;?>" class="required" name="importfile" />
		<input type="hidden" name="MAX_FILE_SIZE" value="800000">
	  </fieldset>

	  <fieldset class="right">
		<legend><?php print_string('other');?></legend>
<?php
			$listname='subject';
			$listlabel='subject';
			$listlabelstyle='external';
			include('scripts/set_list_vars.php');
			list_select_list($subjects,$listoptions);
?>
		<div class="right">
<?php
			$listname='profile';
			$listlabel='profile';
			$listlabelstyle='external';
			include('scripts/set_list_vars.php');
			list_select_list($profiles,$listoptions);
?>
		<br>
<?php
			$listname='year';
			$required='no';
			$listlabelstyle='external';
			include('scripts/list_calendar_year.php');
?>
		</div>
	  </fieldset>

	  <fieldset class="left">
		<legend><?php print_string('fieldseparator',$book);?></legend>
		<label for="sid"><?php print_string('comma',$book);?></label>
		<input type="radio" name="separator" tabindex="<?php print $tab++;?>"
			eitheror="enrolno"   checked="checked" 
			id="comma" title="" value="comma" />

		<label for="enrolno"><?php print_string('semicolon','infobook');?></label>
		<input type="radio" name="separator" tabindex="<?php print $tab++;?>"
		  eitheror="sid"  
		  title="" id="semicolon" value="semicolon" />
	  </fieldset>


	  <fieldset class="left">
		<legend><?php print_string('assessmentcolumnstart',$book);?></legend>
<?php
			$listname='colstart';
			$listlabel='columnno';
			$selcolstart='3';
			$grades=array('2'=>'3','3'=>'4','4'=>'5','5'=>'6','6'=>'7','7'=>'8');
			include('scripts/set_list_vars.php');
			list_select_list($grades,$listoptions,$book);
?>
	  </fieldset>

	  <fieldset class='right'>
	    <legend><?php print_string('firstrowheaders',$book);?></legend>
	  	<div class="left">
				<label><?php print_string('headers');?></label>
				<input type="checkbox" name="headers" value="yes">
			</div>
	  </fieldset>


 	<input type="hidden" name="eid" value="<?php print $eid; ?>">
 	<input type="hidden" name="curryear" value="<?php print $curryear; ?>">
 	<input type="hidden" name="cancel" value="<?php print $choice; ?>">
 	<input type="hidden" name="current" value="<?php print $action; ?>">
 	<input type="hidden" name="choice" value="<?php print $choice; ?>">
	</form>
  </div>

