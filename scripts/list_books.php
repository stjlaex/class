<?php 
/**									scripts/list_books.php
 *
 * lists only those books available to the chosen role
 */

$books=$CFG->books;

if(!isset($required)){$required='yes';}
if(isset($User['Role']['value'])){$role=$User['Role']['value'];}
?>

  <label for="book"><?php print_string('book');?></label>
  <select name="book" id="book" size="1" tabindex="<?php print $tab++;?>" 
  <?php if($required=='yes'){ print ' class="required" ';} ?> >
   	<option value=""></option>
<?php
	$showbooks=$books[$role];
	foreach($showbooks as $index => $bookname){
		print '<option ';
		if(isset($selbook)){if($selbook==$index){print 'selected="selected"';}}
		print	' value="'.$index.'">'.$bookname.'</option>';
		}
?>
  </select>
