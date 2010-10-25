<?php
/**                                  student_attendance.php
 *
 *
 * TODO: complete so it uses school start date to offer all previous years.
 */

$cancel='student_view.php';

include('scripts/sub_action.php');

two_buttonmenu();
$date1=date('Y-m-d');
$currentyear=get_curriculumyear();
$yearstart=$currentyear-1;
$date0=$yearstart.'-08-20';

$script='report_attendance_print.php';

$reports=array();
$params=array(
			  'uniqueid'=>$sid,
			  'sids[]'=>$sid,
			  'startdate'=>$date0,
			  'enddate'=>$date1
			  );
$url=url_construct($params,$script);
$reports[]=array('title'=>'Current academic year','url'=>$url);



$date1=$yearstart.'-08-01';
$yearstart--;
$date0=$yearstart.'-08-20';
$params=array(
			  'uniqueid'=>$sid,
			  'sids[]'=>$sid,
			  'startdate'=>$date0,
			  'enddate'=>$date1
			  );
$url=url_construct($params,$script);
$reports[]=array('title'=>'Previous academic year','url'=>$url);
?>


  <div id="heading">
	<label><?php print_string('attendance'); ?></label>
	<?php print $Student['DisplayFullName']['value'];?>
  </div>
  <div class="content">

	<fieldset class="center divgroup">
	  <legend>
			<?php print get_string('attendance','reportbook'). ' '.get_string('reports',$book);?>
	  </legend>
<?php
	foreach($reports as $report){
?>
	<div style="float:left;width:24%;margin:2px;">
	  <table class="listmenu smalltable">
		<tr>
		  <td><h4>
<?php
print $report['title'];
?>
</h4>
		  </td>
		  <td>
		<button style="float:right;" title="<?php print_string('print');?>" 			 
			onclick="clickToPresent('reportbook','<?php print $report['url'];?>','attendance_summary')" >
			<img src="images/printer.png" />
		</button>
	  </td>
		</tr>
	  </table>
	</div>
<?php
		}
?>
	</fieldset>

	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <input type="hidden" name="cancel" value="<?php print $cancel;?>"/>
	  <input type="hidden" name="choice" value="<?php print $choice;?>"/>
	</form>
</div>
