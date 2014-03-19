<?php
/**                                  med_view.php
 */

$action='med_search_student_action.php';
$choice='med_search_student.php';

two_buttonmenu();

?>
<div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <fieldset class="divgroup medbook">
		<h5><?php print_string('studentsearch');?></h5>
		<label>
			<span title="Search by forename, surname, full name, formgroup, name formgroup, enrolment number or student id.<br/><br/>If you select a yeargroup or a from group the speed of the search increases.">
				<?php print_string('search');?>
			</span>
		</label>
		<input tabindex="<?php print $tab++;?>" type="text" name="searchvalue" value="" maxlength="30" title="E.g.: Claire Anderson 7EE or Anderson Claire"/>

			<?php 
				$newyid="";
				include('scripts/list_year.php'); ?>

			<?php include('scripts/list_form.php'); ?>

			<button type="submit" name="submit">
			  <?php print_string('search');?>
			</button>
            <span>
                <?php
                    if($_SESSION['searchstring']!='' and $_SESSION['searchtime']!=''){echo 'The search for "'.$_SESSION['searchstring'].'" took '.number_format($_SESSION['searchtime'],2).' seconds.';}
                ?>
            </span>
	  </fieldset>

	  <input type="hidden" name="current" value="<?php print $action;?>"/>
	  <input type="hidden" name="choice" value="<?php print $current;?>"/>
	  <input type="hidden" name="cancel" value=""/>
	</form>

	<br/>

	<div>
		<fieldset class="divgroup">
			<h5><?php print_string('students');?></h5>
<?php
	if(count($sid)>1){echo print_string("didyoumean").":<br>";}
    print ('<ul>');
	foreach($sid as $student_id){
		$student_info=fetchStudent($student_id);
		if($student_info['Forename']['value']!='' or $student_info['Surname']['value']!=''){
?>
            <li>
                <a href="medbook.php?current=med_add_visit.php&sid=<?php print $student_id;?>">
				    <img src="<?php print 'scripts/photo_display.php?epfu='.$student_info['EPFUsername']['value'].'&enrolno='.$enrolno.'&size=midi';?>" style="display:block;margin-left:auto;margin-right:auto;"/>
				</a>
				<?php print $student_info['Forename']['value'].' '.$student_info['MiddleNames']['value'].' '.$student_info['Surname']['value'].' ('.$student_info['RegistrationGroup']['value'].')'; ?>
            </li>
			  
<?php
			}
		}
?>
		</fieldset>
	</div>

</div>
