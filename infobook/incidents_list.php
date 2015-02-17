<?php
/**                                  incidents_list.php    
 */

$current='incidents_list.php';
$action='incidents_list_action.php';

if(isset($_GET['bid'])){$bid=$_GET['bid'];}

$secid=get_student_section($sid);

three_buttonmenu();
?>
    <div id="heading">
        <h4>
            <label><?php print_string('incidents');?></label>
            <?php
            	print $Student['DisplayFullName']['value'].' ';
            	print '('.$Student['RegistrationGroup']['value'].')';
            ?>
        </h4>
    </div>
    <div id="viewcontent" class="content">
        <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
            <div id="formstatus-new" class="">
                <?php print_string('recordnewincident',$book);?>
            </div>
            <div class="left">
                <div id="formstatus-edit" class="hidden">
                    <?php print_string('editincident',$book);?>
                </div>
                <div id="formstatus-action" class="hidden">
                    <?php print_string('newaction',$book);?>
                </div>
            </div>
            <div class="right">
                <?php $listlabel='incidentstatus'; $required='yes'; $listname='closed';
                    include('scripts/set_list_vars.php');
                    list_select_enum('closed',$listoptions,'infobook');
                ?>
            </div>
            <div class="left">
                <label for="Detail"><?php print_string('details',$book);?></label>
                <textarea name="detail"   tabindex="<?php print $tab++;?>" class="required" id="Detail" rows="5" cols="35"></textarea>
            </div>
            <div class="right" >
                <?php $xmldate='Entrydate'; include('scripts/jsdate-form.php'); ?>
            </div>
            <div class="right">
<?php 
                    $listlabel='sanction'; 
                    $listid='sanction';$catsecid=$secid;$cattype='inc';
                    $required='yes'; include('scripts/list_category.php');
?>
            </div>
            <div class="right">
                <!--label for="Subject"><?php print_string('subjectspecific');?></label-->
<?php
                    $required='no'; $listname='bid'; $listid='subject';$listlabel='subjectspecific';
                    $subjects=list_student_subjects($sid);
                    include('scripts/set_list_vars.php');
                    list_select_list($subjects,$listoptions,$book);
                    unset($listoptions);
?>
            </div>
            <input type="text" style="display:none;" id="Id_db" name="id_db" value="" />
            <input type="text" style="display:none;" id="No_db" name="no_db" value="" />
            <input type="hidden" name="current" value="<?php print $action;?>" />
            <input type="hidden" name="cancel" value="<?php print 'student_view.php';?>" />
            <input type="hidden" name="choice" value="<?php print $choice;?>" />
        </form>

  
	<div class="center">
	  <table class="listmenu">
		<caption><?php print_string('existingincidents',$book);?></caption>
		<thead>
		  <tr>
			<th></th>
			<th><?php print_string('yeargroup');?></th>
			<th><?php print_string('date');?></th>
			<th><?php print_string('subject');?></th>
			<th><?php print_string('status');?></th>
		  </tr>
		</thead>
<?php
	$Incidents=(array)fetchIncidents($sid);
   	$yid=$Student['YearGroup']['value'];
	$perm=getYearPerm($yid, $respons);
	if(is_array($Incidents['Incident'])){
		reset($Incidents['Incident']);
		while(list($key,$Incident)=each($Incidents['Incident'])){
			if(is_array($Incident)){
				$rown=0;
				$entryno=$Incident['id_db'];
		if($Incident['Closed']['value']=='N'){$styleclass=' ';}
		else{$styleclass='';}
?>
		<tbody id="<?php print $entryno;?>">
		  <tr class="rowplus" onClick="clickToReveal(this)" 
							id="<?php print $entryno.'-'.$rown++;?>">
			<th>&nbsp</th>
			<td><?php print $Incident['YearGroup']['value'];?></td>
			<td><?php print $Incident['EntryDate']['value'];?></td>
			<td><?php print $Incident['Subject']['value'];?></td>
			<td	<?php print $styleclass;?>>&nbsp 
			  <?php if($Incident['Closed']['value']=='N'){print_string('open');}?>
			</td>
		  </tr>
		  <tr class="hidden" id="<?php print $entryno.'-'.$rown++;?>">
			<td colspan="5">
			  <p>
				<?php if(isset($Incident['Detail']['value'])){print $Incident['Detail']['value'];}?>
				<?php if(isset($Incident['Teacher']['value'])){print
				'  - '.$Incident['Teacher']['value'];}?>
			  </p>
			  <p>
				<?php print get_string('sanction','infobook').': '.$Incident['Sanction']['value'];?>
			  </p>
			  <button class="rowaction" title="Delete this incident" id="delete<?php print $entryno;?>" name="current" value="delete_incident.php" onClick="clickToAction(this)">
				  <span class="clicktodelete"></span>
			  </button>
			  <button class="rowaction" title="Edit" name="Edit" id="edit<?php print $entryno;?>" onClick="clickToAction(this)">
				  <span class="clicktoedit"></span>
			  </button>
			</td>
		  </tr>
<?php
			 if(is_array($Incident['Actions']['Action'])){
				 reset($Incident['Actions']['Action']);
				while(list($index,$Action)=each($Incident['Actions']['Action'])){
?>
		  <tr class="hidden" id="<?php print $entryno.'-'.$rown++;?>">
			<td colspan="5">
			  <p>
				<?php print $Action['Comment']['value'];?>
				<?php print '('.$Action['EntryDate']['value'].' - ';?>
				<?php print $Action['Teacher']['value'].')';?>
			  </p>
			  <p>
				<?php print get_string('sanction','infobook').': '.$Action['Sanction']['value'];?>
			  </p>
			  <button class="rowaction" title="Edit" name="Edit" onClick="clickToAction(this)">
				  <span class="clicktoedit"></span>
			  </button>
			</td>
		  </tr>
<?php
					}
				 }
			if($Incident['Closed']['value']=='N'){
?>
		  <tr id="<?php print $entryno.'-'.$rown++;?>">
			<td colspan="5" <?php print $styleclass;?>>
			  <button class="rowaction" title="New action"name="New" onClick="clickToAction(this)">
				<?php print_string('newaction',$book);?>
			  </button>
			</td>
		  </tr>
<?php
				}
?>
		  <div id="<?php print 'xml-'.$entryno;?>" style="display:none;">
<?php
				xmlechoer('Incident',$Incident);
?>
		  </div>
		</tbody>
<?php
			}
		}
	}
?>
	  </table>
	</div>
  </div>
