<?php
/**
 *											api.php
 *
 *	@package		Classis
 *	@version		0.7
 *	@date		2014-05-28
 *	@author		marius@learningdata.ie
 */

	if(isset($_GET['action']) and $_GET['action']!=''){$action=$_GET['action'];}else{$action='';}
	if(isset($_GET['username']) and $_GET['username']!=''){$username=$_GET['username'];}else{$username='';}
	//if(isset($_GET['clientid']) and $_GET['clientid']!=''){$clientid=$_GET['clientid'];}else{$clientid='';}
	if(isset($_GET['token']) and $_GET['token']!=''){$token=$_GET['token'];}else{$token='';}
	if(isset($_GET['schoolid']) and $_GET['schoolid']!=''){$schoolid=$_GET['schoolid'];}else{$schoolid='';}
	if(isset($_GET['email']) and $_GET['email']!=''){$email=$_GET['email'];}else{$email='';}

	if((($username!='' and $token!='' and $action!='') or ($action=='register' and $email!='')) and $schoolid!=''){
		$postdata=array();
		$params='';
		if(isset($_GET['post']) and $_GET['post']=='true'){
			$script='api_postqueries.php';
			$content=file_get_contents('php://input');
			$postdata=array('data'=>$content);
			}
		else{
			$script='api_getqueries.php';
			foreach($_GET as $paramname=>$paramvalue){
				if($paramname!='action' and $paramname!='username' and $paramname!='password'){
					$postdata[$paramname]=$paramvalue;
					}
				}
			}

		if(isset($_SERVER['HTTP_USER_AGENT'])){$device=$_SERVER['HTTP_USER_AGENT'];}else{$device='';}
		if(isset($_SERVER["REMOTE_ADDR"])){$ip=$_SERVER["REMOTE_ADDR"];}else{$ip='';}
		$postdata['ip']=$ip;
		$postdata['device']=$device;

		$schools=array(
			'demoes'=>'http://demo.learningdata.net/es/classis/classnew',
			'demo'=>'http://demo.learningdata.net/classis/classnew',
			'local'=>'http://192.168.1.237:8080/devclass/classdev2'
			);
		if(isset($schools[$schoolid])){
			$classis_path=$schools[$schoolid];

			if($action=='register'){
				$postdata['classispath']=$classis_path;
				}

			$url=$classis_path.'/admin/httpscripts/'.$script.'?username=' 
					.$username.'&token='.$token.'&action='.$action.$params;

			$curl=curl_init();
			curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
			curl_setopt($curl,CURLOPT_URL,$url);
			curl_setopt($curl,CURLOPT_REFERER,"//".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
			curl_setopt($curl,CURLOPT_POST,1);
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($curl,CURLOPT_POSTFIELDS,$postdata);
			$response=curl_exec($curl);
			curl_close($curl);
			}
		else{
			$response['success']=false;
			$response['errors'][]='Couldn\'t find the school: '.$schoolid;
			$response=json_encode($response);
			}
		}
	else{
		$response['success']=false;
		$response['errors'][]='Invalid request!';
		if($action=='register' and $email==''){$response['errors'][]='Invalid email';}
		$response['errors'][]='Authentication and action needed.';
		$response['errors'][]='Go to http://wiki.classlearning.net/doku.php?id=api for documentation.';
		$response=json_encode($response);
		}

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: Content-Type");
	header("Content-Type: application/json; charset=utf-8"); 
	echo $response;
?>
