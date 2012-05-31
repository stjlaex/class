<?php 
/**						list_calendar_year.php
 *
 */

if(!isset($required)){$required='yes';}
if(!isset($listname)){$listname='year';}
if(!isset($listlabel)){$listlabel='year';}
if(!isset($year)){$selyear=get_curriculumyear();}else{$selyear=$year;}
if(!($selyear>1900)){$selyear=get_curriculumyear();}
$c=$selyear-17;
$years=array();
while($c<$selyear){
	$years[]=array('id'=>$c,'name'=>display_curriculumyear($c));
	$c++;
	}
$c=$selyear;
while($c<$selyear+5){
	$years[]=array('id'=>$c,'name'=>display_curriculumyear($c));
	$c++;
	}
include('scripts/set_list_vars.php');
list_select_list($years,$listoptions,$book);
unset($listoptions);
?>
