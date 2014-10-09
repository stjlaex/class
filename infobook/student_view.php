ƒ<?php
/**                                 student_view.php
 *
 *  A composite view of all informaiton for one sid
 */

$action = 'student_view_action.php';

/* Check user has permission to view students within this section. */
$perm = get_section_perm($student_secid);
include ('scripts/perm_action.php');
/**/

$house = get_student_house($sid);
$Siblings = array();
$field = fetchStudent_singlefield($sid, 'Course');
$Student = array_merge($Student, $field);

$extrabuttons = array();
if ($_SESSION['role'] == 'office' or $_SESSION['role'] == 'admin') {
    $extrabuttons['print'] = array('name' => 'current', 'pathtoscript' => $CFG -> sitepath . '/' . $CFG -> applicationdirectory . '/infobook/', 'value' => 'student_profile_print.php', 'xmlcontainerid' => 'profile', 'onclick' => 'checksidsAction(this)');
}
$editbutton['clicktoedit'] = array('name' => 'process', 'value' => 'edit', 'title' => 'edit');

twoplus_buttonmenu($sidskey, sizeof($sids), $extrabuttons);
?>
<div id="viewcontent" class="content">
    <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>">
        <div class="profile-details profile">
            <a class="clicktoedit-bg" href="infobook.php?current=student_view_student.php&cancel=student_view.php">
                <?php rowaction_buttonmenu($editbutton); ?>
            </a>
            <?php
                photo_img($Student['EPFUsername']['value'], $Student['EnrolNumber']['value'], 'w');
            ?>
            <h4><?php print $Student['DisplayFullName']['value']; ?></h4>
            <ul>
                <li>
                    <label><?php print_string($Student['Surname']['label'], $book); ?></label>
                    <?php print $Student['Surname']['value']; ?>
                </li>
                <li>
                    <label><?php print_string($Student['Forename']['label'], $book); ?></label>
                    <?php print $Student['Forename']['value']; ?>
                </li>
                <li>
                    <label><?php print_string($Student['DOB']['label'], $book); ?></label>
                    <?php print display_date($Student['DOB']['value']); ?>                    
                </li>
                <li>
                    <label><?php print_string('age', $book); ?></label>
                  <?php print get_age($Student['DOB']['value']); ?>
                </li>
                <li>
                    <label><?php print_string($Student['Gender']['label'], $book); ?></label>
                  <?php print_string(displayEnum($Student['Gender']['value'], $Student['Gender']['field_db']), $book); ?>
                </li>
                <li>
                    <label><?php print_string($Student['TutorGroup']['label'], $book); ?></label>
                  <?php print $Student['TutorGroup']['value']; ?>
                </li>
            </ul>
            <ul>                
                <li>
                    <label><?php print_string('formtutor'); ?></label>
                  <?php
                    $tutoremails = '';
                    foreach($Student['RegistrationTutor'] as $Tutor){
                        print $Tutor['value'] . ' ';
                        $tutoremails .= $Tutor['email'] . ';';
                    }
                    emaillink_display($tutoremails);
                  ?>
                </li>
                <li>
                    <label><?php print_string($Student['Course']['label'], $book); ?></label>
                  <?php print $Student['Course']['value']; ?>
                </li>
                <li>
                    <label><?php print_string($Student['Nationality']['label'], $book); ?></label> 
                  <?php print_string(displayEnum($Student['Nationality']['value'], $Student['Nationality']['field_db']), $book); ?>
                </li>
                <li>
                    <label><?php print_string($Student['Language']['label'], $book); ?></label>
                  <?php print_string(displayEnum($Student['Language']['value'], $Student['Language']['field_db']), $book); ?>
                </li>
                <li>
                    <label><?php print_string($Student['EnrolNumber']['label'], $book); ?></label> 
                  <?php
                    if ($_SESSION['role'] != 'support') {print $Student['EnrolNumber']['value'];
                    }
                ?>
                </li>
                <li>
                    <label><?php print_string($Student['EntryDate']['label'], $book); ?></label>
                  <?php print display_date($Student['EntryDate']['value']); ?>
                </li>
                <?php if($Student['MobilePhone']['value']!=''){ ?>
                <li>
                    <label><?php print_string($Student['MobilePhone']['label'], $book); ?></label>
                  <?php print $Student['MobilePhone']['value']; ?>
                </li>
                <?php
                    }
                ?>
                <?php if($house!=''){ ?>
                <li>
                  <label><?php print_string('house', $book); ?></label>
                  <?php print $house; ?>
                </li>
                <?php
                    }
                ?>
            </ul>
        </div>
<?php
            if($_SESSION['role']!='support'){
?>
        <div class="profile-history profile">
            <h4><?php print_string('studenthistory', $book); ?></h4>
        <ul>
        <li>
            <a href="infobook.php?current=student_attendance.php&cancel=student_view.php&sid=<?php print $sid; ?>">
                <?php print_string('attendance'); ?>
            </a> 
        </li>
        <li>
            <a href="infobook.php?current=student_reports.php&cancel=student_view.php">
                <?php print_string('subjectreports'); ?>
            </a>
        </li>
        <?php
            if($_SESSION['role']!='office'){
        ?>
        <li>
            <a href="infobook.php?current=student_scores.php&cancel=student_view.php&sid=<?php print $sid; ?>">
                <?php print_string('assessments'); ?>
            </a> 
        </li>
        <li>
            <a href="infobook.php?current=comments_list.php&cancel=student_view.php">
                <?php print_string('comments'); ?>
            </a>
            <?php
                $date = '';
                $Comments = (array)fetchComments($sid, $date, '');
                $Student['Comments'] = $Comments;
                if (array_key_exists('Comment', $Comments)) {
                    $Comment = $Comments['Comment'][0];
                    print '<label>' . display_date($Comment['EntryDate']['value']) . '</label>' . substr($Comment['Detail']['value'], 0, 120) . '...';
                } else {
                }
            ?>
        </li>
        <li>
            <a href="infobook.php?current=incidents_list.php&cancel=student_view.php">
                <?php print_string('incidents'); ?>
            </a>
        
        <?php
            $Incidents = (array)fetchIncidents($sid);
            $Student['Incidents'] = $Incidents;
            if(array_key_exists(0, $Incidents['Incident'])){
                $Incident = $Incidents['Incident'][0];
                print display_date($Incident['EntryDate']['value']) . substr($Incident['Detail']['value'], 0, 60) . '...';
        } 
      else{
        }
            ?>
        </li>
        <li>
            <a href="infobook.php?current=targets_list.php&cancel=student_view.php">
                <?php print_string('targets', $book); ?>
            </a>
        <?php
            $Targets = (array)fetchTargets($sid);
            $detail = '';
            $date = '';
            foreach ($Targets['Target'] as $Target) {
            if ($Target['Detail']['value_db'] != '' and strtotime($date) < strtotime($Target['EntryDate']['value'])) {
                $detail = substr($Target['Detail']['value_db'], 0, 120);
                $date = display_date($Target['EntryDate']['value']);
                }
            }
            if ($detail != '') {
                print '<label>' . $date . '</label>'. $detail . '...' ;
            } else {
            }
        ?>
        </li>
        <?php
            $Backgrounds=(array)fetchBackgrounds($sid);
            foreach($Backgrounds as $tagname => $Ents){
        ?>
        <li>
            <a href="infobook.php?current=ents_list.php&cancel=student_view.php&tagname=<?php print $tagname; ?>">
                <?php print_string(strtolower($tagname), $book); ?>
          </a>
        <?php
            /* Ensure private entries for Background are not displayed here! */
            if (array_key_exists(0, $Ents) and !($tagname == 'Background' and $Ents[0]['Categories']['Category'][0]['rating']['value'] < 0)) {
                $Ent = $Ents[0];
                print '<label>' . display_date($Ent['EntryDate']['value']) . '</label>' . substr($Ent['Detail']['value_db'], 0, 120) . '...';
            } else {
            }
            ?>
            <?php
                }
            }
        ?>
        </li>
    </div>


    
    
<div class="profile-info-col">    
        <div class="profile profile-info">
            <h6>
              <a href="infobook.php?current=student_view_sen.php&cancel=student_view.php">
                <?php print_string('sen', 'seneeds'); ?>
            </a>
          </h6>
            <a href="infobook.php?current=student_view_sen.php&cancel=student_view.php">
              <?php rowaction_buttonmenu($editbutton); ?>
            </a>
            <?php
                if ($Student['SENFlag']['value'] == 'Y') {print_string('senprofile', 'seneeds');
                } else {print_string('noinfo', $book);
                }
            ?>
        </div>
        
        <div class="profile profile-info ">
                <h6><a href="infobook.php?current=student_view_medical.php&cancel=student_view.php"><?php print_string('medical', $book); ?></a></h6>
                <a href="infobook.php?current=student_view_medical.php&cancel=student_view.php">
                    <?php rowaction_buttonmenu($editbutton); ?>
                </a>
                <?php
                    if ($Student['MedicalFlag']['value'] == 'Y') {print_string('infoavailable', $book);
                    } else {print_string('noinfo', $book);
                    }
                ?>
            </div>
      
<?php
                }
                if(isset($CFG->enrol_boarders) and $CFG->enrol_boarders=='yes'){
            ?>
            <div class="profile profile-info">
                <h6><a href="infobook.php?current=student_view_boarder.php&cancel=student_view.php"><?php print_string('boarder', $book); ?></a></h6>
                <a href="infobook.php?current=student_view_boarder.php&cancel=student_view.php">
                    <?php rowaction_buttonmenu($editbutton); ?>
                </a>
                <?php
                    if ($Student['Boarder']['value'] != 'N' and $Student['Boarder']['value'] != '') {
                    print '<div>' . get_string(displayEnum($Student['Boarder']['value'], $Student['Boarder']['field_db']), $book) . '</div>';
                    } else {print_string('noinfo', $book);
                    }
                ?>
            </div>
            
            
            
            
            <?php
                }
                $transport=display_student_transport($sid);
            ?>
            
            <div class="profile profile-info">
                <h6><a href="infobook.php?current=student_transport.php&cancel=student_view.php"><?php print_string('transport', 'admin'); ?></a></h6>
                <a href="infobook.php?current=student_transport.php&cancel=student_view.php">
                  <?php rowaction_buttonmenu($editbutton); ?>
                </a>
                <p><?php print $transport; ?></p>
            </div>
            
            
            

        <div class="profile profile-info">
                <h6><a href="infobook.php?current=student_transport.php&cancel=student_view.php"><?php print_string('club', 'admin'); ?></a></h6>
                <a href="infobook.php?current=student_transport.php&cancel=student_view.php">
                  <?php rowaction_buttonmenu($editbutton); ?>
                </a>
                <p><?php print get_student_club($sid); ?></p>
            </div>


        <div class="profile profile-info">
                <h6><a href="infobook.php?current=student_view_enrolment.php&cancel=student_view.php"><?php print_string('enrolment', 'admin'); ?></a></h6>
                <a href="infobook.php?current=student_view_enrolment.php&cancel=student_view.php">
                    <?php rowaction_buttonmenu($editbutton); ?>
                </a>
                <p>
                    <?php
                        print '<label>' . get_string('status', 'admin') . '</label> ' 
                        . get_string(displayEnum($Student['EnrolmentStatus']['value'], $Student['EnrolmentStatus']['field_db']), $book);
                    ?>
                </p>
            </div>

<?php
        if(empty($_SESSION['accessfees'])){
?>
<div class="profile profile-info">
      <fieldset >
        <h6><?php print_string('fees','admin');?></h6>
        <input type="password" name="accesstest" maxlength="20" value="" />
        <input type="password" name="accessfees" maxlength="4" value="" />
<?php
            $buttons=array();
            $buttons['access']=array('name'=>'access','value'=>'access');
            all_extrabuttons($buttons,$book,'');
?>
      </fieldset>
      </div>
<?php
            }
        else{
            require_once('lib/fetch_fees.php');
            $Account=(array)fetchAccount($gid);
?>
        <div class="profile profile-info">
        <fieldset>
          <h6><a href="infobook.php?current=student_fees.php&cancel=student_view.php"><?php print_string('fees','admin');?></a></h6>
            <a href="infobook.php?current=student_fees.php&cancel=student_view.php">
              <?php rowaction_buttonmenu($editbutton); ?>
            </a>
        </fieldset>
        </div>
<?php
            }
?>
</div>


<div class="profile-col">
<?php
    $Contacts=(array)$Student['Contacts'];
?>
        <div class="profile-contact profile">
            
            <div class="tinytabs" id="contact" style="">
                <ul>
                    <?php
                        $n=0;
                        foreach($Contacts as $contactno => $Contact){
                            if($Contact['id_db']!=' '){
                                $gid=$Contact['id_db'];
                                $relation=displayEnum($Contact['Relationship']['value'],'relationship');
                                $Dependents=(array)fetchDependents($gid);
                                $Dependents=(array)array_merge($Dependents['Dependents'],$Dependents['Others']);
                            if(sizeof($Dependents)>0){
                        foreach($Dependents as $depindex=>$Dependent){
                            $r=$Dependent['Relationship']['value'];
                        /* Check relationship to only list true siblings. */
                        if($Dependent['id_db']!=$sid and ($r=='PAF' or $r=='PAM' or $r=='STP')){$Siblings[$Dependent['id_db']]=$Dependent['Student'];}
                        }
                    }
                    ?>
                    <li id="<?php print 'tinytab-contact-'.$relation. $contactno;?>"><p 
                        <?php if($n==0){ print ' id="current-tinytab" ';}?>
                        class="<?php print $relation. $contactno;?>"
                        onclick="parent.tinyTabs(this)"><?php print_string($relation,$book);?></p></li>
                        <div class="hidden" id="tinytab-xml-contact-<?php print $relation. $contactno;?>">
                            <label><?php print_string($Contact['Order']['label'],$book);?>  <?php print_string(displayEnum($Contact['Order']['value'],'priority'),$book);?></label>
                            <div class="full-contact">
                                <div>
                                    <span title="<?php print $Contact['Note']['value'];?>">
                                    <a href="infobook.php?current=contact_details.php&cancel=student_view.php&contactno=<?php print $contactno;?>">
                                        <?php rowaction_buttonmenu($editbutton); ?>
                                        <?php print $Contact['DisplayFullName']['value'];?>
                                    </a>
                                    <?php emaillink_display($Contact['EmailAddress']['value']);?>
                                </div>
                                <div>
                                    <?php
                                        $Phones=$Contact['Phones'];
                                        while(list($phoneno,$Phone)=each($Phones)){
                                            print '<label>'.get_string(displayEnum($Phone['PhoneType']['value'],$Phone['PhoneType']['field_db']),$book).'</label> '.$Phone['PhoneNo']['value'];             
                                        }
                                    ?>
                                </div>
                            </div>                                        
                                        
                        </div>
                        <?php
                                $n++;
                                }
                            }
                            $relation='newcontact';
                        ?>
                        <li id="<?php print 'tinytab-contact-'.$relation;?>"><p 
                            <?php if($n==0){ print ' id="current-tinytab" ';}?>
                            class="<?php print $relation;?>" 
                            onclick="parent.tinyTabs(this)"><?php print_string($relation,$book);?>
                            </p>
                        </li>
                        <div class="hidden" id="tinytab-xml-contact-<?php print $relation;?>">
                            <div class="full-contact">
                                <div>
                                    <a href="infobook.php?current=contact_details.php&cancel=student_view.php&contactno=-1">
                                        <?php rowaction_buttonmenu($editbutton); ?>
                                        <?php print_string('addnewcontact',$book);?>
                                    </a>
                                </div>
                            </div>
                        </div>          
                    </ul>
                </div>
                <h5><?php print_string('contacts', $book); ?></h5>
            <div id="tinytab-display-contact" class="tinytab-display"></div>
        </div>
<?php
		if($CFG->emailoff!='yes'){
?>
		  <div class="profile">
			<h5><?php print_string('contactsemails', $book); ?></h5>
			<div class="left"><?php echo print_string(displayEnum($Contacts[0]['Relationship']['value'],'relationship'),$book).": ".$Contacts[0]['EmailAddress']['value'];?></div>
			<div class="right"><?php echo print_string(displayEnum($Contacts[1]['Relationship']['value'],'relationship'),$book).": ".$Contacts[1]['EmailAddress']['value'];?></div>
			<div style="float:right;">
				<?php emaillink_display($Contacts[0]['EmailAddress']['value'].";".$Contacts[1]['EmailAddress']['value']);?>
				<a href="infobook.php?current=message_list.php&cancel=student_view.php&sid=<?php print $sid;?>"><?php print_string('parentmessages',$book); ?></a>
			</div>
		  </div>
<?php
			}
?>




      <div class="profile profile-sibling">
        <h5><?php print_string('siblings',$book);?></h5>
            <?php
            foreach($Siblings as $Sibling){
                if($Sibling['EnrolmentStatus']['value']!='C'){$displayclass=' class="lowlite" ';}
            else{$displayclass='';}
            ?>
            <a href="infobook.php?current=student_view.php&cancel=contact_list.php&sid=<?php print $Sibling['id_db'];?>&sids[]=<?php print $Sibling['id_db'];?>">
                <?php print $Sibling['DisplayFullName']['value']; ?>
            </a>
            <?php print ' ('.$Sibling['TutorGroup']['value'].')';?>     
            <?php
                }
            ?>
    </div>
    </div>

      </div>

  <input type="hidden" name="current" value="<?php print $action;?>" />
  <input type="hidden" name="cancel" value="<?php print 'student_list.php';?>" />
  <input type="hidden" name="choice" value="<?php print $choice;?>" />
</form>
</div>
<?php
  if($CFG->tempinfosheet!=''){$profileprint=$CFG->tempinfosheet;}
  else{$profileprint="student_profile_print";}
?>
  <div id="xml-profile" style="display:none;">
    <params>
    <sids><?php print $sid;?></sids>
    <transform><?php print $profileprint;?></transform>
    <paper>portrait</paper>
    </params>
  </div>
