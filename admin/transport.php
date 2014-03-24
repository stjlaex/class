<?php 
/**									transport.php
 *
 * This is the entry page to the Transport book - it lives within Admin
 * but has its own lib/fetch_transport.php functions and is essentialy a
 * set of self-contained scripts.
 *
 *
 */

$choice='transport.php';
$action='transport_action.php';

/* TO DO: add a transport admin group */
$aperm=get_admin_perm('b',get_uid($tid));
$todate=date('Y-m-d');

$extrabuttons=array();
if($_SESSION['username']=='administrator'){
	//$extrabuttons['newbus']=array('name'=>'current','value'=>'new_bus.php');
	$extrabuttons['import']=array('name'=>'current','value'=>'transport_import.php');
	}
if($_SESSION['role']=='admin' or $aperm==1 or $_SESSION['role']=='office'){
	//$extrabuttons['export']=array('name'=>'current','value'=>'transport_export.php');
	$extrabuttons['route']=array('name'=>'current',
								   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
								   'value'=>'transport_route_print.php',
								   'xmlcontainerid'=>'route',
								   'onclick'=>'checksidsAction(this)');
	}

$extrabuttons['list']=array('name'=>'current',
							'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
							'value'=>'transport_print.php',
							'xmlcontainerid'=>'list',
							'onclick'=>'checksidsAction(this)');
$extrabuttons['attendance']=array('name'=>'current',
								  'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
								  'value'=>'transport_print.php',
								  'xmlcontainerid'=>'attendance',
								  'onclick'=>'checksidsAction(this)');
$extrabuttons['morning']=array('name'=>'current',
							   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
							   'value'=>'transport_print.php',
							   'xmlcontainerid'=>'listin',
							   'onclick'=>'checksidsAction(this)');
$extrabuttons['afternoon']=array('name'=>'current',
								 'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
								 'value'=>'transport_print.php',
								 'xmlcontainerid'=>'listout',
								 'onclick'=>'checksidsAction(this)');
$extrabuttons['changes']=array('name'=>'current',
									   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
									   'value'=>'transport_print.php',
									   'xmlcontainerid'=>'changes',
									   'onclick'=>'checksidsAction(this)');
two_buttonmenu($extrabuttons);
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
        <fieldset class="divgroup" id="viewcontent">
        	  <h5><?php print_string('transport',$book);?></h5>
                  <div class="right">
                    <?php $required='no'; include('scripts/jsdate-form.php');?>
                  </div>
                 <div class="chk-height">
        		  <?php print_string('checkall'); ?>
        		  <input type="checkbox" name="checkall" value="yes" onChange="checkAll(this,'busnames[]');" />
        		</div>
                <ul class="chk-list chk-list-alt">   
                <?php
                    $busnames=list_busnames();
                    foreach($busnames as $busname){
                ?>
                    <li>
                        <input type="checkbox" name="busnames[]" value="b-<?php print $busname['name']; ?>" />
                        <?php
                            print '<a  href="admin.php?current=transport_list.php&cancel='.$choice.'&choice='.$choice.'&busname='.$busname['name'].'">'.$busname['name'].'</a>';
                        ?>
                        <div class="bus-route"> 
                        <?php
                        	$noi=count_bus_journey_students($busname['name'],'I',$todate,1);
                        	$noo=count_bus_journey_students($busname['name'],'O',$todate,1);
                        	print ' AM:'.$noi. ' PM:' .$noo;
                        ?>
                        </div>
                    </li>
                <?php
                    }
                ?>
            </ul>
    	</fieldset>
	
        <fieldset class="divgroup" id="viewcontent">
        <h5><?php print get_string('formgroups',$book);?></h5>
    		<div style="height: 30px;">
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
                        print '<a  href="admin.php?current=transport_list.php&cancel='.$choice.'&choice='.$choice.'&comid='.$form['id'].'">'.$form['name'].'</a>';
                    ?>
                </li>
            <?php
                }
            ?>
            </ul>
       </fieldset>
    	<div id="xml-listin" style="display:none;">
    	  <params>
    		<checkname>busnames</checkname>
    		<checkname>formnames</checkname>
    		<selectname>date0</selectname>
    		<length>full</length>
    		<transform>transport_list_in</transform>
    		<paper>landscape</paper>
    	  </params>
    	</div>
    	<div id="xml-listout" style="display:none;">
    	  <params>
    		<checkname>busnames</checkname>
    		<checkname>formnames</checkname>
    		<selectname>date0</selectname>
    		<length>full</length>
    		<transform>transport_list_out</transform>
    		<paper>landscape</paper>
    	  </params>
    	</div>
    	<div id="xml-changes" style="display:none;">
    	  <params>
    		<checkname>busnames</checkname>
    		<checkname>formnames</checkname>
    		<selectname>date0</selectname>
    		<length>full</length>
    		<transform>transport_list_changes</transform>
    		<paper>landscape</paper>
    	  </params>
    	</div>
    	<div id="xml-attendance" style="display:none;">
    	  <params>
    		<checkname>busnames</checkname>
    		<checkname>formnames</checkname>
    		<selectname>date0</selectname>
    		<length>short</length>
    		<transform>transport_list_attendance</transform>
    		<paper>landscape</paper>
    	  </params>
    	</div>
    	<div id="xml-list" style="display:none;">
    	  <params>
    		<checkname>busnames</checkname>
    		<checkname>formnames</checkname>
    		<selectname>date0</selectname>
    		<length>short</length>
    		<transform>transport_list</transform>
    		<paper>landscape</paper>
    	  </params>
    	</div>
    	<div id="xml-route" style="display:none;">
    	  <params>
    		<checkname>busnames</checkname>
    		<transform>transport_route</transform>
    		<paper>portrait</paper>
    	  </params>
    	</div>
    </form>
    </div>

