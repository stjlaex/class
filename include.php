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
					  ,'reportbook' => 'Report'
					  ,'markbook' => 'MarkBook'
					  ,'register' => 'Register'
					  ,'infobook' => 'InfoBook'
					  ,'entrybook' => 'EntryBook'
					  ,'seneeds' => 'Support'
					  ,'medbook' => 'Medical'
					  //,'library' => 'Library'
				 );
/** 
 * User roles (office, teacher etc.) can have books added or removed
 * to restrict and customise access. Care though is  needed to not
 * break inter-book shortcuts ie. MarkBook always needs access to 
 * Infobook and Admin. The order of the array decides the displayed 
 * Tabs running from right to left.
 * @books Array of book location and the displayed name.
 */
$books['admin']=array(
					  'admin' => 'Admin'
					  ,'reportbook' => 'Report'
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
						'admin' => 'Admin'
						,'infobook' => 'InfoBook'
						,'register' => 'Register'
						);
$books['sen']=array(
					'infobook' => 'InfoBook'
					,'register' => 'Register'
					,'seneeds' => 'Support'
					);
$books['medical']=array(
						'infobook' => 'InfoBook'
						,'register' => 'Register'
						,'medbook' => 'Medical'
						);
$books['district']=array(
						'admin' => 'Admin'
						);
$books['supply']=array(
					   'register' => 'Register'
					   );
$books['library']=array(
						'admin' => 'Admin'
						,'infobook' => 'InfoBook'
						//,'library' => 'Library'
						);
$books['teacher']=array(
						'admin' => 'Admin'
						,'reportbook' => 'Report'
						,'register' => 'Register'
						,'markbook' => 'MarkBook'
						,'infobook' => 'InfoBook'
						);
$externalbooks=array();
$externalbooks['all']=array(
							'lms' => $CFG->lmstabname
							);
$externalbooks['admin']=array(
							  //'lms' => $CFG->lmstabname
							  );
$externalbooks['teacher']=array(
								//'lms' => $CFG->lmstabname
								);
$books['external']=$externalbooks;
?>
