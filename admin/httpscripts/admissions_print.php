<?php
/**									admissions_print.php
 *
 */

require_once('../../scripts/http_head_options.php');

$book='admin';
//if(isset($_GET['sids'])){}else{}
//if(isset($_POST['sids'])){}
if(isset($_GET['format'])){$format=$_GET['format'];}else{$format='long';}
if(isset($_POST['format'])){$format=$_POST['format'];}

$todate=date('Y-m-d');
$currentyear=get_curriculumyear();
$enrolyear=$currentyear+1;
$lastenrolyear=$enrolyear-1;
$beforelastenrolyear=$enrolyear-2;
$yearstart=$currentyear-1;
$yearstartdate=$yearstart.'-08-18';
$yearenddate=$yearstart.'-07-20';
$cutoffdate=$currentyear.'-'.$CFG->enrol_cutoffmonth.'-01';
$targetdate=date('Y-m-d',mktime(0,0,0,$CFG->enrol_cutoffmonth+2,1,$currentyear));
$yeargroups=list_yeargroups();

/* Does the school have boarders? */
if(isset($CFG->enrol_boarders) and $CFG->enrol_boarders=='yes'){
	$yeargroups[]=array('id'=>'boarder','name'=>'Residence','type'=>'subtotal');
	}

$d_a=mysql_query("SELECT MAX(date) FROM admission_stats WHERE year='$enrolyear' AND date<='$todate';");
if(mysql_result($d_a,0)>0){

	$currentdate=mysql_result($d_a,0);/* Date of most recent stats in the db */

	/* TODO: Set this last date properly */
	$lastdate=date('Y-m-d',mktime(0,0,0,date('m')-12,date('d')+14,date('Y')));
	$d_a=mysql_query("SELECT MAX(date) FROM admission_stats WHERE year='$lastenrolyear' AND date<='$lastdate';");
	$lastdate=mysql_result($d_a,0);/* Nearest date of most stats in the db 12 months ago*/

	$beforelastdate=date('Y-m-d',mktime(0,0,0,date('m')-24,date('d')+14,date('Y')));
	$d_a=mysql_query("SELECT MAX(date) FROM admission_stats WHERE year='$beforelastenrolyear' AND date<='$beforelastdate';");
	$beforelastdate=mysql_result($d_a,0);/* Nearest date of most stats in the db 24 months ago*/

	$currents=explode('-',$currentdate);
	$starts=explode('-',$yearstartdate);
	$diff=mktime(0,0,0,$currents[1],$currents[2],$currents[0]) - mktime(0,0,0,$starts[1],$starts[2],$starts[0]);
	$month=round($diff/(60*60*24*30));/* How months into academic year */
	}

$d_a=mysql_query("SELECT MAX(date) FROM admission_stats WHERE year='$lastenrolyear' AND date<='$cutoffdate';");
if(mysql_result($d_a,0)>0){
	$lastcurrentdate=mysql_result($d_a,0);/* Date of most recent stats in the db */
	}
$d_a=mysql_query("SELECT MAX(date) FROM admission_stats WHERE year='$beforelastenrolyear' AND date<='$todate';");
if(mysql_result($d_a,0)>0){
	$beforelastcurrentdate=mysql_result($d_a,0);/* Date of most recent stats in the db */
	}

$Stats=array();
$Stats['Stat']=array();
$Stats['School']['value']=$CFG->schoolname;
$Stats['School']['value']=$CFG->schoolname;
$doing=array();
$doing[]=array($enrolyear,$currentdate);
$doing[]=array($lastenrolyear,$lastdate);
$doing[]=array($lastenrolyear,$lastcurrentdate);
$doing[]=array($beforelastenrolyear,$beforelastcurrentdate);
$doing[]=array($beforelastenrolyear,$beforelastdate);

foreach($doing as $tableno => $todo){
	$year=$todo[0];
	$date=$todo[1];
	$Stat=array();
	$Stat['Groups']=array();
	if($tableno!=2){$Stat['Date']['value']=$date;}
	else{$Stat['Date']['value']=$currentdate;}
	$Stat['Enrolyear']['value']=$year;
	foreach($yeargroups as $group){
		$yid=$group['id'];
		$Group=array();
		$Group['id']=$yid;
		$Group['name']=$group['name'];
		if(isset($group['type'])){$Group['type']=$group['type'];}else{$Group['type']='year';}
		$Group['Number']=array();
		$d_s=mysql_query("SELECT name, count FROM admission_stats WHERE 
							name LIKE '%:$yid' AND year='$year' AND date='$date' ORDER BY name;");
		while($stat=mysql_fetch_array($d_s,MYSQL_ASSOC)){
			$Number=array();
			$Number['name']=''.$stat['name'];
			$Number['value']=''.$stat['count'];
			if($stat['name']=='EN:'.$yid and $year<$enrolyear){
				/* A work around because enquiries are not recorded continuosly - just get year end total and interpolate. */
				$d_en=mysql_query("SELECT MAX(count) FROM admission_stats WHERE year='$year';");
				$en=mysql_result($d_en,0);
				$d_en=mysql_query("SELECT SUM(count) FROM admission_stats WHERE 
							name LIKE '%:$yid' AND year='$year' AND date='$date';");
				$one=mysql_result($d_en,0);
				$d_en=mysql_query("SELECT SUM(count) FROM admission_stats WHERE 
							year='$year' AND date='$date';");
				$tot=mysql_result($d_en,0);
				$Number['value']=round(($en/11)*($month-1)*($one/$tot));
				//$Number['value']=$one. ' : ' .$tot;
				}
			$Group['Number'][]=$Number;
			}
		$Stat['Groups']['Group'][]=$Group;
		}
	$Stats['Stat'][]=$Stat;
	}


if($format=='short'){

	$Stats['Transform']='admission_tables';

	$app_cols=array(
				'applications'=>'TOTAL'
				,displayEnum('AT','enrolstatus')=>'AT'
				,displayEnum('CA','enrolstatus')=>'CA'
				,displayEnum('RE','enrolstatus')=>'RE'
				,displayEnum('ACP','enrolstatus')=>'ACP'
				,displayEnum('AC','enrolstatus')=>'AC'
				,displayEnum('WL','enrolstatus')=>'WL'
				);
	$appnext_cols=array('enquiries'=>'EN'
				,'applications'=>'TOTAL'
				,displayEnum('AT','enrolstatus')=>'AT'
				,displayEnum('CA','enrolstatus')=>'CA'
				,displayEnum('RE','enrolstatus')=>'RE'
				,displayEnum('ACP','enrolstatus')=>'ACP'
				,displayEnum('AC','enrolstatus')=>'AC'
				,displayEnum('WL','enrolstatus')=>'WL'
				);
	$enrol_cols=array('reenroled'=>'reenroled'
				  ,'newnewenrolments'=>'newnewenrolments'
				  ,'leaverssince'=>'leaverssince'
				  ,'currentroll'=>'currentroll'
				  ,'budgetroll'=>'budgetroll'
				  ,'capacity'=>'capacity'
				  ,'spaces'=>'spaces'
				  );
	$enrolnext_cols=array('reenroling'=>'reenroling'
					  ,'pending'=>'pending'
					  ,'notreenroling'=>'leavers'
					  ,'transfersin'=>'transfersin'
					  ,'newenrolments'=>'newenrolments'
					  ,'projectedroll'=>'projectedroll'
					  ,'targetroll'=>'targetroll'
					  ,'budgetroll'=>'budgetroll'
					  ,'capacity'=>'capacity'
					  ,'spaces'=>'spaces'
					  );
	}
else{

	$Stats['Transform']='admission_tables_long';

	$app_cols=array(
				'applications'=>'TOTAL'
				,displayEnum('AT','enrolstatus')=>'AT'
				,displayEnum('CA','enrolstatus')=>'CA'
				,displayEnum('RE','enrolstatus')=>'RE'
				,displayEnum('ACP','enrolstatus')=>'ACP'
				,displayEnum('AC','enrolstatus')=>'AC'
				,displayEnum('WL','enrolstatus')=>'WL'
				,'newnewenrolments'=>'newnewenrolments'
				);
	$appnext_cols=array('enquiries'=>'EN'
				,'applications'=>'TOTAL'
				,displayEnum('AT','enrolstatus')=>'AT'
				,displayEnum('CA','enrolstatus')=>'CA'
				,displayEnum('RE','enrolstatus')=>'RE'
				,displayEnum('ACP','enrolstatus')=>'ACP'
				,displayEnum('AC','enrolstatus')=>'AC'
				,displayEnum('WL','enrolstatus')=>'WL'
				);
	$enrol_cols=array('reenroled'=>'reenroled'
				  ,'newenrolmentsprevious'=>'newenrolmentsprevious'
					  //,'transfersprevious'=>'transfersprevious'
				  ,'newnewenrolments'=>'newnewenrolments'
				  ,'leaverssince'=>'leaverssince'
				  ,'currentroll'=>'currentroll'
				  ,'budgetroll'=>'budgetroll'
				  ,'leaverstotal'=>'leaverstotal'
				  ,'notinvoiced'=>'notinvoiced'
				  ,'capacity'=>'capacity'
				  ,'spaces'=>'spaces'
				  );
	$enrolnext_cols=array('reenroling'=>'reenroling'
					  ,'transfersin'=>'transfersin'
					  ,'newenrolments'=>'newenrolments'
					  ,'projectedroll'=>'projectedroll'
					  ,'budgetroll'=>'budgetroll'
					  ,'targetroll'=>'targetroll'
					  ,'transfersout'=>'transfersout'
					  ,'notreenroling'=>'leavers'
					  ,'projectedleavers'=>'projectedleavers'
					  ,'capacity'=>'capacity'
					  ,'pending'=>'pending'
					  ,'spaces'=>'spaces'
					  );
	}



$tables=array('appnext'=>$appnext_cols,'enrolnext'=>$enrolnext_cols,'appcurrent'=>$app_cols,'enrolcurrent'=>$enrol_cols);
$Stats['tables']=array();
$Stats['tables']['table']=array();
foreach($tables as $tablename=>$table_cols){
	$Table=array();
	$Table['cols']=array();
	$Table['name']=$tablename;
	foreach($table_cols as $name => $col){
		$Col=array('name'=>'','date'=>'');
		$Col['name']=get_string($name,$book);
		if($name=='projectedroll'){$Col['date'].=display_date($todate);}
		elseif($name=='targetroll'){$Col['date'].=display_date($targetdate);}
		$Col['value']=$col;
		$Table['cols']['col'][]=$Col;
		}
	$Stats['tables']['table'][]=$Table;
	}

$Stats['DateStamp']=display_date($todate);
$Stats['Paper']='landscape';

$returnXML=$Stats;
$rootName='Stats';

require_once('../../scripts/http_end_options.php');
exit;
?>
