<?php
/**												class_view_marks.php
 *
 *	Fetch information about the classes (indexed by i)
 *		- first the teachers
 */
$bid=array();
for($i=0;$i<sizeof($cids);$i++){
	$cid=$cids[$i];
    $teachers[$i]='';
	$d_tidcid=mysql_query("SELECT teacher_id FROM tidcid WHERE
					class_id='$cid' ORDER BY teacher_id");
	while($tidcid=mysql_fetch_array($d_tidcid,MYSQL_ASSOC)){
		$teachers[$i]=$teachers[$i].' / '.$tidcid{'teacher_id'};
		}
	
	/*	Fetch the subject of the class*/
	$d_class = mysql_query("SELECT * FROM class WHERE id='$cid'");
	$class = mysql_fetch_array($d_class,MYSQL_ASSOC);
	$bid[$i] = $class{'subject_id'};

	
	/* Fetch marks for classes. Where there is more than one class, any */
	/*				marks must be shared by all*/
	$table='markselect'.$i;
	if($i==0){
		if(mysql_query("CREATE TEMPORARY TABLE $table (SELECT mark.* FROM mark LEFT
				JOIN midcid ON mark.id=midcid.mark_id WHERE midcid.class_id='$cid'
				ORDER BY mark.entrydate DESC)")){}
	 		else {print 'Failed!<br />'; $error=mysql_error(); print $error.'<br />';}
		}
	else {$lasttable='markselect'.($i-1);
		if(mysql_query("CREATE TEMPORARY TABLE $table (SELECT $lasttable.* FROM $lasttable LEFT
				JOIN midcid ON $lasttable.id=midcid.mark_id WHERE midcid.class_id='$cid'
				ORDER BY $lasttable.entrydate DESC)")){}
	 		else {print 'Failed!<br />'; $error=mysql_error(); print $error.'<br />';}
		}

	
	/* Fetch students for these classes.  */
	if($i==0){ if(mysql_query("CREATE TEMPORARY TABLE students
		(SELECT a.student_id, b.surname, b.forename,
		b.preferredforename, b.form_id, a.class_id FROM
		cidsid a, student b WHERE a.class_id='$cid' AND
		b.id=a.student_id ORDER BY b.surname)")){} else {print
		'Failed!<br />'; $error=mysql_error(); print $error.'<br />';} 
		}
	else
		{if(mysql_query("INSERT INTO students SELECT
		a.student_id, b.surname, b.forename, b.preferredforename, 
		b.form_id, a.class_id FROM cidsid a,
		student b WHERE a.class_id='$cid' AND b.id=a.student_id ORDER
		BY b.surname")){} else {print 'Failed!<br />';
		$error=mysql_error(); print $error.'<br />';} 
		}
	
}

	/*Fetch information about all marks and store in array $umns*/	
	$umns=array();
	$d_marks=mysql_query("SELECT * FROM $table");
	$c_marks=mysql_num_rows($d_marks); /*number of marks for class*/
	$c=0;
	while($mark=mysql_fetch_array($d_marks,MYSQL_ASSOC)){
		  /*Store all the mark's attributes in arrays for use later in each cell*/	      
	      $mid[$c]=$mark{'id'};
	      $mark_total[$c]=$mark{'total'};
	      $marktype[$c]=$mark{'marktype'};
	      $midlist[$c]=$mark{'midlist'};
	      $lena[$c]=$mark{'levelling_name'};
	
		  /*decide whether mark is to be displayed: depends on hidden */
		  /*property of mark and on tid specific selections in visible*/
  		  $visible=explode (';',$mark{'visible'});
		  if($mark{'hidden'}=='yes' xor in_array($tid, $visible)){$display='no';}else{$display='yes';}

		  /*umn an array of mark properties for this column*/	
	      $umn=array('id'=>$mark{'id'}, 'mark_total'=>$mark{'total'}, 
					'marktype' => $mark{'marktype'},
					'scoretype' => '',
					'midlist'=>$mark{'midlist'},
					'def_name'=>$mark{'def_name'}, 
					'hidden'=>$mark{'hidden'}, 'display'=>$display, 'topic'=>$mark{'topic'}, 
					'entrydate'=>$mark{'entrydate'},
					'lena'=>$mark{'levelling_name'}, 
					'comment'=>$mark{'comment'},
					'assessment'=>$mark{'assessment'},
					'component'=>$mark{'component_id'});
		 $umns[$c] = $umn;/*each mark in umns is referenced by its column count*/


		if($display=='yes' or $mark{'assessment'}=='yes'){
			/*only need to fetch if the column is displayed - improves speed*/	
			/*though may still be needed by a report column if an assessment*/

			if($marktype[$c]=='average'){
				/*no markdef for an average, have to get grading_name from the levelname*/
				$scoregrading[$c]=$lena[$c];
				if($scoregrading[$c]!=""){
					$d_grading=mysql_query("SELECT grades FROM grading WHERE name='$scoregrading[$c]'");
					$scoregrades[$c]=mysql_result($d_grading,0);
					}
				else{$scoregrades[$c]='';}
				}
			elseif($marktype[$c]=='level'){
				/*no markdef for a level, have to get grading_name from the levelname*/
				$scoregrading[$c]=$lena[$c];
				if($lena[$c]!=""){
					$d_levelling=mysql_query("SELECT levels FROM levelling WHERE name='$lena[$c]'");
					$levels[$c]=mysql_result($d_levelling,0);
					}
				else{$levels[$c]='';}
				}
			elseif($marktype[$c]=='compound' or $marktype[$c]=='report'){
				/*no markdef for a compound or report*/
				$scoregrading[$c]='';
				$scoregrades[$c]='';   
				}

			elseif ($marktype[$c]=='score'){
	      	  $markdef_name=$mark{'def_name'};
		      $d_markdef=mysql_query("SELECT * FROM markdef WHERE name='$markdef_name'");
			  $markdef=mysql_fetch_array($d_markdef,MYSQL_ASSOC);	      
		      $scoretype[$c]=$markdef{'scoretype'};
		      $umns[$c]['scoretype']=$markdef{'scoretype'};
		      $scoregrading[$c]=$markdef{'grading_name'};
		      if($scoregrading[$c]!=""){
				$d_grading=mysql_query("SELECT grades FROM grading WHERE name='$scoregrading[$c]'");
		      	$scoregrades[$c]=mysql_result($d_grading,0);
				}
			  else{$scoregrades[$c]="";}	      
    		  }
			}
		$c++;
		}
?>