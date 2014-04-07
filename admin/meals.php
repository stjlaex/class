<?php 
/**									transport.php
 *
 * This is the entry page to the Transport book - it lives within Admin
 * but has its own lib/fetch_transport.php functions and is essentialy a
 * set of self-contained scripts.
 *
 *
 */
$action='meals_list.php';
$choice='meals.php';

/* TO DO: add a transport admin group */
$aperm=get_admin_perm('b',get_uid($tid));
$todate=date('Y-m-d');

$extrabuttons=array();
if($_SESSION['username']=='administrator'){
	$extrabuttons['import']=array('name'=>'current','value'=>'meals_import.php');
	}
$extrabuttons['add']=array('name'=>'current','title'=>'editmeals','value'=>'meals_add.php');
$extrabuttons['list']=array('name'=>'current',
							'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
							'value'=>'meals_print.php',
							'xmlcontainerid'=>'list',
							'onclick'=>'checksidsAction(this)');
two_buttonmenu($extrabuttons,'admin');
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />

        <fieldset class="divgroup" id="viewcontent">
            <h5><?php print_string('meals',$book);?></h5>
            <div class="right">
                <?php $required='no'; include('scripts/jsdate-form.php');?>
            </div>
            <div class="chk-height">
                <?php print_string('checkall'); ?>
                <input type="checkbox" name="checkall" value="yes" onChange="checkAll(this,'meals[]');" />
            </div>
            <ul class="chk-list chk-list-alt">
            <?php
                $meals=list_meals();
            		foreach($meals as $meal){
            			if($meal['name']!='NOT LUNCHING'){
            ?>
                        <li>
                            <input type="checkbox" name="meals[]" value="m-<?php print $meal['id']; ?>" />
                            <?php
                            	print ''.$meal['name'].'';
                            ?>
                            <div class="meal-item">
                            <?php
                                /*Does not count past bookings*/
                                $no=count_meals_students($meal['name'],$todate,1);
                                print $no;
                            ?>
                            </div>
                        </li>
            <?php
                }
            	   }
            ?>
            </ul>    
        
    </fieldset>
	<fieldset class="divgroup" id="viewcontent">
	  <h5><?php print get_string('formgroups',$book);?></h5>
		<div class="chk-height">
		  <?php print_string('checkall'); ?>
		  <input type="checkbox" name="checkall" value="yes" onChange="checkAll(this,'formnames[]');" />
		</div>
		<ul class="chk-list">
        <?php
    		$forms=list_formgroups();
    		foreach($forms as $index => $form){
        ?>
            <li>
		      <input type="checkbox" name="formnames[]" value="f-<?php print $form['id']; ?>" />
                <?php
        		  print '<a  href="admin.php?current=meals_list.php&cancel='.$choice.'&choice='.$choice.'&comid='.$form['id'].'">'.$form['name'].'</a>';
                ?>
            </li>
        <?php
        	}
        ?>
        </ul>
	</fieldset>

	<div id="xml-list" style="display:none;">
	  <params>
		<checkname>formnames</checkname>
		<checkname>meals</checkname>
		<selectname>date0</selectname>
		<transform></transform>
		<length>short</length>
		<paper>landscape</paper>
	  </params>
	</div>

  </form>



  </div>

