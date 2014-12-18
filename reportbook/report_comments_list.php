<?php
/**									report_comments_list.php
 *
 *	Finds and lists students identified as having concerns.
 */

$action='report_comments.php';

$startdate=$_POST['date0'];
$enddate=$_POST['date1'];
if(isset($_POST['bid']) and $_POST['bid']!=''){$bid=$_POST['bid'];}else{$bid='%';}
if(isset($_POST['catid']) and $_POST['catid']!=''){$catid=$_POST['catid'];}else{$catid='%';}
if(isset($_POST['ratvalue']) and $_POST['ratvalue']!=''){$ratvalue=$_POST['ratvalue'];}else{$ratvalue='%';}
if(isset($_POST['stage'])){$stage=$_POST['stage'];}
if(isset($_POST['year'])){$year=$_POST['year'];}
if(isset($_POST['yid'])){$yid=$_POST['yid'];}else{$yid='';}
if(isset($_POST['formid']) and $_POST['formid']!=''){$comid=$_POST['formid'];}
if(isset($_POST['secid']) and $_POST['secid']!=''){$secid=$_POST['secid'];}
elseif(isset($_POST['houseid'])  and $_POST['houseid']!=''){$comid=$_POST['houseid'];}else{$comid='';}

include('scripts/sub_action.php');


list($ratingnames,$catdefs)=fetch_categorydefs('con');
$filtercat=$catid.':'.$ratvalue.';';


	if($comid!=''){
		if($yid!=''){
			$d_comments=mysql_query("SELECT * FROM comments WHERE
							comments.entrydate >= '$startdate' AND comments.entrydate<='$enddate' 
							AND comments.subject_id LIKE '$bid' AND comments.category LIKE '$filtercat'
							AND comments.student_id=ANY(SELECT student.id FROM student JOIN comidsid AS a ON comidsid.student_id=student.id
							WHERE student.yeargroup_id='$yid' AND a.community_id='$comid' 
							AND (a.leavingdate>'$enddate' OR a.leavingdate='0000-00-00' OR a.leavingdate IS NULL));");
			}
		else{
			$d_comments=mysql_query("SELECT * FROM comments JOIN
					comidsid AS a ON a.student_id=comments.student_id WHERE
					a.community_id='$comid' AND (a.leavingdate>'$enddate' OR a.leavingdate='0000-00-00' OR a.leavingdate IS NULL)
					AND comments.entrydate >= '$startdate' AND comments.entrydate<='$enddate' 
							AND comments.subject_id LIKE '$bid' AND comments.category LIKE '$filtercat';");
			}
		}
	elseif($yid!=''){
		$d_comments=mysql_query("SELECT * FROM comments JOIN
			student ON student.id=comments.student_id WHERE comments.entrydate >= '$startdate' AND
			comments.entrydate<='$enddate' AND student.yeargroup_id='$yid' AND comments.subject_id LIKE '$bid' 
			AND comments.category LIKE '$filtercat' ORDER BY student.surname;");
		}
	elseif($secid!=''){
		if($secid==1){$section=" AND yeargroup.section_id LIKE '%' ";}
		else{$section=" AND yeargroup.section_id='$secid' ";}
		$d_comments=mysql_query("SELECT * FROM comments JOIN
			student ON student.id=comments.student_id JOIN yeargroup ON yeargroup.id=student.yeargroup_id 
			WHERE comments.entrydate >= '$startdate' AND comments.entrydate<='$enddate' 
			$section AND comments.subject_id LIKE '$bid' 
			AND comments.category LIKE '$filtercat' ORDER BY student.surname;");
		}
	else{
		$pastorals=(array)list_pastoral_respon();
		$ryids=$pastorals['years'];
		if(sizeof($ryids)>0){
			$yearsearch='(';
			$separator='';
			foreach($ryids as $ryid){
				if($ryid>-100){
					$yearsearch.=$separator."student.yeargroup_id='$ryid'";
					$separator=' OR ';
					}
				}
			$yearsearch.=')';
			$d_comments=mysql_query("SELECT * FROM comments JOIN
					student ON student.id=comments.student_id WHERE comments.entrydate >= '$startdate' AND
					comments.entrydate<='$enddate' AND $yearsearch AND comments.subject_id LIKE '$bid' 
					AND comments.category LIKE '$filtercat' ORDER BY student.surname;");
			}
		}


	if(mysql_num_rows($d_comments)==0){
		$error[]=get_string('nonefound',$book);
		$action='report_comments.php';
    	include('scripts/results.php');
	    include('scripts/redirect.php');
		exit;
		}

	$summarys=array();
	$sids=array();
	while($comment=mysql_fetch_array($d_comments,MYSQL_ASSOC)){
		$sid=$comment['student_id'];
		if($comment['subject_id']=='%'){$comment['subject_id']='G';}
		if(!in_array($sid,$sids)){
			$sids[]=$sid;
			$summary=array();
			}
		else{
			$summary=$summarys[$sid];
			}
		$pairs=explode(';',$comment['category']);
		while(list($pairindex,$pair)=each($pairs)){
			list($cat,$value)=explode(':',$pair);
			if(isset($summary[$cat]['value'])){$summary[$cat]['value']+=$value;}
			else{$summary[$cat]['value']=$value;}
			if(isset($summary[$cat]['count'])){$summary[$cat]['count']++;}
			else{$summary[$cat]['count']=1;}
			$summarys[$sid]=$summary;
			}
		}

$extrabuttons=array();
$extrabuttons['print']=array('name'=>'current',
									   'value'=>'report_comments_print.php',
									   'onclick'=>'checksidsAction(this)');
$extrabuttons['summary']=array('name'=>'current',
									   'xmlcontainerid'=>'summary',
									   'value'=>'report_comments_print.php',
									   'onclick'=>'checksidsAction(this)');
two_buttonmenu($extrabuttons,$book);
?>
<div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>"> 

	  <div id="xml-checked-action" style="display:none;">
		<period>
		  <startdate><?php print $startdate;?></startdate>
		  <enddate><?php print $enddate;?></enddate>
		</period>
	  </div>
	  <div id="xml-summary" style="display:none;">
		<period>
		  <startdate><?php print $startdate;?></startdate>
		  <enddate><?php print $enddate;?></enddate>
		  <transform>progress_summary_short</transform>
		</period>
	  </div>

	  <table class="listmenu sidtable table-comments">
		<tr>
		  <th class="checkall table-comments-checkall">
			  <input type="checkbox" name="checkall" value="yes" onChange="checkAll	(this);" />
		  </th>
		  <th class="table-comments-student"><?php print_string('student');?></th>
		  <th class="smalltable table-comments-formgroup"><?php print_string('formgroup');?></th>
<?php
		foreach($catdefs as $catdef){
			print '<th class="smalltable table-comments-smalltable">'.$catdef['name'].'</th>';
			}
?>
		</tr>
<?php
	while(list($index,$sid)=each($sids)){
		$Student=fetchStudent_short($sid);
?>
		<tr>
		  <td>
			<input type='checkbox' name='sids[]' value='<?php print $sid; ?>' />
		  </td>
		  <td>
			<a href="infobook.php?current=comments_list.php&sid=<?php
			  print $sid;?>&sids[]=<?php print $sid;?>"  target="viewinfobook"
			  onclick="parent.viewBook('infobook');"> 
			  <?php print $Student['DisplayFullName']['value']; ?>
			</a>
		  </td>
		  <td>
			<?php print $Student['RegistrationGroup']['value']; ?>
		  </td>
<?php
		$summary=$summarys[$sid];
		foreach($catdefs as $catid => $catdef){
			if(!isset($summary[$catid]['value'])){$colourclass='';$summary[$catid]['count']='';}
			elseif($summary[$catid]['value']==0){$colourclass='nolite';}
			elseif($summary[$catid]['value']<-1){$colourclass='hilite';}
			elseif($summary[$catid]['value']<0){$colourclass='midlite';}
			elseif($summary[$catid]['value']>1){$colourclass='golite';}
			elseif($summary[$catid]['value']>0){$colourclass='gomidlite';}
			print '<td class="'.$colourclass.'">&nbsp;'. 
					$summary[$catid]['count'].'</td>';
			}
?>
		</tr>
<?php	
		}
	reset($sids);
?>
	  </table>

	</fieldset>

 	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
 	<input type="hidden" name="choice" value="<?php print $choice;?>" />
 	<input type="hidden" name="current" value="<?php print $action;?>" />
	</form>
  </div>
