#! /usr/bin/php -q
<?php
/** 
 *                                                       admissions_enquiries.php
 * 
 */
$book='admin';
$current='admissions_enquiries.php';

/* The path is passed as a command line argument. */
function arguments($argv){
    $ARGS=array();
    foreach($argv as $arg){
		if(ereg('--([^=]+)=(.*)',$arg,$reg)){
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


if($CFG->email_imap_off=='no'){
	$emailif=new email_imap_fetch();
	$emailif->connect($CFG->email_imap_host, '/pop3:995/ssl', $CFG->email_imap_user, $CFG->email_imap_passwd);
	$emailif->inbox_read();
	}


/*
 * end options: 
 */ 

require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');

exit;

/*
 * end options: 
 */ 


/**
 * Original idea from Ernest Wojciuk's EMAIL_TO_DB script (emailtodb.moldo.pl)
 *
 */

//ini_set('max_execution_time', 3000);
//ini_set('default_socket_timeout', 3000);
//ini_set('memory_limit','512M');
 

class email_imap_fetch{

	var $IMAP_host; //pop3 server
	var $IMAP_port; //pop3 server port
	var $IMAP_login;
	var $IMAP_pass;
	var $link;
	var $error=array();
	var $status;
	var $partsarray=array();
	var $msgid =1; 
	var $newid;
	var $logid;
 
 function connect($host, $port, $login, $pass){

	 $this->IMAP_host=$host;
	 $this->IMAP_login=$login;
  
	 $this->link=imap_open("{". $host . $port."}INBOX", $login, $pass);
	 if($this->link){
		 $this->status='Connected';
		 } 
	 else{
		 $this->error[]=imap_last_error();
		 $this->status='Not connected';
		 }
	 }
 


  /**
   * Flag message
   */
  function email_flag(){
    
	  switch ($char) {
	  case 'S':
		  if (strtolower($flag) == '\\seen') {
			  $msg->is_seen=true;
			  }
		  break;
	  case 'A':
		  if (strtolower($flag) == '\\answered') {
			  $msg->is_answered=true;
			  }
		  break;
	  case 'D':
		  if (strtolower($flag) == '\\deleted') {
			  $msg->is_deleted=true;
			  }
		  break;
	  case 'F':
		  if (strtolower($flag) == '\\flagged') {
			  $msg->is_flagged=true;
			  }
		  break;
	  case 'M':
		  if (strtolower($flag) == '$mdnsent') {
			  $msg->is_mdnsent=true;
			  }
		  break;
	  default:
		  break;
		  }
	  }
  
  /**
   * Parse e-mail structure
   */
  function parsepart($p,$msgid,$i){
   
	  $part=imap_fetchbody($this->link,$msgid,$i);

	  /* Ignore attachments and everything except the email body text */
	  if($p->type==0){
		  /* decode text */
		  if($p->encoding==4){$part=quoted_printable_decode($part);}
		  if($p->encoding==3){$part=base64_decode($part);}
		  /* if plain text or HTML
		  if(strtoupper($p->subtype)=='PLAIN'){1;}
		  elseif(strtoupper($p->subtype)=='HTML'){1;}
		  */
		  $this->partsarray[$i]['text']=array('type'=>$p->subtype,'string'=>$part);
		  }

	  /* iterate over subparts */
	  if(count($p->parts)>0){
		  foreach($p->parts as $pno=>$parr){
			  $this->parsepart($parr,$this->msgid,($i.'.'.($pno+1)));           
			  }
		  }

	  return;
	  }
  


  /**
   * Get email
   */
  function email_get(){
	  $email=array();
	  
	  $header=imap_headerinfo($this->link, $this->msgid, 80,80);
	  $from=$header->from;
	  $udate=$header->udate;
	  $to=$header->to;
	  $size=$header->Size;

	  if($header->Unseen == "U" || $header->Recent == "N"){

		  /* Check is it a multipart messsage */
		  $s=imap_fetchstructure($this->link,$this->msgid);
		  if(count($s->parts)>0){
			  foreach ($s->parts as $partno=>$partarr){
				  //parse parts of email
				  $this->parsepart($partarr,$this->msgid,$partno+1);
				  }
			  } 
		  else{ 
			  $text=imap_body($this->link,$this->msgid);
			  //decode if quoted-printable
			  if ($s->encoding==4) $text=quoted_printable_decode($text);
			  if (strtoupper($s->subtype)=='PLAIN') $text=$text;
			  if (strtoupper($s->subtype)=='HTML') $text=$text;
			  $this->partsarray['not multipart']['text']=array('type'=>$s->subtype,'string'=>$text);
			  }
		  
		  if(is_array($from)){
			  foreach($from as $id => $object){
				  $fromname=$object->personal;
				  $fromaddress=$object->mailbox . "@" . $object->host;
				  }
			  }

		  if(is_array($to)){
			  foreach ($from as $id => $object) {
				  $toaddress=$object->mailbox . "@" . $object->host;
				  }
			  }

		  $email['CHARSET']=$charset;
		  $email['SUBJECT']=$this->mime_text_decode($header->Subject);
		  $email['FROM_NAME']=$this->mime_text_decode($fromname);
		  $email['FROM_EMAIL']=$fromaddress;
		  $email['TO_EMAIL']=$toaddress;
		  $email['DATE']=date("Y-m-d H:i:s",strtotime($header->date));
		  $email['SIZE']=$size;
		  //SECTION - FLAGS
		  $email['FLAG_RECENT']=$header->Recent;
		  $email['FLAG_UNSEEN']=$header->Unseen;
		  $email['FLAG_ANSWERED']=$header->Answered;
		  $email['FLAG_DELETED']=$header->Deleted;
		  $email['FLAG_DRAFT']=$header->Draft;
		  $email['FLAG_FLAGGED']=$header->Flagged;
		  }

	  return $email;
	  }


  
  function mime_text_decode($string){
    
	  $txt='';
	  $string=htmlspecialchars(chop($string));
	  $elements=imap_mime_header_decode($string);
	  if(is_array($elements)){
		  for ($i=0; $i<count($elements); $i++) {
			  $charset=$elements[$i]->charset;
			  $txt .= $elements[$i]->text;
			  }
		  }
	  else{
		  $txt=$string;
		  }


	  return $txt;
	  }


  /**
   * Set flags
   */ 
  function email_setflag(){
    
	  imap_setflag_full($this->link, "2,5","\\Seen \\Flagged"); 
  
	  }

  /**
   * Mark a message for deletion 
   */ 
  function email_delete(){

	  imap_delete($this->link, $this->msgid); 

	  }
  
  /**
   * Delete marked messages 
   */ 
  function email_expunge(){
    
	  imap_expunge($this->link);
  
	  }
  
  
  /**
   * Close IMAP connection
   */ 
  function close(){
	  imap_close($this->link);   
	  }


  /* Read the current email pointed to by this->msgid */
  function email_read(){

	  $email=$this->email_get();
	  foreach($this->partsarray as $part){
		  if($part['text']['type']=='HTML'){
			  xmlreader($part['text']['string']);
			  }
		  elseif($part['text']['type']=='PLAIN'){
			  }
		  }

	  if($email!=''){
		  unset($this->partsarray);
		  }

	  trigger_error('READ: '.$this->msgid,E_USER_WARNING);

	  }

  /**
   * Parse the first 10 emails in the inbox - check if they are for this school
   */
  function inbox_read(){
	  global $CFG;

	  $inbox_count=imap_num_msg($this->link);
	  
	  trigger_error('INBOX COUNT: '.$inbox_count,E_USER_WARNING);

	  if($inbox_count>=1){

		  if($inbox_count>10){
			  $inbox_count=10;
			  }

		  if(!isset($this->msgid) or $this->msgid<=0){
			  $this->msgid=1;
			  }

		  while($this->msgid <= $inbox_count){
			  $header=imap_headerinfo($this->link, $this->msgid, 80,80);
			  $subject=$this->mime_text_decode($header->Subject);
			  if(strpos($subject,$CFG->shortname)>0){

				  trigger_error('INBOX: '.$this->msgid.' '.$subject,E_USER_WARNING);

				  $this->email_read();

				  //$this->email_setflag();
				  //$this->email_delete();
				  //$this->email_expunge();

				  }
			  $this->msgid++;
			  }
		  }
	  }

	/* End of the class */
	}

?>