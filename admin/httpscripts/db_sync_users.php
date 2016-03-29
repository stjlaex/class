#! /usr/bin/php -q
<?php
/**
 *												 db_sync_users.php
 *
 * It merely ensures all staff, students and guardians have unique epfusernames.
 *
 */ 
$book='admin';

/* The path is passed as a command line argument. */
function arguments($argv){
    $ARGS=array();
    foreach($argv as $arg){
		if (ereg('--([^=]+)=(.*)',$arg,$reg)){
			$ARGS[$reg[1]]=$reg[2];
			} 
		elseif(ereg('-([a-zA-Z0-9])',$arg,$reg)){
            $ARGS[$reg[1]]='true';
			}
		}
	return $ARGS;
	}
$ARGS=arguments($_SERVER['argv']);
require_once($ARGS['path'].'/school.php');
require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_head_options.php');




	$info=array();
	$row=array();
	/* This is the default password for students and parents. */
	$firstpass=$CFG->clientid.'1234';

		/**
		 *	STEP 1: Process all users (teachers) from ClaSS
		 *	
		 */
		$users=list_all_users();
		
		/* process result */
		$entries=0.0;
		foreach($users as $uid => $row) {
			$info=array();
			$Newuser=(array)fetchUser_short($row);
			$epfusername=$Newuser['EPFUsername']['value'];
			if($epfusername=='' or $epfusername==' '){

				$epfusername=new_epfusername($Newuser,'staff');

				}
			$entries++;
			}
		trigger_error('Step 1: '.$entries.' User entries have been processed', E_USER_NOTICE);


		/**
		 * STEP 2: Process all Students from Classis DB
		 *
		 */
		$yearcoms=array();
		$Students=array();
		$yearcoms=(array)list_communities('year');
		$entries=0.0;
		while(list($yearindex,$com)=each($yearcoms)){
			$yid=$com['name'];
			$students=listin_community($com);
			while(list($studentindex,$student)=each($students)){
				$sid=$student['id'];
				$Students[$sid]=fetchStudent_short($sid);
				$Email=fetchStudent_singlefield($sid,'EmailAddress');
				$EnrolNumber=fetchStudent_singlefield($sid,'EnrolNumber');
				$EPFUsername=fetchStudent_singlefield($sid,'EPFUsername');
				$Students[$sid]['EmailAddress']['value']=$Email['EmailAddress']['value'];
				$Students[$sid]['EPFUsername']['value']=$EPFUsername['EPFUsername']['value'];

				$epfusername=$Students[$sid]['EPFUsername']['value'];
				if($epfusername=='' or $epfusername==' '){
					/* Treat as a completely new entry. */
					$epfusername=new_epfusername($Students[$sid],'student');
					}

				$entries++;
				}
			}

		trigger_error('Step 2: '.$entries.' Student entries have been processed', E_USER_NOTICE);


		/**
		 * STEP 3: Process all Contacts from Classis DB
		 *
		 *
		 * Want all contacts who may recieve any sort of mailing to be
		 * given an account.
		 */

		$Contacts=array();
		$entries=0;
		$d_c=mysql_query("SELECT DISTINCT guardian_id FROM gidsid JOIN
   					student ON gidsid.student_id=student.id 
   					WHERE gidsid.mailing!='0';");
		while($contact=mysql_fetch_array($d_c,MYSQL_ASSOC)){
			$gid=$contact['guardian_id'];
			$Contacts[$gid]=fetchContact(array('guardian_id'=>$gid));
			
			if($Contacts[$gid]['Surname']['value']!='' and $Contacts[$gid]['Surname']['value']!=' '){
				$epfusername=$Contacts[$gid]['EPFUsername']['value'];
				$email=$Contacts[$gid]['EmailAddress']['value'];
				if($epfusername=='' or $epfusername==' '){
					/* Treat as a completely new entry. */

					/* Check for duplicate email addresses first
					$sr=mysql_query("SELECT epfusername FROM guardian WHERE email='$email' 
											AND id!='$gid' AND epfusername!='' AND email!='';");
					if(mysql_num_rows($sr)>1){
						$epfusername=mysql_result($sr,0);
						}
						else{
						}
					*/
					$epfusername=new_epfusername($Contacts[$gid],'contact');

					}
				/* entry counter */
				$entries++;
				}
			}


		trigger_error('Step 3: '.$entries.' Contact entries have been processed', E_USER_NOTICE);


require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');

?>
