<?php
/**                                  student_reports.php    
 */

$cancel='student_view.php';

include('scripts/sub_action.php');

$extrabuttons=array();
$extrabuttons['previewselected']=array('name'=>'current',
									   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/reportbook/',
									   'value'=>'report_reports_print.php',
									   'onclick'=>'checksidsAction(this)');
two_buttonmenu($extrabuttons);
?>
  <div id="heading">
	<label><?php print_string('subjectreports'); ?></label>
	<?php print $Student['DisplayFullName']['value'];?>
  </div>
  <div class="content">
	<fieldset class="center">
	  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

<?php	  include('scripts/list_student_report.php');?>

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
