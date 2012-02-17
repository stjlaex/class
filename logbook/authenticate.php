<?php
/**
 *
 */
function session_defaults(){
	//$_SESSION['logged']=false;
	$_SESSION['uid']=0;
	$_SESSION['username']='';
	$_SESSION['role']='';
	$_SESSION['worklevel']='';
	$_SESSION['cookie']=0;
	$_SESSION['remember']=false;
	$_SESSION['respons']=array();
	$_SESSION['prespons']=array();
	}


/**
 * 
 *  The special array respons is loaded once at login. It holds an
 *  entry for each academic permission group this user belongs to. Numericaly
 *  indexed the logbook variable $r points to the currently active
 *  selection for academic reponsibilities.
 *
 *
 */
function get_respons($uid,$type='%'){
	$groups=array();
	$d_groups=mysql_query("SELECT groups.*, perms.r, perms.w, perms.x
					   FROM groups LEFT JOIN perms ON groups.gid=perms.gid WHERE
					   perms.uid='$uid' AND groups.type LIKE
					   '$type' ORDER BY groups.course_id
					   DESC, groups.yeargroup_id;");
    while($group=mysql_fetch_array($d_groups,MYSQL_ASSOC)){
		/* Setting a -ve silly number for yeargroup distinguishes an
		 * admin group. There is one admin group for each permissions
		 * type. These won't be included in the respons array. 
		 */
		if($group['yeargroup_id']=='' or $group['yeargroup_id']>-9000){
			if($group['type']=='a'){
				if($group['course_id']=='%'){$name=$group['subject_id'];}
				elseif($group['subject_id']=='%'){$name=$group['course_id'];}
				else{$name=$group['course_id'].'/'.$group['subject_id'];}
				$group['name']=$name;
				}
			elseif($group['type']=='p' and $group['community_id']=='0'){
				$yid=$group['yeargroup_id'];
				if($yid!=''){
					$d_y=mysql_query("SELECT name FROM yeargroup WHERE id='$yid';");
					$group['name']=mysql_result($d_y,0);
					$group['comtype']='year';
					}
				else{
					unset($group);
					}
				}
			elseif($group['type']=='p' or $group['type']=='c'){
				$comid=$group['community_id'];
				$d_c=mysql_query("SELECT name, type FROM community WHERE id='$comid';");
				$com=mysql_fetch_array($d_c,MYSQL_ASSOC);
				$group['name']=$com['name'];
				$group['comtype']=$com['type'];
				$group['id']=$comid;
				}
			if(isset($group)){$groups[]=$group;}
			}
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
			$this->_checkSession();
			} 
		elseif(isset($_COOKIE['ClaSSlogin'])){
			$this->_checkRemembered($_COOKIE['ClaSSlogin']);
			}
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
	$this->uid=$values->uid;
	if($remember){
		$this->updateCookie($values->cookie, true);
		}
	if($init){
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
		$_SESSION['respons']=(array)get_respons($this->uid,'a');
		$_SESSION['prespons']=(array)get_respons($this->uid,'p');
		$session=$this->db->quote(session_id());
		$ip=$this->db->quote($_SERVER['REMOTE_ADDR']);
		$sql="UPDATE users SET session=$session, ip=$ip, logcount=logcount+1 WHERE uid=$this->uid";
		$this->db->query($sql);
		}
	}
  function updateCookie($cookie, $save){
	  global $CFG;
	$_SESSION['cookie']=$cookie;
	if($save){
		$cookie=serialize(array($_SESSION['username'], $cookie));
		/*setcookie( name, value, expire, path, domain, secure, httponly);*/
		setcookie('ClaSSlogin', $cookie, time() + 31104000, $CFG->sitepath,'',isset($_SERVER["HTTPS"]),true);
		}
	}
  function _checkRemembered($cookie){
	list($username, $cookie)=@unserialize($cookie);
	if (!$username or !$cookie) return;
		$username=$this->db->quote($username);
		$cookie=$this->db->quote($cookie);
		$sql="SELECT * FROM users WHERE (username=$username) AND (cookie=$cookie)";
		$result=$this->db->getRow($sql);
	if (is_object($result)){
		$this->_setSession($result, true);
		}
	}
  function _checkSession(){
	  global $CFG;
	  $username=$this->db->quote($_SESSION['username']);
	  $cookie=$this->db->quote($_SESSION['cookie']);
	  $session=$this->db->quote(session_id());
	  $ip=$this->db->quote($_SERVER['REMOTE_ADDR']);
	  if(!isset($CFG->ipfixed) or $CFG->ipfixed==true){
		  $sql="SELECT * FROM users WHERE (username=$username) 
				AND (cookie=$cookie) AND (session=$session) AND (ip=$ip)";
		  }
	  else{
		  $sql="SELECT * FROM users WHERE (username=$username) AND (session=$session)";
		  /*
		  $sql="SELECT * FROM users WHERE (username=$username) 
				AND (cookie=$cookie) AND (session=$session)";

		  */
		  }
	  $result=$this->db->getRow($sql);
	  if(is_object($result)){
		  $this->_setSession($result, false, false);} 
	  else{
		  $this->_logout();}
	  }
  function _logout(){
	session_defaults();
	$_SESSION['logged']=false;
	}
}
?>