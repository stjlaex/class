<?php 
/** 			   					year_end_action.php
 *
 */

$action='year_end_action2.php';

include('scripts/sub_action.php');
include('scripts/answer_action.php');

	/* This is all to try and cover for the fact that their could be a
	 * choice of year groups to move in to. In practise I suspect there
	 * will not be.
	 */
	$years=array();
	$yidsyears=array();/* An array to hold the years indexed by yid. */
	$years=list_yeargroups();
	while(list($yindex,$year)=each($years)){
		$yidsyears[$year['id']]=$year;
		}
	reset($years);
	$yidsyears[1000]['name']='Alumni';/* The end point for graduation. */
	$yidsyears[1000]['sequence']='1000';
	$seqyears=array();
	while(list($yid,$year)=each($yidsyears)){
		$seqyear=$year['sequence'];
		$seqyears[$seqyear][]=$yid;
		}

	reset($yidsyears);
	for($c=0;$c<sizeof($years);$c++){
		$yid=$years[$c]['id'];
		$seqyear=$years[$c]['sequence'];
		$yidsyears[$yid]['nextyid']=array();
		/* Find where this yeargroup is moving to. If only one option
		 * then easy but if more than one yeargroup has the same
		 * sequence number then there is a choice.
		 */
		if(sizeof($seqyears[$seqyear+1])==1){
			$yidsyears[$yid]['nextyid'][]=$seqyears[$seqyear+1][0];
			$yidsyears[$yid]['nextyid'][]=1000;
			}
		else{
			for($c2=0;$c2<sizeof($seqyears[$seqyear+1]);$c2++){
				$tempyid=$seqyears[$seqyear+1][$c2];
				/* Give preference to yeargroups witin the same section. */
				if($yidsyears[$yid]['section_id']==$yidsyears[$tempyid]['section_id']){
					$yidsyears[$yid]['nextyid'][]=$seqyears[$seqyear+1][$c2];
					}
				}
			$yidsyears[$yid]['nextyid'][]=1000;
			}
		}


	/* For students at the last stage, find the next course to move on to. */
	$cridscourses=array();
	$courses=(array)list_courses();
	foreach($courses as $course){
		$cridscourses[$course['id']]=$course;
		}
	$cridscourses[1000]['name']='Graduate';
	$cridscourses[1000]['sequence']=1000;

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
		 if(isset($year['nextyid'])){
			 $seqyear=$year['sequence'];
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
		 if(isset($course['nextcrid'])){
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