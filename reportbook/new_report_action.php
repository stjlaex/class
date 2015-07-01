<?php
/**                    new_report_action.php
 *
 *
 */

$action='new_report.php';

/* The rcrid decides wether its a report binder or a subject report*/
if($r>-1){$rcrid=$respons[$r]['course_id'];}
else{$rcrid='';}
include('scripts/sub_action.php');

/* Can only create a new report for the current curriculum year... */
$curryear=get_curriculumyear();

if($sub!='Submit'){
	$action='new_report_action.php';

	if(isset($_POST['recordid'])){
		$oldrid=$_POST['recordid'];
		}
	else{$oldrid=-1;}
	$RepDef=fetchReportDefinition($oldrid);


/* Unlikely but Could be editting a report from a previous years, so
   set the academic year.
*/
	if($oldrid!=-1){$curryear=$RepDef['Year']['value'];}

three_buttonmenu();
?>
    <div class="content">
        <form id="formtoprocess" name="formtoprocess" novalidate method="post" action="<?php print $host;?>">
            <fieldset class="divgroup">
                <h5><?php print_string('identityofreport',$book);?></h5>
                <div class="left">
<?php
                    	$tab=xmlelement_div($RepDef['Title'],'',$tab,'center','reportbook');
                     	$tab=xmlelement_div($RepDef['PublishedDate'],'',$tab,'center','reportbook');
                     	$tab=xmlelement_div($RepDef['Deadline'],'',$tab,'center','reportbook');
                     	if($RepDef['AttendanceStartDate']['value']=="0000-00-00"){$RepDef['AttendanceStartDate']['value']=substr($RepDef['PublishedDate']['value'],0,4)."-08-15";}
                     	if(($oldrid!=-1 and $RepDef['Type']['value']!='profile') or $oldrid==-1){
						$tab=xmlelement_div($RepDef['AttendanceStartDate'],'',$tab,'center','reportbook');
						}
?>
                </div>
                <div class="right">
                	<div class="center">
<?php
					$seltype=$RepDef['Type']['value'];
?>
						<select name='type' class='required'>
							<option value='profile' <?php if($seltype=='profile'){echo 'selected';}?> ><?php print_string('profile',$book);?></option>
							<option value='subject' <?php if($seltype=='subject'){echo 'selected';}?> ><?php print_string('subject','admin');?></option>
							<option value='wrapper' <?php if($seltype=='wrapper'){echo 'selected';}?> ><?php print_string('wrapper',$book);?></option>
						</select>
				</div>
<?php
		if($rcrid!=''){
?>
                	<div class="center">
<?php
                            $selstage=$RepDef['Stage']['value'];
                            include('scripts/list_stage.php');
?>
                	</div>
                	<div class="center">
<?php
                            $selsubjectstatus=$RepDef['SubjectStatus']['value'];
                            include('scripts/list_subjectstatus.php');
?>
                	</div>
                	<div class="center">
<?php
                            $selcomponentstatus=$RepDef['ComponentStatus']['value'];
                            include('scripts/list_componentstatus.php');
?>
                	</div>
<?php
                    	}
?>
                </div>
            </fieldset>
<?php
		if($rcrid!=''){
?>
            <div class="left">
                <fieldset class="divgroup">
                    <h5><?php print_string('includeassessmentscores',$book);?></h5>
                    <?php
                        $seleids=array();
                        while(list($assindex,$eid)=each($RepDef['eids'])){
                        	$seleids[]=$eid;
                        	}
                        while(list($assindex,$eid)=each($RepDef['stateids'])){
                        	$seleids[]=$eid;
                        	}
                        $required='no';
                        $selprofid='%';
                        include('scripts/list_assessment.php');
?>
                </fieldset>
            </div>
<?php
			if(($oldrid!=-1 and $RepDef['Type']['value']!='profile') or $oldrid==-1){
?>
            <div class="right">
                <fieldset class="divgroup">
                    <h5><?php print_string('comments',$book);?></h5>
        			<div class="center">
<?php
                        $checkchoice=$RepDef['CommentsOn']['value'];
                        print_string('allowsubjectcomments',$book);
                        $checkname='reptype'; include('scripts/check_yesno.php');

?>
        			</div>
        			<div class="center">
					  <br />
        			</div>
        			<div class="center">
<?php
                        $checkchoice=$RepDef['CommentsCompulsory']['value'];
                        print_string('commentsarecompulsory',$book);
                        $checkname='commentcomp'; include('scripts/check_yesno.php');
?>
        			</div>
        			<div class="center">
					  <br />
        			</div>
        			<div class="center">
                        <label><?php print_string($RepDef['CommentsLength']['label'],$book);?></label>
            			<input type="text" name="commentlength" value="<?php print $RepDef['CommentsLength']['value'];?>" tabindex="<?php print $tab++;?>" style="width:4em;" pattern="integer" maxlength="4" />
<?php
                            // $tab=xmlelement_div($RepDef['CommentsLength'],'',$tab,'center','reportbook');
?>
        			</div>
                </fieldset>
            </div>
<?php
				}
			}
		else{
        	   /**
        	       * This is a wrapper which binds the following subject reports
        	       * together.
    	       */
?>
            <div class="left">
                <fieldset class="divgroup">
                    <h5><?php print_string('subjectreports',$book);?></h5>
<?php
                        $selrids=array();
                        while(list($repindex,$rep)=each($RepDef['reptable']['rep'])){
                        	$selrids[]=$rep['id_db'];
                        	}
                        include('scripts/list_report.php');
?>
                </fieldset>
            </div>
<?php
                /**
                 * The rest covers the summary matter like signature boxes, form
                 * and section level comments.
                 */
                $selsigs=array();
                $selcoms=array();
                while(list($sumindex,$catdef)=each($RepDef['summaries'])){
                	if($catdef['type']=='sig'){$selsigs[]=$catdef['id'];}
                	if($catdef['type']=='com'){$selcoms[]=$catdef['id'];}
                	}
?>
            <div class="right">
                <fieldset class="divgroup">
                    <h5><?php print_string('summarymatter',$book);?></h5>
                    <div class="center">
                        <label for="Summary comments"><?php print_string('summarycomment',$book);?></label>
                        <select id="Summary comments" type="text" name="catdefids[]" class="required" size="3" multiple="multiple" tabindex="<?php print $tab++;?>" >
                            <option
<?php
                                	if(sizeof($selcoms)==0){print ' selected="selected" ';}
?>
                                	value="-100">
                                	<?php print_string('none');?>
                            </option>
<?php
                            	$d_categorydef=mysql_query("SELECT id, name, subject_id FROM
                            	categorydef WHERE type='com' AND (course_id LIKE '$rcrid'
                            	OR course_id='%') ORDER BY rating");
                            	while($catdef=mysql_fetch_array($d_categorydef,MYSQL_ASSOC)){
?>
                            <option
                        		<?php if(in_array($catdef['id'], $selcoms)){print ' selected="selected" ';}?>
                        		value="<?php print $catdef['id'];?>">
                                <?php print $catdef['name'];?>
                            </option>
<?php
                    		  }
?>
                        </select>
                    </div>
                    <div class="center">
                        <label for="Summary comments"><?php print_string('summarysignature',$book);?></label>
                        <select id="Summary signatures" type="text" name="catdefids[]" class="required" size="3" multiple="multiple" tabindex="<?php print $tab++;?>" >
                            <option
<?php
					if(sizeof($selsigs)==0){print ' selected="selected" ';}
?>
        			            value="-100">
        			            <?php print_string('none');?>
                            </option>
<?php
                            $d_categorydef=mysql_query("SELECT id, name, subject_id FROM
                            	categorydef WHERE type='sig' AND (course_id LIKE '$rcrid'
                            	OR course_id='%') ORDER BY rating;");
                            while($catdef=mysql_fetch_array($d_categorydef,MYSQL_ASSOC)){

?>
                    		<option
<?php
					if(in_array($catdef['id'], $selsigs)){print ' selected="selected" ';}
?>
                                value="<?php print $catdef['id'];?>">
                    			<?php print $catdef['name'];?>
                    		</option>
<?php
                            	}
?>
                        </select>
                    </div>
                </fieldset>
            </div>
<?php
			}
?>
            <div class="left">
                <fieldset class="divgroup">
        		  <h5><?php print_string('ratingboxes',$book);?></h5>
<?php
                    	$checkchoice=$RepDef['CategoriesOn']['value'];
                    	$checkname='addcategory'; include('scripts/check_yesno.php');
?>
						<div class="right">
<?php
                    	$selratingname=$RepDef['CategoriesRating']['value'];
                    	include('scripts/list_rating_name.php');
?>
						</div>
                </fieldset>
<?php
		if(($oldrid!=-1 and $RepDef['Type']['value']!='profile') or $oldrid==-1){
?>
                <fieldset class="divgroup">
				<h5><?php print_string('reportphotos',$book);?></h5>
<?php
			$checkchoice=$RepDef['AddPhotos']['value'];
			$checkname='addphotos'; include('scripts/check_yesno.php');
?>
			</fieldset>
<?php
			}
?>
            </div>
            <div class="right">
<?php
		if($rcrid!=''){
?>
                <fieldset class="divgroup">
                    <h5><?php print_string('assessmentprofile',$book);?></h5>
<?php
                    	$listname='profid';
                    	$onchange='no';
                    	$required='no';
                    	$multi=4;
                    	$profids=array();
                    	if(isset($RepDef['ProfileLinks']) and sizeof($RepDef['ProfileLinks']>0)){
                    		foreach($RepDef['ProfileLinks'] as $ProfileLink){
                    			$profids[]=$ProfileLink['id_db'];
                    			}
                    		}
                    	include('scripts/list_assessment_profile.php');
?>
			</fieldset>
<?php
			}
?>
		<fieldset class="divgroup">
			<h5><?php print_string('subcomments',$book);?></h5>
<?php
			if($rcrid!=''){
				$selbid='%';
				$listlabelstyle='external';
				$listlabel='subject';
				$required='no';
				$selbid='0';
				include('scripts/list_subjects.php');
				}
			else{
?>
				<label><?php print_string('subject',$book); ?></label>
                <select id="bid" name="bid">
					<option></option>
<?php
				$d_categorydef=mysql_query("SELECT id, name, subject_id, subtype FROM
                            categorydef WHERE type='com' AND (course_id LIKE '$rcrid'
                            OR course_id='%') ORDER BY rating");
                while($catdef=mysql_fetch_array($d_categorydef,MYSQL_ASSOC)){
?>
                    <option	value="<?php print $catdef['subtype'];?>">
                        <?php print $catdef['name'];?>
                    </option>
<?php
					}
?>
				</select>

<?php
				}

			$listlabelstyle='external';
			$listlabel='subcomment';
			$required='no';
			$cattype='sub';
			include('scripts/list_category.php');

			$imagebuttons['add']=array('name'=>'add',
								'onclick'=>'alert(\'asdasf\');');
			rowaction_buttonmenu($imagebuttons,'',$book);

			$d_sc=mysql_query("SELECT ridcatid.subject_id as bid,categorydef.name, categorydef.id as catid
								FROM ridcatid JOIN categorydef ON categorydef.id=ridcatid.categorydef_id
								WHERE ridcatid.report_id='$oldrid' AND categorydef.type='sub';");
			print '<select size="4" tabindex="19" multiple="multiple" name="subcomms[]" id="subcomms" class="uniform-multiselect">
				<option value="">Subcomments</option>';
			while($subcomments=mysql_fetch_array($d_sc,MYSQL_ASSOC)){
				$bid=$subcomments['bid'];
				$subcomment=$subcomments['name'];
				$catid=$subcomments['catid'];
				print '<option selected="selected" value="'.$bid.'-'.$catid.'">'.$bid.' - '.$subcomment.'</option>';
				}
			print '</select>';
?>
			</fieldset>
        </div>
<?php
		if($rcrid==''){
            		/* Only wrappers are to be printed and need a style and template. */
?>
            <div class="left">
                <fieldset class="divgroup">
            	    <h5><?php print_string('nameoftemplate',$book);?></h5>
            		<div class="left">
<?php
                    		$seltemplate=$RepDef['Template']['value'];
                    		include('scripts/list_template.php');
?>
            		</div>
        		    <div class="left">
<?php
                		unset($key);
                		if($RepDef['Style']['value']!=''){$selpaperstyle=$RepDef['Style']['value'];}
                		else{$selpaperstyle='portrait';}
                		$listname='paperstyle';$listlabel='paperstyle';$required='yes';
                		include('scripts/set_list_vars.php');
                		list_select_enum('paperstyle',$listoptions,$book);
?>
                    </div>
                </fieldset>
            </div>
<?php
                }
?>
    		<input type="hidden" name="oldrid" value="<?php print $oldrid;?>" />
    		<input type="hidden" name="current" value="<?php print $action;?>" />
    		<input type="hidden" name="choice" value="<?php print $choice; ?>"/>
    		<input type="hidden" name="cancel" value="<?php print $choice; ?>"/>
        </form>
    </div>
<?php
		}
elseif($sub=='Submit'){

	if($rcrid==''){$crid='wrapper';}
	else{$crid=$rcrid;}
	$oldrid=$_POST['oldrid'];//-1 if this is a new report
	if($oldrid==-1){
		mysql_query("INSERT INTO report (course_id, year) VALUES ('$crid','$curryear');");
		$rid=mysql_insert_id();
		}
	else{
		$rid=$oldrid;
		}


	$title=$_POST['title'];
	$date=$_POST['date'];
	$deadline=$_POST['deadline'];
	$attendancestartdate=$_POST['attendancestartdate'];
	if(isset($_POST['template'])){$transform=$_POST['template'];}else{$transform='';}
	if(isset($_POST['type'])){$type=$_POST['type'];}else{$type='';}
	if(isset($_POST['paperstyle'])){$paperstyle=$_POST['paperstyle'];}else{$paperstyle='portrait';}
	if(isset($_POST['addcategory0'])){$addcategory=$_POST['addcategory0'];}
	if(isset($_POST['ratingname'])){$ratingname=$_POST['ratingname'];}
	if(isset($_POST['addphotos0'])){$addphotos=$_POST['addphotos0'];}

	mysql_query("UPDATE report SET title='$title', date='$date', attendancestartdate='$attendancestartdate',
				 deadline='$deadline', style='$paperstyle', transform='$transform', type='$type',
				addcategory='$addcategory', addphotos='$addphotos', rating_name='$ratingname'
				 WHERE id='$rid';");

	if($crid!='wrapper'){
		/*** This is a subject report ****/

		$substatus=$_POST['subjectstatus'];
		$compstatus=$_POST['componentstatus'];
		$stage=$_POST['stage'];
		$reptype=$_POST['reptype0'];
		$commentcomp=$_POST['commentcomp0'];
		if(isset($_POST['commentlength'])){$commentlength=$_POST['commentlength'];}
		else{$commentlength='0';}
		if(isset($_POST['profids'])){$profids=(array)$_POST['profids'];}

		mysql_query("UPDATE report SET subject_status='$substatus', component_status='$compstatus',
				addcomment='$reptype', commentlength='$commentlength',
				commentcomp='$commentcomp', stage='$stage', type='$type',
				addcategory='$addcategory', rating_name='$ratingname' WHERE id='$rid';");

		/* Entry in rideid to link new report with chosen assessments. */
		mysql_query("DELETE FROM rideid WHERE report_id='$rid';");
		$eids=(array)$_POST['eids'];
		foreach($eids as $eid){
			mysql_query("INSERT INTO rideid (report_id, assessment_id) VALUES ('$rid','$eid');");
			}

		/* The categories stored in ridcatid with subject_id set to
		   bid or % and othertype blank. Only used alongwith a written
		   subject comment. Otherwise the categories will be from a
		   profile and do not need ridcatid. */
		mysql_query("DELETE FROM ridcatid WHERE report_id='$rid' AND subject_id!='profile' AND subject_id!='summary' AND subject_id!='wrapper';");
		if($addcategory=='yes' and $reptype=='yes'){
			$d_catdef=mysql_query("SELECT id, subject_id FROM categorydef WHERE
						type='cat' AND (course_id='%' OR course_id='$crid') AND othertype='';");
			while($d_catid=mysql_fetch_array($d_catdef,MYSQL_NUM)){
				$catid=$d_catid[0];
				$catbid=$d_catid[1];
				mysql_query("INSERT INTO ridcatid (report_id, categorydef_id, subject_id) VALUES ('$rid', '$catid', '$catbid');");
				}
			}

		mysql_query("DELETE FROM ridcatid WHERE report_id='$rid' AND subject_id='profile';");
		if(isset($profids) and sizeof($profids)>0){
			foreach($profids as $profid){
				mysql_query("INSERT INTO ridcatid (report_id,categorydef_id,subject_id)
							 VALUES ('$rid', '$profid', 'profile');");
				}
			}

		}
	else{
		/*** This is a wrapper for subject reports. ***/

		/* Summary matter goes into ridcatid with subject_id='summary'. */
		mysql_query("DELETE FROM ridcatid WHERE report_id='$rid'
							AND subject_id='summary';");
		$catdefids=(array)$_POST['catdefids'];
		while(list($index,$catid)=each($catdefids)){
			if($catid!='-100'){
				mysql_query("INSERT INTO ridcatid (report_id,
							categorydef_id, subject_id) VALUES
							('$rid', '$catid', 'summary')");
				}
			}

		/* Wrapper report goes into ridcatid with subject_id='wrapper'*/
		mysql_query("DELETE FROM ridcatid WHERE report_id='$rid'
						AND subject_id='wrapper';");
		$rids=(array)$_POST['rids'];
		foreach($rids as $wraprid){
			mysql_query("INSERT INTO ridcatid (report_id,
							categorydef_id, subject_id) VALUES
							('$rid', '$wraprid', 'wrapper')");
			}

		/* The summary categories stored in ridcatid with subject_id set to form, year or section. */
		mysql_query("DELETE FROM ridcatid WHERE report_id='$rid' AND subject_id!='profile' AND subject_id!='summary' AND subject_id!='wrapper';");
		if($addcategory=='yes'){
			$d_catdef=mysql_query("SELECT id, subject_id FROM categorydef WHERE
						type='cat' AND (subject_id='form' OR subject_id='year' OR subject_id='section');");
			while($d_catid=mysql_fetch_array($d_catdef,MYSQL_NUM)){
				$catid=$d_catid[0];
				$catbid=$d_catid[1];
				mysql_query("INSERT INTO ridcatid (report_id,
					   categorydef_id, subject_id) VALUES ('$rid', '$catid', '$catbid')");
				}
			}

		}

	$bid=$_POST['bid'];
	$catid=$_POST['catid'];
	$subcomms=$_POST['subcomms'];
	$d_cats=mysql_query("SELECT * FROM ridcatid WHERE report_id='$rid' AND subject_id='$bid' 	AND categorydef_id='$catid';");
	if(mysql_num_rows($d_cats)==0 and $bid!=''){
		mysql_query("INSERT INTO ridcatid (report_id,categorydef_id,subject_id)
						 VALUES ('$rid', '$catid', '$bid');");
		}
	if(count($subcomms)>0){
		foreach($subcomms as $subcomm){
			list($bid, $catid)=explode('-',$subcomm);
			if($bid!='' and $catid!=''){
				mysql_query("INSERT INTO ridcatid (report_id,categorydef_id,subject_id)
						 VALUES ('$rid', '$catid', '$bid');");
				}
			}
		}

	include('scripts/redirect.php');
	}
?>
