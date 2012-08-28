<?php
/*
 * Must have sid set. Ideally Student too. 
 * Consider support staff to be not priviliged to access. 
 */
if(isset($sid) and $_SESSION['role']!='support'){
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
	<span title="<?php print $comment['body'];?>">
		<a href="infobook.php?current=comments_list.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>"
				<?php print $target;?> class="<?php print $comment['class'];?>">C</a> 
	</span>
<?php
	if($Student['SENFlag']['value']=='Y'){ ?>
		<a href="infobook.php?current=student_view_sen.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>&bid=G" <?php print $target;?> >S</a>
<?php
		}
	if($Student['MedicalFlag']['value']=='Y'){ ?>
		<a href="infobook.php?current=student_view_medical.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>&bid=G" <?php print $target;?> >M</a>
<?php
		}
	if($Student['Boarder']['value']!='N' and $Student['Boarder']['value']!=''){ ?>
		<a href="infobook.php?current=student_view.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>&bid=G" <?php print $target;?> >B</a>
<?php
		}
	unset($comment);
	if(isset($careful)){
		unset($Student);
		unset($careful);
		}
	}
else{
	print '&nbsp';
	}
?>
