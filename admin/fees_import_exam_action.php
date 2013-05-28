<?php
/**							    fees_import_action.php
 *
 *	This will import a csv file of bus charges for a whole
 *  bunch of students. The student identified either by their ClaSS
 *  db id (sid) or by their enrolment no.
 *
 *  It will insert new concepts and tarifs if they don't already exist.
 *
 */

$action='fees.php';

include('scripts/sub_action.php');

$firstcol=$_POST['firstcol'];
$colstart=$_POST['colstart'];
if($_POST['separator']=='semicolon'){$separator=';';}else{$separator=',';}

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

		$conceptname='EXAMS 2013';

		$d_c=mysql_query("SELECT id FROM fees_concept WHERE name='$conceptname';");
		if(mysql_num_rows($d_c)>0){
			$conid=mysql_result($d_c,0);
			}
		else{
			$d_c=mysql_query("INSERT INTO fees_concept SET name='$conceptname';");
			$conid=mysql_insert_id();
			}


		$sid=-1;
		$students=array();
		$blankstudent=array('sid'=>$sid,'amount'=>0,'conid'=>$conid,'tarid'=>-1);
		$student=$blankstudent;

		/* Pass 1: Tally the amounts for each student */
		foreach($inrows as $row){
			if(trim($row[0])!=''){
				$sid=trim($row[0]);
				if($student['sid']!=$sid){
					if($student['sid']!=-1){$students[]=$student;}
					$student=$blankstudent;
					$student['sid']=$sid;
					}
				$student['amount']+=trim($row[$colstart]);
				}
			}

		if($student['sid']!=-1){$students[]=$student;}

		/* Pass 2: Update concepts and tarifs */
		foreach($students as $sidno => $student){

			$amount=$student['amount'];

			$d_t=mysql_query("SELECT id FROM fees_tarif WHERE amount='$amount' AND concept_id='$conid';");
			if(mysql_num_rows($d_t)>0){
				$tarid=mysql_result($d_t,0);
				}
			else{
				$tarifname='EXAM '.$amount;
				$d_t=mysql_query("INSERT INTO fees_tarif SET name='$tarifname', concept_id='$conid', amount='$amount';");
				$tarid=mysql_insert_id();
				}

			$students[$sidno]['tarid']=$tarid;
			}


		/* Pass 3: Now insert the charges */
		$incharge=0;
		foreach($students as $sidno =>$student){

			$sid=$student['sid'];
			if($firstcol=='enrolno' and $sid!=-1){
				$d_student=mysql_query("SELECT student_id FROM info WHERE CONVERT(formerupn,UNSIGNED INTEGER)='$sid' AND enrolstatus='C' AND epfusername!='';");
				if(mysql_num_rows($d_student)>0){$sid=mysql_result($d_student,0);}
				else{$sid='';}
				}

			if($sid!=''){

				$amount=trim($student['amount']);
				$paymenttype='1';
				$conid=$student['conid'];
				$tarid=$student['tarid'];
				apply_student_fee($sid,$conid,$tarid,$paymenttype);
				$incharge++;

				}
			else{
				trigger_error('BLANK!!!!! '.$sidno,E_USER_WARNING);
				}

			}

		$sidno++;
		$result[]='Entered '.$incharge.' charges for '. $sidno .' students.';
		}
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
