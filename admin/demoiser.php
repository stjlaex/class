<?php
/**								demoiser.php
 *
 * Update the database tables to match with entries from the curriculum
 * files. It does not (as yet) remove any data fro mthe database even if 
 * it has been removed from the curriculum files.
 */

$host='admin.php';
$current='demoiser.php';
$choice='demoiser.php';

function tableRead($table,$sort='',$order='ASC'){
	$trows=array();
	if($sort!=''){$d_table=mysql_query("SELECT * FROM $table ORDER BY $sort $order;");}
	else{$d_table=mysql_query("SELECT * FROM $table;");}
   	while($row=mysql_fetch_array($d_table,MYSQL_ASSOC)){
		$trows[]=$row;
		}
	return $trows;
	}

function tableClear($table){
	$d_table=mysql_query("DELETE FROM $table");
	}


function generate_random_name($gender){
	if($gender=='M'){
		$start=array('John','Paul','David','James','Eric','Ryan','Christopher','Mark','Edward',
					 'Chris','Luke','Robert','Terence');
		$middle=array('John','Paul','David','James','Eric','Ryan','Christopher','Mark','Edward',
					  'Chris','Luke','Robert','Terence');
		}
	else{
		$start=array('Emma','Claire','Tracy','Jane','Ann','Fiona','Lara','Sophie','Rachel',
					 'Louise','Jessica','Pamela');
		$middle=array('Emma','Claire','Tracy','Jane','Ann','Fiona','Lara','Sophie','Rachel',
					  'Louise','Jessica','Pamela');
		}
	$end=array('Smith','Lee','Patrick','Nunn','Bowman','Stewart','Jenkins',
			   'White','Kirkpatrick','Ibbotson','Owen','Davidson','Rowell','Phillips',
			   'Wainwright','Robson','Ball','Quinn','Davis','Johnson','Hope','Blair',
			   'Fawcett','Lawrence','Whitehead','Robinson','Wylie','McCartney','Collins',
			   'West','Anderson','Carter','Mitchell','Main','Mander','Royal','Welsh','Roy',
			   'Robertson','Riley','Newman','Turner','Hardy','Dene','Poll','Wright','Malick',
			   'Montgomery','Oscar','Forrest','Hughes','Reid','Murray','Hurley','Ashurst');
	$name=array();
    srand((double)microtime()*1000000);
	$name[]=$start[(rand() %  count($start))];
	$name[]=$middle[(rand() % count($middle))];
	$name[]=$end[(rand() %    count($end))];
    return($name);
	}


	$table='address';
	$trows=array();
	$trows=tableRead($table);
	while(list($index, $row)=each($trows)){
		$id=$row['id'];
		mysql_query("UPDATE $table SET 
		street='36 Longstreet', neighbourhood='Housing
		estate', region='Small town', postcode='SG4 9PQ', country='England'
					 WHERE id='$id'");
		}

	$table='background';	
	$trows=array();
	$trows=tableRead($table);
	while(list($index, $row)=each($trows)){
		$id=$row['id'];
		mysql_query("UPDATE $table SET detail='A specific piece of
			background information.', entrydate='2000-01-01' WHERE id='$id'");
		}

	$table='comments';
	$trows=array();
	$trows=tableRead($table);
	while(list($index, $row)=each($trows)){
		$id=$row['id'];
		/*		mysql_query("UPDATE $table SET detail='A general comment
			about positive or negative progress.' WHERE id='$id'");
		*/
		}

	$table='exclusions';
	$trows=array();
	$trows=tableRead($table);
	while(list($index, $row)=each($trows)){
		$id=$row['student_id'];
		mysql_query("UPDATE $table SET reason='The reason for the exclusion.',
	   		startdate='2000-01-01', enddate='2000-01-02' WHERE student_id='$id'");
		}


	$table='guardian';
	$trows=array();
	$trows=tableRead($table);
	while (list($index, $row) = each($trows)) {
		$id=$row['id'];
		$d_gidsid=mysql_query("SELECT relationship FROM gidsid 
				WHERE guardian_id='$id'");
		$rel=mysql_result($d_gidsid,0);
		if($rel=='PAF'){$gender='M';$title='1';}else{$gender='F';$title='2';}
		$name=generate_random_name($gender);
		mysql_query("UPDATE $table SET surname='$name[2]',
			forename='$name[0]', middlenames='$name[1]', title='$title',
			profession='', email='', companyname='', nationality='', language='',
			dob='', epfusername='', note='', code='' WHERE id='$id'");
		}

	$table='history';
	$trows=tableClear($table);

	$table='incidenthistory';
	$trows=tableClear($table);

	mysql_query("UPDATE attendance SET comment='', teacher_id=''");

	$table='incidents';
	$trows=array();
	$trows=tableRead($table);
	while(list($index, $row)=each($trows)){
		$id=$row['id'];
		mysql_query("UPDATE $table SET detail='The nature of the incident.',
					entrydate='2007-09-21' WHERE id='$id'");
		}

	$table='info';
	$trows=array();
	$trows=tableRead($table);
	while(list($index,$row)=each($trows)){
		$id=$row['student_id'];
		mysql_query("UPDATE $table SET formerupn='20987', otherpn1='', otherpn2='',
			ethnicity='', email='', phonenumber='', countryoforigin='',
			language='EN', religion='', incare='N', appnotes='', appdate='',
			staffchild='N', entrydate='2001-04-01', leavingdate='', nationality='GB', 
			appcategory='', secondnationality='', medical='N', epfusername='', appnotes='', appdate='',
				staffchild='N' WHERE student_id='$id'");
		}

	$table='phone';
	$trows=array();
	$trows=tableRead($table);
	while (list($index, $row) = each($trows)) {
		$id=$row['id'];
		mysql_query("UPDATE $table SET number='1907893333'
				WHERE id='$id'");
		}

	$table='sencurriculum';
	$trows=array();
	mysql_query("UPDATE $table SET comments='The background.',
					targets='To improve.', outcome='The result.'");

	$table='student';
	$trows=array();
	$trows=tableRead($table);
	while(list($index, $row)=each($trows)){
		$id=$row['id'];
		//if($row['gender']=='M'){$gender='F';}else{$gender='M';};
		$name=generate_random_name($gender);
		mysql_query("UPDATE $table SET surname='$name[2]',
				forename='$name[0]', middlenames='$name[1]', dob='1998-04-01'
				WHERE id='$id'");
		}

	$table='users';
	$trows=tableRead($table,'logcount','DESC');
	$profindex=1;
	$officeindex=1;
	$adminindex=1;
	$senindex=1;
	$medicalindex=1;
	$libraryindex=1;
	while(list($index, $row)=each($trows)){
		$id=$row['uid'];
		$username=$row['username'];
		$role=$row['role'];
		$passwd=md5('guest');
		if($role=='teacher'){
			$nun='Prof'.$profindex++;
			mysql_query("UPDATE tidcid SET teacher_id='$nun'
			WHERE teacher_id='$username'");
			mysql_query("UPDATE reportentry SET teacher_id='$nun'
			WHERE teacher_id='$username'");
			mysql_query("UPDATE comments SET teacher_id='$nun'
			WHERE teacher_id='$username'");
			mysql_query("UPDATE incidents SET teacher_id='$nun'
			WHERE teacher_id='$username'");
			mysql_query("UPDATE grading SET author='$nun'
			WHERE author='$username'");
			mysql_query("UPDATE form SET teacher_id='$nun'
			WHERE teacher_id='$username'");
			mysql_query("UPDATE markdef SET author='$nun'
			WHERE author='$username'");
			mysql_query("UPDATE homework SET author='$nun'
			WHERE author='$username'");
			}
		elseif($role=='admin'){
			if($username!='administrator'){$nun='admin'.$adminindex++;}
			else{$nun=$username;}
			$passwd=md5('demoadmin');
			}
		elseif($role=='office'){
			$nun='office'.$officeindex++;
			}
		elseif($role=='sen'){
			$nun='sen'.$senindex++;
			}
		elseif($role=='library'){
			$nun='library'.$libraryindex++;
			}
		elseif($role=='medical'){
			$nun='medical'.$medicalindex++;
			}
		mysql_query("UPDATE $table SET username='$nun',
			forename='P', surname='Prof', email='', nologin='0', logcount='0',
			passwd='$passwd', ip='', epfusername='$nun' WHERE uid='$id'");
		mysql_query("UPDATE orderaction SET author='$nun'
			WHERE author='$username'");
		mysql_query("UPDATE orderorder SET author='$nun'
			WHERE author='$username'");
		}

	$table='orderorder';
	mysql_query("UPDATE $table SET entrydate='2008-01-01';");

	$table='orderaction';
	mysql_query("UPDATE $table SET detail='';");

	$table='ordermaterial';
	mysql_query("UPDATE $table SET unitcost='1.23', detail='some
	stuff', refno='st';");

	$table='orderbudget';
	mysql_query("UPDATE $table SET costlimit='600', name='A Budget';");

	$table='orderinvoice';
	mysql_query("UPDATE $table SET reference='aref1',
	deliverycost='0', taxcost='0', discountcost='0', totalcost='0', debitcost='0';");

	$table='reportentry';
	mysql_query("UPDATE $table SET comment='A constructive comment from a subject teacher.'");

	$table='score';
//mysql_query("UPDATE $table SET comment=''");

	$table='form';
	$trows=tableRead($table,'yeargroup_id');
	$name=array('AA','BB','CC','DD','EE','FF','GG','HH','JJ','KK','LL','MM');
	$i=2;
	$yid=-100;
	while(list($index,$row)=each($trows)){
		if($yid!=$row['yeargroup_id']){$i=2;}
		else{$i++;}
		$yid=$row['yeargroup_id'];
		$id=$row['id'];
		if($yid=='-2'){$nid='PRE'.$name[$i];}
		elseif($yid=='-1'){$nid='NUR'.$name[$i];}
		elseif($yid=='0'){$nid='REC'.$name[$i];}
		else{$nid=$yid.''.$name[$i];}
		if(mysql_query("UPDATE $table SET id='$nid', name='$nid' 
				WHERE id='$id';")){}
		else{print '<br />'.$id.' '.$nid. mysql_error();}
		mysql_query("UPDATE student SET form_id='$nid'
				WHERE form_id='$id'");
		mysql_query("UPDATE community SET name='$nid'
				WHERE name='$id' AND type='form'");
		}

	$table='classes';
	$trows=tableRead($table);
	$name=array('AA','BB','CC','DD','EE','FF','GG','HH','JJ','KK','LL','MM');
	while(list($index,$row)=each($trows)){
		if($row['generate']=='forms'){
			$bid=$row['subject_id'];
			$crid=$row['course_id'];
			$stage=$row['stage'];
			$d_class=mysql_query("SELECT * FROM class WHERE stage='$stage'
				AND course_id='$crid' AND subject_id='$bid'");
			$i=0;
			while($row=mysql_fetch_array($d_class,MYSQL_ASSOC)){
				$cid=$row['id'];
				$ncid=$bid . $stage . $name[$i];
				$i++;
				mysql_query("UPDATE class SET id='$ncid'
				WHERE id='$cid'");
				mysql_query("UPDATE tidcid SET class_id='$ncid'
				WHERE class_id='$cid'");
				mysql_query("UPDATE midcid SET class_id='$ncid'
				WHERE class_id='$cid'");
				mysql_query("UPDATE cidsid SET class_id='$ncid'
				WHERE class_id='$cid'");
				}
			}
		}
$result[]='You\'ve been demoised!';
include('scripts/results.php');
?>