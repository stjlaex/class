<?php
/**												class_view_marks.php
 *
 *	Fetch information about the classes (indexed by i)
 *		- first the teachers
 *  Each column and all its associated info has an entry in the array
 *	$umns.
 *
 */

$bid=array();
for($i=0;$i<sizeof($cids);$i++){
	$cid=$cids[$i];
    $teachers[$i]='';
	$d_tidcid=mysql_query("SELECT teacher_id FROM tidcid WHERE
					class_id='$cid' ORDER BY teacher_id");
	while($tidcid=mysql_fetch_array($d_tidcid,MYSQL_ASSOC)){
		$teachers[$i]=$teachers[$i].' - '.$tidcid['teacher_id'];
		}
	
	/* Fetch the subject of the class */
	$d_class=mysql_query("SELECT * FROM class WHERE id='$cid'");
	$class=mysql_fetch_array($d_class,MYSQL_ASSOC);
	$bid[$i]=$class['subject_id'];

	
	/* Fetch marks for classes. Where there is more than one class,
	 * any marks must be shared by all
	 */
	$table='markselect'.$i;
	if($i==0){
		mysql_query("CREATE TEMPORARY TABLE $table (SELECT mark.* FROM mark LEFT
				JOIN midcid ON mark.id=midcid.mark_id WHERE midcid.class_id='$cid'
				ORDER BY mark.entrydate DESC, mark.id);");
		}
	else{$lasttable='markselect'.($i-1);
		mysql_query("CREATE TEMPORARY TABLE $table 
				(SELECT $lasttable.* FROM $lasttable
				LEFT JOIN midcid ON $lasttable.id=midcid.mark_id WHERE
				midcid.class_id='$cid' ORDER BY $lasttable.entrydate
				DESC, $lasttable.id );");
		}

	/* Fetch students for these classes.  */
	if($i==0){
		mysql_query("CREATE TEMPORARY TABLE students
		(SELECT a.student_id, b.surname, b.forename,
		b.preferredforename, b.form_id, a.class_id FROM
		cidsid a, student b WHERE a.class_id='$cid' AND
		b.id=a.student_id ORDER BY b.surname);"); 
		}
	else{
		mysql_query("INSERT INTO students SELECT
		a.student_id, b.surname, b.forename, b.preferredforename, 
		b.form_id, a.class_id FROM cidsid a,
		student b WHERE a.class_id='$cid' AND b.id=a.student_id ORDER
		BY b.surname;");
		}
	}

/*
 * Fetch information about all marks and store in array $umns
 * (ie. columns) The umntype is used to filter the mark columns and is
 * set in the sideoptions.
 */

	if($umnfilter!='hw' and $umnfilter!='cw' and $umnfilter!='%' and $umnfilter!='t'){
		$profile=$profiles[$umnfilter];
		$umntype='p';
		}
	else{$umntype=$umnfilter;}

	$umns=array();
	if($umntype=='t'){
		$d_marks=mysql_query("SELECT $table.* FROM $table WHERE
				$table.marktype='report' OR ($table.marktype='score' AND
				$table.assessment='yes' AND $table.id=ANY(SELECT
				eidmid.mark_id FROM eidmid JOIN assessment ON
				assessment.id=eidmid.assessment_id WHERE assessment.profile_name=''));");
		$c=0;
	   	}
	elseif($umntype=='p'){
		$profile_crid=$classes[$cid]['crid'];
		$profile_bid=$classes[$cid]['bid'];
		$profile_name=$profile['name'];
		$profile_pidstatus=$profile['component_status'];
		$profile_marktype=$profile['rating_name'];
		trigger_error($profile_name.':'.$profile_crid.':'.':',E_USER_WARNING);
		$d_marks=mysql_query("SELECT $table.* FROM $table WHERE $table.marktype='score'
				AND $table.assessment='yes' AND $table.id=ANY(SELECT
				eidmid.mark_id FROM eidmid JOIN assessment ON
				assessment.id=eidmid.assessment_id 
				WHERE assessment.profile_name='$profile_name');");
		$c=1;
		}
	else{
		if($umntype=='%'){$filtertype='%';$filterass='%';}
		elseif($umntype=='cw'){$filtertype='score';$filterass='no';}
		elseif($umntype=='hw'){$filtertype='hw';$filterass='no';}
		$d_marks=mysql_query("SELECT * FROM $table WHERE marktype LIKE
				'$filtertype' AND assessment LIKE '$filterass';");
		$c=0;
		}

	/* Store each mark's attributes in arrays for use later in each cell
	 * TODO: these are stored twice for historical reasons! 
	 */
	$c_marks=mysql_num_rows($d_marks); /*number of marks for class*/
	while($mark=mysql_fetch_array($d_marks,MYSQL_ASSOC)){
	      $mid[$c]=$mark['id'];
	      $mark_total[$c]=$mark['total'];
	      $marktype[$c]=$mark['marktype'];
		  $midlist[$c]=trim($mark['midlist']);
	      $lena[$c]=$mark['levelling_name'];
	
		  /*umn an array of mark properties for this column*/	
	      $umn=array('id'=>$mark['id'], 
					 'mark_total'=>$mark['total'], 
					 'marktype'=>$mark['marktype'],
					 'scoretype'=>'',
					 'midlist'=>trim($mark['midlist']),
					 'def_name'=>$mark['def_name'], 
					 'topic'=>$mark['topic'], 
					 'entrydate'=>$mark['entrydate'],
					 'lena'=>$mark['levelling_name'], 
					 'comment'=>$mark['comment'],
					 'assessment'=>$mark['assessment'],
					 'component'=>$mark['component_id']);
		  /*each mark in umns is referenced by its column count*/
		  $umns[$c]=$umn;


			if($marktype[$c]=='average'){
				/*no markdef for an average, have to get grading_name from the levelname*/
				$scoregrading[$c]=$lena[$c];
				if($scoregrading[$c]!=''){
					$d_grading=mysql_query("SELECT grades FROM grading 
								WHERE name='$scoregrading[$c]'");
					$scoregrades[$c]=mysql_result($d_grading,0);
					}
				else{$scoregrades[$c]='';}
				}
			elseif($marktype[$c]=='level'){
				/*no markdef for a level, have to get grading_name from the levelname*/
				$scoregrading[$c]=$lena[$c];
				if($lena[$c]!=''){
					$d_levelling=mysql_query("SELECT levels FROM
									levelling WHERE name='$lena[$c]'");
					$levels[$c]=mysql_result($d_levelling,0);
					}
				else{$levels[$c]='';}
				}
			elseif($marktype[$c]=='compound' or $marktype[$c]=='report'){
				/*no markdef for a compound or report*/
				$scoregrading[$c]='';
				$scoregrades[$c]='';   
				}
			elseif($marktype[$c]=='score' or $marktype[$c]=='hw'){
				$markdef_name=$mark['def_name'];
				$d_markdef=mysql_query("SELECT * FROM markdef WHERE name='$markdef_name'");
				$markdef=mysql_fetch_array($d_markdef,MYSQL_ASSOC);	      
				$scoretype[$c]=$markdef['scoretype'];
				$umns[$c]['scoretype']=$markdef['scoretype'];
				$scoregrading[$c]=$markdef['grading_name'];
				if($scoregrading[$c]!=''){
					$grading_name=$scoregrading[$c];
					$d_grading=mysql_query("SELECT grades FROM grading 
											WHERE name='$grading_name'");
					$scoregrades[$c]=mysql_result($d_grading,0);
					}
				else{$scoregrades[$c]='';}     
				}
		$c++;
		}
	if($umntype=='p'){
		$c_marks++;
		$profile_midlist='';
		$profile_pids=array();
		if($pid==''){

		   /* Not filtering for a specific pid so include all. */
			if(sizeof($pids)>0 and $profile_pidstatus!='None'){
				$profile_pids=$pids;
				}
			else{
				$profile_pids[]='';
				}
			}
		else{
			/* Just filter for one pid and its strands*/
			$profile_pids[]=$pid;
			$strands=list_subject_components($pid,$profile_crid,'V');
			while(list($sindex,$strand)=each($strands)){
				if(!in_array($strand['id'],$profile_pids)){
					$profile_pids[]=$strand['id'];
					}
				}
			}

		for($iumn=1;$iumn<$c_marks;$iumn++){
			if(in_array($umns[$iumn]['component'],$profile_pids)){
				$profile_midlist.=$umns[$iumn]['id'].' ';
				}
			}

		$marktype=$profile_marktype;
		if($marktype==''){
			/* The derivation of the profile summary columns is now in
			   the catregorydef table this clause is just for backward
			   compatibility. */
			if($profile_name=='FS Steps'){$marktype='tally';}
			else{$marktype='sum';}
			}

		$profile_midlist=trim($profile_midlist);
		$scoregrades[0]=$scoregrades[1];
		$scoregrading[0]=$scoregrading[1];
		$mid[0]=-1;
		$mark_total[0]=$mark_total[1];
		$marktype[0]=$marktype;
		$lena[0]=$lena[1];
		$midlist[0]=$profile_midlist;
		$umns[0]=array('id'=>-1, 
					   'mark_total'=>'', 
					   'marktype' => $marktype,
					   'scoretype' => '',
					   'midlist'=>$profile_midlist,
					   'def_name'=>'', 
					   'topic'=>$profile_name, 
					   'entrydate'=>date('Y-m-d'),
					   'lena'=>'', 
					   'comment'=>'',
					   'assessment'=>'no',
					   'component'=>$pid
					   );
		}
?>
