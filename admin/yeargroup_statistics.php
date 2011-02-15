<?php
/**								  		yeargroup_statistics.php
 *
 */

$choice='yeargroup_matrix.php';
$action='yeargroup_matrix.php';

two_buttonmenu();
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post"
										action="<?php print $host; ?>" >
	    <input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>

	<div class="center"  id="viewcontent">
	  <div>
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('yeargroup');?></th>
		  <th><?php print_string('numberofstudents',$book);?></th>
		  <th><?php print_string('capacity',$book);?></th>
		  <th><?php print get_string('male',$book) .'</th><th>'. 
			get_string('female',$book);?>
		  </th>
		</tr>
<?php
	$totalsids=0;
	$totalmalesids=0;
	$totalfemalesids=0;
	$capacitytotal=0;
	$countrys=array();
	$gender_countrys=array();
	$birth_years=array();
	$gender_birth_years=array();
	$postcodes=array();

	$yeargroups=list_yeargroups();
	foreach($yeargroups as $year){
		$yid=$year['id'];
		$d_groups=mysql_query("SELECT gid FROM groups WHERE
				yeargroup_id='$yid' AND course_id=''");
		$gid=mysql_result($d_groups,0);
		$perms=getYearPerm($yid, $respons);
		$comid=update_community(array('type'=>'year','name'=>$yid));
		$com=get_community($comid);
		$students=listin_community($com);
		$nosids=countin_community($com);
		$nomalesids=countin_community_gender($com,'M');
		$nofemalesids=countin_community_gender($com,'F');
		$totalsids=$totalsids+$nosids;
		while(list($index,$student)=each($students)){
			$Student=(array)fetchStudent_singlefield($student['id'],'Nationality');
			$countrycode=strtoupper($Student['Nationality']['value']);
			$Student=(array)fetchStudent_singlefield($student['id'],'SecondNationality');
			$secondcountrycode=strtoupper($Student['SecondNationality']['value']);
			if($countrycode=='' or $countrycode==' '){
				$countrycode='BLANK';
				}
			if($countrycode=='ES'){$countrytype='home';}
			else{$countrytype='foreign';}

			$gender=strtoupper($student['gender']);
			if($gender=='' or $gender==' '){
				$gender='BLANK';
				}

			/* First nationality */
			if(!isset($countrys[$countrycode])){
				$countrys[$countrycode]=0;
				$gender_countrys[$countrycode]=array('BLANK'=>0,'M'=>0,'F'=>0);
				$second_countrys[$countrycode]=0;
				}
			$gender_countrys[$countrycode][$gender]++;
			$countrys[$countrycode]++;
			if($secondcountrycode!='' and $secondcountrycode!=' '){trigger_error('2ND: '.$secondcountrycode,E_USER_WARNING);$second_countrys[$countrycode]++;}

			/* Do postcodes. Use localcode to restrict to those within the local area. */
			$Student=fetchStudent_singlefield($student['id'],'Postcode');
			$poststring=$Student['Postcode']['value'];
			if(isset($CFG->localpostcode)){$localcode=$CFG->localpostcode;}
			else{$localcode='2';}
			$student_pcodes=explode(' : ',$poststring);
			foreach($student_pcodes as $pcode){
				$pos=stripos($pcode,$localcode);
				if($pos === false){
					}
				else{
					$postcode=substr($pcode,$pos,5);
					if(!isset($postcodes[$postcode])){
						$postcodes[$postcode]=0;
						$app_postcodes[$postcode]=array('EN'=>0,'AP'=>0,'AT'=>0,'RE'=>0,'CA'=>0,'WL'=>0,'ACP'=>0,'AC'=>0);
						}
					$postcodes[$postcode]++;
					}
				}

			$dobs=explode('-',$student['dob']);
			$yob=$dobs[0];
			if(!isset($birth_years[$countrytype][$yob])){
				$birth_years[$countrytype][$yob]=0;
				$gender_birth_years[$countrytype][$yob]=array('BLANK'=>0,'M'=>0,'F'=>0);
				}
			$birth_years[$countrytype][$yob]++;
			$gender_birth_years[$countrytype][$yob][$gender]++;

			/**
			 * Limited sub-group based on year of birth.
			 *
			 */
			if($yob>2002 and $yob<2006){
				if(!isset($lcountrys[$countrycode])){
					$lcountrys[$countrycode]=0;
					$lgender_countrys[$countrycode]=array('BLANK'=>0,'M'=>0,'F'=>0);
					}
				$lgender_countrys[$countrycode][$gender]++;
				$lcountrys[$countrycode]++;
				}
			}
?>
		<tr>
		  <td>
<?php
	   		print $year['name'];
?>
		  </td>
		  <td><?php print $nosids;?></td>
		  <td>
<?php
		$capacity=$com['capacity'];
		$capacitytotal+=$capacity;
		print $capacity;
?>
		  </td>
		  
<?php 
	    print '<td>'.$nomalesids.'</td><td>'.$nofemalesids.'</td>';
		$totalmalesids+=$nomalesids;
		$totalfemalesids+=$nofemalesids;
?>
		  </td>
		</tr>
<?php
		}
?>
		<tr>
		  <th>
			  <?php print get_string('total',$book);?>
		  </th>
		  <td><?php print $totalsids;?></td>
		  <td><?php print $capacitytotal;?></td>
		  <td><?php print $totalmalesids.'</td><td>'.$totalfemalesids;?></td>
		</tr>
	  </table>
	</div>

<br />

	<div>
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('nationality','infobook');?></th>
		  <th><?php print_string('numberofstudents',$book);?></th><th>(Second Nationality)</th>
		  <th><?php print get_string('male',$book) .'</th><th>'.get_string('female',$book);?></th>
		  <th>2002 - 2006 <?php print get_string('male',$book) .'</th><th>2002 - 2006 '.get_string('female',$book);?></th>
		</tr>
<?php
		asort($countrys,SORT_NUMERIC);
		$countrys=array_reverse($countrys,true);
		while(list($countrycode,$nosids)=each($countrys)){
?>
		<tr>
		  <td>
			<?php print_string(displayEnum($countrycode,'nationality'),$book);?>
			<?php print ' '.$countrycode;?>
		  </td>
		  <td><?php print $nosids;?></td>  
			<td><?php if($second_countrys[$countrycode]>0){print '('.$second_countrys[$countrycode].')';}?></td>  
		  <td><?php print $gender_countrys[$countrycode]['M']. 
		  '</td><td>'. $gender_countrys[$countrycode]['F'];?></td>
		  <td><?php print $lgender_countrys[$countrycode]['M']. 
		  '</td><td>'. $lgender_countrys[$countrycode]['F'];?></td>
		</tr>
<?php
			}
?>
	  </table>
	</div>

<br />

		<div>
			<table class="listmenu">
				<tr>
					<th><?php print 'Postcodes'; ?></th>
					<th><?php print_string('currentroll',$book);?></th>
					<th><?php print_string('enquired',$book);?></th>
					<th><?php print_string('applied',$book);?></th>
					<th><?php print_string('cancelled',$book);?></th>
					<th><?php print_string('waitinglist',$book);?></th>
				</tr>
<?php

	$application_steps=array('EN','AP','AT','RE','CA','WL','ACP','AC');
	$currentyear=get_curriculumyear();
	$enrolyear=$currentyear+1;
	foreach($application_steps as $enrolstatus){
	  if($enrolstatus=='EN'){$comtype='enquired';}
	  elseif($enrolstatus=='AC'){$comtype='accepted';}
	  else{$comtype='applied';}
      foreach($yeargroups as $year){
		$comid=update_community(array('id'=>'','type'=>$comtype,'name'=>$enrolstatus.':'.$year['id'],'year'=>$enrolyear));
		$com=get_community($comid);
		$students=listin_community($com);
		$nosids=countin_community($com);
		foreach($students as $student){
			/* Do postcodes. Use localcode to restrict to those within the local area. */
			$Student=fetchStudent_singlefield($student['id'],'Postcode');
			$poststring=$Student['Postcode']['value'];
			if(isset($CFG->localpostcode)){$localcode=$CFG->localpostcode;}
			else{$localcode='2';}
			$student_pcodes=explode(' : ',$poststring);
			foreach($student_pcodes as $pcode){
				$pos=stripos($pcode,$localcode);
				if($pos === false){
					}
				else{
					$postcode=substr($pcode,$pos,5);
					if(!isset($postcodes[$postcode])){
						$postcodes[$postcode]=0;
						$app_postcodes[$postcode]=array('EN'=>0,'AP'=>0,'AT'=>0,'RE'=>0,'CA'=>0,'WL'=>0,'ACP'=>0,'AC'=>0);
						}
					$app_postcodes[$postcode][$enrolstatus]++;
					}
				}
			}
	   	  }
		}
			asort($postcodes,SORT_NUMERIC);
			$postcodes=array_reverse($postcodes,true);
			while(list($postcode,$nosids)=each($postcodes)) {
?>
					<tr>
						<td><?php print ' '.$postcode; ?></td>
						<td><?php print $nosids; ?></td>
						<td><?php print $app_postcodes[$postcode]['EN']; ?></td>
						<td><?php print $app_postcodes[$postcode]['AP']+$app_postcodes[$postcode]['AC']+$app_postcodes[$postcode]['ACP']+$app_postcodes[$postcode]['AT']+$app_postcodes[$postcode]['RE']; ?></td>
						<td><?php print $app_postcodes[$postcode]['CA']; ?></td>
						<td><?php print $app_postcodes[$postcode]['WL']; ?></td>
					</tr>
				<?php
				}
				?>
			</table>
		</div>

<br />

	<div>
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('year','infobook');?></th>
		  <th><?php print_string('numberofstudents',$book);?></th>
		  <th><?php print get_string('male',$book) .'</th><th>'. 
			get_string('female',$book);?>
		  </th>
		</tr>
<?php
		while(list($countrytype,$years)=each($birth_years)){
			krsort($years,SORT_NUMERIC);
?>
		<tr>
		  <th colspan="4"><?php print_string($countrytype,'infobook');?></th>
		</tr>
<?
			while(list($yob,$nosids)=each($years)){
?>
		<tr>
		  <td>
			<?php print ' '.$yob;?>
		  </td>
		  <td><?php print $nosids;?></td>  
		  <td><?php print $gender_birth_years[$countrytype][$yob]['M']. 
		  '</td><td>'. $gender_birth_years[$countrytype][$yob]['F'];?></td>
		</tr>
<?php
				}
			}
?>
	  </table>
	</div>
	</div>
  </div>