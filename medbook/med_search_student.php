<?php
/**                                  med_view.php
 */

$action='med_search_student_action.php';
$choice='med_search_student.php';

two_buttonmenu();

?>
<div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <fieldset class="medbook">
		<legend><?php print_string('studentsearch');?></legend>

		<label>
			<span title="Search by forename, surname, full name, formgroup, name formgroup, enrolment number or student id.<br/><br/>If you select a yeargroup or a from group the speed of the search increases.">
				Search
			</span>
		</label>
		<input tabindex="<?php print $tab++;?>" type="text" name="searchvalue" value="" maxlength="30" title="E.g.: Claire Anderson 7EE or Anderson Claire"/>

		<div style="float:left">
			<?php 
				$newyid="";
				include('scripts/list_year.php'); ?>
		</div>


		<div style="float:left">
			<?php include('scripts/list_form.php'); ?>
		</div>

		<div style="float:left">
			<button type="submit" name="submit">
			  <?php print_string('search');?>
			</button>
		</div>

		<div style="float:right">
<?php
			if($_SESSION['searchstring']!='' and $_SESSION['time']!=''){echo 'The search for "'.$_SESSION['searchstring'].'" took '.number_format($_SESSION['time'],2).' seconds.';}
?>
		</div>
	  </fieldset>

	  <input type="hidden" name="current" value="<?php print $action;?>"/>
	  <input type="hidden" name="choice" value="<?php print $current;?>"/>
	  <input type="hidden" name="cancel" value=""/>
	</form>

	<br/>

	<div>
		<fieldset class="medbook">
			<legend>Students</legend>
<?php
	if(count($sid)>1){echo print_string("didyoumean").":<br>";}
	foreach($sid as $student_id){
		$student_info=fetchStudent($student_id);
		if($student_info['Forename']['value']!='' or $student_info['Surname']['value']!=''){
?>
			<div style="float:left;padding:1%;">
			  <div class="icon">
				  <a href="medbook.php?current=med_add_visit.php&sid=<?php print $student_id;?>">
					  <img src="<?php print 'scripts/photo_display.php?epfu='.$student_info['EPFUsername']['value'].'&enrolno='.$enrolno.'&size=midi';?>" 
					  	  style="display:block;margin-left:auto;margin-right:auto;"/>
				  </a>
			  </div>
			  <?php print $student_info['Forename']['value'].' '.$student_info['MiddleNames']['value'].' '.$student_info['Surname']['value'].' ('.$student_info['RegistrationGroup']['value'].')'; ?>
			</div>
<?php
			}
		}
?>
		</fieldset>
	</div>

</div>
