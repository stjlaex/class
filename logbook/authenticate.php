<?php
/**
 *
 */
function session_defaults(){
	$_SESSION['logged']=false;
	$_SESSION['uid']=0;
	$_SESSION['username']='';
	$_SESSION['role']='';
	$_SESSION['worklevel']='';
	$_SESSION['cookie']=0;
	$_SESSION['remember']=true;
	}

/**
 *
 *
 */
function getRespons($username,$type='%'){
	$groups=array();
	$d_users=mysql_query("SELECT uid FROM users WHERE username='$username';");
	$uid=mysql_result($d_users,0);
	$d_groups=mysql_query("SELECT groups.*, perms.r, perms.w, perms.x
					   FROM groups LEFT JOIN perms ON groups.gid=perms.gid WHERE
					   perms.uid='$uid' AND groups.type LIKE
					   '$type' AND groups.name!='admin' ORDER BY groups.course_id
					   DESC, groups.yeargroup_id;");
	$c=0;
    while($group=mysql_fetch_array($d_groups, MYSQL_ASSOC)){
		$groups[$c]=$group;
		if($groups[$c]['name']==''){
			$groups[$c]['name']=$groups[$c]['course_id'].$groups[$c]['subject_id'];
			}
		$c++;
		}
	$d_form=mysql_query("SELECT id, yeargroup_id FROM form WHERE
						  teacher_id='$username' ORDER BY yeargroup_id DESC;");
    while($form=mysql_fetch_array($d_form, MYSQL_ASSOC)){
		$groups[$c]=array('name'=>'Form','form_id'=>$form['id'],'r'=>'1','w'=>'1','x'=>'1',
						  'course_id'=>'','subject_id'=>'','type'=>'p',
						  'yeargroup_id'=>$form['yeargroup_id']);
		$c++;
		}
	return $groups;
    }

/**
 *
 */
class User{
	var $db=null;
	var $failed=false;
	var $uid=0;
  function User(&$db){
		$this->db=$db;
		if($_SESSION['logged']){
				$this->_checkSession();} 
		elseif(isset($_COOKIE['ClaSSwebLogin'])){
				$this->_checkRemembered($_COOKIE['ClaSSwebLogin']);}
		}
  function _checkLogin($username, $passwd, $remember) {
	$username=$this->db->quote($username);
	$passwd=$this->db->quote(md5($passwd));
	$sql="SELECT * FROM users WHERE username=$username AND passwd=$passwd AND nologin='0'";
	$result=$this->db->getRow($sql);
	if(is_object($result)){
		$this->_setSession($result, $remember);
		return true;
		}
	else{
		$this->failed=true;
		$this->_logout();
		return false;
		}
	}
  function _setSession(&$values, $remember, $init=true){
    ini_set('session.gc_maxlifetime', 7200);
	$this->uid=$values->uid;
	$_SESSION['uid']=$this->uid;
	$_SESSION['username']=htmlspecialchars($values->username);
	$_SESSION['cookie']=$values->cookie;
	$_SESSION['lang']=$values->language;
	$_SESSION['firstbookpref']=$values->firstbookpref;
	$_SESSION['role']=$values->role;
	$_SESSION['senrole']=$values->senrole;
	$_SESSION['medrole']=$values->medrole;
	$_SESSION['worklevel']=$values->worklevel;
	$_SESSION['logged']=true;
	if($remember){
		$this->updateCookie($values->cookie, true);
		}
	$this->updateSharedCookie($values->cookie, true);
	if($init){
		$session=$this->db->quote(session_id());
		$ip=$this->db->quote($_SERVER['REMOTE_ADDR']);
		$sql="UPDATE users SET session=$session, ip=$ip, 
				logcount=logcount+1 WHERE uid=$this->uid";
		$this->db->query($sql);
		}
	}
  function updateCookie($cookie, $save){
	$_SESSION['cookie']=$cookie;
	if($save){
		$cookie=serialize(array($_SESSION['username'], $cookie) );
		setcookie('ClaSSwebLogin', $cookie, time() + 31104000, '');
		}
	}
  function updateSharedCookie($cookie){
	$cookie=serialize(array($_SESSION['uid'], $_SESSION['lang'], ) );
	setcookie('ClaSSsharedLogin', $cookie);
	}
  function _checkRemembered($cookie){
	list($username, $cookie)=@unserialize($cookie);
	if (!$username or !$cookie) return;
		$username=$this->db->quote($username);
		$cookie=$this->db->quote($cookie);
		$sql="SELECT * FROM users WHERE (username=$username) AND (cookie=$cookie)";
		$result=$this->db->getRow($sql);
	if (is_object($result) ) {
		$this->_setSession($result, true);
		}
	}
  function _checkSession(){
	$username=$this->db->quote($_SESSION['username']);
	$cookie=$this->db->quote($_SESSION['cookie']);
	$session=$this->db->quote(session_id());
	$ip=$this->db->quote($_SERVER['REMOTE_ADDR']);
	$sql="SELECT * FROM users WHERE (username=$username) 
				AND (cookie=$cookie) AND (session=$session) AND (ip=$ip)";
	$result=$this->db->getRow($sql);
	if(is_object($result)){
		$this->_setSession($result, false, false);} 
	else{
		$this->_logout();}
	}
  function _logout(){
	session_defaults();
	}
}
?>