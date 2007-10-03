<?php 
/**								   			import_students_action3.php
 *
 *	Insert the imported values into the tables.	
 */

$action='';

include('scripts/sub_action.php');

$idef=$_SESSION['idef'];
$instudents=$_SESSION['instudents'];
$nofields=$_SESSION['nofields'];
$enrolyear=get_curriculumyear();
$enrolstatus='C';

if($sub=='Save'){
	$action='import_students_cidef.php';

	$idef=array();
	for($c=0;$c<$nofields;$c++){
		$table='';
		$field='';
		$preset='';
		if(isset($_POST["table$c"])){
			$table=$_POST["table$c"];
			$field=$_POST["field$c"];
			if(isset($_POST["preset$c"])){$preset=$_POST["preset$c"];}
			else{$preset='';}
			}
		$out=array($c,$table,$field,$preset);
		array_push($idef,$out);
		}
	$_SESSION['idef']=$idef;

	three_buttonmenu();
?>
  <div class="content">
	<fieldset class="center">
	  <legend><?php print_string('savedefinitionfile',$book);?></legend>
	  <form  name="formtoprocess" id="formtoprocess" method="post"
		action="<?php print $host;?>">
		<label><?php print_string('filename');?></label>
		<input type="text" class="required" id="importfile" name="importfile"/>	
		  <input type="hidden" name="current" value="<?php print $action;?>"/>
		  <input type="hidden" name="choice" value="<?php print $choice;?>"/>
		  <input type="hidden" name="cancel" value="<?php print $cancel;?>"/>
	  </form>
	</fieldset>
  </div>
<?php
	}

/**********************************************************/
elseif($sub=='Submit'){
	/*		set up arrays to point from table fields to input values*/
	/*				null values point to -1 is default*/

	/*	sidfields is combination of student and info and accomodation tables	*/
		$sidfields=array();
		$sidformats=array();
   		$d_table=mysql_query("DESCRIBE student");
		while($table_field=mysql_fetch_array($d_table,MYSQL_ASSOC)){
			$field_key=$table_field['Field'];
			$row=array("$field_key" => -1);
  			$sidfields=array_merge($row, $sidfields);
			$field_format=$table_field['Type'];
			$row=array("$field_key" => "$field_format");
  			$sidformats=array_merge($row, $sidformats);
			}
	
		$infofields=array();
   		$d_table=mysql_query("DESCRIBE info");
		while($table_field=mysql_fetch_array($d_table,MYSQL_ASSOC)){
			$field_key=$table_field['Field'];
			$row=array("$field_key" => -1);
  			$infofields=array_merge($row, $infofields);
			$field_format=$table_field['Type'];
			$row=array("$field_key" => "$field_format");
  			$sidformats=array_merge($row, $sidformats);
			}

		$accfields=array();
   		$d_table=mysql_query("DESCRIBE accomodation");
		while($table_field=mysql_fetch_array($d_table,MYSQL_ASSOC)){
			$field_key=$table_field['Field'];
			$row=array("$field_key" => -1);
  			$accfields=array_merge($row, $accfields);
			$field_format=$table_field['Type'];
			$row=array("$field_key" => "$field_format");
  			$sidformats=array_merge($row, $sidformats);
			}


		/*gidfields is a combination of guardian, address and phone tables*/
		$gid1fields=array();
		$gid2fields=array();
		$gid3fields=array();
		$gidformats=array();
   		$d_table=mysql_query("DESCRIBE guardian");
		while($table_field=mysql_fetch_array($d_table,MYSQL_ASSOC)){
			$field_key=$table_field['Field'];
			$row=array("$field_key" => -1);
  			$gid1fields=array_merge($row, $gid1fields);
  			$gid2fields=array_merge($row, $gid2fields);
  			$gid3fields=array_merge($row, $gid3fields);
			$field_format=$table_field['Type'];
			$row=array("$field_key" => "$field_format");
  			$gidformats=array_merge($row, $gidformats);
			}
		/*add in relationship from gidsid*/
		$row=array('relationship' => -1);
		$gid1fields=array_merge($row, $gid1fields);
		$gid2fields=array_merge($row, $gid2fields);
		$gid3fields=array_merge($row, $gid3fields);
		$row=array('relationship' => 'enum');
		$gidformats=array_merge($row, $gidformats);

		/*add in receives mailings from gidsid*/
		$row=array('mailing' => -1);
		$gid1fields=array_merge($row, $gid1fields);
		$gid2fields=array_merge($row, $gid2fields);
		$gid3fields=array_merge($row, $gid3fields);
		$row=array('mailing' => 'enum');
		$gidformats=array_merge($row, $gidformats);

		$gid1address=array();
		$gid2address=array();
		$gid3address=array();
   		$d_table=mysql_query("DESCRIBE address");
		while($table_field=mysql_fetch_array($d_table,MYSQL_ASSOC)){
			$field_key=$table_field['Field'];
			$row=array("$field_key" => -1);
  			$gid1address=array_merge($row, $gid1address);
  			$gid2address=array_merge($row, $gid2address);
  			$gid3address=array_merge($row, $gid3address);
			$field_format=$table_field['Type'];
			$row=array("$field_key" => "$field_format");
  			$gidformats=array_merge($row, $gidformats);
			}

		$gid1phone=array();
		$gid2phone=array();
		$gid3phone=array();
   		$d_table=array('home phone', 'mobile phone', 'work phone', 'fax');
		while(list($c, $field_key)=each($d_table)){
			$row=array("$field_key" => -1);
  			$gid1phone=array_merge($row, $gid1phone);
  			$gid2phone=array_merge($row, $gid2phone);
  			$gid3phone=array_merge($row, $gid3phone);
			}

	/*match the imported fields with the table fields*/
	for($c=0;$c<$nofields;$c++){
		if(isset($_POST["table$c"])){
			$table=$_POST["table$c"];
			$field=$_POST["field$c"];
			if($table=='sid'){
				if(array_key_exists("$field", $sidfields)){$sidfields["$field"]=$c;}
				if(array_key_exists("$field", $infofields)){$infofields["$field"]=$c;}
				if(array_key_exists("$field", $accfields)){$accfields["$field"]=$c;}
				}
			if($table=='gid1'){
				$gid1='exists';
				if(array_key_exists("$field", $gid1fields)){$gid1fields["$field"]=$c;}
				if(array_key_exists("$field",
					$gid1address)){$gid1address["$field"]=$c; $gid1a="exists";}
				if(array_key_exists("$field",
					$gid1phone)){$gid1phone["$field"]=$c; $gid1p="exists";}
				}
			if($table=='gid2'){
				$gid2='exists';
				if(array_key_exists("$field", $gid2fields)){$gid2fields["$field"]=$c;}
				if(array_key_exists("$field",
					$gid2address)){$gid2address["$field"]=$c; $gid2a="exists";}
				if(array_key_exists("$field", $gid2phone)){$gid2phone["$field"]=$c; $gid2p="exists";}
				}
			if($table=='gid3'){
				$gid3='exists';
				if(array_key_exists("$field", $gid3fields)){$gid3fields["$field"]=$c;}
				if(array_key_exists("$field",
					$gid3address)){$gid3address["$field"]=$c; $gid3a="exists";}
				if(array_key_exists("$field",
					$gid3phone)){$gid3phone["$field"]=$c; $gid3p="exists";}
				}
			}
		}

	/*******************cycle through imported rows****************************/
	for($r=0;$r<sizeof($instudents);$r++){
		$student=$instudents[$r];	
		/*get the sid for the new student*/
		mysql_query("INSERT INTO student SET surname='', forename=''");
		$new_sid=mysql_insert_id();
		reset($sidfields);
		while(list($field_name, $field_no)=each($sidfields)){
			if($field_no==-1){$val='';}//value is null
			else{
				$val=$student[$field_no];
				$format=$sidformats[$field_name];
				if(isset($_POST["preset$field_no"])){$val=$_POST["preset$field_no"];}
				$val=checkEntry($val, $format, $field_name);
				mysql_query("UPDATE student SET $field_name='$val' WHERE id='$new_sid'");
				if($field_name=='yeargroup_id'){
					if($enrolstatus=='EN'){$newtype='enquired';}
					elseif($enrolstatus=='AC' or $enrolstatus=='C'){$newtype='accepted';}
					else{$newtype='applied';}
					$newcom=array('id'=>'','type'=>$newtype, 
								  'name'=>$enrolstatus.':'.$val,'year'=>$enrolyear);
					$oldcoms=join_community($new_sid,$newcom);
					$oldcoms=join_community($new_sid,array('id'=>'','type'=>'year','name'=>$val));

					}
				elseif($field_name=='form_id'){
					$oldcoms=join_community($new_sid,array('id'=>'','type'=>'form','name'=>$val));
					}
				}
			}

		reset($infofields);
		mysql_query("INSERT INTO info SET student_id='$new_sid'");
		while(list($field_name, $field_no)=each($infofields)){
			if($field_no==-1){$val='';}//value is null
			else{
				$val=$student[$field_no];
				$format=$sidformats[$field_name];
				if(isset($_POST["preset$field_no"])){$val=$_POST["preset$field_no"];}
				$val=checkEntry($val, $format, $field_name);
				mysql_query("UPDATE info SET $field_name='$val' WHERE student_id='$new_sid'");			
				}
			}

		$Student=fetchStudent_singlefield($new_sid,'Boarder');
		if($Student['Boarder']['value']!='N' and $Student['Boarder']['value']!=''){
			reset($accfields);
			mysql_query("INSERT INTO accomodation SET student_id='$new_sid'");
			$accid=mysql_insert_id();
			while(list($field_name, $field_no)=each($accfields)){
				if($field_no==-1){$val='';}//value is null
				else{
					$val=$student[$field_no];
					$format=$sidformats[$field_name];
					if(isset($_POST["preset$field_no"])){$val=$_POST["preset$field_no"];}
					$val=checkEntry($val, $format, $field_name);
					mysql_query("UPDATE accomodation SET $field_name='$val' WHERE id='$accid'");
					}
				}
			set_accomodation($new_sid,$accid);
			}


		/********* input for guardian number $gno ********/
		for($gno=1;$gno<4;$gno++){
			$gname='gid'.$gno;
			$gfields=$gname.'fields';
			$gaddress=$gname.'address';
			$gphone=$gname.'phone';
			if(isset(${$gname})){
				$surname=$student[${$gfields}['surname']];
				$forename=$student[${$gfields}['forename']];
				$middlenames=$student[${$gfields}['middlenames']];

				/*check if there is already an entry for this guardian*/
				$d=mysql_query("SELECT id FROM guardian WHERE
					surname='$surname' AND forename='$forename' AND middlenames='$middlenames'");
				/*this is not fool-proof need to offer user check to*/
				/*avoid wrong matches*/

				if(mysql_num_rows($d)==0){
					mysql_query("INSERT INTO guardian SET 
									surname='$surname', forename='$forename'");
					$new_gid=mysql_insert_id();
					}
				else{
					$new_gid=mysql_result($d,0);
					}

				/*input to guardian table*/
				reset(${$gfields});
				while (list($field_name, $field_no)=each(${$gfields})) {
					if($field_no==-1){$val='';}//value is null
					else{
						$val=$student[$field_no];
						$format=$gidformats[$field_name];
						if(isset($_POST["preset$field_no"])){$val=$_POST["preset$field_no"];}
						$val=checkEntry($val, $format, $field_name);
						if($field_name=='relationship'){$relationship=$val;}
						elseif($field_name=='mailing'){$mailing=$val;}
						else{mysql_query("UPDATE guardian SET
									$field_name='$val' WHERE id='$new_gid'");}
						}
					}	
				if(!isset($relationship)){$relationship='';}
				if(!isset($mailing)){$mailing='';}
				mysql_query("INSERT INTO gidsid SET guardian_id='$new_gid', 
						student_id='$new_sid',
						mailing='$mailing', relationship='$relationship'");
				/*input to address table: the four key items to check*/ 
				$ok=0;	
				if(isset(${$gname.'a'})){
					if(${$gaddress}['region']!=-1){$region=$student[${$gaddress}['region']]; $ok++;}
						else{$region=''; }
					if(${$gaddress}['street']!=-1){
						$street=$student[${$gaddress}['street']]; 
						$ok++;
						}
					else{$street='';}
					}

				/*if region and street blank then too little info for new entry*/
				if($ok>1){
					/*check if there is already an entry for this address*/
					$d_aid=mysql_query("SELECT id FROM address WHERE
						street='$street' AND region='$region'");
					if(mysql_num_rows($d_aid)==0){
						/*no entry found so make a new one*/
						mysql_query("INSERT INTO address SET region=''");
						$new_aid=mysql_insert_id();
						mysql_query("INSERT INTO gidaid SET 
										guardian_id='$new_gid', address_id='$new_aid'");
						}
					else{
						$new_aid=mysql_result($d_aid,0);
						$d_gidaid=mysql_query("SELECT * FROM gidaid
							WHERE guardian_id='$new_gid' AND address_id='$new_aid'");
						if(mysql_num_rows($d_gidaid)==0){
							/*no association exists so make one*/
							mysql_query("INSERT INTO gidaid SET 
											guardian_id='$new_gid', address_id='$new_aid'");
							}
						}
	
					reset(${$gaddress});
					while (list($field_name, $field_no)=each(${$gaddress})) {
						if($field_no==-1){$val='';}//value is null
						else{
							$val=$student[$field_no];
							$format=$gidformats[$field_name];
							if(isset($_POST["preset$field_no"])){$val=$_POST["preset$field_no"];}
							$val=checkEntry($val, $format, $field_name);
							mysql_query("UPDATE address SET
								$field_name='$val' WHERE id='$new_aid'");
							}
						}
					}
	
				/*input to phone table*/	
				if(isset(${$gname.'p'})){	
					reset(${$gphone});
					while (list($field_name, $field_no) = each(${$gphone})) {
						if($field_no==-1){$val='';}//value is null
						else{
							$val=$student[$field_no];
							if($field_name=='home phone'){$type='H';}
							if($field_name=='work phone'){$type='W';}
							if($field_name=='mobile phone'){$type='M';}
							if($field_name=='fax'){$type='Fax';}
							if(isset($_POST["preset$field_no"])){$val=$_POST["preset$field_no"];}
							$format="varchar(22)";
							$val=checkEntry($val, $format, $field_name);
							$d_phone=mysql_query("SELECT * FROM phone
												WHERE some_id='$new_gid' AND number='$val'");
							if(mysql_num_rows($d_phone)==0){
								/*number does not already exist so insert*/ 
								mysql_query("INSERT INTO phone SET
								some_id='$new_gid', number='$val',phonetype='$type'");
								}
							}
						}
					}
				/*finished with guardians*/	
				}
			}

		/*read the newly input student to check okay*/
		$d_student=mysql_query("SELECT * FROM student WHERE id='$new_sid'");
		$d_info=mysql_query("SELECT * FROM info WHERE student_id='$new_sid'");
		$student=mysql_fetch_array($d_student,MYSQL_ASSOC);
		$info=mysql_fetch_array($d_info,MYSQL_ASSOC);
		$result[]=$student['id'].' '.$student['surname'].'
			'.$student['forename'].' '.$student['form_id'].'
				'.$student['yeargroup_id'].' '.$student['dob'];
		}
	$result[]=get_string('studentsaddedtodatabase',$book);
	include('scripts/results.php');
	}
?>

