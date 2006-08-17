<?php 
/** 			   					year_end_action.php
 */

$action='year_end_action2.php';

include('scripts/sub_action.php');
include('scripts/answer_action.php');

	$years=array();
	$yidsyears=array();
	$d_yeargroup=mysql_query("SELECT id, ncyear, section_id, name FROM
							yeargroup ORDER BY section_id, ncyear");
	while($year=mysql_fetch_array($d_yeargroup,MYSQL_ASSOC)){
		$years[]=$year;
		$yidsyears[$year['id']]=$year;
		}
	$yidsyears[1000]['name']='Alumni';

	$ncyears=array();
	while(list($yid,$year)=each($yidsyears)){
		$ncyear=$year['ncyear'];
		$ncyears[$ncyear][]=$yid;
		}

	reset($yidsyears);
	for($c=0;$c<sizeof($years);$c++){
		$yid=$years[$c]['id'];
		$ncyear=$years[$c]['ncyear'];
		$yidsyears[$yid]['nextyid']=array();
		if(sizeof($ncyears[$ncyear+1])==1){
			$yidsyears[$yid]['nextyid'][]=$ncyears[$ncyear+1][0];
			$yidsyears[$yid]['nextyid'][]=1000;
			}
		else{
			for($c2=0;$c2<sizeof($ncyears[$ncyear+1]);$c2++){
				$yidsyears[$yid]['nextyid'][]=$ncyears[$ncyear+1][$c2];
				}
			$yidsyears[$yid]['nextyid'][]=1000;
			}
		}


	/**/
	$courses=array();
	$cridscourses=array();
	$d_course=mysql_query("SELECT id, sequence, section_id, name FROM
							course ORDER BY section_id, sequence");
	while($course=mysql_fetch_array($d_course,MYSQL_ASSOC)){
		$courses[]=$course;
		$cridscourses[$course['id']]=$course;
		}
	$cridscourses[1000]['name']='Graduate';

	$sequences=array();
	while(list($crid,$course)=each($cridscourses)){
		$sequence=$course['sequence'];
		$sequences[$sequence][]=$crid;
		}

	reset($cridscourses);
	for($c=0;$c<sizeof($courses);$c++){
		$crid=$courses[$c]['id'];
		$sequence=$courses[$c]['sequence'];
		$cridscourses[$crid]['nextcrid']=array();
		if(sizeof($sequences[$sequence+1])==1){
			$cridscourses[$crid]['nextcrid'][]=$sequences[$sequence+1][0];
			$cridscourses[$crid]['nextcrid'][]=1000;
			}
		else{
			for($c2=0;$c2<sizeof($sequences[$sequence+1]);$c2++){
				$cridscourses[$crid]['nextcrid'][]=$sequences[$sequence+1][$c2];
				}
			$cridscourses[$crid]['nextcrid'][]=1000;
			}
		}


three_buttonmenu();
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="left"> 
		<legend><?php print_string('endofyearpromotions',$book);?></legend> 
		  <p><?php print_string('confirmyeargroupstopromote',$book);?></p>
<?php
     while(list($yid,$year)=each($yidsyears)){
	   if($year['nextyid']){
		   $ncyear=$year['ncyear'];
?>
		  <label for="<?php print $year['name'];?>"><?php print $year['name'];?></label>
		  <select id="<?php print $year['name'];?>" name="<?php print $yid;?>">
<?php
    	while(list($index,$newyid)=each($year['nextyid'])){
			print '<option ';
			if(($yid==$newyid)){print 'selected="selected"';}
			print	' value="'.$newyid.'"> '.$yidsyears[$newyid]['name'].'</option>';
			}
?>
		  </select>
<?php
		}
	 }
?>
	  </fieldset>

	  <fieldset class="right"> 
		<legend><?php print_string('endofcoursepromotions',$book);?></legend> 
		  <p><?php print_string('confirmcoursestopromote',$book);?></p>
<?php
     while(list($crid,$course)=each($cridscourses)){
		 if($course['nextcrid']){
			 $sequence=$course['sequence'];
?>
		  <label for="<?php print $course['name'];?>"><?php print $course['name'];?></label>
		  <select id="<?php print $course['name'];?>" name="<?php print $crid;?>">
<?php
    	while(list($index,$newcrid)=each($course['nextcrid'])){
			print '<option ';
			if(($crid==$newcrid)){print 'selected="selected"';}
			print	' value="'.$newcrid.'"> '.$cridscourses[$newcrid]['name'].'</option>';
			}
?>
		  </select>
<?php
		}
	 }
?>
	  </fieldset>

		<input type="hidden" name="cancel" value="<?php  print $cancel;?>" />
		<input type="hidden" name="current" value="<?php print $action;?>" />
	    <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
  </div>