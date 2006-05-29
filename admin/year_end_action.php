<?php 
/** 			   					year_end_action.php
 */

$action='year_end_action2.php';

if($_POST{'answer'}=='no'){
	$current='';
 	$result[]='NO action taken.';
	include('scripts/results.php');
	include('scripts/redirect.php');
	exit;
	}

$years=array();
$yeargroups=array();
$d_yeargroup=mysql_query("SELECT id, ncyear,section,name FROM
							yeargroup ORDER BY section, ncyear");
while($year=mysql_fetch_array($d_yeargroup,MYSQL_ASSOC)){
    $yeargroups[$year['id']]=$year;
	$years[]=$year;
	}
	$yeargroups[1000]['name']='Alumni';

$ncyears=array();
while(list($yid,$year)=each($yeargroups)){
	$ncyear=$year['ncyear'];
    $ncyears[$ncyear][]=$yid;
	}
reset($yeargroups);

for($c=0;$c<sizeof($years);$c++){
	$yid=$years[$c]['id'];
	$ncyear=$years[$c]['ncyear'];
	$yeargroups[$yid]['nextyid']=array();
	if($years[$c+1]['ncyear']==$ncyear+1 
		and $years[$c+1]['section']==$years[$c]['section']){
				$yeargroups[$yid]['nextyid'][]=$years[$c+1]['id'];
				}
	elseif(sizeof($ncyears[$ncyear+1])==1){
   		$yeargroups[$yid]['nextyid'][]=$ncyears[$ncyear+1][0];
   		$yeargroups[$yid]['nextyid'][]=1000;
		}
	else{
		for($c2=0;$c2<sizeof($ncyears[$ncyear+1]);$c2++){
			$yeargroups[$yid]['nextyid'][]=$ncyears[$ncyear+1][$c2];
			}
   		$yeargroups[$yid]['nextyid'][]=1000;
		}
	}

three_buttonmenu();
?>
<div class="content">
<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	<fieldset> 
	<legend>End of Year Promotions</legend> 
	<p>Confirm the yeargroups to promote students to:</p>
<?php
     while(list($yid,$year)=each($yeargroups)){
	   if($year['nextyid']){
		$ncyear=$year['ncyear'];
?>
	<label for="<?php print $year['name'];?>"><?php print $year['name'];?></label>
	<select id="<?php print $year['name'];?>" name="<?php print $yid;?>">
<?php
    	while(list($index,$newyid)=each($year['nextyid'])){
			print "<option ";
			if(($yid==$newyid)){print "selected='selected'";}
			print	" value='".$newyid."'> ".$yeargroups[$newyid]['name']."</option>";
			}
?>
	</select>
<?php
		}
	 }
?>
	</fieldset> 
	<input type="hidden" name="" value="<?php  ?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
</form> 
</div>