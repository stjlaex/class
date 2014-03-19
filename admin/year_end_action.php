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
	foreach($years as $year){
		$yidsyears[$year['id']]=$year;
		}
	$yidsyears[1000]['name']='Alumni';/* The end point for graduation. */
	$yidsyears[1000]['sequence']='1000';
	$seqyears=array();
	reset($yidsyears);
	foreach($yidsyears as $yid => $year){
		$seqyear=$year['sequence'];
		$seqyears[$seqyear][]=$yid;
		}

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
	reset($cridscourses);
	foreach($cridscourses as $crid => $course){
		$sequence=$course['sequence'];
		$sequences[$sequence][]=$crid;
		}

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
            <div class="left">
                <fieldset class="divgroup"> 
                    <h5><?php print_string('endofyearpromotions',$book);?></h5> 
                    <p><?php print_string('confirmyeargroupstopromote',$book);?></p>
                    <?php
                     foreach($yidsyears as $yid => $year){
                    	 if(isset($year['nextyid'])){
                    		 $seqyear=$year['sequence'];
                    ?>
                    <label for="<?php print $year['name'];?>"><?php print $year['name'];?></label>
                    <select id="<?php print $year['name'];?>" name="<?php print $yid;?>">
                        <?php
                         foreach($year['nextyid'] as $newyid){
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
            </div>
            <div class="right">
                <fieldset class="divgroup"> 
                    <h5><?php print_string('endofcoursepromotions',$book);?></h5> 
                    <p><?php print_string('confirmcoursestopromote',$book);?></p>
                    <?php
                     foreach($cridscourses as $crid => $course){
                    	 if(isset($course['nextcrid'])){
                    		 $sequence=$course['sequence'];
                    ?>
                    <label for="<?php print $course['name'];?>"><?php print $course['name'];?></label>
                    <select id="<?php print $course['name'];?>" name="<?php print $crid;?>">
                        <?php
                            foreach($course['nextcrid'] as $newcrid){
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
        </div>
        <input type="hidden" name="cancel" value="<?php  print $cancel;?>" />
        <input type="hidden" name="current" value="<?php print $action;?>" />
        <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
  </div>