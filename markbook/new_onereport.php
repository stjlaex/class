<?php
/**	   	   								new_onereport.php
 *
 * Included as part of new_edit_reports when the complete subject
 * report of a single sid is being edited. Could include teacher
 * comments and rating categroies and assessments.
 *
 */
		$inc=0;
		$Report=array();
		$Report['Assessments']['Assessment']=array();
		/*this is the xml-ready array*/
		$Report['Subject']=array('id'=>$bid, 'value'=>$subjectname);
		if($pid!=''){$Report['Component']=array('id'=>$pid, 'value'=>$componentname);}
?>
	<tr id="sid-<?php print $sid;?>"
<?php
		if($edit_comments_off!='yes'){
?>
	style="height:4em;">
	<th>
	</th>
<?php
			}
		else{
?>
	><td>
	  <input type="checkbox" name="sids[]" value="<?php print $sid;?>" />
		<?php print $tab;?>
	</td>
<?php
			}
?>
	<td>
	<a href="infobook.php?current=student_view.php&sid=<?php print $viewtable[$row]['sid'];?>&sids[]=<?php print $viewtable[$row]['sid'];?>" target="viewinfobook" onclick="parent.viewBook('infobook');"><?php print $viewtable[$row]['surname'];?>,&nbsp;<?php print $viewtable[$row]['forename'].$viewtable[$row]['preferredforename'];?></a>
	</td>

<?php


//		reset($AssDefs);
//		while(list($index,$AssDef)=each($AssDefs)){
			reset($inasses);
			while(list($index,$inass)=each($inasses)){
				$eid=$inass['eid'];
				$Assessments=(array)fetchAssessments_short($sid,$eid,$bid,$inass['pid']);
				if(sizeof($Assessments)>0){
					$Report['Assessments']['Assessment'][]=$Assessments[0];
					$value=$Assessments[0]['Value']['value'];
					}
				else{
					$value='';
					}
				$grading_grades=$inass['grading_grades'];
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
					list($level_grade, $level)=explode(':',$pairs[$c3]);
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
?>
	</tr>
<?php
	if($reportdef['report']['addcomment']=='yes' or 
						$reportdef['report']['addcategory']=='yes'){ 
		$teacherdone=false;
 		$Report['Comments']=fetchReportEntry($reportdef,$sid,$bid,$pid);
		if(!isset($Report['Comments']['Comment'])){$Report['Comments']['Comment']=array();}
		$totalentryn=sizeof($Report['Comments']['Comment']);
		for($entryn=0;$entryn<=$totalentryn;$entryn++){
			if($entryn==$totalentryn and !$teacherdone){
				$Comment=array('Text'=>array('value'=>'','value_db'=>''),
				'Teacher'=>array('value'=>'ADD NEW ENTRY'));
				$inmust='yes';
				$rowstate='rowminus';
				$rowclass='revealed';
				}
			else{
				if($tid==$Report['Comments']['Comment'][$entryn]['Teacher']['id_db']){$teacherdone=true;}
				$Comment=$Report['Comments']['Comment'][$entryn];
				$inmust=$Comment['id_db'];
				if($totalentryn<1 or $tid==$Report['Comments']['Comment'][$entryn]['Teacher']['id_db']){
					$rowstate='rowminus';
					$rowclass='revealed';
					}
				else{
					$rowstate='rowplus';
					$rowclass='hidden';
					}
				}
		  $rown=0;
/*TODO: the xmlid must have the real entryn not the index!!!!*/
		  $en=$entryn+1;
		  $openId=$rid.'-'.$sid.'-'.$bid.'-'.$pid.'-'.$en;
		  $Comment['id_db']=$openId;
		
		  if($edit_comments_off!='yes' and (!$teacherdone and $entryn==$totalentryn or $entryn<$totalentryn)){
?>
  <tbody id="<?php print $openId;?>">
	<tr onClick="clickToReveal(this)" class="<?php print $rowstate;?>" 
					id="<?php print $openId.'-'.$rown++;?>">
	  <th>&nbsp</th>
	  <th><?php print_string('teachercomment');?>:</th>
	  <td><div class="special"><?php print $Comment['Teacher']['value'];?></div></td>
	  <td id="icon<?php print $openId;?>" class="" colspan="<?php print $ass_colspan;?>">		  
		<img class="clicktowrite" name="Write"  
		  onClick="clickToWriteCommentNew(<?php print $sid.','.$rid.',\''.$bid.'\',\''.$pid.'\',\''.$entryn.'\',\''.$openId.'\'';?>);" 
		  title="<?php print_string('clicktowritecomment');?>" />
	  </td>
	  <input type="hidden" id="inmust<?php print $openId;?>" 
		name="inmust<?php print $sid.':'.$inc++;?>" 
		value="<?php print $inmust;?>" />
	</tr>
<?php
		if($reportdef['report']['addcategory']=='yes'){
			$ass_colspan++;
			$catdefs=get_report_categories($rid,$bid,$pid,'cat',$class_stage);
			$ratings=$reportdef['ratings'];

			reset($catdefs);
			unset($Categories);
			if(isset($Comment['Categories'])){$Categories=$Comment['Categories'];}
			else{
				$Categories['Category']=array();
				$Categories['ratingname']=get_report_ratingname($reportdef,$bid);
				}
			$ratings=$reportdef['ratings'][$Categories['ratingname']];

			while(list($catindex,$catdef)=each($catdefs)){
				$catid=$catdefs[$catindex]['id'];
				$catname=$catdefs[$catindex]['name'];
				print '<tr class="'.$rowclass.'" id="'.$openId.'-'.$rown++.'"><th></th>';
				print '<td colspan="'.$ass_colspan.'"><div class="row" style="width:26%;"><p>'
					.$catname.'</p></div>';
				reset($ratings);

				/* Find any previously recorded value for this catid,
				   make a first guess that they will have been
				   recorded in the same order as the cats are
				   defined. But any blanks or changes will have
				   scuppered this.
				 */
				$setcat_value=-1000;

				
				if(isset($Categories['Category'][$catindex]) 
				   and $Categories['Category'][$catindex]['id_db']==$catid){
					$setcat_value=$Categories['Category'][$catindex]['value'];
					}
	   			else{
						foreach($Categories['Category'] as $Category){
							if($Category['id_db']==$catid){
								$setcat_value=$Category['value'];
								}
							}
					}
				if(($setcat_value==' ' or $setcat_value=='') and $setcat_value!='0'){
					$setcat_value=-1000;
					}

				while(list($value,$descriptor)=each($ratings)){
					$checkclass='';
					if($setcat_value==$value){$checkclass='checked';}

					print '<div class="row '.$checkclass.'"><label>'.$descriptor.'</label>';
					print '<input type="radio" name="sid'.$sid.':'.$inc.'"
						tabindex="'.$tab.'" value="'.$value.'" '.$checkclass;
					print ' /></div>';
					}
				$inc++;
				print '</td></tr>';
				}
			}
		if($reportdef['report']['addcomment']=='yes'){
			if($reportdef['report']['commentlength']=='0'){$commentlength='';}
		    else{$commentlength=' maxlength="'.$reportdef['report']['commentlength'].'"';}
			print '<tr class="'.$rowclass.'" id="'.$openId.'-'.$rown++.'" >';
			print '<th></th><td colspan="'.$ass_colspan.'">';
			print '<textarea '.$commentlength.' rows="1" cols="80" readonly="readonly" style="display:none;"';
/*TODO: the xmlid must have the real entryn not the index!!!!*/
			print 'onClick="clickToWriteCommentNew('.$sid.','.$rid.',\''.$bid.'\',\''.$pid.'\',\''.$entryn.'\',\''.$openId.'\');"'; 
			print ' tabindex="'.$tab.'" name="sid'.$sid.':'.$inc++.'" id="text'.$openId.'">';
			print $Comment['Text']['value_db'];
			print '</textarea>';
			$extrabuttons=array();
			$imagebuttons=array();
			if($inmust=='yes' and $reportdef['report']['addcategory']=='yes'){
				$imagebuttons['clicktoconfigure']=array('name'=>'current',
														'onclick'=>"clickToConfigureCategories('cat',$rid,'$bid','$pid','$class_stage','0')", 
														'value'=>'category_editor.php',
														'title'=>'configure');
				}
			if($inmust!='yes'){
				$imagebuttons['clicktodelete']=array('name'=>'current',
													 'value'=>'delete_reportentry.php',
													 'title'=>'deletethiscomment');
				}
			rowaction_buttonmenu($imagebuttons,$extrabuttons,$book);
			print '</td></tr>';
			}
?>
	<div id="<?php print 'xml-'.$openId;?>" style="display:none;">
	  <?php	 xmlechoer('Comment',$Comment); ?>
	</div>
  </tbody>
<?php
		  }
		}
	}
?>
