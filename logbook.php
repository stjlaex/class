<?php
    $host='logbook.php';
    $book='logbook';
    $fresh='';
    include('scripts/head_options.php');
    if(isset($_POST['new_r'])){$_SESSION['r']=$_POST['new_r'];$fresh='yes';}
    if(!isset($_SESSION['r'])){$_SESSION['r']=-1;$fresh='very';}
?>

  <div style="visibility:hidden;" id="hiddenbookoptions"></div>

  <div style="visibility:hidden;" id="hiddenloginlabel">
    <?php print get_teachername($tid);?>
  </div>

  <div style="visibility:hidden;" id="hiddenlogbook">
<!--
      <button id="siteicon" class="show" onClick="loadBook('');" title="<?php print_string('reload');?>" ><img src="images/view-refresh.png" alt="<?php print_string('reload');?>" /></button>
-->
  </div>

<?php
    if($fresh!=''){
        $role=$_SESSION['role'];
        if($_SESSION['senrole']=='1'){$books[$role]['seneeds']='Support';}
        if($_SESSION['medrole']=='1'){$books[$role]['medbook']='Medical';}
        }

    if($fresh=='yes'){
        /* Responsibilities selection has changed
         * (re)loading all the $r dependent Classis books.
         */
        foreach($books[$role] as $bookhost=>$bookname){
            if($bookhost=='markbook' or $bookhost=='reportbook'
               or $bookhost=='admin'){
?>
                <script>parent.loadBook("<?php print $bookhost; ?>")</script>
<?php
                }
            if($bookhost=='markbook'){
                /* Clear everything because the user's current
                 * selection will no longer be avaible.
                 */
                unset($_SESSION['classes']);
                unset($_SESSION['cids']);
                unset($_SESSION['cid']);
                unset($_SESSION['pids']);
                unset($_SESSION['pid']);
                unset($_SESSION['umntype']);
                unset($_SESSION['umnrank']);
                unset($_SESSION['umns']);
                unset($_SESSION['viewtable']);
                }
           }
        }
    elseif($fresh=='very'){
        /* This was loaded after a new login so do some extra stuff:
         * load the externalbooks, booktabs, update langpref, and raise firstbook
         */

        $firstbookpref=$_SESSION['firstbookpref'];

        if($role=='office' or $role=='medical' or $role=='admin' or $role=='library'){
            /* This will prevent session timeouts, making an
             * xmlhttprequest to the logbook/httpscripts/session_alive.php
             * every 15 minutes. But only for select roles.
             */
?>
        <script>setInterval("parent.sessionAlive(pathtobook);",15*60*1000);</script>
<?php
            if($tid=='administrator'){
                check_class_release();
                }
            }

        foreach($books[$role] as $bookhost=>$bookname){
?>
                <script>parent.loadBook("<?php print $bookhost; ?>");</script>
<?php
           }

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

        <script>tabtimer=setTimeout("parent.viewBook('<?php print $firstbookpref; ?>');",1);</script>

    <div style="visibility:hidden;" id="hiddennavtabs">
        <div class="booktabs">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="fa fa-bars"></span>
          </button>
        </div>

            <div class="user-logout">
			  <a id="logbooktab" class="logbook" onclick="logOut();" title="<?php get_string('logout',$book);?>">
				<span class="fa fa-power-off"></span>
			    | <?php print_string('logout');?>
			  </a>
            </div>
			<div class="user-logout">
			  <a class="logbook" target="viewadmin" onclick="parent.viewBook('admin');" href="admin.php?current=staff_details.php&cancel=&choice=staff_list.php&seluid=<?php print $_SESSION['uid'];?>">
				<label id="loginlabel"></label>
			  </a>
			</div>
            <a class="aboutinfo" onclick="openModalWindow('aboutbook.php','');" title="<?php print_string('about');?>">
                <span class="fa fa-info-circle"></span>
            </a>
            <a class="printcontent" onClick="printGenericContent();" title="<?php print_string('print');?>">
            <span class="fa fa-print"></span>
            </a>
            <div id="loginworking">
                <form  id="loginchoice" name="workingas" method="post" action="logbook.php" target="viewlogbook">
                    <select name="new_r" size="1" onChange="document.workingas.submit();">
                        <option value="-1" <?php  if($r==-1){print 'selected="selected" ';} ?>><?php print_string('myclasses');?></option>
<?php
                            foreach($respons as $rindex => $respon){
                                /* Lists the academic responsibilities. */
                                print '<option value="'.$rindex.'"';
                                if(isset($r) and $r==$rindex){print ' selected="selected" ';}
                                print '>'.$respon['name'].'</option>';
	                            }
?>
                    </select>
              </form>
            </div>
                    <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
<?php
			$firsttab='id="currentbook"';
			foreach($showtabs as $bookhost=>$bookname){
?>
			  <li id="<?php print $bookhost.'tab';?>"><a <?php print $firsttab;?> class="<?php print $bookhost;?>" onclick="viewBook(this.getAttribute('class'));"><?php print $bookname;?></a></li>
<?php
						$firsttab='';
                        }
?>
            </ul>
            </div>
        </div>
    </div>
<?php
		update_user_language(current_language());
?>
		<script>parent.logInSuccess();</script>
<?php
		}
    include('scripts/end_options.php');
?>
  </body>
</html>
