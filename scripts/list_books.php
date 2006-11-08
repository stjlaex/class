<?php 
/**									scripts/list_books.php
 *
 * lists only those books available to the chosen role
 */

$books=$CFG->books;

if(!isset($required)){$required='yes';}
if(!isset($user['role'])){$role=$_SESSION['role'];}else{$role=$user['role'];}
?>

  <label for="book"><?php print_string('book');?></label>
  <select name="book" id="book" size="1" tabindex="<?php print $tab++;?>" 
  <?php if($required=='yes'){ print ' class="required" ';} ?>
	>
   	<option value=""></option>
<?php
	$showbooks=$books[$role];
	foreach($showbooks as $key => $bookname){
		print '<option ';
		if(isset($selbook)){if($selbook==$key){print 'selected="selected"';}}
		print	' value="'.$key.'">'.$bookname.'</option>';
		}
?>
  </select>

 





















