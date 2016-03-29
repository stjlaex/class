<?php
/**							    fees_import_action.php
 *
 *	This will import a csv file of bus charges for a whole
 *  bunch of students. The student identified either by their Classis
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

		/* Pass 1: Update concepts and tarifs */
		foreach($inrows as $c => $row){
			$conceptname=trim($row[$colstart-1]);
			$tarifname=trim($row[$colstart]);
			$amount=trim($row[$colstart+1]);

			$d_c=mysql_query("SELECT id FROM fees_concept WHERE name='$conceptname';");
			if(mysql_num_rows($d_c)>0){
				$conid=mysql_result($d_c,0);
				}
			else{
				$d_c=mysql_query("INSERT INTO fees_concept SET name='$conceptname';");
				$conid=mysql_insert_id();
				}

			$d_t=mysql_query("SELECT id FROM fees_tarif WHERE name='$tarifname' AND concept_id='$conid';");
			if(mysql_num_rows($d_t)>0){
				$tarid=mysql_result($d_t,0);
				}
			else{
				$d_t=mysql_query("INSERT INTO fees_tarif SET name='$tarifname', concept_id='$conid';");
				$tarid=mysql_insert_id();
				}
			mysql_query("UPDATE fees_tarif SET amount='$amount' WHERE id='$tarid';");

			$inrows[$c]['conid']=$conid;
			$inrows[$c]['tarid']=$tarid;
			}


		/* Pass 2: Now insert the charges */
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

				$amount=trim($row[$colstart+1]);
				$paymenttype=trim($row[$colstart+2]);
				$banknumber=trim($row[$colstart+9]);
				$conid=$row['conid'];
				$tarid=$row['tarid'];
				$rel=trim($row[$colstart+10]);

				/* Have to check the payee details */
				if(checkEnum($rel, 'relationship')!=''){
					$d_g=mysql_query("SELECT guardian_id FROM gidsid WHERE relationship='$rel' AND student_id='$sid';");
					if(mysql_num_rows($d_g)>0){
						$gid=mysql_result($d_g,0);
						update_student_payee($sid,$gid,$paymenttype);
						$payeescore++;

						$accounts=(array)list_accounts($gid);
						$okay=false;
						foreach($accounts as $account){
							if($account['banknumber']==$banknumber){
								$okay=true;
								}
							}
						if(!$okay){
							/* If the payee's account does not exist then create it */
							$bankcode=$row[$colstart+5];
							$bankname=$row[$colstart+6];
							$bankbranch=$row[$colstart+7];
							$bankcontrol=$row[$colstart+8];
							$access=$_SESSION['accessfees'];
							$accname='';
							$bankcountry='ES';
							mysql_query("INSERT INTO fees_account SET accountname='$accname' ,guardian_id='$gid';");
							$acid=mysql_insert_id();
							mysql_query("UPDATE fees_account SET bankname=AES_ENCRYPT('$bankname','$access'),
								banknumber=AES_ENCRYPT('$banknumber','$access'), bankcode=AES_ENCRYPT('$bankcode','$access'), 
								bankbranch=AES_ENCRYPT('$bankbranch','$access'), bankcontrol=AES_ENCRYPT('$bankcontrol','$access'),
								bankcountry=AES_ENCRYPT('$bankcountry','$access') 
											WHERE id='$acid';");
							}
						}
					}
				apply_student_fee($sid,$conid,$tarid,$paymenttype);
				$chargescore++;
				}
			}
		$result[]='Entered '.$chargescore.' charges for students.';
		}
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
