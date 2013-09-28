<?php
/**							    meals_import_action.php
 *
 *	This will import a csv file of bookings for a whole
 *  bunch of students. The student identified either by their ClaSS
 *  db id (sid) or by their enrolment no.
 *
 *  WARNING: All existing meals and booking information is overwritten!!!!
 */

$action='meals.php';

include('scripts/sub_action.php');

$firstcol=$_POST['firstcol'];
$colstart=$_POST['colstart'];
if($_POST['separator']=='semicolon'){$separator=';';}else{$separator=',';}

$today=date('Y-m-d');

if($sub=='Submit'){

	$fname=$_FILES['importfile']['tmp_name'];
	if($fname!=''){
   	   	$result[]='Loading file '.$fname;
   		include('scripts/file_import_csv.php');
		if(sizeof($inrows>0)){
			}
		else{
			$error[]='The file was empty!';
			}
		}
	else{
		$error[]='No file specified!';
		}


	if(!isset($error)){
		mysql_query("TRUNCATE TABLE meals_list;");
		mysql_query("TRUNCATE TABLE meals_booking;");

		/* First adds the meals to its table. */
		foreach($inrows as $row){
			$lunchname=trim($row[$colstart-1]);
			$detail=clean_text($row[$colstart]);
			$time=clean_text($row[$colstart+1]);
			$meal=(array)get_meal(-1,$lunchname,'%');
			if(!isset($meal['id'])){
				mysql_query("INSERT INTO meals_list SET name='$lunchname', type='meal',
							detail='$detail', day='%', time='$time';");
				}
			}

		/* Now read each student row and books it in booking table.*/
		$inscore=0;
		foreach($inrows as $row){
			$sid='';
			if($firstcol=='enrolno' and $row[0]!=''){
				$d_student=mysql_query("SELECT student_id FROM info WHERE formerupn='$row[0]';");
				if(mysql_num_rows($d_student)>0){$sid=mysql_result($d_student,0);}
				}
			elseif($firstcol=='sid'){
				$sid=$row[0];
				}
			if($sid!=''){
				$lunchname=trim($row[$colstart-1]);
				$comment=clean_text($row[$colstart+2]);
				$detail=clean_text($row[$colstart]);
				$meal=(array)get_meal(-1,$lunchname,'%');
				if(isset($meal['id'])){
					/*It adds every booking as everyday option starting from today*/
					add_meal_booking($sid,$meal['id'],'every',$today,$comment);
					$inscore++;
					}
				}


			}
		$result[]='Entered '.$inscore.' bookings.';
		}
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
