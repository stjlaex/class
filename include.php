<?php 
/**			   								include.php   
 *
 * Makes available all the books listed in the array. 
 * Any new books should be added here to the admin user. The admin
 * role must have ALL books available, always!
 * Other user roles (office, teacher) can have books added or removed
 * to restrict access but admin must have ALL possible books! Care
 * needed to not break inter-book shortcuts ie. MarkBook always needs
 * access to Infobook and Admin.
 * The order of the array decides the displayed Tabs running from right to left.
 * @books Array of book location and the displayed name.
 * A book can be disabled by commenting it out.
 * The LogBook and AboutBook are special cases, should not be in this
 * list, and cannot be disabled!
 */
$roles=array('admin','office','support','teacher');
$books=array();
$books['admin']=array(
					  'admin' => 'Admin'
					  ,'reportbook' => 'ReportBook'
					  ,'markbook' => 'MarkBook'
					  //,'register' => 'Register'
					  ,'infobook' => 'InfoBook'
					  ,'entrybook' => 'EntryBook'
				 );
$books['office']=array(
					   //'register' => 'Register'
					   'infobook' => 'InfoBook'
					   ,'entrybook' => 'EntryBook'
				 );
$books['support']=array(
						'infobook' => 'InfoBook'
				 );
$books['teacher']=array(
						'admin' => 'Admin'
						,'reportbook' => 'ReportBook'
						,'infobook' => 'InfoBook'
						//,'register' => 'Register'
						,'markbook' => 'MarkBook'
				 );
$externalbooks=array();
$externalbooks['admin']=array(
							  //'webmail' => $CFG->webmailtabname
							  //,'lms' => $CFG->lmstabname
							  //'eportfolio' => $CFG->eportfoliotabname
				 );
$externalbooks['office']=array(
							   //'webmail' => $CFG->webmailtabname
				 );
$externalbooks['support']=array(
				 );
$externalbooks['teacher']=array(
								//'webmail' => $CFG->webmailtabname
								//,'lms' => $CFG->lmstabname
								//,'eportfolio' => $CFG->eportfoliotabname
				 );
$books['external']=$externalbooks;
?>
