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
	<button onClick="processContent(this);" name="current" value="new_mark.php">
	  <?php print_string('new',$book);?>
	</button>
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
<?php
	}
?>
  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" 
	  method="post" action="markbook.php">

	  <table id="marktable">
		<tr>
<?php 
/**
 *	   	The main table.
 *	   	All cells are referenced by their (sid,mid) only.
 */
	$n=sizeof($cids);
	$cidcolour=array();

	$rowcolour=array('#ffeeff', '#ffddff', '#ffccff', '#ffbbff',
	'#ffaaff', '#ff99ff', '#ff88ff', '#ff77ff', '#ff66ff', '#ff55ff',
	'#ff44ff', '#ff33ff', '#ff22ff', '#ff11ff', '#ff00ff');

	print '<td colspan="5">';
	print '<table>';
	for($i=0;$i<$n;$i++){ 
		/*colour students by their teaching class */	
		$cidcolour[$cids[$i]]=$rowcolour[$i];
		if($cids[$i]!=""){
			print '<tr bgcolor="'.$rowcolour[$i].'">';
			if($_SESSION['worklevel']>-1){
				print '<td colspan="5">&nbsp;&nbsp;<a
					href="admin.php?current=class_edit.php&newcid='.$cids[$i].'" 
					target="viewadmin" 
					onclick="parent.viewBook(\'admin\');">'.$cids[$i]
					.$teachers[$i].'</a></td></tr>';
				}
			else{
				print '<td colspan="5">&nbsp;&nbsp;'.$cids[$i]
					.$teachers[$i].'</td></tr>';
				}
			}
		}
	print '</table>';
	print '</td>';


/** The mark's column header, with a checkbox which provides $mid */	      
	for($col=0;$col<sizeof($umns);$col++){
//	  if($umns[$col]['display']=='yes'){
		if($umns[$col]['marktype']=='score'){
			  print '<th id="'.$umns[$col]['id'].'"><span title="'.$umns[$col]['comment'].'"><a 
				href="markbook.php?current=edit_scores.php&cancel=class_view.php&scoretype='. 
					  $scoretype[$col].'&grading_name='. 
					  $scoregrading[$col].'&mid='.$umns[$col]['id'].'&col='.$col.'">' 
					  .$umns[$col]['topic'].'</a><p>'.$umns[$col]['entrydate'].'</p>
	      <p class="component">'.$umns[$col]['component'].'</p>'.$umns[$col]['marktype'].'<input type="checkbox" name="checkmid[]" value="'.$umns[$col]['id'].'" /></span></th>';
	      	  }
		elseif($umns[$col]['marktype']=='report'){
			  print '<th  id="'.$umns[$col]['id'].'"><span title="'.$umns[$col]['comment'].'"><a 
	      href="markbook.php?current=edit_reports.php&cancel=class_view.php&midlist='.$umns[$col]['midlist']. 
					  '&title='.$umns[$col]['topic'].'&mid='.$umns[$col]['id'].'&pid='. 
					  $umns[$col]['component'].'&col='. $col.'&bid='.$bid[0].'">' 
					  . $umns[$col]['topic']. '</a><p>'.$umns[$col]['entrydate']. 
		  '</p><p class="component">'.$umns[$col]['component'].'</p>'.
			  $umns[$col]['marktype']. '</span></th>';
	      	  }
		elseif($umns[$col]['marktype']=='compound'){
			  print '<th id="'.$umns[$col]['id'].'"><span title="'.$umns[$col]['comment'].'"><a 
	      href="markbook.php?current=edit_scores.php&cancel=class_view.php&scoretype='.$scoretype[$col]. 
					  '&grading_name='.$scoregrading[$col]. 
					  '&mid='.$umns[$col]['id'].'&col='.$col.'">'.$umns[$col]['topic']. 
					  '</a><p>'.$umns[$col]['entrydate']. 
					  '</p><p class="component">'.$umns[$col]['component'].'</p>' 
					  .$umns[$col]['marktype'].'<input type="checkbox" 
					name="checkmid[]" value="'.$umns[$col]['id'].'" /></span></th>';
	      	  }
		else{
	      	print '<th id="'.$umns[$col]['id'].'">'.$umns[$col]['topic'].'</a><p>'. 
					$umns[$col]['entrydate'].'</p><p class="component">'. 
					$umns[$col]['component'].'</p>'.$umns[$col]['marktype']. 
					'<input type="checkbox" name="checkmid[]" value="'
					.$umns[$col]['id'].'" /></th>';
			}
//		  }
		}
?>
		</tr>
<?php
   	/*********************************************************/
   	/*	Generate each student's row in the table*/
   	include('class_view_table.php');

	for($c2=0;$c2<$row;$c2++){
		$c4=$c2+1;
		print '<tr id="'.$viewtable[$c2]['sid']. 
				'" bgcolor="'.$cidcolour[$viewtable[$c2]['class_id']].'">';
		print '<td>'.$c4.'</td>';
		if($viewtable[$c2]['sen']=='Y'){
		  /*links through to SEN info in infobook for this sid*/
			print '<td><a href="infobook.php?current=student_view_sen.php&sid='.
					$viewtable[$c2]['sid']. '&sids[]='.$viewtable[$c2]['sid']. 
					'" target="viewinfobook" onclick="parent.viewBook(\'infobook\');">S</a>';
			}
		else{print '<td>&nbsp;';}

		 /*links through to cross-curricular assessment grades in the infobook*/
		print '&nbsp;<a href="infobook.php?current=student_scores.php&sid=' 
				.$viewtable[$c2]['sid'].'&sids[]='.$viewtable[$c2]['sid']. 
				'" target="viewinfobook" onclick="parent.viewBook(\'infobook\');">T</a>';

		 /*links through to comments in the infobook*/
		print '&nbsp;<a href="infobook.php?current=comments_list.php&bid='
				.$bid[0].'&sid='.$viewtable[$c2]['sid'].'&sids[]='. 
				$viewtable[$c2]['sid'].'" target="viewinfobook" 
				onclick="parent.viewBook(\'infobook\');" ';
		print  ' class="'.$viewtable[$c2]['commentclass'].'"';
		print '><span title="'.$viewtable[$c2]['commentbody'].'">C</span></a>';

		 /*links through to incidents in the infobook*/
		print '&nbsp;<a href="infobook.php?current=incidents_list.php&bid='. 
				$bid[0].'&sid='.$viewtable[$c2]['sid'].'&sids[]='. 
				$viewtable[$c2]['sid']. 
				'" target="viewinfobook" onclick="parent.viewBook(\'infobook\');" ';
		print '>I</a>';
		print '</td>';

		print '<td>'.$viewtable[$c2]['surname'].'</td>';
		print '<td>'.$viewtable[$c2]['forename'].$viewtable[$c2]['preferredforename'].'</td>';
		print '<td>'.$viewtable[$c2]['form_id'].'</td>';
		for($c=0;$c<$c_marks;$c++) {
//			if ($umns[$c]['display']=='yes'){
				$col_mid=$umns[$c]['id'];
				print '<td class="'.$viewtable[$c2]['score'.$col_mid]['scoreclass']. '" '. 
						' id="'.$viewtable[$c2]['sid'].'-'. $col_mid. '" ';
				if($viewtable[$c2]['score'.$col_mid]['comment']!=''){
					print 'title=""><span title="'.$viewtable[$c2]['score'.$col_mid]['comment'].'">';
					print $viewtable[$c2][$col_mid].'</span></td>';
					}
				else{
					print '>'.$viewtable[$c2][$col_mid].'</td>';
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
