<?php 
/** 									class_view.php
 *
 * This is the main monster page that wraps other monster pages
 * (class_view_marks.php for the columns and class_view_table.php for
 * the rows) and finally produces everything for displaying the markbook
 * spreadsheet. Its all very old and horrible but it works.
 *
 */

$choice='class_view.php';
/* Automatically lock assessment columns older than 60 days. */
$cutoffdate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-60,date('Y')));
/*Fetches all the info needed for this view*/
include('class_view_marks.php');

/*buttonmenu contains action buttons for column checkboxes */
if($_SESSION['worklevel']>-1){
?>
  <div class="buttonmenu">
	<div class="buttongroup">
	  <label>
		<?php print get_string('new',$book).' '.get_string('mark',$book);?>
	  </label>
	<button onClick="processContent(this);" name="current" value="new_mark.php">
	  <?php print_string('classwork',$book);?>
	</button>
<?php
	 /* Only display HW for a single class and only for courses
	  *			   which do do homework.
	  */
	if($cidsno==1 and !in_array($classes[$cid]['crid'],getEnumArray('nohomeworkcourses'))){
?>
	<button onClick="processContent(this);" name="current" value="new_homework.php">
	  <?php print_string('homework',$book);?>
	</button>
<?php
		}
?>
	</div>
	<div class="buttongroup">
	<button onClick="processContent(this);" name="current" value="column_edit.php">
	  <?php print_string('edit',$book);?>
	</button>
	<button onClick="processContent(this);" name="current" value="column_delete.php">
	  <?php print_string('delete',$book);?>
	</button>	
	<button onClick="processContent(this);" name="current" value="column_copy.php">
	 <?php print_string('copy',$book);?>
	</button>
	<button onClick="processContent(this);" name="current" value="column_average.php">
	  <?php print_string('average',$book);?>
	</button>
	<button onClick="processContent(this);" name="current" value="column_level.php">
	  <?php print_string('level',$book);?>
	</button>
	<button onClick="processContent(this);" name="current"value="column_rank.php">
	  <?php print_string('rank',$book);?>
	  </button>
	<button onClick="processContent(this);" name="current" value="column_sum.php">
	  <?php print_string('sum',$book);?>
	  </button>
	<button onClick="processContent(this);" name="current" value="column_export.php">
	  <?php print_string('export',$book);?>
	</button>
	</div>
 </div>
<?php
	}
?>
  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" 
	  method="post" action="markbook.php">
        <div class="table-scrollable">
	  <table class="sidtable marktable listmenu" id="sidtable">
		<thead>
		<tr>
<?php 
/**
 *	   	The main table.
 *	   	All cells are referenced by their (sid,mid) only.
 */
	$cidcolour=array();
	$rowcolour=array('#ffffee', '#ffffcc', '#ffffaa', '#ffff99',
					 '#ffff77', '#ffff55', '#ffff33', '#ffff11', '#ffffdd', '#ffffbb',
					 '#ffff00', '#ffff88', '#ffff66', '#ffff44', '#ffff22');
	if($cidsno==1 and $lessonatt>0){$headcols=5+$lessonatt;}
	else{$headcols=5;}
?>
<td class="td-status" colspan="<?php print $headcols;?>">
			<table>
<?php
	/* cidsno is the size of the cids array being displayed */
	for($i=0;$i<$cidsno;$i++){ 
		/*colour students by their teaching class */	
		$cidcolour[$cids[$i]]=$rowcolour[$i];
		if($cids[$i]!=''){
			print '<tr bgcolor="'.$rowcolour[$i].'">';
			if($_SESSION['worklevel']>-1){
				$params=array(
							  'cid'=>$cids[$i]
							  );
				$url=url_construct($params,'class_photo_print.php');
?>
				<td colspan="3">
				  <span title="<?php print $classes[$cids[$i]]['detail'];?>">
				      <a href="admin.php?current=class_edit.php&newcid=<?php print $cids[$i];?>" target="viewadmin" onclick="parent.viewBook('admin');">
				        <span class="clicktoconfigure" title="<?php print_string('clicktoconfigure','admin');?>" /></span>
					  <?php print $classes[$cids[$i]]['name'].$teachers[$i];?>
					  </a>
				  </span>
				 </td>
				<td>
				  <div style="float:right;" title="<?php print_string('tracking','markbook');?>" name="current" value="student_grades_print.php" onclick="clickToPresent('markbook','<?php print $url;?>','class_photo_print')" >
					<span class="clicktoprint" title="<?php print get_string('print','infobook').' '.get_string('students','infobook');?>" /></span>
				  </div>
				</td>
				 <td status="p">
				   <a style="color:#fff;" href="register.php?current=register_list.php&newcomid=&newcid=<?php print $cids[$i];?>&nodays=1&startday=" target="viewregister" onclick="parent.viewBook('register');">R</a>
				 </td>
			</tr>
<?php
				}
			else{
?>
			   <td colspan="5">&nbsp;&nbsp;<?php print $classes[$cids[$i]]['name'].$teachers[$i];?>
		  </td>
		</tr>
<?php
				}
			}
		}
?>
	  </table>
	</td>
<?php
	/*The mark's column header, with a checkbox which provides $mid */	      
	for($col=0;$col<sizeof($umns);$col++){

		if ($umns[$col]['component'] == $pid or $pid == '') {
			if($umns[$col]['marktype']=='score' or $umns[$col]['marktype']=='hw'){
				/* If it is an assessment column and older than 60 days then lock from editing, unless you have course permissions. */
				if(($umns[$col]['locklevel']==1 and $umns[$col]['assessment']!='no' and $r==-1)){
					print '<th class="'.$umns[$col]['displayclass'].'" id="'.$umns[$col]['id'].'"><span title="'.$umns[$col]['comment'].'">' 
						  .$umns[$col]['topic'].'<p>'.display_date($umns[$col]['entrydate']).'</p>
			 <p class="component">'.$umns[$col]['component'].'</p>'.'<input type="checkbox" name="checkmid[]" value="'.$umns[$col]['id'].'" /></span></th>';
					}
				elseif($umns[$col]['locklevel']==2 and $umns[$col]['assessment']!='no'){
					print '<th class="'.$umns[$col]['displayclass'].'" id="'.$umns[$col]['id'].'"><span title="'.$umns[$col]['comment'].'">' 
						  .$umns[$col]['topic'].'<p>'.display_date($umns[$col]['entrydate']).'</p>
			 <p class="component">'.$umns[$col]['component'].'</p>'.'<input type="checkbox" name="checkmid[]" value="'.$umns[$col]['id'].'" /></span></th>';
					}
				else{
					print '<th class="'.$umns[$col]['displayclass'].'" id="'.$umns[$col]['id'].'"><span title="'.$umns[$col]['comment'].'"><a 
					href="markbook.php?current=edit_scores.php&cancel=class_view.php&scoretype='. 
						  $scoretype[$col].'&grading_name='. 
						  $scoregrading[$col].'&mid='.$umns[$col]['id'].'&col='.$col.'">';
					if($umns[$col]['locklevel']==0){print '<span class="clicktoedit" style="float:right;"/></span> ';}
					print $umns[$col]['topic'].'<p>'.display_date($umns[$col]['entrydate']).'</p></a>
			 <p class="component">'.$umns[$col]['component'].'</p>'.'<input type="checkbox" name="checkmid[]" value="'.$umns[$col]['id'].'" /></span></th>';
					}
				}
			elseif($umns[$col]['marktype']=='report'){
				  print '<th class="'.$umns[$col]['displayclass'].'" id="'.$umns[$col]['id'].'"><span title="'.$umns[$col]['comment'].'"><a 
			 href="markbook.php?current=new_edit_reports.php&cancel=class_view.php&midlist='.$umns[$col]['midlist']. 
						  '&title='.$umns[$col]['topic'].'&mid='.$umns[$col]['id'].'&pid='. 
						  $umns[$col]['component'].'&col='. $col.'&bid='.$bid[0].'">' 
						  . $umns[$col]['topic']. '<p>'.display_date($umns[$col]['entrydate']). 
			  '</p></a><p class="component">'.$umns[$col]['component'].'</p>'.
				  $umns[$col]['marktype']. '</span></th>';
			 	  }
			elseif($umns[$col]['marktype']=='compound'){
				  print '<th class="'.$umns[$col]['displayclass'].'" id="'.$umns[$col]['id'].'"><span title="'. 
						  $umns[$col]['comment'].'">'.$umns[$col]['topic']. 
						  '<p>'.display_date($umns[$col]['entrydate']). 
						  '</p><p class="component">'.$umns[$col]['component'].'</p>' 
						  .'<input type="checkbox" 
						name="checkmid[]" value="'.$umns[$col]['id'].'" /></span></th>';
			 	  }
			else{
			 	print '<th class="'.$umns[$col]['displayclass'].'" id="'.$umns[$col]['id'].'">'. 
						$umns[$col]['topic'].'<p>'. 
						display_date($umns[$col]['entrydate']).'</p></a><p class="component">'. 
						$umns[$col]['component'].'</p>'.$umns[$col]['marktype']. 
						'<input type="checkbox" name="checkmid[]" value="'
						.$umns[$col]['id'].'" /></th>';
				}
			}
		}
?>
		</tr>
	  </thead>
<?php
   	/*******************************************
	 *	Generate each student's row in the table, $rowno is set as the sizeof
	 *	the viewtable
	 */
   	include('class_view_table.php');

	for($c2=0;$c2<$rowno;$c2++){
		$c4=$c2+1;
?>
		<tr id="sid-<?php print $viewtable[$c2]['sid'];?>" 
		  bgcolor="<?php print $cidcolour[$viewtable[$c2]['class_id']];?>" >
		  <td><?php print $c4;?></td>
		  <td>
<?php
				$sid=$viewtable[$c2]['sid'];
				include('scripts/studentlist_shortcuts.php');
?>
		  </td>
		  <td class="student">
			<a href="infobook.php?current=student_view.php&sid=<?php print $viewtable[$c2]['sid'];?>&sids[]=<?php print $viewtable[$c2]['sid'];?>" target="viewinfobook" onclick="parent.viewBook('infobook');">
			<?php if($viewtable[$c2]['preferredforename']!=''){$preferredforename='&nbsp;('.$viewtable[$c2]['preferredforename'].')';}else{$preferredforename='';}?>
			<?php print $viewtable[$c2]['surname'];?>,&nbsp;<?php print $viewtable[$c2]['forename']. '&nbsp;'.$viewtable[$c2]['middlenames'].$preferredforename;?></a>
			<div class="miniature" id="mini-<?php echo $viewtable[$c2]['sid']; ?>"></div>
			<div class="merit" id="merit-<?php print $viewtable[$c2]['sid'];?>"></div>
		  </td>
		  <td><?php print $viewtable[$c2]['form_id'];?></td>
<?php
   		if($lessonatt>0){
			$Attendances=(array)fetch_classAttendances($cids[0],$viewtable[$c2]['sid'],0,$lessonatt);
			$lessonno=0;
			foreach($Attendances['Attendance'] as $Att){
				print '<td style="border:1px solid #bac1c8;" status="'.$Att['Status']['value'].'"><span title="'.$Att['Date']['value'].' P'.$Att['Period']['value'].' '.$Att['Comment']['value'].'">'.$Att['Code']['value'].'</span></td>';
					 $lessonno++;
				}
			while($lessonno<$lessonatt){
				print '<td style="border:1px solid #bac1c8;" status=""></td>';
				$lessonno++;
				}
			}
?>
		  <td status="<?php print $viewtable[$c2]['attstatus'];?>" 
<?php 
			if($viewtable[$c2]['attcode']!='' and $viewtable[$c2]['attcode']!=' '){
?>			
				title="">
				<span title="<?php print 
				 date('H:i',$viewtable[$c2]['atttime']).' '.$viewtable[$c2]['attcomment'];?>">
				  <?php print $viewtable[$c2]['attcode'];?>
				</span>
<?php 
				}
			else{print '>&nbsp;';}
?>
		  </td>
<?php
		for($c=0;$c<$c_marks;$c++){
				$col_mid=$umns[$c]['id'];
				if($viewtable[$c2]['score'.$col_mid]['comment']==''){
					print '<td class="'.$viewtable[$c2]['score'.$col_mid]['scoreclass']. '" '. 
						' id="'.$viewtable[$c2]['sid'].'-'. $col_mid. '" >';
					print $viewtable[$c2][$col_mid].'</td>';
					}
				elseif($viewtable[$c2][$col_mid]!=''){
					print '<td class="'.$viewtable[$c2]['score'.$col_mid]['scoreclass']. '" '. 
						' id="'.$viewtable[$c2]['sid'].'-'. $col_mid.
							'" title="" >';
					print '<span title="'.$viewtable[$c2]['score'.$col_mid]['comment'].'" >';
					print '&nbsp;'.$viewtable[$c2][$col_mid].'&nbsp;</span></td>';
					}
				else{
					print '<td class="'.$viewtable[$c2]['score'.$col_mid]['scoreclass']. '" '. 
						' id="'.$viewtable[$c2]['sid'].'-'. $col_mid.
							'" title="" >';
					print '<span style="padding:0 2em;" title="'.$viewtable[$c2]['score'.$col_mid]['comment'].'" >';
					print '&nbsp;</span></td>';
					}
			}
		print '</tr>';
		}
?>

		<tr id="sid-0" bgcolor="#ffffff">
		  <td></td>
		  <td></td>
		  <td class="student"></td>
		  <td></td>
<?php

if(sizeof($cids)==1){
	if($lessonatt>0){
		$nextlessonatt=$lessonatt+4;
		$attlink='<a style="color:#fff;"
				 href="markbook.php?current=class_view.php&lessonatt='.$nextlessonatt.'"><</a>';
		$attlink.='<a style="color:#fff;"
				 href="markbook.php?current=class_view.php&lessonatt=0">></a>';
		}
	else{
		$attlink='<a style="color:#fff;"
				 href="markbook.php?current=class_view.php&lessonatt=4"><</a>';
		}
	}
else{
	$attlink='';
	$lessonatt=0;
	}

if($lessonatt>0){
	print '<td colspan="'.$lessonatt.'"></td>';
	}
print ' <td status="p" >'.$attlink.'</td>';

/**
 * This is the bottom row of the mark table for the totals.
 */
		for($c=0;$c<$c_marks;$c++){
			$col_mid=$umns[$c]['id'];
			$out='';
			if($totals[$col_mid]['no']>0){
				if($umns[$c]['marktype']=='tally' or $umns[$c]['marktype']=='dif'){
					$out=round($totals[$col_mid]['value']/$totals[$col_mid]['no']);
					}
				elseif($umns[$c]['marktype']=='compound'){
					$out=round(100*$totals[$col_mid]['value']/$totals[$col_mid]['outoftotal']);
					if($out>=85){$outclass='golite';}
					elseif($out>=60){$outclass='gomidlite';}
					elseif($out>=35){$outclass='pauselite';}
					elseif($out>=10){$outclass='midlite';}
					else{$outclass='nolite';}
					$out='<div class="'.$outclass.'">'.$out.'</div>';
					}
				elseif($umns[$c]['scoretype']=='grade'){
					$out=round($totals[$col_mid]['grade']/$totals[$col_mid]['no']);
					$out=scoreToGrade($out,$scoregrades[$scoregrading[$c]]);
					}
				elseif($umns[$c]['scoretype']=='value' or $umns[$c]['scoretype']=='sum' or $umns[$c]['scoretype']=='average'){
					$out=round($totals[$col_mid]['value']/$totals[$col_mid]['no']);
					}
				elseif($umns[$c]['scoretype']=='percentage'){
					list($display,$out,$outrank)=scoreToPercent($totals[$col_mid]['value'],$totals[$col_mid]['outoftotal']);
					}
				}
			print '<td class="grade" id="0-'. $col_mid. '" >'.$out.'</td>';
			}
?>
		</tr>
	  </table>
    </div>
	<input type="hidden" name="current" value="" />		
	<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="mid" value="" />
	<input type="hidden" name="bid" value="<?php print $bid[0]; ?>" />
	</form>
  </div>

<?php
    include('scripts/studentlist_extra.php');
?>
