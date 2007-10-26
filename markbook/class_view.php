<?php 
/** 									class_view.php
 */

$choice='class_view.php';

/*Fetches all the info needed for this view*/
include('class_view_marks.php');

/*buttonmenu contains action buttons for column checkboxes */
if($_SESSION['worklevel']>-1){
?>
  <div class="buttonmenu">
	<div class="buttongroup">
	  <label>
		<?php print get_string('new',$book).'<br />'.get_string('mark',$book);?>
	  </label>
	<button onClick="processContent(this);" name="current" value="new_mark.php">
	  <?php print_string('classwork',$book);?>
	</button>
<?php
	if($cidsno==1){
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

	  <table class="sidtable" id="marktable">
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
?>
		  <td colspan="5">
			<table>
<?php
	for($i=0;$i<$cidsno;$i++){ 
		/*colour students by their teaching class */	
		$cidcolour[$cids[$i]]=$rowcolour[$i];
		if($cids[$i]!=''){
			print '<tr bgcolor="'.$rowcolour[$i].'">';
			if($_SESSION['worklevel']>-1){
?>
			  <td colspan="5">
				<span title="<?php print $classes[$cid]['detail'];?>">&nbsp;&nbsp;<a
				  href="admin.php?current=class_edit.php&newcid=<?php print $cids[$i];?>" 
				  target="viewadmin" onclick="parent.viewBook('admin');">
				  <?php print $cids[$i].$teachers[$i];?></a></span>
			  </td>
			</tr>
<?php
				}
			else{
?>
			   <td colspan="5">&nbsp;&nbsp;<?php print $cids[$i].$teachers[$i];?>
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
//	  if($umns[$col]['display']=='yes'){
		if($umns[$col]['marktype']=='score' or $umns[$col]['marktype']=='hw'){
			  print '<th id="'.$umns[$col]['id'].'"><span title="'.$umns[$col]['comment'].'"><a 
				href="markbook.php?current=edit_scores.php&cancel=class_view.php&scoretype='. 
					  $scoretype[$col].'&grading_name='. 
					  $scoregrading[$col].'&mid='.$umns[$col]['id'].'&col='.$col.'">' 
					  .$umns[$col]['topic'].'<p>'.display_date($umns[$col]['entrydate']).'</p></a>
	      <p class="component">'.$umns[$col]['component'].'</p>'.$umns[$col]['marktype'].'<input type="checkbox" name="checkmid[]" value="'.$umns[$col]['id'].'" /></span></th>';
	      	  }
		elseif($umns[$col]['marktype']=='report'){
			  print '<th  id="'.$umns[$col]['id'].'"><span title="'.$umns[$col]['comment'].'"><a 
	      href="markbook.php?current=edit_reports.php&cancel=class_view.php&midlist='.$umns[$col]['midlist']. 
					  '&title='.$umns[$col]['topic'].'&mid='.$umns[$col]['id'].'&pid='. 
					  $umns[$col]['component'].'&col='. $col.'&bid='.$bid[0].'">' 
					  . $umns[$col]['topic']. '<p>'.display_date($umns[$col]['entrydate']). 
		  '</p></a><p class="component">'.$umns[$col]['component'].'</p>'.
			  $umns[$col]['marktype']. '</span></th>';
	      	  }
		elseif($umns[$col]['marktype']=='compound'){
			  print '<th id="'.$umns[$col]['id'].'"><span title="'.$umns[$col]['comment'].'"><a 
	      href="markbook.php?current=edit_scores.php&cancel=class_view.php&scoretype='.$scoretype[$col]. 
					  '&grading_name='.$scoregrading[$col]. 
					  '&mid='.$umns[$col]['id'].'&col='.$col.'">'.$umns[$col]['topic']. 
					  '<p>'.display_date($umns[$col]['entrydate']). 
					  '</p></a><p class="component">'.$umns[$col]['component'].'</p>' 
					  .$umns[$col]['marktype'].'<input type="checkbox" 
					name="checkmid[]" value="'.$umns[$col]['id'].'" /></span></th>';
	      	  }
		else{
	      	print '<th id="'.$umns[$col]['id'].'">'.$umns[$col]['topic'].'<p>'. 
					display_date($umns[$col]['entrydate']).'</p></a><p class="component">'. 
					$umns[$col]['component'].'</p>'.$umns[$col]['marktype']. 
					'<input type="checkbox" name="checkmid[]" value="'
					.$umns[$col]['id'].'" /></th>';
			}
//		  }
		}
?>
		</tr>
<?php
   	/********************************************/
   	/*	Generate each student's row in the table*/
   	include('class_view_table.php');

	for($c2=0;$c2<$row;$c2++){
		$c4=$c2+1;
?>
		<tr id="<?php print $viewtable[$c2]['sid'];?>" 
		  bgcolor="<?php print $cidcolour[$viewtable[$c2]['class_id']];?>" >
		  <td><?php print $c4;?></td>
		  <td>
			<a href="infobook.php?current=student_scores.php&sid=<?php print $viewtable[$c2]['sid'];?>&sids[]=<?php print $viewtable[$c2]['sid'];?>"
			  target="viewinfobook" onclick="parent.viewBook('infobook');">T</a>
			<a href="infobook.php?current=comments_list.php&bid=<?php print $bid[0];?>&sid=<?php print $viewtable[$c2]['sid'];?>&sids[]=<?php print $viewtable[$c2]['sid'];?>"
			  target="viewinfobook" onclick="parent.viewBook('infobook');" 
			  class="<?php print $viewtable[$c2]['commentclass'];?>"
			  ><span title="<?php print $viewtable[$c2]['commentbody'];?>">C</span></a>
			<a href="infobook.php?current=incidents_list.php&bid=<?php print $bid[0];?>&sid=<?php print $viewtable[$c2]['sid'];?>&sids[]=<?php print $viewtable[$c2]['sid'];?>"
			  target="viewinfobook" onclick="parent.viewBook('infobook');">I</a>
<?php		if($viewtable[$c2]['sen']=='Y'){ ?>
			<a href="infobook.php?current=student_view_sen.php&sid=<?php print $viewtable[$c2]['sid'];?>&sids[]=<?php print $viewtable[$c2]['sid'];?>&bid=<?php print $bid[0];?>"
			  target="viewinfobook" onclick="parent.viewBook('infobook');">S</a>
<?php			} ?>
		  </td>
		  <td>
			<a href="infobook.php?current=student_view.php&sid=<?php print $viewtable[$c2]['sid'];?>&sids[]=<?php print $viewtable[$c2]['sid'];?>"
			  target="viewinfobook" onclick="parent.viewBook('infobook');">
			<?php print $viewtable[$c2]['surname'];?>,&nbsp;<?php print $viewtable[$c2]['forename'].$viewtable[$c2]['preferredforename'];?></a>
		  </td>
		  <td><?php print $viewtable[$c2]['form_id'];?></td>
		  <td status="<?php print $viewtable[$c2]['attstatus'];?>" 
<?php 
		  if($viewtable[$c2]['attcomment']!=' '){
?>			
				title="">
				<span title="<?php print $viewtable[$c2]['attcode'].':<br />'. 
				 date('H:i',$viewtable[$c2]['atttime']).' '.$viewtable[$c2]['attcomment'];?>">
				&nbsp;</span>
<?php 
				}
		  else{print '>';}
?>

		  &nbsp;</td>
<?php
		for($c=0;$c<$c_marks;$c++){
//			if($umns[$c]['display']=='yes'){
				$col_mid=$umns[$c]['id'];
				if($viewtable[$c2]['score'.$col_mid]['comment']!=''){
					print '<td class="'.$viewtable[$c2]['score'.$col_mid]['scoreclass']. '" '. 
						' id="'.$viewtable[$c2]['sid'].'-'. $col_mid.
							'" title="" >';
					print '<span title="'.$viewtable[$c2]['score'.$col_mid]['comment'].'" >';
					print $viewtable[$c2][$col_mid].'&nbsp</span></td>';
					}
				else{
					print '<td class="'.$viewtable[$c2]['score'.$col_mid]['scoreclass']. '" '. 
						' id="'.$viewtable[$c2]['sid'].'-'. $col_mid. '" >';
					print $viewtable[$c2][$col_mid].'</td>';
					}
//				}
			}
		print '</tr>';
	}
?>
	  </table>

	<input type="hidden" name="current" value="" />		
	<input type="hidden" name="cancel" value="<?php print $cancel;?>" />		
	<input type="hidden" name="choice" value="<?php print $choice;?>" />		
	<input type="hidden" name="mid" value="" />
	<input type="hidden" name="bid" value="<?php print $bid[0]; ?>" />
	</form>
  </div>
