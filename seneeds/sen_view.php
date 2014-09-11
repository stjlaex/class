<?php
/**                                  sen_view.php
 */

/**
 *  Have to be careful to check current as this can be called from the
 *  InfoBook too.
 */
if($current=='sen_view.php'){$action='sen_view_action.php';}
if(!isset($selbid)){$selbid='G';}
$SEN=fetchSEN($sid,$senhid);

?>
    <div id="heading">
        <h4>
            <label><?php print_string('senprofile',$book);?></label>
            <?php print $Student['Forename']['value'].' '.$Student['Surname']['value']; print ' - '.display_date($SEN['StartDate']['value']); ?>
        </h4>
    </div>
<?php 

	three_buttonmenu(array('removesen'=>array('name'=>'sub','value'=>'senstatus')));
?>
    <div class="content form-margin">
	   <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
            <div class="left">
                <fieldset class="divgroup assessment">
                    <h5><?php print get_string('internal',$book).' '.get_string('assessment',$book);?></h5>
                    <label for="Start Date">
                	   <?php print_string($SEN['StartDate']['label'],$book);?>
                    </label>
<?php
                        $todate=$SEN['StartDate']['value'];
                        include('scripts/jsdate-form.php');
?>
                    <label>
                        <?php print_string($SEN['NextReviewDate']['label'],$book);?>
                    </label>
<?php
                        $todate=$SEN['NextReviewDate']['value'];
                        include('scripts/jsdate-form.php');

	/* Allow up to 3 records with blanks for new entries*/
	while(sizeof($SEN['SENinternaltypes']['SENtype'])<3){
		$SEN['SENinternaltypes']['SENtype'][]=fetchSENtype();
		}
	$asscode='I';
	foreach($SEN['SENinternaltypes']['SENtype'] as $entryn => $SENtype){
		$enum=getEnumArray($SENtype['SENtype']['field_db'].'internal');
		print '<p><select id="Type"  tabindex="'.$tab++.'"
			name="'.$asscode. $SENtype['SENtype']['field_db'].$entryn.'">';
		print '<option value=""></option>';
		while(list($inval,$description)=each($enum)){ 
			print '<option ';
			if($SENtype['SENtype']['value']==$inval){print 'selected="selected" ';}
			print ' value="'.$inval.'">'.get_string($description,$book).'</option>';
			}
		print '</select>';

		$enum=getEnumArray($SENtype['SENtypeRank']['field_db']);
		print '<select id="Rank"  tabindex="'.$tab++.'" 
			name="'.$asscode. $SENtype['SENtypeRank']['field_db'].$entryn.'" size="1">';
		print '<option value=""></option>';		
		while(list($inval,$description)=each($enum)){
			print '<option ';
			if($SENtype['SENtypeRank']['value']==$inval){print "selected='selected'";}
			print " value='".$inval."'>".$description."</option>";
			}
		print '</select> </p>';
		}
?>
                </fieldset>
	           <fieldset class="divgroup">
                    <h5><?php print get_string('external',$book).' '.get_string('assessment',$book);?></h5>
                    <label for="Date">
        			 <?php print_string($SEN['AssessmentDate']['label'],$book);?>
                    </label>
<?php
                        $todate=$SEN['AssessmentDate']['value'];
                        $required='no';
                        include('scripts/jsdate-form.php');

                    /* Allow up to 3 records with blanks for new entries*/
                    while(sizeof($SEN['SENtypes']['SENtype'])<3){
                    	$SEN['SENtypes']['SENtype'][]=fetchSENtype();
                    	}
                    
                    $asscode='E';
                    foreach($SEN['SENtypes']['SENtype'] as $entryn => $SENtype){
                    	$enum=getEnumArray($SENtype['SENtype']['field_db']);
                    	print '<p><select id="Type"  tabindex="'.$tab++.'"
                    		name="'.$asscode. $SENtype['SENtype']['field_db'].$entryn.'">';
                    	print '<option value=""></option>';
                    	while(list($inval,$description)=each($enum)){ 
                    		print '<option ';
                    		if($SENtype['SENtype']['value']==$inval){print 'selected="selected" ';}
                    		print ' value="'.$inval.'">'.get_string($description,$book).'</option>';
                    		}
                    	print '</select>';
                    
                    	$enum=getEnumArray($SENtype['SENtypeRank']['field_db']);
                    	print '<select id="Rank"  tabindex="'.$tab++.'" 
                    		name="'.$asscode. $SENtype['SENtypeRank']['field_db'].$entryn.'" size="1">';
                    	print '<option value=""></option>';		
                    	while(list($inval,$description)=each($enum)){	
                    		print '<option ';
                    		if($SENtype['SENtypeRank']['value']==$inval){print "selected='selected'";}
                    		print " value='".$inval."'>".$description."</option>";
                    		}
                    	print '</select></p>';
                    	}
?>
	               </fieldset>
                </div>
                <div class="right">
                    <div class="tinytabs" id="sen">
                        <ul>
<?php
                            	$key=-1;
                            	$keybids=array();
                            	foreach($SEN['Curriculum'] as $key => $Subject){
                            		if(is_array($Subject)){
                            			$keybids[$Subject['Subject']['value_db']]=$key;
                            			}
                            		}
                            
                            	if(array_key_exists($selbid,$keybids)){$selkey=$keybids[$selbid];}
                            	else{$selkey=0;}
                            
                            	foreach($SEN['Curriculum'] as $key => $Subject){
                            		if(is_array($Subject)){
?>
                			<li id="<?php print 'tinytab-sen-'.$Subject['Subject']['value'];?>"><p 
                					 <?php if($key==$selkey){ print ' id="current-tinytab" ';}?>
                				class="<?php print $Subject['Subject']['value'];?>"
                				onclick="parent.tinyTabs(this)"><?php print $Subject['Subject']['value'];?></p>
                			</li>
                			<div class="hidden" id="tinytab-xml-sen-<?php print $Subject['Subject']['value'];?>">
                			    <fieldset class="divgroup">
                			    <p>
<?php
                                    $cattype='sen';$required='no';
                                    $listname='extrasupport';
                                    $selextrasupport=$Subject['ExtraSupport']['value_db'];
                                    $listlabel=$Subject['ExtraSupport']['label'];
                                    include('scripts/list_category.php');
?>
                                </p>
                                <p>
                    		  <label for="Strengths">
                    			<?php print_string($Subject['Strengths']['label'],$book); ?>
                    		  </label>
                    		  <textarea id="Strengths" wrap="on" rows="5" tabindex="<?php print $tab++;?>" name="<?php print $Subject['Strengths']['field_db'].$key;?>"><?php print $Subject['Strengths']['value']; ?></textarea>
                    		    </p>
                		        <p>
                    		  <label for="Weaknesses">
                    			<?php print_string($Subject['Weaknesses']['label'],$book); ?>
                    		  </label>
                    		  <textarea id="Weaknesses" wrap="on" rows="5" tabindex="<?php print $tab++;?>" name="<?php print $Subject['Weaknesses']['field_db'].$key;?>"><?php print $Subject['Weaknesses']['value']; ?></textarea>
                    		  </p>
                    		  <p>
                    		  <label for="Strategies">
                    		  <?php print_string($Subject['Strategies']['label'],$book); ?> 
                    		  </label>
                    		  <textarea id="Strategies" wrap="on" rows="5" tabindex="<?php print $tab++;?>"name="<?php print $Subject['Strategies']['field_db'].$key;?>"><?php print $Subject['Strategies']['value']; ?></textarea>
                    		  </p>
                    		  <p>
                    		      <label for="Targets">
                    		      <?php print_string($Subject['Targets']['label'],$book); ?> 
                    		  </label>
                    		  <textarea id="Targets" wrap="on" rows="5" tabindex="<?php print $tab++;?>" name="<?php print $Subject['Targets']['field_db'].$key;?>"><?php print $Subject['Targets']['value']; ?></textarea>
                			</p>
                			</fieldset>
                			</div>
<?php
                                	}
                                }
                            	$subject='addsubject';
?>
                			<li id="<?php print 'tinytab-sen-'.$subject;?>"><p 
                					 <?php if($key==-1){ print ' id="current-tinytab" ';}?>
                				class="<?php print $subject;?>"
                				onclick="parent.tinyTabs(this)"><?php print_string($subject,$book);?></p>
                            </li>
                			<div class="hidden" id="tinytab-xml-sen-<?php print $subject;?>">
<?php 
                                	$subjects=(array)list_student_subjects($sid,'%');
                                	unset($key);
                                	$listname='bid';
                                	$listlabel='subject';
                                	include('scripts/set_list_vars.php');
                                	list_select_list($subjects,$listoptions,$book);
                                	unset($listoptions);
?>
                                <button class="rowaction" name="ncmod" value="-1" onClick="processContent(this);"> 
                                  <?php print_string('addsubject',$book);?>
                                </button>
                			</div>
                       </ul>
                    </div>
                    <div id="tinytab-display-sen" class="tinytab-display"></div>
        </div>
     	<input type="hidden" name="selbid" value="<?php print $selbid;?>"/>
     	<input type="hidden" name="current" value="<?php print $action;?>"/>
     	<input type="hidden" name="choice" value="<?php print $current;?>"/>
     	<input type="hidden" name="cancel" value=""/>
	</form>
<?php
    	$senhistories=(array)list_student_senhistories($sid);
?>
<div id="records" class="left">
    <fieldset class="divgroup">
		<h5><?php print_string('records','admin');?></h5>
		 <div class="selery" style="float:left">
<?php
                foreach($senhistories as $no => $senhistory){

        			 if($senhid==$senhistory['id']){$displayclass=' class="hilite" ';}
        			 else{$displayclass=' class="lolite" ';}
?>
			   
				   <a <?php print $displayclass;?> href="<?php print $currentbook;?>.php?current=<?php print $action;?>&sid=<?php print $sid;?>&senhid=<?php print $senhistory['id'];?>"  onclick="parent.viewBook('<?php print $currentbook;?>');" style="text-decoration: none !important;">
				       <?php print '&nbsp;'.display_date($senhistory['startdate']); ?>
		         </a>
<?php
			 }
?>
		 </div>
<?php
		if(strtotime($senhistory['reviewdate']) <= mktime() and $senhistory['reviewdate']!=''){
			 /* If the last IEP's reviewdate has past then allow option to create to a new one. */
?>
		 <div style="float:left;">
			<button class="rowaction" name="sub" value="newrecord" onClick="processContent(this);">
				<?php print_string('newrecord',$book);?>
			</button>
		 </div>
<?php
    }
?>
	  </fieldset>
	  <fieldset class="divgroup">
<?php
        	require_once('lib/eportfolio_functions.php');
        	html_document_drop($Student['EPFUsername']['value'],'support',$senhid,$sid);
?>
	  </fieldset>
</div>

  </div>

