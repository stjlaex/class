<?php 
/**			   								include.php   
 *
 * Makes available all the books listed in the array. 
 * Any new books should be added here to the allbooks array. 
 * The LogBook and AboutBook are special cases, should not be in this
 * list, and obviously cannot be disabled!
 */
$roles=array('teacher','office','support','sen','medical','library','admin','district');
$books=array();
$books['all']=array(
					  'admin' => 'Admin'
					  ,'reportbook' => 'ReportBook'
					  ,'markbook' => 'MarkBook'
					  ,'register' => 'Register'
					  ,'infobook' => 'InfoBook'
					  ,'entrybook' => 'EntryBook'
					  ,'seneeds' => 'SpecialNeeds'
					  ,'medbook' => 'MedBook'
					  //,'library' => 'Library'
				 );
/** User roles (office, teacher etc.) can have books added or removed
 * to restrict and customise access. Care though is  needed to not
 * break inter-book shortcuts ie. MarkBook always needs access to 
 * Infobook and Admin. The order of the array decides the displayed 
 * Tabs running from right to left.
 * @books Array of book location and the displayed name.
 */
$books['admin']=array(
					  'admin' => 'Admin'
					  ,'reportbook' => 'ReportBook'
					  ,'markbook' => 'MarkBook'
					  ,'register' => 'Register'
					  ,'infobook' => 'InfoBook'
					  ,'entrybook' => 'EntryBook'
					  );
$books['office']=array(
					   'admin' => 'Admin'
					   ,'register' => 'Register'
					   ,'infobook' => 'InfoBook'
					   ,'entrybook' => 'EntryBook'
					   );
$books['support']=array(
						'infobook' => 'InfoBook'
						);
$books['sen']=array(
					'infobook' => 'InfoBook'
					,'seneeds' => 'SpecialNeeds'
					);
$books['medical']=array(
						'infobook' => 'InfoBook'
						,'medbook' => 'MedBook'
						);
$books['district']=array(
						'admin' => 'Admin'
						);
$books['library']=array(
						'infobook' => 'InfoBook'
						//,'library' => 'Library'
						);
$books['teacher']=array(
						'admin' => 'Admin'
						,'reportbook' => 'ReportBook'
						,'register' => 'Register'
						,'markbook' => 'MarkBook'
						,'infobook' => 'InfoBook'
						);
$externalbooks=array();
$externalbooks['all']=array(
							'webmail' => $CFG->webmailtabname
							,'lms' => $CFG->lmstabname
							,'eportfolio' => $CFG->eportfoliotabname
							);
$externalbooks['admin']=array(
							  'webmail' => $CFG->webmailtabname
							  //,'lms' => $CFG->lmstabname
							  //'eportfolio' => $CFG->eportfoliotabname
							  );
$externalbooks['office']=array(
							   'webmail' => $CFG->webmailtabname
							   );
$externalbooks['district']=array(
							   //'webmail' => $CFG->webmailtabname
				 );
$externalbooks['support']=array(
							   'webmail' => $CFG->webmailtabname
				 );
$externalbooks['teacher']=array(
								'webmail' => $CFG->webmailtabname
								//,'lms' => $CFG->lmstabname
								//'eportfolio' => $CFG->eportfoliotabname
								);
$books['external']=$externalbooks;
?>
