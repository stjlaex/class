<?php 
/** 									new_mark.php
 *
 *	Creates a new row in the table:mark for one or more classes using table:midcid
 */

$action='new_mark_action1.php';

if($umnfilter!='%'){$umnfilter='cw';$_SESSION['umnfilter']=$umnfilter;}

	$markdefs=array();
	foreach($cids as $cid){
		$class=(array)get_this_class($cid);
		$bid=$class['bid'];
		$crid=$class['crid'];
   		$d_markdef=mysql_query("SELECT name, comment FROM markdef WHERE 
					(subject_id LIKE '$bid' OR subject_id='%') AND
					(course_id LIKE '$crid' OR course_id='%') ORDER BY subject_id");
		while($new=mysql_fetch_array($d_markdef,MYSQL_ASSOC)){
			if(!in_array($new,$markdefs)){
				$markdefs[]=$new;
				}
			}
		}

three_buttonmenu();
?>
  <div id="heading">
			  <label><?php print_string('newmark',$book); ?></label>
			<?php print_string('classwork',$book);?>
  </div>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <div class="center">
		<table class="listmenu">
		  <caption><?php print_string('chooseamarkdefinition',$book);?></caption>
<?php
	   	foreach($markdefs as $markdef){
?>
		  <tr>
			<td>
			  <input type="radio" name="def_name" id="def_name" 
				tabindex="<?php print $tab++;?>"
				value="<?php print $markdef['name'];?>" />
			</td>
			<td>
			  <?php print $markdef['name'];?>
			</td>
			<td>
			  <label><?php print $markdef['comment'];?></label>
			</td>
		  </tr>
<?php
	 }
?>
		</table>
		<br />
<?php
if($_SESSION['role']=='admin'){
?>
		<table class="listmenu">
		  <tr class="special">
			<td>
			  <input type="radio" name="def_name" id="def_name"
				 tabindex="<?php print $tab++;?>" value="custom" />
			</td>
			<td>
			  <?php print_string('newdefinition',$book);?>
			</td>
			<td>
			  <label><?php print_string('newuserdefinedmarktype',$book);?></label>
			</td>
		  </tr>
		</table>
<?php
		}
?>
	  </div>
 	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print $choice;?>" />
	</form>
  </div>
