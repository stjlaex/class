<?php
	foreach($users as $user){
		$User=(array)fetchUser($user['uid']);
		$uid=$user['uid'];
		$username=$user['username'];
		if((in_array($user['role'],$listroles) or sizeof($listroles)==0) and $user['username']!='administrator'){

			$d_ua=mysql_query("SELECT * FROM user_attendance WHERE username='".$username."' AND date='$today';");
			$attendancecomment=mysql_result($d_ua,0,'comment');
			$attendancestatus=mysql_result($d_ua,0,'status');

			if($attendancestatus=='a'){$userclass='staffabsent';}
			else{$userclass='';}
?>
		<div class="stafflist_user" >
				<div onclick="if(document.getElementById('img-<?php echo $uid;?>')){this.style.border=0;removeHiddenInput($(this).closest('form').attr('id'),'img-<?php echo $uid;?>');}else{this.style.border='1px solid #0000FF';appendHiddenInput($(this).closest('form').attr('id'),'uids[]','img-<?php echo $uid;?>','<?php echo $uid;?>');}">
					<div id="profilepicture-<?php print $uid;?>" class="<?php echo $userclass;?>" onmouseover="loadMidi(this);">
						<?php photo_img($User['EPFUsername']['value'],$uid,'','staff'); ?>
					</div>
				</div>
				<div>
					<a href="admin.php?current=staff_details.php&cancel=<?php echo $choice;?>&choice=<?php echo $choice;?>&seluid=<?php print $uid;?>">
						<?php print $User['Forename']['value'].' '.$User['Surname']['value']; ?>
					</a>
				</div>
		</div>
<?php
			}
		}
?>
	<script>
		function loadMidi(object){
			var src=$(object).find('img').attr('src');
			$(object).find('img').attr('src',src+"&size=midi");
			}
	</script>
	<div">
		<button onClick="processContent(this);" name="current" value="staff_attendance_action.php" style="margin-top:30px"><?php print_string("absent",$book);?></button>
	</div>
