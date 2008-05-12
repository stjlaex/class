<?php
	$host='logbook.php';
	$book='logbook';
	$fresh='';
	include('scripts/head_options.php');
?>
  <div style="visibility:hidden;" id="hiddenbookoptions">	
  </div>
  <div style="visibility:hidden;" id="hiddenloginlabel">
	<?php print $tid;?>
  </div>
  <div style="visibility:hidden;" id="hiddenlogbook">
	<div id="logbookstripe" class="logbook"></div>
	<div id="sitelogo">
			<img onclick="viewBook('aboutbook')"
			  name="sitelogo" src="images/orangelogo.png" />
	</div>
	<div id="sitestatus" class="fixed">
	  <img name="sitelogo" src="images/roller.gif"/>
	</div>
	<div id="loginworking">
	<form  id="loginchoice" name="workingas" method="post" action="logbook.php" target="viewlogbook">
	  <select name="new_r" size="1" onChange="document.workingas.submit();">
		<option value="-1" 
<?php  if($r==-1){print 'selected="selected" ';} ?>
		  ><?php print_string('myclasses');?></option>
<?php 
    for($c=0;$c<(sizeof($respons));$c++){
		/*only lists the academic responsibilities*/
		if($respons[$c]['type']=='a'){
			print '<option value="'.$c.'"';
			if(isset($r) and $r==$c){print ' selected="selected" ';}
			print '>'.$respons[$c]['name'].'</option>';
			}
		}
?>
	  </select>
	</form>
	</div>
	<div id="sidebuttons">
	  <button onClick="loadBook('');"  title="<?php print_string('reload');?>">
		<img src="images/helper.png" alt="<?php print_string('reload');?>" /></button>
	  <button onClick="printGenericContent();" title="<?php print_string('print');?>">
		<img src="images/printer.png" alt="<?php print_string('print');?>" /></button>
	</div>
  </div>

<?php
	if($fresh!=''){
		$role=$_SESSION['role'];
		if($role=='office'){
			/* This will prevent session timeouts, making an
			 * xmlhttprequest to the logbook/httpscripts/session_alive.php 
			 * every 15 minutes. But only for office users.
			 */
?>
		<script>setInterval("parent.sessionAlive(pathtobook);",15*60*1000);</script>
<?php
			}
		if($_SESSION['senrole']=='1'){$books[$role]['seneeds']='SEN';}
		foreach($books[$role] as $bookhost=>$bookname){
			/*(re)loading all the ClaSS books*/
?>
			<script>parent.loadBook("<?php print $bookhost; ?>")</script>
<?php
		   }
	   }
	if($fresh=='very'){
		/*this was loaded after a new login so do some extra stuff:*/
		/*load the externalbooks, booktabs, update langpref, and raise firstbook*/

		$externalbooks=array();
		if(isset($books['external'][$role])){$externalbooks[$role]=$books['external'][$role];}
		else{$externalbooks[$role]=array();}
		foreach($externalbooks[$role] as $bookhost=>$bookname){
			/*loading all the external books - only needed once*/
?>
			<script>parent.loadBook("<?php print $bookhost; ?>")</script>
<?php
		   }

		$showtabs=$books[$role]+$externalbooks[$role];
?>
			<script>parent.loadBook("aboutbook")</script>

  <div style="visibility:hidden;" id="hiddennavtabs">
	<div class="booktabs">
	  <ul>
		<label id="loginlabel">
		</label>
		<li id="logbooktab"><p class="logbook" onclick="logOut();">LogOut</p></li>
		<li id="aboutbooktab" style="display:none;"><p id="currentbook" class="aboutbook">About</p>
		</li>
<?php
		foreach($showtabs as $bookhost=>$bookname){
?>
		<li id="<?php print $bookhost.'tab';?>"><p class="<?php print $bookhost;?>"
		onclick="viewBook(this.getAttribute('class'))"><?php print $bookname;?></p></li>
<?php
			}
?>
	  </ul>
	</div>
  </div>
<?php
		$firstbookpref=$_SESSION['firstbookpref'];
		update_user_language(current_language());
?>
		<script>parent.logInSuccess();</script>
		<script>setTimeout("parent.viewBook('<?php print $firstbookpref; ?>');",5000);</script>
<?php
		}
?>
</body>
</html>
