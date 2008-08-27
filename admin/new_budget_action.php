<?php
/**			  					new_budget_action.php
 */

$action='orders.php';

$budgetyear=$_POST['budgetyear'];
if(isset($_POST['budid'])){$overbudid=$_POST['budid'];}
else{$overbudid=0;}
$action_post_vars=array('budgetyear');

include('scripts/sub_action.php');

if($sub=='Submit'){

	if(isset($_POST['gid']) and $_POST['gid']!=''){
		/*academic budget*/
		$gid_a=$_POST['gid'];
		$catid=0;
		$d_n=mysql_query("SELECT name FROM groups WHERE gid='$gid_a';");
		$name=mysql_result($d_n,0);
		$users_perms=(array)list_group_users_perms($gid_a);
		$d_n=mysql_query("SELECT subject_id FROM groups WHERE gid='$gid_a';");
		$bid=mysql_result($d_n,0);
		$teacher_users=list_teacher_users($crid='%',$bid);
		}
	else{
		/*general budget*/
		$users_perms=array();
		$teacher_users=array();
		$gid_a=0;
		$catid=$_POST['catid'];
		$d_n=mysql_query("SELECT name FROM categorydef WHERE id='$catid';");
		$name=mysql_result($d_n,0);
		}
	$secid=$_POST['secid'];
	$d_section=mysql_query("SELECT name, gid FROM section WHERE id='$secid';");
	$section=mysql_fetch_array($d_section,MYSQL_ASSOC);

	$name.=' - '.$section['name'];
	if($overbudid!=''){
		/* A subbudget must be for the same budgetyear as its overbudget */
		$OverBudget=fetchBudget($overbudid);
		$yearcode=$OverBudget['YearCode']['value'];
		}
	else{
		$yearcode=get_budgetyearcode($budgetyear);
		}
	$Budget=fetchBudget();


	/* Every budget has its own access group - the intial user
	 * permissions are taken from related groups.
	 *
	 * In a budget context, x is to authorise orders lodged against a budget and 
	 * to configure everything about that budget.
	 */
	$d_g=mysql_query("INSERT INTO groups SET type='b';");
	$gid=mysql_insert_id();
	while(list($tid,$user)=each($teacher_users)){
		$perms=array('r'=>1,'w'=>1,'x'=>0);
		update_staff_perms($user['uid'],$gid,$perms);
		}
	while(list($uid,$perms)=each($users_perms)){
		$perms['x']=0;
		update_staff_perms($uid,$gid,$perms);
		}
	$sec_users_perms=list_group_users_perms($section['gid']);
	while(list($uid,$perms)=each($sec_users_perms)){
		update_staff_perms($uid,$gid,$perms);
		}


	$budgetcode=$_POST['code'];
	$d_bud=mysql_query("SELECT id FROM orderbudget 
					WHERE yearcode='$yearcode' AND code='$budgetcode';");
	if(mysql_num_rows($d_bud)>0){
		$error[]='This budget code has already been assigned for the year '. $budgetyear;
		}
	else{
		mysql_query("INSERT INTO orderbudget SET gid='$gid',
					name='$name', code='$budgetcode',
					yearcode='$yearcode', section_id='$secid', 
					overbudget_id='$overbudid';");
		$budid=mysql_insert_id();
		trigger_error('BUDGET: '.$budid.' '.mysql_error(),E_USER_WARNING);
		trigger_error('BUDGET: '.$budgetcode.' '.$yearcode. ' '.$name,E_USER_WARNING);
		reset($Budget);
		while(list($index,$val)=each($Budget)){
			if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
				$field=$val['field_db'];
				$inname=$field;
				$inval=clean_text($_POST[$inname]);
				if($val['table_db']=='orderbudget' and $field!='name'){
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
