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
<?php
		if($edit_comments_off!='yes'){
?>
<?php
			}
		else{
			if($grading_grades=='' or $grading_grades==' '){print '<td>';}
?>
	  <input type="checkbox" name="sids[]" value="<?php print $sid;?>" />
		<?php print $tab;?>
<?php
			if($grading_grades=='' or $grading_grades==' '){print '</td>';}
			}

	if($edit_comments_off=='yes'){print '<td>';}
?>
	<h4><a href="infobook.php?current=student_view.php&sid=<?php print $viewtable[$row]['sid'];?>&sids[]=<?php print $viewtable[$row]['sid'];?>" target="viewinfobook" onclick="parent.viewBook('infobook');"<?php if($viewtable[$row]['preferredforename']!=''){$preferredforename='&nbsp;('.$viewtable[$row]['preferredforename'].')';}else{$preferredforename='';}?>><?php print $viewtable[$row]['surname'];?>,&nbsp;<?php print $viewtable[$row]['forename'].'&nbsp;'.$viewtable[$row]['middlenames'].$preferredforename;?></a></h4>
<?php
	if($edit_comments_off!='yes'){print '</td>';}

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
<?php
				}
			else{
			   	print '<td><input pattern="decimal" type="text" tabindex="'.$tab.'" name="sid'.$sid.':'.$inc++.'" maxlength="8" value="'.$value.'" /></td>';
				}
			}
?>
<?php
	if($reportdef['report']['addcomment']=='yes' or 
						$reportdef['report']['addcategory']=='yes'){ 
		$teacherdone=false;
		if($reportdef['report']['addcomment']=='yes' and $reportdef['report']['addcategory']=='no'){$Report['Comments']=fetchReportEntry($reportdef,$sid,$bid,$pid);}
		if($reportdef['report']['addcategory']=='yes'){$Report['Comments']=fetchSkillLog($reportdef,$sid,$bid,$pid);}
		if(!isset($Report['Comments']['Comment'][0]['Skills']['Skill'])){$Report['Comments']['Comment']=array();}
		$totalentryn=sizeof($Report['Comments']['Comment']);
		for($entryn=0;$entryn<=$totalentryn;$entryn++){
			if($reportdef['report']['addcomment']=='no' and !$teacherdone){
				if($totalentryn<1){
					$inmust='yes';
					$Comment=array('Text'=>array('value'=>'','value_db'=>''),
								   'Teacher'=>array('value'=>''));
					}
				else{
					$Comment=$Report['Comments']['Comment'][$entryn];
					$inmust=$Comment['id_db'];
					}
				$rowstate='rowminus';
				$rowclass='revealed';
				$teacherdone=true;
				}
			elseif($entryn==$totalentryn and !$teacherdone){
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

		  if($edit_comments_off!='yes' and ((!$teacherdone and $entryn==$totalentryn) or ($entryn<$totalentryn) or $totalentryn<1)){
?>
<?php
		if($reportdef['report']['addcomment']=='yes'){
?>
	  <?php print_string('teachercomment');?>:
	       <div class="special"><?php print $Comment['Teacher']['value'];?></div>
	       <span class="clicktowrite" name="Write" onClick="clickToWriteCommentNew(<?php print $sid.','.$rid.',\''.$bid.'\',\''.$pid.'\',\''.$entryn.'\',\''.$openId.'\'';?>);" title="<?php print_string('clicktowritecomment');?>" /></span>
<?php
			  }
?>
	  <input type="hidden" id="inmust<?php print $openId;?>" name="inmust<?php print $sid.':'.$inc++;?>" value="<?php print $inmust;?>" />
<?php
		if($reportdef['report']['addcategory']=='yes'){
			$ass_colspan++;
			unset($Skills);
			if(isset($Comment['Skills'])){$Skills=$Comment['Skills'];}
			else{
				$Skills['Skill']=array();
				$Skills['ratingname']=get_report_ratingname($reportdef,$bid);
				}
			$ratings=get_ratings($Skills['ratingname']);

			foreach($catdefs as $catindex=> $catdef){
				$catid=$catdefs[$catindex]['id'];
				$Statement=array('Value'=>$catdefs[$catindex]['name']);
				$Statement=personaliseStatement($Statement,$Student);
				if($catdefs[$catindex]['rating']!=''){
					if(!isset($cat_grading_grades)){
						/*TODO: Only works with a single uniform grade scheme. */
						$gena=$catdefs[$catindex]['rating_name'];
						$d_g=mysql_query("SELECT grades FROM grading WHERE name='$gena';");
						if(mysql_num_rows($d_g)>0){$cat_grading_grades=mysql_result($d_g,0);}
						else{$cat_grading_grades='';}
						}
					$statementrating='<span>'.scoreToGrade($catdefs[$catindex]['rating'],$cat_grading_grades).'</span>';
					}
				else{
					$statementrating='';
					}

				$extra_colspan=$ass_colspan+1;

				$statementlabel='';

				if($catdefs[$catindex]['rating']!=''){$statementlabel=$statementrating.' ';}
				if($catdefs[$catindex]['subtype']!=''){$statementlabel.='<label style="float:right;">'.get_subjectname($catdefs[$catindex]['subtype']).'</label><br />';}
				elseif($statementrating!=''){
					$statementlabel=$statementrating.'';
					}
				else{$statementlabel='';}
				print '';
				print '<fieldset class="divgroup markbook-img">';
				print '<div class="list-box"><span>'.display_date($setcat_date).'</span>';
				print '<ul class="chk-list">';

				/* Find any previously recorded value for this catid,
				   make a first guess that they will have been
				   recorded in the same order as the cats are
				   defined. But any blanks or changes will have
				   scuppered this.
				 */
				$setcat_value=-1000;
				$setcat_date='';

				if(isset($Skills['Skill'][$catindex]) 
				   and $Skills['Skill'][$catindex]['id_db']==$catid){
					$setcat_value=$Skills['Skill'][$catindex]['value'];
					$setcat_date=$Skills['Skill'][$catindex]['date'];
					}
	   			else{
					foreach($Skills['Skill'] as $Category){
						if($Category['id_db']==$catid){
							$setcat_value=$Category['value'];
							$setcat_date=$Category['date'];
							}
						}
					}
				if(($setcat_value==' ' or $setcat_value=='') and $setcat_value!='0'){
					$setcat_value=-1000;
					$setcat_date='';
					}
				foreach($ratings as $value => $descriptor){
					$checkclass='';
					$checked='';
					$trafficlite='';
					if($setcat_value==$value){
						$checkclass='checked';
						$checked='checked';
						if($value=='1'){$checkclass=' golite';}
						elseif($value=='0'){$checkclass=' pauselite';}
						elseif($value=='-1'){$checkclass='hilite';}
						}
					if($descriptor=='red'){$trafficlite='class="hilite"';}
					elseif($descriptor=='green'){$trafficlite='class="golite"';}
					elseif($descriptor=='yellow'){$trafficlite='class="pauselite"';}
					else{$trafficlite='';}
					print '<li class="row '.$checkclass.'"><label '.$trafficlite.'>'.$descriptor.'</label>';
					print '<input type="radio" name="sid'.$sid.':'.$inc. '" tabindex="'.$tab.'" value="'.$value.'" '.$checked;
					print ' /></li>';
					}

				print '<!--li>'.get_string('uncheck',$book).'</li-->';
				print '<li><input type="radio" name="sid'.$sid.':'.$inc. '" value="uncheck" /></li>';

				$inc++;
				print '';
				
				if($reportdef['report']['addcategory']=='yes' and $reportdef['report']['course_id']=='FS'){

					/* TO REMOVE?
					$profile_eid=get_profile_eid($rid);
					$thisscore=get_assessment_score($eid,$sid,$bid,$pid);
					$eidsid_id=$thisscore['id'];
					*/
                    print '<li>';
					$imagebuttons=array();
					$imagebuttons['clicktoload']=array('name'=>'Attachment',
												 'onclick'=>"clickToAttachFile($sid,$rid,'$catid','$pid','$sid')", 
												 'class'=>'clicktoload',
												 'value'=>'category_editor.php',
												 'title'=>'clicktoattachfile');
					$d_c=mysql_query("SELECT r.id FROM report_skill as r JOIN file as f ON r.id=f.other_id WHERE r.id='$catid' AND r.profile_id='$rid' AND f.owner_id='$sid';");
					rowaction_buttonmenu($imagebuttons,array(),$book);
					require_once('lib/eportfolio_functions.php');

					print '</li></ul>';
					print '</div><h5>'.$statementlabel. $Statement['Value'].'</h5>';

					while($c=mysql_fetch_array($d_c,MYSQL_ASSOC)){
						$files=(array)list_files($Student['EPFUsername']['value'],'assessment',$c['id']);
						//html_document_list($files);
						display_file($Student['EPFUsername']['value'],'assessment',$catid,'');
						/*Displays the comment without files*/
						/*if(count($files)==0 and $c['comment']!=''){
?>
							<span title="<?php echo $c['comment'];?>">
								<button type="button" class="rowaction imagebutton" >
									<img class="displayfile">
								</button>
							</span>
<?php
							}*/
						}

					/* TO REMOVE?
					unset($thisscore);
					unset($eidsid_id);
					*/
					print '</fieldset>';
					}
                    if($setcat_date!=' ' and $setcat_date!=''){
                        print '<input type="hidden" name="cat'.$sid.':'.$catid.'" value="'.$setcat_value.'"/>';
                        print '<input type="hidden" name="dat'.$sid.':'.$catid.'" value="'.$setcat_date.'"/>';
                    }
				}
			}

		if($reportdef['report']['addcomment']=='yes' or $reportdef['report']['addcategory']=='yes'){
			if($reportdef['report']['commentlength']=='0'){$commentlength='';}
		    else{$commentlength=' maxlength="'.$reportdef['report']['commentlength'].'"';}
			print '';
			print '';
			print '<textarea '.$commentlength.' rows="1" cols="80" readonly="readonly" style="display:none;"';
/*TODO: the xmlid must have the real entryn not the index!!!!*/
			print 'onClick="clickToWriteCommentNew('.$sid.','.$rid.',\''.$bid.'\',\''.$pid.'\',\''.$entryn.'\',\''.$openId.'\');"'; 
			print ' tabindex="'.$tab.'" name="sid'.$sid.':'.$inc++.'" id="text'.$openId.'">';
			print $Comment['Text']['value_db'];
			print '</textarea>';
			$imagebuttons=array();
			if($inmust=='yes' and $reportdef['report']['addcategory']=='yes'){
				$imagebuttons['clicktoconfigure']=array('name'=>'current',
    				'onclick'=>"clickToConfigureCategories('cat',$rid,'$bid','$pid','$class_stage','0')", 
    				'value'=>'category_editor.php',
    				'title'=>'configure');
    				}
			if($inmust!='yes' and $reportdef['report']['addcomment']=='yes'){
				$imagebuttons['clicktodelete']=array('name'=>'current',
													 'value'=>'delete_reportentry.php',
													 'title'=>'deletethiscomment');
				}
			rowaction_buttonmenu($imagebuttons,array(),$book);
			print '';
			}
?>
	<div id="<?php print 'xml-'.$openId;?>" style="display:none;">
	  <?php	 xmlechoer('Comment',$Comment); ?>
	</div>
<?php
		  }
		}
	}
?>
