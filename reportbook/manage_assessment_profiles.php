<?php
/**								manage_assessment_profiles.php
 *
 */

$action='manage_assessment_profiles_action.php';
$choice='new_assessment.php';
$cancel="new_assessment.php";

three_buttonmenu();
?>

  <div class="topform divgroup">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
		<fieldset class="divgroup">
			<div class="left">
				<p>
					<label for="Name"><?php print_string('profilename',$book);?></label>
					<input class="required" type="text" id="Name" tabindex="<?php print $tab++;?>" name="name"  maxlength="59" value="" />
				</p>
<?php
					$selbid='%';
					$listlabelstyle='external';
					$listlabel='subject';
					include('scripts/list_subjects.php');

					$selcomponentstatus='None';
					$listlabelstyle='external';
					$required='no';
					include('scripts/list_componentstatus.php');

?>
					<div>
<?php
					$seltemplate='';
					$required='no';
					include('scripts/list_template.php');
?>
					</div>
			</div>
		</fieldset>

	  <input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $current;?>" />
	</form>
  </div>
  <div class="content">
    <table class="listmenu" name="listmenu">
	<caption><?php print_string('profiles', $book); ?></caption>
        <tr>
            <th><?php print_string('profilename', $book); ?></th>
            <th><?php print_string('subjectcomponent', $book); ?></th>
            <th><?php print_string('template', $book); ?></th>
            <th><?php print_string('subject', $book); ?></th>
            <th><?php print_string('ratingname', $book); ?></th>
        </tr>
<?php
    $profiles=(array)list_assessment_profiles($rcrid);
    foreach($profiles as $profile) {
	print "<tr>";
	print "<td>".$profile['name']."</td>";
	print "<td>".$profile['component_status']."</td>";
	print "<td>".$profile['transform']."</td>";
	print "<td>".$profile['subject_id']."</td>";
	print "<td>".$profile['rating_name']."</td>";
	print "</tr>";
    }
?>
    </table>
  </div>
