<?php
/**											lib/eportfolio_functions.php
 *
 */


/**
 * Create a directory in the eportfolio_dataroot.
 *
 * @uses $CFG
 * @param string $directory  a string of directory names under
 * $CFG->dataroot eg stuff/assignment/1
 * param boolean $shownotices If true then notification messages will be printed out on error.
 * @return string|false Returns full path to directory if successful, false if not
 *
 */
function make_portfolio_directory($directory,$shownotices=false){
    global $CFG;
	/* File and directory permissions in the $CFG->eportfolio_dataroot */
	if(!isset($CFG->directorypermissions)){
		//$CFG->directorypermissions=0777;
		$CFG->directorypermissions=0755;
		}
	if(!isset($CFG->filepermissions)){
		//$CFG->filepermissions=0666;
		$CFG->filepermissions=0655;
		}

    $currdir=$CFG->eportfolio_dataroot;
    umask(0000);

    $dirarray=explode('/', $directory);

    /* Remove any trailing slash */
	$currdir=rtrim($currdir, '/');
    
    foreach($dirarray as $dir){
        $currdir=$currdir .'/'. $dir;
        if(!file_exists($currdir)){
            if(!mkdir($currdir,$CFG->directorypermissions)){
                if($shownotices){
                    trigger_error('ERROR: Could not find or create a directory ('. $currdir .')',E_USER_WARNING);
					}
                return false;
				}
            //@chmod($currdir, $CFG->directorypermissions);  // Just in case mkdir didn't do it
			}
		}

    return $currdir;
	}



/**
 *
 * Uploads files to a student's epf file space.  The file properties
 * are given by $file=array($name,$description,$title,$foldertype,$batchfiles)
 *
 * Will upload as many files as batchfiles identifies with epfusername
 * and filename, all with the same porperties defined by file.
 *
 * $foldertype is report or work or null for the root folder.
 *
 * @return true|false Returns true on success
 *
 *
 */
function upload_files($filedata){
	global $CFG;
	$success=false;

	$file_title=$filedata['title'];
	$file_time=time();
	/* TODO: set these values... */
	$file_access='';
	$file_size=0;

	/* Identify the folder to be linked with this file. Note this is a
	 * virtual flolder does not affect the physical directory the file
	 * is stored in.
	 */
	if(isset($filedata['foldertype']) and $filedata['foldertype']=='icon'){
		$folder_name='root';
		$dir_name='icons';
		}
	elseif(isset($filedata['foldertype']) and $filedata['foldertype']=='staff'){
		$folder_usertype='u';
		$folder_name='staff';
		$dir_name='files';
		}
	else{
		/* Just defaults to their parent folder. */
		if(!isset($filedata['foldertype'])){
			$folder_name='root';
			$folder_id=-1;
			}
		else{$folder_name=$filedata['foldertype'];}
		$dir_name='files';
		}
	
	if(!isset($folder_usertype)){$folder_usertype='s';}

	$batchfiles=$filedata['batchfiles'];
	foreach($batchfiles as $batchfile){
		$epfusername=$batchfile['epfusername'];
		$file_name=$batchfile['filename'];
		$file_description=$batchfile['description'];
		$file_originalname=$batchfile['originalname'];
		$file_linkedid=0;
		$uid=get_epfuid($epfusername,$folder_usertype);

		if($folder_name!='root'){
			/* Create the virtual folder if it doesn't exist. */
			$folder_id=new_folder($uid,$folder_name);
			}

		$dir=$dir_name . '/' . substr($epfusername,0,1) . '/' . $epfusername; 
		/* Create the physical folder if it doesn't exist. */
		if(!make_portfolio_directory($dir)){
			trigger_error('Could not create eportfolio directory: '.$dir,E_USER_WARNING);
			}
		else{
			$file_fullpath=$CFG->eportfolio_dataroot . '/' . $dir. '/'. $file_name;
			$file_location=$dir . '/'. $file_name;
			$sql_linked='';

			if($filedata['foldertype']=='report'){
				$file_tmppath=$CFG->eportfolio_dataroot.'/cache/reports/'. $file_name;
				}
			elseif($filedata['foldertype']=='icon'){
				$file_tmppath=$CFG->eportfolio_dataroot.'/cache/images/'. $file_name;
				}
			else{
				$file_tmppath=$batchfile['tmpname'];
				if(isset($batchfile['linkedid']) and $batchfile['linkedid']>0){
					$file_linkedid=$batchfile['linkedid'];
		trigger_error($file_linkedid,E_USER_WARNING);
					}
				}

			if($filedata['foldertype']=='icon'){

				/* Keep a copy of the old icon */
				if(file_exists($file_fullpath)){
					$year=get_curriculumyear()-1;
					$file_old=$CFG->eportfolio_dataroot . '/'. $dir .'/'. $epfusername. '-'.$year. '.jpeg';
					rename($file_fullpath,$file_old);
					}

				/* No db record is required for icons. */

				}
			else{

				$sql_linked="AND other_id='$file_linkedid'";

				$d_f=mysql_query("SELECT id FROM file WHERE originalname='$file_originalname' 
											$sql_linked AND owner='$folder_usertype' AND owner_id='$uid';");
				if(mysql_num_rows($d_f)==0){
					$d_f=mysql_query("INSERT INTO file (owner, owner_id, folder_id, title, originalname,
										description, location, access, size, other_id) VALUES 
										('$folder_usertype', '$uid','$folder_id','$file_title','$file_originalname',
										'$file_description','$file_location','$file_access','$file_size','$file_linkedid');");
					}
				else{
					$file_id=mysql_result($d_f,0);
					$d_f=mysql_query("UPDATE file SET (originalname='$file_originalname', 
										title='$file_title', description='$file_description', other_id='$file_linkedid') WHERE id='$file_id';");
					}
				}

			if(rename($file_tmppath,$file_fullpath)){
				trigger_error('Uploaded file to: '.$dir,E_USER_NOTICE);
				// chmod($file_fullpath, $CFG->filepermissions);
				$success=true;
				}
			else{
				trigger_error('Could not move file to eportfolio: '.$file_fullpath,E_USER_WARNING);
				}
			}

		}


	return $success;
	}



/**
 *
 *
 */
function delete_file($filedata){

	$success=false;

	global $CFG;

	$file_id=$filedata['id'];
	
	if($filedata['context']=='icon'){
		//mysql_query("DELETE FROM $table_icons WHERE owner='$epfuid' AND filename='$file_name';");
		$owner=$filedata['owner'];
		$fname=$filedata['fname'];
		$file=$CFG->eportfolio_dataroot.'/icons/' . substr($owner,0,1) . '/' . $owner.'/'.$fname;
		if(unlink($file)) $success=true;
		}
	else{
		mysql_query("DELETE FROM file WHERE id='$file_id';");
            }

 	$flocation=$filedata['flocation'];
	$d_f=mysql_query("SELECT * FROM file WHERE location='$flocation';");
	if(mysql_num_rows($d_f)==1){
		if(unlink($filedata['path'])){
			$success=true;
			}
		else{trigger_error('Could not remove file from eportfolio: '.$filedata['path'],E_USER_WARNING);}
		}
 

	return $success;
	}


/**
 *
 * If the folder already exists then just returns the folder_id
 *
 * If folder doesn't exist and the @access is set then creates new
 * folder and returns its folder_id.
 *
 * This can only create folders in the user's root folder because
 * parent=-1 always.
 *
 *
 *
 */
function new_folder($owner,$name,$access=''){

	if($name=='staff'){
		$folder_usertype='u';
		}
	else{
		$folder_usertype='s';
		}

	$d_folder=mysql_query("SELECT id FROM file_folder WHERE owner='$folder_usertype' AND owner_id='$owner' AND name='$name';");
	if(mysql_num_rows($d_folder)>0){
		$folder_id=mysql_result($d_folder,0);
		}
	elseif($owner!='' and $name!=''){
		$d_f=mysql_query("INSERT INTO file_folder SET  owner='$folder_usertype', owner_id='$owner',
					 name='$name', access='$access', parent_folder_id='-1';");
		$folder_id=mysql_insert_id();
		}
	else{
		$folder_id=-1;
		}

	return $folder_id;
	}


/** 
 *
 * Returns an array of file urls and descriptions for the given $filetype and $owner.
 * The owner is the epfusername.
 *
 * @params string $epfusername of the owner
 * @params string $filetype
 * @params string $linked_id
 *
 */
function list_files($epfun,$foldertype,$linkedid='-1',$bid=''){
	global $CFG;

	$files=array();

	if($foldertype=='staff'){
		$folder_usertype='u';
		}
	else{
		$folder_usertype='s';
		}

	$epfuid=get_epfuid($epfun,$folder_usertype);
	if(strlen($epfuid)<1){$epfuid='-999999';}

	if($foldertype=='icon'){
		/* Just involves listing the directory contents for icons. */
		$foldername='icons';
		$file_extensions=array('jpeg','jpg');
		$directory=$foldername.'/' . substr($epfun,0,1) . '/' . $epfun;
		foreach($file_extensions as $file_extension){
			$dir_files=(array)list_directory_files($CFG->eportfolio_dataroot.'/'.$directory,$file_extension);
			foreach($dir_files as $file){
				$files[]=array('id'=>'',
							   'description'=>$file,
							   'name'=>$file.'.'.$file_extension,
							   'originalname'=>$file.'.'.$file_extension,
							   'path'=>$CFG->eportfolio_dataroot.'/'.$directory.'/'.$file.'.'.$file_extension,
							   'location'=>$directory.'/'.$file.'.'.$file_extension);
				}
			}
		}
	else{
		/* Could be passing both an id and some description from a linked comment. */
		if(is_array($linkedid)){
			$linked_description=$linkedid['detail'];
			$linkedid=$linkedid['id'];
			}

		if($linkedid>0){
			/* Looking only at the files attached to a single entry. */
			$attachment="file.other_id='$linkedid' AND ";
			}
		else{
			/* Looking only at all files dropped in this context. */
			$attachment='';
			}

		if($foldertype=='enrolment'){
			$sharedfiles=" OR (file_folder.name!='$foldertype' AND parent_folder_id!=0); ";
			}
		else{
			$sharedfiles="";
			}

		$d_f=mysql_query("SELECT file.id, title, description, location, originalname, other_id, folder_id,file_folder.parent_folder_id,file_folder.name FROM file 
						JOIN file_folder ON file_folder.id=file.folder_id
						WHERE $attachment file.owner_id='$epfuid' AND file.owner='$folder_usertype' 
						AND file_folder.name='$foldertype' $sharedfiles");
		while($file=mysql_fetch_array($d_f,MYSQL_ASSOC)){
			if($foldertype=='assessment'){
				/*
				 * The other_id is a comment_id and will have a descriptoin from there.
				 */
				$file['description']=$linked_description;
				}
			else{
				$file['description']=$file['description'];
				}
			$file['name']=$file['originalname'];
			$file['path']=$CFG->eportfolio_dataroot.'/'.$file['location'];
			$files[]=$file;
			}
		}

	return $files;
	}



/** 
 *
 *
 * @params string $file_id
 *
 */
function get_filedata($file_id){
	global $CFG;

	$d_f=mysql_query("SELECT file.id, file.title, file.description, file.location, file.originalname, file_folder.owner_id, file_folder.owner, 
						file.folder_id, file_folder.name AS foldertype FROM file 
						JOIN file_folder ON file_folder.id=file.folder_id WHERE file.id='$file_id';");
	$filedata=mysql_fetch_array($d_f,MYSQL_ASSOC);

	$filedata['path']=$CFG->eportfolio_dataroot.'/'.$filedata['location'];

	return $filedata;
	}


/** 
 * Associates a file to another resource identified by the linkedid.
 *
 * @params string $epfun of the owner
 * @params string $foldertype
 * @params string $linkedid
 */
function link_files($epfun,$foldertype,$linkedid){
	global $CFG;

	if($foldertype=='staff'){
		$folder_usertype='u';
		}
	else{
		$folder_usertype='s';
		}

	$epfuid=get_epfuid($epfun,$folder_usertype);

	$folder_id=new_folder($epfuid,$foldertype,$access='');

	mysql_query("UPDATE file SET other_id='$linkedid' 
						WHERE owner_id='$epfuid' AND owner='$folder_usertype' AND file.other_id='0';");

	}


/** 
 *
 * Returns the uid - sid, gid or uid
 * The owner is identified by their epfusername end the type is 's', 'g' or 'u'.
 *
 */
function get_epfuid($epfun,$user_type){

	if($user_type=='s'){
		/* student */
		$d_u=mysql_query("SELECT student_id FROM info WHERE epfusername='$epfun';");
		}
	elseif($user_type=='g'){
		/* guardian */
		$d_u=mysql_query("SELECT id FROM guardian WHERE epfusername='$epfun';");
		}
	elseif($user_type=='u'){
		/* user */
		$d_u=mysql_query("SELECT uid FROM users WHERE epfusername='$epfun';");
		}

	if(isset($d_u) and mysql_num_rows($d_u)==1){
		$uid=mysql_result($d_u,0);
		}
	elseif(substr($epfun,0,7)=='section'){
		/* special situation for a school section which is within the
		   context of staff and owner will be administrator */
		$uid=1;
		}
	else{
		$uid=-1;
		}

	return $uid;
	}

/** 
 *
 * Returns the epfusername
 * The owner is identified by their sid, gid or uid and the type is 's', 'g' or 'u'.
 *
 */
function get_epfusername($id,$user_type){

	if($user_type=='s'){
		/* student */
		$d_u=mysql_query("SELECT epfusername FROM info WHERE student_id='$id';");
		}
	elseif($user_type=='g'){
		/* guardian */
		$d_u=mysql_query("SELECT epfusername FROM guardian WHERE id='$id';");
		}
	elseif($user_type=='u'){
		/* user */
		$d_u=mysql_query("SELECT epfusername FROM users WHERE uid='$id';");
		}

	if(isset($d_u) and mysql_num_rows($d_u)==1){
		$epfusername=mysql_result($d_u,0);
		}
	else{
		$epfusername=-1;
		}

	return $epfusername;
	}


?>
