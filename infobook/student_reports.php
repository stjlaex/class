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


$epfu=strtolower($Student['EPFUsername']['value']);
if(empty($CFG->eportfolio_db)){
	/* If epfdb='' then simply list epfuser's directory looking for reports. */
	$directory='files/' . substr($epfu,0,1) . '/' . $epfu;
	$report_files=(array)list_directory_files($CFG->eportfolio_dataroot.'/'.$directory,'pdf');
	}
else{
	$report_files=(array)elgg_list_files($epfu,'report',true);
	}
?>

  <div id="heading">
	<label><?php print_string('subjectreports'); ?></label>
	<?php print $Student['DisplayFullName']['value'];?>
  </div>
  <div class="content">

	<fieldset class="center">
	  <legend>
			<?php print get_string('published','reportbook'). ' '.get_string('reports',$book);?>
	  </legend>
<?php
	foreach($report_files as $reportdetails){
?>
	<div style="float:left;width:24%;margin:2px;padding:2px 4px;background-color:#ffffff;">
<?php
	if(trim($epfu)==''){$epfu=strtolower($Student['EnrolNumber']['value']);}
	if(!is_array($reportdetails)){$report=array('title'=>$reportdetails,'name'=>$reportdetails.'.pdf','location'=>$directory.'/'.$reportdetails.'.pdf');}
	else{$report=$reportdetails;}
	print '<a href="http://'.$CFG->siteaddress.$CFG->sitepath.'/'.$CFG->applicationdirectory.'/scripts/file_display.php?epfu='.$epfu.'&location='.$report['location'].'&filename='.$report['name'].'" /><label>'.$report['title'].'</label><img src="images/printer.png" /></a>';
?>
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

