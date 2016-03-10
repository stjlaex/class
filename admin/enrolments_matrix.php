<?php
/**								  		enrolments_matrix.php
 *
 * This produces two tables, first applications during current year
 * and then enrolments (and re-enrolments) for the actual roll.
 *
 */

$choice='enrolments_matrix.php';
$action='enrolments_matrix_action.php';

require_once('lib/curl_calls.php');

/* Some useful dates and times. */
$currentyear=get_curriculumyear();
$todate=date('Y-m-d');
$todate_time=mktime(0,0,0,date('m'),date('d'),date('Y'));
/* The end of the academic year - after which admissions will be closed. */
$cutoffdate=$currentyear.'-'.$CFG->enrol_cutoffmonth.'-01';
/* The start of next academic year. */
$targetdate=date('Y-m-d',mktime(0,0,0,$CFG->enrol_cutoffmonth+2,1,$currentyear));
$targetdate_time=mktime(0,0,0,$CFG->enrol_cutoffmonth+2,1,$currentyear);
$yearstart=$currentyear-1;
$yearstartprevious=$yearstart-1;
/* The date when the current academic year started. */
$yearstart_month=$CFG->enrol_cutoffmonth+1;
$yearstartdate=$yearstart.'-'.$yearstart_month.'-18';
$yearstartdate_time=mktime(0,0,0,$yearstart_month,1,$yearstart);
/* The date when the old academic year ended. */
$yearenddate=$yearstart.'-'.$CFG->enrol_cutoffmonth.'-1';

trigger_error('START:'.$yearstartdate.' END:'.$yearenddate,E_USER_WARNING);

if(isset($_POST['enrolyear']) and $_POST['enrolyear']!=''){$enrolyear=$_POST['enrolyear'];}
/* Display currentyear's enrolments if still near start of term (less than 8 weeks). */
elseif(($todate_time-$yearstartdate_time)<(86400*7*16)){$enrolyear=$currentyear;}
/* Default to displaying enrolment for next curriculum year. */
else{$enrolyear=$currentyear+1;}


$extrabuttons=array();
$extrabuttons['summary']=array('name'=>'chart',
							   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
							   'value'=>'admissions_chart.php',
							   'xmlcontainerid'=>'short',
							   'onclick'=>'checksidsAction(this)');
$extrabuttons['report']=array('name'=>'current',
							  'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
							  'xmlcontainerid'=>'report',
							  'value'=>'admissions_print.php',
							  'onclick'=>'checksidsAction(this)');
$extrabuttons['statistics']=array('name'=>'current',
								  'value'=>'yeargroup_statistics.php'
								  );
twoplus_buttonmenu($enrolyear,$currentyear+5,$extrabuttons,$book,$currentyear);


$d_a=mysql_query("SELECT MAX(date) FROM admission_stats WHERE year='$enrolyear';");
if(mysql_result($d_a,0)>0){
	$update=mysql_result($d_a,0);
	$updates=explode('-',$update);
	$diff=$todate_time - mktime(0,0,0,$updates[1],$updates[2],$updates[0]);
	$dayno=round($diff/(60*60*24));/* How days since last update */
	if($dayno>6){$save_stats=true;}
	else{$save_stats=false;}
	}
else{
	$save_stats=true;
	}


$yeargroups=list_yeargroups();
$yeargroup_names=array();/* The row index for both tables. */
foreach($yeargroups as $year){
	$yid=$year['id'];
	$yearcom=array('id'=>'','type'=>'year','name'=>$yid);
	$yeargroup_names[$yid]=$year['name'];
	$yeargroup_comids[$yid]=update_community($yearcom);
	}

/* List the boarder communities, they have their own nmbers. */
if(isset($CFG->enrol_boarders) and $CFG->enrol_boarders=='yes'){
	$boardercoms=(array)list_communities('accomodation');
	}
else{
	$boardercoms=array();
	}
?>

    <div id="heading">
        <h4><?php print_string('academicyear'); ?> <?php  print display_curriculumyear($enrolyear);?></h4>
    </div>

  <div id="viewcontent" class="content">
 	  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >

<?php
	include('enrolments_matrix_apptable.php');
	include('enrolments_matrix_enroltable.php');
	$tables=array();
	$tables[]=array('caption'=>'applications','rows'=>$app_tablerows,'cols'=>$appcols);
	$tables[]=array('caption'=>'enrolments','rows'=>$enrol_tablerows,'cols'=>$enrolcols);
	foreach($tables as $table){
?>
<h6><?php print get_string($table['caption'],$book). 
        ' - '.$CFG->schoolname.'  ('.display_date($todate).')';?></h6>
        
	  <table class="listmenu center smalltable">
		<tr>
		  <th><?php print display_curriculumyear($enrolyear);?></th>
<?php
		$coltotals=array();
		$totals=array();
		$totals['class']='other';
		$totals['values']=array();
		$totals['displays']=array();
		$totals['label']=get_string('total',$book).' '.get_string('numberofstudents',$book);
		/* Does the school have boarders? Add a row of sub-totals. */
		if(isset($boardercoms)){
			$boarder_totals=array();
			foreach($boardercoms as $index=>$boardercom){
				$boarder_totals[$index]['values']=array();
				$boarder_totals[$index]['displays']=array();
				$boarder_totals[$index]['class']='live';
				$boarder_totals[$index]['label']=get_string('boarders',$book).' '.$boardercom['name'];
				}
			}
		$cellclasses=array();
		foreach($table['cols'] as $colindex => $col){
			if(isset($col['display'])){
				$cellclasses[]=$col['class'];
?>
		  <th class="<?php print $col['class'];?>">
			<?php print $col['display'];?>
		  </th>
<?php
				$total=0;
				$total_boarders=array();
				if(isset($boardercoms)){
					foreach($boardercoms as $index=>$boardercom){
						$total_boarders[$index]=0;
						}
					}
				/* Calculate the totals for each column. */
				foreach($yeargroups as $yeargroup){
					$cellvalue=$table['rows'][$yeargroup['id']][$col['value']]['value'];
					$total+=$cellvalue;

					if(isset($boardercoms)){
						foreach($boardercoms as $index=>$boardercom){
							if(isset($table['rows'][$yeargroup['id']][$col['value']]['value_boarders'])){
								$total_boarders[$index]+=$table['rows'][$yeargroup['id']][$col['value']]['value_boarders'][$index];
								}
							}
						}
					//$total+=$table['rows'][$yeargroup['id']][$col['value']]['extravalue'];
					if($save_stats and isset($table['rows'][$yeargroup['id']][$col['value']]['name'])){
						$cellname=$table['rows'][$yeargroup['id']][$col['value']]['name'];
						mysql_query("INSERT admission_stats SET date='$todate', name='$cellname', year='$enrolyear', count='$cellvalue';");
						}
					}

				$totals['values'][$colindex]=$total;
				if(in_array($col['value'],$application_steps)){
					$totals['displays'][$colindex]='<a href="admin.php?current=enrolments_list.php&cancel='.
						$choice.'&choice='. $choice.'&enrolyear='. $enrolyear.'&yid=-100'.
						'&comid=-1'. '&enrolstatus='.$col['value'].'">'.$total.'</a>';
					}
				elseif($col['value']=='leaverssince'){
					$totals['displays'][$colindex]='<a href="admin.php?current=enrolments_list.php&cancel='.
						$choice.'&choice='. $choice.'&comid=-1&enrolyear='. $enrolyear.'&yid=-100&enrolstage=P">'.$total.'</a>';
					}
				elseif($col['value']=='newnewenrolments'){
					$totals['displays'][$colindex]='<a href="admin.php?current=enrolments_list.php&cancel='.
						$choice.'&choice='. $choice.'&enrolyear='. $enrolyear.
						'&comid=-1&yid=-100&enrolstage=C&startdate='.$yearstartdate.'">'.$total.'</a>';
					}
				 elseif($col['value']=='newenrolmentsprevious'){
					$totals['displays'][$colindex]='<a href="admin.php?current=enrolments_list.php&cancel='.
						$choice.'&choice='. $choice.'&enrolyear='.$enrolyear.
						'&comid=-1&yid=-100&enrolstage=AC&leavingdate='.($enrolyear-1).'-'.$CFG->enrol_cutoffmonth.'-01">'.$total.'</a>';
					}
				else{
					$totals['displays'][$colindex]=$total;
					}

				if(isset($boardercoms)){
					foreach($boardercoms as $index=>$boardercom){
						$boarder_totals[$index]['values'][$colindex]=$total_boarders[$index];

						if(in_array($col['value'],$application_steps)){
							$boarder_totals[$index]['displays'][$colindex]='<a href="admin.php?current=enrolments_list.php&cancel='.
							$choice.'&choice='. $choice.'&enrolyear='. $enrolyear.'&yid=-100'.
							'&comid=-1'. '&enrolstatus='.$col['value'].'&boarder='.$boardercom['name'].'">'.$total_boarders[$index].'</a>';
							}
						else{
							$boarder_totals[$index]['displays'][$colindex]=$total_boarders[$index];
							}
						}
					}
				if(isset($boardercoms) and $save_stats){
					$cellvalue=0;
					foreach($boardercoms as $index=>$boardercom){
						$cellvalue+=$total_boarders[$index];
						}
					if(isset($table['rows'][1][$col['value']]['name_boarders'])){
						$cellname=$table['rows'][1][$col['value']]['name_boarders'][0];
						}
					else{
						$cellname=$table['rows'][1][$col['value']]['name_boarder'];
						}
					mysql_query("INSERT admission_stats SET date='$todate', name='$cellname', year='$enrolyear', count='$cellvalue';");
					}
				}
			}

		$coltotals[]=$totals;
		if(isset($boardercoms)){
			foreach($boardercoms as $index=>$boardercom){
				$coltotals[]=$boarder_totals[$index];
				}
			}
?>
		</tr>
<?php
		foreach($table['rows'] as $rowindex => $tablecells){
			$colindex=0;
?>
		<tr>
		<th><?php print $yeargroup_names[$rowindex];?></th>
<?php
			foreach($tablecells as $cellindex => $cell){
				if(isset($cell['display'])){
?>
		  <td class="<?php print $cellclasses[$colindex++];?>">
			<?php print $cell['display'];?>
		  </td>
<?php
					}
				}
?>
		</tr>
<?php
			}
		foreach($coltotals as $totals){
?>
		<tr>
		  <th class="<?php print $totals['class'];?>">
			<?php print $totals['label'];?>
		  </th>
<?php
			foreach($totals['displays'] as $totaldisplay){
?>
				<td class="<?php print $totals['class'];?>"><?php print $totaldisplay;?></td>
<?php
				}
?>
		</tr>

<?php
			}
?>
	  </table>
<?php
		}
?>

	  <input type="hidden" name="enrolyear" value="<?php print $enrolyear;?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>

  </div>
	<div id="xml-short" style="display:none;">
	  <params>
		<transform>admission_chart_current_new</transform>
		<sids></sids>
	  </params>
	</div>
	<div id="xml-report" style="display:none;">
	  <params>
		<sids></sids>
	  </params>
	</div>
