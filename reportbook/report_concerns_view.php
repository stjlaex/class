<?php
/**			   					report_conerns_view.php
 *
 *	A composite view of the conerns reported	
 */

$current='report_concerns_view.php';
$action='report_concerns_list.php';
$choice='report_concerns.php';
$host='reportbook.php';

if(isset($_GET{'sid'})){$sid=$_GET{'sid'};}else{$sid='';}
if(isset($_GET{'date'})){$date=$_GET{'date'};}else{$date='';}
if(isset($_GET{'pubdate'})){$pubdate=$_GET{'pubdate'};}else{$pubdate=$today;}
if(isset($_GET{'bid'})){$bid=$_GET{'bid'};}else{$bid='';}
if(isset($_GET{'yid'})){$yid=$_GET{'yid'};}else{$yid='';}
if(isset($_GET{'fid'})){$fid=$_GET{'fid'};}else{$fid='';}


$Student=fetchshortStudent($sid);

two_buttonmenu();
?>
  <div id="heading">
	<label><?php print_string('comments');?></label>
<?php
	print $Student['Forename']['value'].' ';
	print $Student['MiddleNames']['value'].' '. $Student['Surname']['value'];
	print ' ('.$Student['RegistrationGroup']['value'].')';
?>
  </div>

  <div id="viewcontent" class="content">
	<form onChange="validateForm();" id="formtoprocess"
	  name="formtoprocess" method="post" action="<?php print $host;?>"> 

 	<input type="hidden" name="bid" value="<?php print $bid;?>">
 	<input type="hidden" name="newyid" value="<?php print $yid;?>">
 	<input type="hidden" name="fid" value="<?php print $fid;?>">
 	<input type="hidden" name="date0" value="<?php print $date;?>">
 	<input type="hidden" name="date1" value="<?php print $pubdate;?>">
 	<input type="hidden" name="current" value="<?php print $action;?>">
 	<input type="hidden" name="choice" value="<?php print $choice;?>" />

	</form>

	  <table class="listmenu">
<?php
	$thisncyear=$Student['NCyearActual']['value'];
	$Comments=fetchConcerns($sid,$date,$thisncyear);
	$Student['Concerns']=$Comments;
?>
	  <tr>
		<th>
		  <?php print_string($Comments[0]['EntryDate']['label']);?>
		</th>
		<th>
		  <?php print_string($Comments[0]['Subject']['label']);?>
		</th>
		<th>
		  <?php print_string($Comments[0]['Categories']['label']);?>
		</th>
		<th>
		  <?php print_string($Comments[0]['Detail']['label']);?>
		</th>
	  </tr>
<?php
		while(list($index,$Comment)=each($Comments)){
			if($Comment['Categories']['Category'][0]['rating']==-1){
				$class='negative';
				}
			else{
				$class='positive';
				}
			if(strtotime($Comment['EntryDate']['value'])>=strtotime($date)){
				print '<tr>';
				print '<td>'.$Comment['EntryDate']['value'].'</td>';
				print '<td>'.$Comment['Subject']['value'].'</td>';
				print '<td class="'.$class.'">';
				for($c3=0; $c3<sizeof($Comment['Categories']['Category']); $c3++){
					print $Comment['Categories']['Category'][$c3]['value'].' ';
					print '('.$class.')<br />';
					}
				print '</td>';
				print '<td>'.$Comment['Detail']['value'].'</td>';
				print	'</tr>';
				}
			}
?>
	  </table>
  </div>

