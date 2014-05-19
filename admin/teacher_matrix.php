<?php 
/**			  									teacher_matrix.php
 */

$choice='teacher_matrix.php';
$action='teacher_matrix_action.php';

list($crid,$bid,$error)=checkCurrentRespon($r,$respons);
if(sizeof($error)>0){include('scripts/results.php');exit;}

$curryear=get_curriculumyear();

$teachers=(array)list_teacher_users($crid,$bid);

$perm=getCoursePerm($crid,$respons);
if($perm['x']==1){
	$extrabuttons['editclasses']=array('name'=>'current','value'=>'new_class.php');
	}
else{
	$extrabuttons=array();
	}
three_buttonmenu($extrabuttons,$book);
?>
    <div class="content" id="viewcontent">
        <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >
            <div class="left">
                <fieldset class="divgroup">
                    <h5><?php print_string('teacher',$book);?></h5>
                    <label for="tid"><?php print_string('unassigned',$book);?></label>
                    <select name="tid" id="tid" eitheror="subtid"  class="requiredor" tabindex="<?php print $tab++;?>" size="1">
                        <?php
                        	$allteachers=(array)list_teacher_users();
                           	print '<option value="" selected="selected" ></option>';
                        	foreach($allteachers as $tid => $user){
                        		if(!array_key_exists($tid,$teachers)){
                        			print '<option  value="'.$tid.'">'.$tid.' ('.$user['surname'].')</option>';
                        			}
                           		}
                        ?>		
                    </select>
                
            </fieldset>
        </div>
        <div class="right">
            <fieldset class="divgroup">
                <label for="subtid"><?php print get_string('course',$book) .' '.get_string('teacher',$book);?></label>
                <select tabindex="<?php print $tab++;?>"  id="subtid" eitheror="tid"  class="requiredor" name="subtid" size="1">
                    <?php
                        print '<option value="" selected="selected" ></option>';
                        foreach($teachers as $tid => $user){
                            print '<option  value="'.$tid.'">'.$tid.' ('.$user['surname'].')</option>';
                            }
                    ?>      
                </select>
            </fieldset>        
        </div>
        <div class="center">        
            <fieldset class="divgroup">
                <h5><?php print_string('classes',$book);?></h5>
                <div class="left">
                    <label for="Unassigned"><?php print_string('unassigned',$book);?></label>
                    <select id="Unassigned" name="newcid[]" tabindex="<?php print $tab++;?>" size="8" multiple="multiple">
                        <?php
                        	$classes=list_course_classes($crid,$bid,'%',$curryear,'nottaught');
                        	foreach($classes as $class){
                           		print '<option ';
                        		print	' value="'.$class['id'].'">'.$class['name'].'</option>';
                        	   	}
            ?>		</select>
                </div>
                <div class="right">
                    <label for="Assigned"><?php print_string('assigned',$book);?></label>
                    <select id="Assigned" name="newcid[]" tabindex="<?php print $tab++;?>" size="8" multiple="multiple">
                        <?php
                        	$classes=list_course_classes($crid,$bid,'%',$curryear,'taught');
                        	foreach($classes as $class){
                        		print '<option ';
                        		print	' value="'.$class['id'].'">'.$class['name'].'</option>';
                        		}
                        ?>		
                    </select>
                </div>
            </fieldset>
        </div>
	    <input type="hidden" name="curryear" value="<?php print $curryear;?>" />
	    <input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>

  
	<div class="center">

	<form id="formtoprocess2" name="formtoprocess2" method="post" action="<?php print $host; ?>" >

	  <table class="listmenu">
		<tr>
		  <th style="width:1em;" class="checkall">
		  <input type="checkbox" name="checkall" value="yes" onChange="checkAll(this,'tids[]');" />
		  </th>
		  <th style="width:35%;">
			<?php print get_string('course',$book).' '.get_string('teachers',$book);?>
		  </th>
		  <th>
			<?php print_string('classesalreadyassigned',$book);?>
		  </th>
		</tr>
<?php
	foreach($teachers as $tid => $user){
?>
		<tr>
		  <td>
			<input type="checkbox" name="tids[]" value="<?php print $tid;?>" />
		  </td>
<?php
		print '<td>'.$tid.' ('.$user['surname'].')</td>';
		print '<td>';
		$classes=list_teacher_classes($tid,$crid,'%',$curryear);
		foreach($classes as $no => $class){
			if(fmod($no,8)==0){print '<br />';}
			print '<span title="'.$class['detail'].'"><a href="admin.php?current=class_edit.php&choice='.$choice.'&cancel='.$choice.'&newtid='.$tid.'&newcid='.$class['id'].'">'.$class['name'].'</a>&nbsp&nbsp</span>';
			}
		print '</td></tr>';
		}

?>
	<tfoot class="noprint">
	  <tr>
		<th>
		  <div class="rowaction">
<?php
	$buttons=array();
	$buttons['unassign']=array('name'=>'sub','value'=>'Unassign');
	all_extrabuttons($buttons,'admin','');
 ?>
		  </div>
		</th>
		<th colspan="2">
		</th>
	  </tr>
	</tfoot>

  </table>


	<input type="hidden" name="curryear" value="<?php print $curryear;?>" />
	  <input type="hidden" name="current" value="teacher_matrix_unassign.php" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>

	</div>
  </div>
