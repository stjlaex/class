<?php 
/**								staff_list.php
 *
 */

$choice='staff_list.php';
$action='staff_list_action.php';
if(isset($_POST['listsecid'])){$listsecid=$_POST['listsecid'];}else{$listsecid=1;}
if(isset($_POST['listroles'])){$listroles=$_POST['listroles'];}else{$listroles=array();}
if(isset($_POST['listoption'])){$listoption=$_POST['listoption'];}else{$listoption='current';}

/* Super user perms for user accounts. */ 
$aperm=get_admin_perm('u',$_SESSION['uid']);

if($_SESSION['role']=='admin' or $aperm==1 or $_SESSION['role']=='office'){
	$extrabuttons['export']=array('name'=>'current','value'=>'staff_export.php');
	}
two_buttonmenu($extrabuttons);

	$sort_types='';
	$sortno=0;
?>

    <div class="content" id="viewcontent">
            <fieldset class="divgroup">
                <div class="center">
                    <form name="formtoprocess2" id="formtoprocess2" method="post" novalidate action="<?php print $host; ?>">
                        <?php
                            $sections=list_sections();
                            $listlabel='section';
                            $listname='listsecid';
                            include('scripts/set_list_vars.php');
                            list_select_list($sections,$listoptions,$book);
                            unset($listoptions);
                        ?>
            <div style="float:right;">
              <button type="submit" name="sub" value="list">
                <?php print_string('filterlist');?>
              </button>
            </div>
            
            <div class="center">
              <table class="listmenu">
                <tr>
                  <th><?php print get_string('role',$book);?></th>
                </tr>
            <?php
            $userroles=$CFG->roles;
            foreach($userroles as $userrole){
                if(in_array($userrole,$listroles)){$checked=' checked="checked" ';}else{$checked='';}
                print '<tr><td><input type="checkbox" name="listroles[]" '.$checked.' value="'.$userrole.'">'.get_string($userrole).'</input></td></tr>';
                }
            ?>
              </table>
            
            <?php
            if($aperm==1){
            ?>
                <table class="listmenu">
                  <tr>
            <?php
            $options=array(array('id'=>'current','name'=>'current'),array('id'=>'previous','name'=>'previous'));
            foreach($options as $option){
                if($option['id']==$listoption){$checked=' checked="checked" ';}else{$checked='';}
                print '<td><input type="radio" name="listoption" '.$checked.' value="'.$option['id'].'">'.get_string($option['name'],$book).get_string('staff',$book).'</input></td>';
                }
            ?>
                  </tr>
                </table>
            <?php
                }
            ?>
            
            </div>
              <input type="hidden" name="current" value="<?php print $action; ?>">
              <input type="hidden" name="choice" value="<?php print $choice; ?>">
              <input type="hidden" name="cancel" value="<?php print ''; ?>">
              </form>
            </div>
            </fieldset>
        
                            <div class="div-sortable">
                                <a href="#" class="sortable"></a>
                            </div>
  <div class="center">
  <form name="formtoprocess" id="formtoprocess" method="post" novalidate action="<?php print $host; ?>">
  <h5><?php print get_string($listoption,$book).' '.get_string('staff',$book);?></h5>
<table id="sidtable" class="listmenu sidtable">
    <thead>
      <tr>
        <th style="width:1em;">
          <?php print_string('checkall'); ?>
          <input type="checkbox" name="checkall" value="yes" onChange="checkAll(this,'uids[]');" />
        </th>
        <th>
            <div class="div-sortable">
                <span style="display: inline-block; margin-right: 10px;"><?php print_string('surname',$book);?></span>
                <a class="sortable"></a>
            </div>
        </th>
        <th>
            <div class="div-sortable">
                <span style="display: inline-block; margin-right: 10px;"><?php print_string('forename',$book);?></span>
                <a class="sortable"></a>
            </div>
        </th>
        <th>
            <div class="div-sortable">
                <span style="display: inline-block; margin-right: 10px;"><?php print_string('username');?></span>
                <a class="sortable"></a>
            </div>
        </th>
        <th><?php print_string('email',$book);?></th>
      </tr>
    </thead>
<?php


if($aperm==1 and $listoption=='previous'){
    $nologin='1';
    }
else{
    $nologin='0';
    }

if($listsecid>1){
    /* Limit staff list to one section. */
    foreach($sections as $section){
        if($section['id']==$listsecid){$selsection=$section;}
        }
    $users=(array)list_group_users_perms($selsection['gid'],$nologin);
    }
else{
    $users=(array)list_all_users($nologin);
    $selsection=$sections[0];
    }



foreach($users as $user){
    $User=(array)fetchUser($user['uid']);
    if((in_array($user['role'],$listroles) or sizeof($listroles)==0) and $user['username']!='administrator'){
?>
<?php
        if($aperm==1 or $user['uid']==$_SESSION['uid'] or $_SESSION['role']=='office'){
            print '<tr class="clickableRow" data-url="admin.php?current=staff_details.php&cancel='.$choice.'&choice='.$choice.'&seluid='.$user['uid'].'">';
            }
        else{
	  print '<tr>';
            }
?>
      <td>
        <input type="checkbox" name="uids[]" value="<?php print $user['uid'];?>" />
      </td>
      <td><?php print $User['Surname']['value'];?></td>
      <td><?php print $User['Forename']['value'];?></td>
      <td><?php print $User['Username']['value'];?></td>
      <td><?php print $User['EmailAddress']['value'];?></td>
    </tr>
<?php
                }
            }
?>

  </table>

  <input type="hidden" name="current" value="<?php print $action; ?>" />
  <input type="hidden" name="choice" value="<?php print $choice; ?>" />
  <input type="hidden" name="cancel" value="<?php print ''; ?>" />
</form>
</div>


<?php
  /* TO DO: remove document drop permanently from this page (if no one complains!) */
  //            require_once('lib/eportfolio_functions.php');
  //         html_document_drop('section'.$selsection['id'],'staff',$selsection['id']);
?>

  </div>
