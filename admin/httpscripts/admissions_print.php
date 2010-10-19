<?php
/**									admissions_print.php
 *
 */

require_once('../../scripts/http_head_options.php');

$book='admin';
//if(isset($_GET['sids'])){}else{}
//if(isset($_POST['sids'])){}

$todate=date('Y-m-d');
$currentyear=get_curriculumyear();
$enrolyear=$currentyear+1;
$lastenrolyear=$enrolyear-1;
$beforelastenrolyear=$enrolyear-2;
$yearstart=$currentyear-1;
$yearstartdate=$yearstart.'-08-20';
$yearenddate=$yearstart.'-07-01';
$yeargroups=list_yeargroups();
$d_a=mysql_query("SELECT MAX(date) FROM admission_stats WHERE year='$enrolyear' AND date<='$todate';");
if(mysql_result($d_a,0)>0){
	$currentdate=mysql_result($d_a,0);/* Date of most recent stats in the db */
	$lastdate=date('Y-m-d',mktime(0,0,0,date('m')-11,date('d'),date('Y')));
	$d_a=mysql_query("SELECT MAX(date) FROM admission_stats WHERE year='$lastenrolyear' AND date<='$lastdate';");
	$lastdate=mysql_result($d_a,0);/* Nearest date of most stats in the db 12 months ago*/
	$currents=explode('-',$currentdate);
	$starts=explode('-',$yearstartdate);
	$diff=mktime(0,0,0,$currents[1],$currents[2],$currents[0]) - mktime(0,0,0,$starts[1],$starts[2],$starts[0]);
	$month=round($diff/(60*60*24*30));/* How months into academic year */
	}
$d_a=mysql_query("SELECT MAX(date) FROM admission_stats WHERE year='$lastenrolyear' AND date<='$todate';");
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

foreach($doing as $todo){
	$year=$todo[0];
	$date=$todo[1];
	$Stat=array();
	$Stat['Groups']=array();
	$Stat['Date']['value']=$date;
	$Stat['Enrolyear']['value']=$year;
	foreach($yeargroups as $group){
		$yid=$group['id'];
		$Group=array();
		$Group['id']=$yid;
		$Group['name']=get_yeargroupname($yid);
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
				  ,'newenrolments'=>'newenrolments'
				  ,'leaverssince'=>'leaverssince'
				  ,'currentroll'=>'currentroll'
				  ,'budgetroll'=>'budgetroll'
				  ,'capacity'=>'capacity'
				  ,'spaces'=>'spaces'
				  );
$enrolnext_cols=array('reenroling'=>'reenroling'
					  ,'pending'=>'pending'
					  ,'transfersin'=>'transfersin'
					  ,'newenrolments'=>'newenrolments'
					  ,'leavers'=>'leavers'
					  ,'projectedroll'=>'projectedroll'
					  ,'targetroll'=>'targetroll'
					  ,'budgetroll'=>'budgetroll'
					  ,'capacity'=>'capacity'
					  ,'spaces'=>'spaces'
					  );

/* Boarders */

$enrolres_cols=array('reenroled'=>'reenroled'
				  ,'newenrolments'=>'newenrolments'
				  ,'leaverssince'=>'leaverssince'
				  ,'currentroll'=>'currentroll'
				  ,'budgetroll'=>'budgetroll'
				  ,'capacity'=>'capacity'
				  ,'spaces'=>'spaces'
				  );
$enrolresnext_cols=array('reenroling'=>'reenroling'
					  ,'pending'=>'pending'
					  ,'transfersin'=>'transfersin'
					  ,'newenrolments'=>'newenrolments'
					  ,'leavers'=>'leavers'
					  ,'projectedroll'=>'projectedroll'
					  ,'targetroll'=>'targetroll'
					  ,'budgetroll'=>'budgetroll'
					  ,'capacity'=>'capacity'
					  ,'spaces'=>'spaces'
					  );

$tables=array('appnext'=>$appnext_cols,'enrolnext'=>$enrolnext_cols,'appcurrent'=>$app_cols,'enrolcurrent'=>$enrol_cols);
//$tables=array('enrolresnext'=>$enrolresnext_cols,'enrolrescurrent'=>$enrolres_cols);
$Stats['tables']=array();
$Stats['tables']['table']=array();
foreach($tables as $tablename=>$table_cols){
	$Table=array();
	$Table['cols']=array();
	$Table['name']=$tablename;
	foreach($table_cols as $name => $col){
		$Col=array();
		$Col['name']=get_string($name,$book);
		if($name=='projectedroll'){$Col['date'].=display_date($todate);}
		elseif($name=='targetroll'){$Col['date'].=display_date($lastenrolyear. '-'.$CFG->enrol_cutoffmonth.'-30');}
		$Col['value']=$col;
		$Table['cols']['col'][]=$Col;
		}
	$Stats['tables']['table'][]=$Table;
	}

$Stats['DateStamp']=display_date($todate);
//$Stats['Transform']='admission_tables_boarding';
$Stats['Transform']='admission_tables';
$Stats['Paper']='landscape';

$returnXML=$Stats;
$rootName='Stats';

require_once('../../scripts/http_end_options.php');
exit;
?>
