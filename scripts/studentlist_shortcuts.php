<?php
/*
 * Must have sid set. Ideally Student too. 
 * Consider support staff to be not priviliged to access. 
 */
if(isset($sid) and $_SESSION['role']!='support' and (!isset($CFG->schooltype) or $CFG->schooltype!='ela')){
	if(!isset($Student)){
		$Student=(array)fetchStudent_short($sid);
		$careful='yes';
		}
	if($book!='infobook'){
		$target=' target="viewinfobook" onclick="parent.viewBook(\'infobook\');" ';
		}
	else{
		$target='';
		}

	$comment=comment_display($sid);
?>
		<a href="infobook.php?current=comments_list.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>"
		    <?php print $target;?> class="<?php print $comment['class'];?>"><span class="fa fa-comment" title="<?php print $comment['body'];?>"></span>
        </a> 
<?php
	if($Student['SENFlag']['value']=='Y'){ 
		if(isset($bid)){$sensbuject=$bid[0];}else{$sensubject='';}
?>
		<a href="infobook.php?current=student_view_sen.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>&bid=<?php print $sensubject;?>" <?php print $target;?>>
		    <span class="fa fa-shield"></span>
		</a>
<?php
		}
	if($Student['MedicalFlag']['value']=='Y'){ ?>
		<a href="infobook.php?current=student_view_medical.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>" <?php print $target;?> ><span class="fa fa-medkit"></span></a>
<?php
		}
	if($Student['Boarder']['value']!='N' and $Student['Boarder']['value']!=''){ ?>
		<a href="infobook.php?current=student_view.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>" <?php print $target;?> >B</a>
<?php
		}
	unset($comment);
	if(isset($careful)){
		unset($Student);
		unset($careful);
		}
	}
/**
 * Language academy option for flagging new students and leavers
 */
elseif(isset($CFG->schooltype) and $CFG->schooltype=='ela'){
	if(!isset($Student)){
		$Student=(array)fetchStudent_short($sid);
		$careful='yes';
		}
	if($book!='infobook'){
		$target=' target="viewinfobook" onclick="parent.viewBook(\'infobook\');" ';
		}
	else{
		$target='';
		}

	$freshdate=explode('-',$Student['EntryDate']['value']);
	$diff=mktime(0,0,0,date('m'),date('d'),date('Y')) - mktime(0,0,0,$freshdate[1],$freshdate[2],$freshdate[0]);
	if(round($diff/(60*60*24)) < 8){
?>
		<a href="infobook.php?current=student_view_enrolment.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>" <?php print $target;?>>
		    <span class="fa fa-shield"></span>
		</a>
<?php
		}
	$freshdate=explode('-',$Student['LeavingDate']['value']);
	$diff=mktime(0,0,0,$freshdate[1],$freshdate[2],$freshdate[0]) - mktime(0,0,0,date('m'),date('d'),date('Y'));
	if(round($diff/(60*60*24)) >= -8){
?>
		<a href="infobook.php?current=student_view_enrolment.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>" <?php print $target;?>>
		    <span class="fa fa-star"></span>
		</a>
<?php
		}

	if(isset($careful)){
		unset($Student);
		unset($careful);
		}
	}
else{
	print '&nbsp';
	}
?>
