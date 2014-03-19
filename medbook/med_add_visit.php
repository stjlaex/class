<?php
/**                                  med_add_visit.php
 */

$action='med_add_visit_action.php';

$action_post_vars=array('tagname');

if(isset($_POST['tagname'])){$tagname=$_POST['tagname'];}

include('scripts/sub_action.php');

$Entry=array();
if($sub=='edit'){
	$action_post_vars=array('tagname','entid');
	$action='med_add_visit_action.php';
	if(isset($_POST['recordid'])){
		$entid=$_POST['recordid'];
		$Entry=fetchMedicalLog($entid);
		$choice3='med_student_list.php';
		}
	else{$entid=-1;}
	}
else{$entid=-1;$choice='med_search_student.php';}
?>
    <div id="heading">
        <h4>
            <label><?php print_string('medicalrecord',$book);?></label>
            <a href="infobook.php?current=student_view.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>" target="viewinfobook" onclick="parent.viewBook('infobook');">
                <?php print $Student['Forename']['value'].' '.$Student['Surname']['value'];?>
            </a>
	   </h4>
    </div>
    <?php
    	three_buttonmenu();
    ?>
<div class="content">
  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	<?php include("view_info_student.php");?>
	<div class='center'>
		<fieldset>
			<h5><?php print_string('newvisit',$book);?></h5>
			<table>
				<tr>
					<td style="width:300px">
						<label for="Date0" style="width: 55px; display: inline-block;"><?php print_string('date');?></label>
						<input id="Date0" class="required" type="date" value="<?php if($Entry[0]['Date']['value']!=''){echo $Entry[0]['Date']['value'];}?>" name="date0" tabindex="1" onchange="validateRequired(this)"><?php if($Entry[0]['Date']['value']==''){echo '<script>displayCurrentDate(\'Date0\');</script>';}?>
						<img class="calendar">
					<td>
					<td rowspan='3'>
						<label for="Detail"><?php print_string('details');?></label>
						<textarea id="Detail" class="required" tabindex="4" wrap="on" rows="5" tabindex="<?php print $tab++;?>" onchange="validateRequired(this)" name="detail"><?php if($entid!=-1){echo $Entry[0]['Details']['value'];}?></textarea>
					</td>
				</tr>
				<tr>
					<td style="width:300px">
						<label style="width: 70px; display: inline-block;"><?php print_string('time');?></label>
						<input type="time" id='time' name="time" tabindex="2" <?php if($Entry[0]['Time']['value']!='00:00:00' and $Entry[0]['Time']['value']!=''){echo "value=\"".$Entry[0]['Time']['value']."\">";}else{echo "><script>displayCurrentTime('time');</script>";}?>
					<td>
				</tr>
				<tr>
					<td style="width:300px;">
						<label style="width: 70px; display: inline-block;"><?php print_string('category');?></label>
						<input type="text" name="category" tabindex="3" value="<?php echo $Entry[0]['Category']['value'];?>">
					<td>
				</tr>
			</table>
		</fieldset>
	</div>
<?php
$logs=fetchMedicalLog('-1',$sid,'1');
$lastlog=$logs[0];
?>
	<fieldset class="divgroup">
			<h5><?php print_string('lastvisit',$book);?></h5>
				<table>
					<tr>
						<td>
							<?php print_string('date');?>: <?php echo display_date($lastlog['Date']['value']);?>
						</td>
						<td>
							<?php if($lastlog['Time']['value']!='00:00:00'){ echo print_string('time').': '.$lastlog['Time']['value'];}?>
						</td>
						<td>
							<?php if($lastlog['Category']['value']!=''){ echo print_string('category').': '.$lastlog['Category']['value'];}?>
						</td>
						<td colspan="3">
							<?php print_string('details');?>: <?php echo $lastlog['Details']['value'];?>
						</td>
					</tr>
				</table>
		</fieldset>

<?php if($entid!='-1'){ ?>

	<input type="hidden" name="visitid" value="<?php print $entid;?>">
	<input type="hidden" name="visitaction" value="update">
	
<?php } ?>
	
	<input type="hidden" name="current" value="<?php print $action;?>"/>
	<input type="hidden" name="choice" value="<?php print $current;?>"/>
	<input type="hidden" name="cancel" value=""/>
  </form>
  </div>
