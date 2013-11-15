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
		}
	else{$entid=-1;}
	}
else{$entid=-1;}
$date=date('Y-m-d');
$time=date('H:i:s');
?>
  <div id="heading"><label><?php print_string('medicalrecord',$book);?></label>
	<a href="infobook.php?current=student_view.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>"
			  target="viewinfobook" onclick="parent.viewBook('infobook');">
  <?php print $Student['Forename']['value'].' '.$Student['Surname']['value'];?>
	</a>
  </div>
<?php
	three_buttonmenu();
?>
<div class="content">
  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	<?php include("view_info_student.php");?>

	<div class='center'>
		<fieldset>
			<legend>New visit</legend>
			<table>
				<tr>
					<td style="width:300px">
						<label for="Date0">Date</label>
						<input id="Date0" class="required" type="date" value="<?php if($Entry[0]['Date']['value']!=''){echo $Entry[0]['Date']['value'];}else{echo $date;}?>" name="date0" tabindex="1" 
										onchange="validateRequired(this)">
						<img class="calendar">
					<td>
					<td rowspan='3'>
						<label for="Detail">Details</label>
						<textarea id="Detail" style="font-wight:600; font-size:large;" class="required" tabindex="4"
							wrap="on" rows="5" tabindex="<?php print $tab++;?>" onchange="validateRequired(this)"
							name="detail"><?php if($entid!=-1){echo $Entry[0]['Details']['value'];}?></textarea>
					</td>
				</tr>
				<script>
					function getTimeNow(){
						var d = new Date();
						var x = document.getElementById("time");
						x.value=d.getHours()+':'+d.getMinutes();
						}
				</script>
				<tr>
					<td style="width:300px">
						<label>Time</label>
						<input type="time" id='time' name="time" tabindex="2" <?php if($Entry[0]['Time']['value']!='00:00:00' and $Entry[0]['Time']['value']!=''){echo "value=\"".$Entry[0]['Time']['value']."\">";}else{echo "><script>getTimeNow();</script>";}?>
					<td>
				</tr>
				<tr>
					<td style="width:300px;">
						<label>Category</label>
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
	<fieldset class="center">
			<legend><?php print_string('lastvisit','infobook');?></legend>
				<table class="listmenu">
					<tr>
						<td>
							Date: <?php echo display_date($lastlog['Date']['value']);?>
						</td>
						<td>
							<?php if($lastlog['Time']['value']!='00:00:00'){ echo 'Time: '.$lastlog['Time']['value'];}?>
						</td>
						<td>
							<?php if($lastlog['Category']['value']!=''){ echo 'Category: '.$lastlog['Category']['value'];}?>
						</td>
						<td colspan="3">
							Details: <?php echo $lastlog['Details']['value'];?>
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
