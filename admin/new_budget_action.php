<?php
/**			  					new_budget_action.php
 */

$action='orders.php';

$budgetyear=$_POST['budgetyear'];
$action_post_vars=array('budgetyear');

include('scripts/sub_action.php');

if($sub=='Submit'){

	if(isset($_POST['gid']) and $_POST['gid']!=''){
		$gid_a=$_POST['gid'];
		$catid=0;
		$d_n=mysql_query("SELECT name FROM groups WHERE gid='$gid_a';");
		$name=mysql_result($d_n,0);
		$users_perms=(array)list_group_users_perms($gid_a);
		trigger_error($gid_a.' '.$name,E_USER_WARNING);
		}
	else{
		$users_perms=array();
		$gid_a=0;
		$catid=$_POST['catid'];
		$d_n=mysql_query("SELECT name FROM categorydef WHERE id='$catid';");
		$name=mysql_result($d_n,0);
		}
	$secid=$_POST['secid'];
	$d_section=mysql_query("SELECT name, gid FROM section WHERE id='$secid';");
	$section=mysql_fetch_array($d_section,MYSQL_ASSOC);

	$name.=' - '.$section['name'];
		trigger_error($secid.' '.$name,E_USER_WARNING);
	$yearcode=get_budgetyearcode($budgetyear);
	$Budget=fetchBudget();

	/* Every budget has its own access group - the intial user
	 * permissions are taken from related groups.
	 */
	$d_g=mysql_query("INSERT INTO groups SET type='b';");
	$sec_users_perms=list_group_users_perms($section['gid']);
	$users_perms=$users_perms + $sec_users_perms;
	$gid=mysql_insert_id();
	while(list($uid,$perms)=each($users_perms)){
		update_staff_perms($uid,$gid,$perms);
		}


	$budgetcode=$_POST['code'];
	$d_bud=mysql_query("SELECT id FROM orderbudget 
					WHERE yearcode='$yearcode' AND code='$budgetcode';");
	if(mysql_num_rows($d_bud)>0){
			$error[]='This budget code as already been assigned for the year '. $budgetyear;
		}
	else{
		mysql_query("INSERT INTO orderbudget SET gid='$gid', name='$name',
						section_id='$secid', yearcode='$yearcode';");
		$budid=mysql_insert_id();
		reset($Budget);
		while(list($index,$val)=each($Budget)){
			if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
				$field=$val['field_db'];
				$inname=$field;
				$inval=clean_text($_POST[$inname]);
				if($val['table_db']=='orderbudget'){
					mysql_query("UPDATE orderbudget SET $field='$inval' WHERE id='$budid';");
					}
				}
			}
		$result[]=get_string('newbudgetadded',$book);
		}
	}


include('scripts/results.php');
include('scripts/redirect.php');
?>
