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
    <?php print $tid;?>
  </div>

  <div style="visibility:hidden;" id="hiddenlogbook">
    <!--div id="logbookstripe" class="logbook"></div>
    <div id="sidebuttons" class="sidebuttons">
      <button onclick="viewBook('aboutbook');" title="<?php print_string('about');?>"><img src="images/help-browser.png" /></button>
      <button id="sitestatus" class="hide" ><img src="images/roller.gif"/></button>
      <button id="siteicon" class="show" onClick="loadBook('');" title="<?php print_string('reload');?>" ><img src="images/view-refresh.png" alt="<?php print_string('reload');?>" /></button>
      <button onClick="printGenericContent();" title="<?php print_string('print');?>"><img src="images/printer.png" alt="<?php print_string('print');?>" /></button>
    </div-->
  </div>

<?php
    if($fresh!=''){
        $role=$_SESSION['role'];
        if($_SESSION['senrole']=='1'){$books[$role]['seneeds']='Support';}
        if($_SESSION['medrole']=='1'){$books[$role]['medbook']='Medical';}
        }

    if($fresh=='yes'){
        /* Responsibilities selection has changed 
         * (re)loading all the $r dependent ClaSS books.
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

        <script>tabtimer=setTimeout("parent.viewBook('<?php print $firstbookpref; ?>');",1000);</script>

    <div style="visibility:hidden;" id="hiddennavtabs">
        <div class="booktabs">
            <div class="user-logout">
                <a id="logbooktab" class="logbook" onclick="logOut();">
                    <label id="loginlabel"></label> |
                    <span class="fa fa-power-off"></span>
                </a>
            </div>
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
            <a class="aboutinfo" onclick="openModalWindow('','aboutbook.php?subtype=thanks');" title="<?php print_string('about');?>">
                <span class="fa fa-info-circle"></span>
            </a>
            <ul>
                <li id="admintab" style="display:none;"><a id="currentbook" class="admin">Admin</a></li>
                    <?php
                        foreach($showtabs as $bookhost=>$bookname){
                    ?>
                <li id="<?php print $bookhost.'tab';?>"><a class="<?php print $bookhost;?>" onclick="viewBook(this.getAttribute('class'));"><?php print $bookname;?></a></li>
                    <?php
                        }
                    ?>
            </ul>
        </div>
    </div>
<?php
		update_user_language(current_language());
?>
		<script>parent.logInSuccess();</script>
<?php
		$time=date('Y-m-d', strtotime("last Monday"));
		$uid=$_SESSION['uid'];
		$d_page=mysql_query("SELECT COUNT(*) FROM history 
			WHERE time>'$time' AND page='login.php' AND uid='$uid' AND classis_version!='';");
		$count=mysql_result($d_page,0);
		if(isset($CFG->theme20) and $CFG->theme20!="" and $count<=4){
?>
		<script>parent.openModalWindow('','aboutbook.php?subtype=thanks');</script>
<?php
			}
		}
?>
  </body>
</html>
