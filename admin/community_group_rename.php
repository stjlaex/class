<?php 
/**				   	   				   community_group_rename.php
 */

$action='community_group_rename.php';
$action_post_vars=array('newcomtype','newcomid','comid');

if(isset($_POST['comid'])){$comid=$_POST['comid'];}
if(isset($_POST['newcomid'])){$newcomid=$_POST['newcomid'];}else{$newcomid='';}
if(isset($_POST['newcomtype'])){$newcomtype=$_POST['newcomtype'];}
if(isset($_POST['newname'])){$newname=$_POST['newname'];}


include('scripts/sub_action.php');

if($sub=='Submit'){

	if(isset($comid) and $comid!=''){
		/* Existing group is being edited. */
		$community=(array)get_community($comid);
		$communityfresh=(array)$community;
		$communityfresh['name']=$newname;
		$oldname=$community['name'];

		/* Changing the name of a form has implications for any
		 * classes which are named after it.
		 */
		if($community['type']=='form'){
			$oldclasses=(array)list_forms_classes($oldname);
			foreach($oldclasses as $oldclass){
				$oldcid=$oldclass['id'];
				$d_c=mysql_query("SELECT subject_id FROM class WHERE id='$oldcid';");
				if(mysql_num_rows($d_c)>0){
					$bid=mysql_result($d_c,0);
					$newname=$bid.''.$newname;
					mysql_query("UPDATE class SET name='$newname' WHERE id='$oldcid';");
					}
				}
			}
		}
	else{
		/* New group is being created. */
		$community=array('id'=>'','type'=>$newcomtype,'name'=>$newname);
		if($newcomtype=='form'){$community['yeargroup_id']=$_POST['newyid'];}
		$comid=update_community($community);
		$community=(array)get_community($comid);
		$communityfresh=(array)$community;

		/* TODO: create subject classes in a similar manner to the renaming above. */
		}


	/* Only two fields, charge and sessions, can be edited apart from the name. */
	if(isset($_POST['charge'])){$communityfresh['charge']=$_POST['charge'];}
	if(isset($_POST['sessions'])){
		$sessions=$_POST['sessions'];
		unset($communityfresh['sessions']);
		foreach($sessions as $sess){
			if(isset($communityfresh['sessions'])){$sep=':';}
			else{$sep='';$communityfresh['sessions']='';}
			$communityfresh['sessions'].=$sep . 'A'.$sess;
			}
		}
	else{
		$communityfresh['sessions']='';
		}

	$comid=update_community($community,$communityfresh);

	if($community['type']=='academic'){
		if(isset($_POST['cohids'])){$cohids=(array)$_POST['cohids'];}else{$cohids=array();}
		mysql_query("DELETE FROM cohidcomid WHERE community_id='$comid';");
		foreach($cohids as $cohid){
			mysql_query("INSERT INTO cohidcomid SET cohort_id='$cohid', community_id='$comid';");
			}
		}


	$action=$cancel;
	include('scripts/redirect.php');
	exit;

	}
else{


	if(isset($comid)){$com=get_community($comid);}
	three_buttonmenu();
?>

    <div class="content">
        <form name="formtoprocess" id="formtoprocess" method="post"
            <div class="center">
                <fieldset class="divgroup">
        		  <h5><?php print_string('changegroupname',$book);?></h5>
        		  
                    <label for="Newname"><?php print_string('newgroupname',$book);?></label>
                    <input type="text" id="Newname" name="newname" tabindex="<?php print $tab++;?>" maxlength="30" class="required" value="<?php if(isset($com['name'])){print $com['name'];} ?>">
                </fieldset>
            </div>
            <?php
            	if($newcomtype=='form' and !isset($comid)){
            		$yeargroups=list_yeargroups();
            ?>
            <div class="center">
                <fieldset class="divgroup">
            	<h5><?php print_string('yeargroups',$book);?></h5>
                <?php $required='yes'; include('scripts/list_year.php');?>
            <?php
		      }
	           elseif($newcomtype=='ACADEMIC'){
            ?>
            <fieldset class="center">
            <?php
        		$cohids=array();
        		$cohorts=array();
        		if(isset($comid) and $comid!=''){
        			$com_cohorts=(array)list_community_cohorts($com,true);
        			foreach($com_cohorts as $com_cohort){
        				$cohids[]=$com_cohort['id'];
        				}
        			}
        		$courses=(array)list_courses();
        		foreach($courses as $course){
        			$course_cohorts=(array)list_course_cohorts($course['id']);
        			foreach($course_cohorts as $index => $course_cohort){
        				$course_cohort['name']=$course['name'].' ('.$course['id'].') - '.$course_cohort['stage'];
        				$cohorts[]=$course_cohort;
        				}
        			}
        		$multi=6;
        		$required='yes';
        		$listname='cohid';
        		$listlabel='cohort';
        		include('scripts/set_list_vars.php');
        		list_select_list($cohorts,$listoptions,$book);
        
        		}
        	   elseif($newcomtype=='TUTOR'){
                    $days=getEnumArray('dayofweek');
            ?>
            <fieldset class="center">
                <h5 for="Charge"><?php print_string('fee',$book);?></h5>
                <input  name="charge" value="<?php print $com['charge'];?>" >
        <div class="center">
            <fieldset class="divgroup">
                <h5 for="days"><?php print_string('sessions',$book);?></h5>
                <ul class="chk-list">
                <?php
                	foreach($days as $day => $dayname){
                		$pos=strpos($com['sessions'],"A$day");
                ?>
                <li>
					  <?php print_string($dayname);?>
					  <input type="checkbox" name="sessions[]" value="<?php print $day;?>" 
							 <?php if($pos!==false){print 'checked="checked"';}?>/>
                </li>
                <?php
                	}
                ?>
                </ul>
            </fieldset>
        </div>
        <?php
        	}
        ?>
        <?php
        	if(isset($comid)){
        ?>
	   <input type="hidden" name="comid" value="<?php print $comid;?>" />
<?php
		}
?>
	<input type="hidden" name="newcomtype" value="<?php print $newcomtype;?>" />
	<input type="hidden" name="newcomid" value="<?php print $newcomid;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
	</form>
  </div>

<?php
	}
?>
