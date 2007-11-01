<?php
/**	   	   								onereport.php
 */
		$inc=0;
		$Report=array();
		/*this is the xml-ready array*/
		$Report['Subject']=array('id'=>$bid, 'value'=>$subjectname);
		if($pid!=''){$Report['Component']=array('id'=>$pid, 'value'=>$componentname);}
?>
	<tr id="sid-<?php print $sid;?>">
<?php
		if($edit_comments_off!='yes'){
?>
	<th>
	</th>
<?php
			}
		else{
?>
	<td>
	  <input type="checkbox" name="sids[]" value="<?php print $sid;?>" />
		<?php print $tab;?>
	</td>
<?php
			}
?>
	<td>
<?php
		print $viewtable[$row]['surname'].', ';
		print $viewtable[$row]['forename'].$viewtable[$row]['preferredforename'].' (';
		print $viewtable[$row]['form_id'].')</td>';

		reset($AssDefs);
		while(list($index,$AssDef)=each($AssDefs)){
			$Assessments=array();
			$eid=$AssDef['id_db'];
			$Assessments=fetchAssessments_short($sid,$eid,$bid,$pid);
			if(sizeof($Assessments)>0){$value=$Assessments[0]['Value']['value'];}
			else{$value='';}
			$grading_grades=$AssDef['GradingScheme']['grades'];
			if($grading_grades!='' and $grading_grades!=' '){
				$pairs=explode (';', $grading_grades);
?>
		  <td>
			<select tabindex="<?php print $tab;?>" name="sid<?php print $sid.':'.$inc++;?>">
<?php 
				print '<option value="" ';
				if($value==''){print 'selected';}	
				print ' ></option>';
				for($c3=0;$c3<sizeof($pairs);$c3++){
					list($level_grade, $level)=split(':',$pairs[$c3]);
					print '<option value="'.$level.'" ';
					if($value==$level){print 'selected';}	
					print '>'.$level_grade.'</option>';
					}
?>
			</select>
		  </td>
<?php
				}
			else{
			   	print '<td><input pattern="decimal" type="text" tabindex="'.$tab.'" name="sid'.$sid.':'.$inc++.'" maxlength="8" value="'.$value.'" /></td>';
				}
		}
   	$Report['Assessments']=$Assessments;
?>
	</tr>
<?php
	if($report['addcomment']=='yes' or $report['addcategory']=='yes'){
		if(isset($catdefs)){$reportdef['catdefs']=$catdefs;}
		if(isset($ratingnames)){$reportdef['ratingnames']=$ratingnames;}
		$Report['Comments']=array();
		$Report['Comments']=fetchReportEntry($reportdef, $sid, $bid, $pid);
		for($entryn=0;$entryn<=sizeof($Report['Comments']['Comment']);$entryn++){
		  if($entryn==sizeof($Report['Comments']['Comment'])){
				$Comment=array('Text'=>array('value'=>''),
				'Teacher'=>array('value'=>'ADD NEW ENTRY'));
				$inmust='yes';
				$rowstate='rowminus';
				$rowclass='revealed';
				}
		  else{$Comment=$Report['Comments']['Comment'][$entryn]; 
				$inmust=$Comment['id_db'];
				$rowstate='rowplus';
				$rowclass='hidden';
				}
		  $rown=0;
		  $en=$entryn+1;
		  $openId=$rid.'-'.$sid.'-'.$bid.'-'.$pid.'-'.$en;
		  $Comment['id_db']=$openId;
		  if($edit_comments_off!='yes'){
?>
  <tbody id="<?php print $openId;?>">
	<tr onClick="clickToReveal(this)" class="<?php print $rowstate;?>" 
					id="<?php print $openId.'-'.$rown++;?>">
	  <th>&nbsp</th>
	  <td><?php print_String('teachercomment');?>:</td>
	  <td><?php print $Comment['Teacher']['value'];?></td>
	  <td id="icon<?php print $openId;?>" class="">		  
		<img class="clicktoedit" name="Write"  
		  onClick="clickToWriteComment(<?php print $sid.','.$rid.',\''.$bid.'\',\''.$pid.'\',\''.$entryn.'\',\''.$openId.'\'';?>);" 
		  title="<?php print_string('clicktowritecomment');?>" />
	  </td>
	  <input type="hidden" id="inmust<?php print $openId;?>" name="inmust<?php print $sid.':'.$inc++;?>" 
		value="<?php print $inmust;?>" />
	</tr>
<?php
		if($report['addcategory']=='yes'){
		  reset($catdefs);
  		  while(list($c4,$catdef)=each($catdefs)){
			$catid=$catdefs[$c4]['id'];
			$catname=$catdefs[$c4]['name'];
			$ratings=$ratingnames[$catdefs[$c4]['rating_name']];
		   	print '<tr class="'.$rowclass.'" id="'.$openId.'-'.$rown++.'"><th></th>';
			print '<td>'.$catname.'</td>';
			while(list($value,$descriptor)=each($ratings)){
			  print '<td><label>'.$descriptor.'</label>';
			  print '<input type="radio" name="sid'.$sid.':'.$inc.'"
						tabindex="'.$tab.'" value="'.$value.'" ';
			  if(($Comment['Categories']['Category'][$c4]['value']!=' ' 
				 and $Comment['Categories']['Category'][$c4]['value']!='') 
				 or $Comment['Categories']['Category'][$c4]['value']=='0'){
					if($Comment['Categories']['Category'][$c4]['value']==$value){
						print ' checked ';
						}
					}
				print '/></td>';
				}
			$inc++;
			print '</tr>';
	   		}
		  }
		if($report['addcomment']=='yes'){
			if($report['commentlength']=='0'){$commentlength='';}
		    else{$commentlength=' maxlength="'.$report['commentlength'].'"';}
			print '<tr class="'.$rowclass.'" id="'.$openId.'-'.$rown++.'" >';
			print '<th></th><td colspan="5">';
			print '<textarea '.$commentlength.' rows="2" cols="80" ';
			print 'onClick="clickToWriteComment('.$sid.','.$rid.',\''.$bid.'\',\''.$pid.'\',\''.$entryn.'\',\''.$openId.'\');"'; 
			print ' tabindex="'.$tab.'" name="sid'.$sid.':'.$inc++.'" id="text'.$openId.'">';
			print $Comment['Text']['value'];
			print '</textarea>';
			if($inmust!='yes'){
?>
			  <button class="rowaction" type="button" title="Delete this entry"
				name="current" value="delete_reportentry.php" onClick="clickToAction(this)">
				<img class="clicktodelete" />
			  </button>
<?php
				}
			print '</td></tr>';
			}
?>
		  <div id="<?php print 'xml-'.$openId;?>" style="display:none;">
	  <?php				xmlechoer('Comment',$Comment); ?>
		  </div>
  </tbody>
<?php
		  }
		}
	}
?>