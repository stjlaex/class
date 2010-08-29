<?php
/**                                  student_reports.php
 *
 */

$cancel='student_view.php';

include('scripts/sub_action.php');
require_once($CFG->dirroot.'/lib/eportfolio_functions.php');

$extrabuttons=array();
$extrabuttons['previewselected']=array('name'=>'current',
									   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/reportbook/',
									   'value'=>'report_reports_print.php',
									   'onclick'=>'checksidsAction(this)');
two_buttonmenu($extrabuttons);

$report_files=(array)elgg_list_files($Student['EPFUsername']['value'],'report',true);
?>

<?php
include('scripts/epf_access.php');
?>

  <div id="heading">
	<label><?php print_string('subjectreports'); ?></label>
	<?php print $Student['DisplayFullName']['value'];?>
  </div>
  <div class="content">

	<fieldset class="center divgroup">
	  <legend>
			<?php print get_string('published','reportbook'). ' '.get_string('reports',$book);?>
	  </legend>
	  <table class="listmenu center">
<?php
	foreach($report_files as $file){
		print '<tr><td><a href="'.$file['url'].'" target="_blank">'.$file['title'].'</a></td></tr>';
		print '<tr><td></td></tr>';
		}
?>
	  </table>
	</fieldset>

	<fieldset class="center">
	  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

<?php
	  include('scripts/list_student_report.php');
?>

		<div id="xml-checked-action" style="display:none;">
		  <params>
			<sids><?php print $sid;?></sids>
			<selectname>wrapper_rid</selectname>
		  </params>
		</div>

		<input type="hidden" name="cancel" value="<?php print $cancel;?>"/>
		<input type="hidden" name="choice" value="<?php print $choice;?>"/>
	  </form>
	</fieldset>
  </div>

