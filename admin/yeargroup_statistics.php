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
		  <th><?php print get_string('male',$book) .'/'. 
			get_string('female',$book) .' '. get_string('ratio',$book);?>
		  </th>
		</tr>
<?php
	$totalsids=0;
	$totalmalesids=0;
	$totalfemalesids=0;
	$capacitytotal=0;
	$countrys=array();
	$d_year=mysql_query("SELECT * FROM yeargroup ORDER BY section_id, id");
	while($year=mysql_fetch_array($d_year,MYSQL_ASSOC)){
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
			$Student=fetchStudent_singlefield($student['id'],'Nationality');
			$countrycode=strtoupper($Student['Nationality']['value']);
			if($countrycode=='' or $countrycode==' '){
				$countrycode='BLANK';
				}
			if(!isset($countrys[$countrycode])){$countrys[$countrycode]=1;}
			else{$countrys[$countrycode]++;}
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
		  <td>
<?php 
	    print $nomalesids.' / '.$nofemalesids;
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
		  <td><?php print $totalmalesids.' / '.$totalfemalesids;?></td>
		</tr>
	  </table>
	</div>

<br />

	<div>
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('nationality','infobook');?></th>
		  <th><?php print_string('numberofstudents',$book);?></th>
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
		</tr>
<?php
			}
?>
	  </table>
	</div>
	</div>
  </div>