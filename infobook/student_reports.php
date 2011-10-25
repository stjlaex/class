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

/* TODO: If epfdb='' then simply list epfuser's directory looking for reports. */
$report_files=(array)elgg_list_files($Student['EPFUsername']['value'],'report',true);
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
<?php
	foreach($report_files as $report){
?>
	<div style="float:left;width:24%;margin:2px;">
	  <table class="listmenu smalltable">
		<tr>
		  <td>
			<h4><?php print $report['title'];?></h4>
		  </td>
		  <td>
<?php
	$epfu=strtolower($Student['EPFUsername']['value']);
	if(trim($epfu)==''){$epfu=strtolower($Student['EnrolNumber']['value']);}
	print '<a style="float:right;" href="http://'.$CFG->siteaddress.$CFG->sitepath.'/'.$CFG->applicationdirectory.'/scripts/file_display.php?epfu='.$epfu.'&location='.$report['location'].'&filename='.$report['name'].'" /><img src="images/printer.png" /></a>';
?>
		  </td>
		</tr>
	  </table>
	</div>
<?php
		}
?>
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

