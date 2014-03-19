<?php
/**									new_budget.php
 *
 */

$action='new_budget_action.php';

if(isset($_GET['budgetyear'])){$budgetyear=$_GET['budgetyear'];}else{$budgetyear='';}
if(isset($_POST['budgetyear'])){$budgetyear=$_POST['budgetyear'];}
if(isset($_GET['budid'])){$budid=$_GET['budid'];}else{$budid='';}
if(isset($_POST['budid'])){$budid=$_POST['budid'];}

three_buttonmenu();

$Budget=fetchBudget();
if($budid!=''){
	$OverBudget=fetchBudget($budid);
	$yearcode=$OverBudget['YearCode']['value'];
	}
?>

    <div id="heading">
        <h4>
            <?php
                if($budid!=''){print $OverBudget['Name']['value'].' - ';}
                print_string('newbudget',$book);
            ?>
        </h4>
    </div>
    <div class="content">
        <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
            <div class="left">
                <fieldset class="divgroup">
                    <h5><?php print_string('budgetscope',$book);?></h5>
                    <?php 
                    if($budid!=''){
                    $selsecid=$OverBudget['Section']['value_db'];
                    	}
                    $d_group=mysql_query("SELECT id, name FROM section ORDER BY sequence;"); 
                    $listname='secid';
                    $listlabel='section';
                    $required='yes';
                    include('scripts/set_list_vars.php');
                    list_select_db($d_group,$listoptions,$book);
                    unset($listoptions);
                    ?>
                </fieldset>
            </div>
            <div class="right">
                <fieldset class="divgroup">
                    <h5><?php print_string('type',$book);?></h5>
                    <?php 
                        /*
                        list($ratingnames,$catdefs)=fetch_categorydefs('bud');
                        $listname='catid';
                        $listlabel='category';
                        $listeitheror='Gid';
                        $required='eitheror';
                        include('scripts/set_list_vars.php');
                        list_select_list($catdefs,$listoptions,$book);
                        unset($listoptions);
                        	*/
                    ?>
                    <div class="center">
                        <?php 
                            /* crid must be % to only grab curriculum subject groups*/
                            $d_group=mysql_query("SELECT gid AS id, subject.name AS name FROM groups 
                            JOIN subject ON subject_id=subject.id 
                            WHERE groups.course_id='%' ORDER BY subject.name;");
                            $listname='gid';
                            $listlabel='department';
                            $listeitheror='Catid';
                            $required='no';
                            include('scripts/set_list_vars.php');
                            list_select_db($d_group,$listoptions,$book);
                            unset($listoptions);
                        ?>
                    </div>
                </fieldset>
            </div>
            <div class="center">
                <fieldset class="divgroup">
                    <label for="<?php print $Budget['Limit']['label'];?>">
                        <?php print_string($Budget['Limit']['label'],$book);?>
                    </label>
                     <?php $tab=xmlelement_input($Budget['Limit'],'',$tab,$book);?>
                    
                    <label for="<?php print $Budget['Code']['label'];?>">
                      <?php print_string($Budget['Code']['label'],$book);?>
                    </label>
                    <?php
                        if($budid!=''){
                        /* Automatically assign a code to sub-budgets */
                        $okay='no';
                        $d_bud=mysql_query("SELECT COUNT(id) FROM orderbudget
                        WHERE overbudget_id='$budid';");
                        $subno=mysql_result($d_bud,0)+1;
                        while($okay=='no'){
                        /*No guarantee these will be kept sequential, budgets could
                        	have been deleted, so check everyone. */
                        $budgetcode=$OverBudget['Code']['value']. $subno;
                        $d_bud=mysql_query("SELECT id FROM orderbudget 
                        WHERE yearcode='$yearcode' AND code='$budgetcode';");
                        if(mysql_num_rows($d_bud)>0){$subno++;}
                        else{$okay='yes';}
                        	}
                        $Budget['Code']['value']=$budgetcode;
                        print '<input  readonly="readonly" name="'. $Budget['Code']['field_db']. 
                        '" value="' .$Budget['Code']['value'].'" >';
                        	}
                        else{
                        	$tab=xmlelement_input($Budget['Code'],'',$tab,$book);
                        }
                    ?>
                </fieldset>
            </div>

    		<input type="hidden" name="budgetyear" value="<?php print $budgetyear;?>" />
    		<input type="hidden" name="budid" value="<?php print $budid;?>" />
    	    <input type="hidden" name="current" value="<?php print $action;?>">
    		<input type="hidden" name="cancel" value="<?php print $choice;?>">
    		<input type="hidden" name="choice" value="<?php print $choice;?>">
        </form>
    </div>