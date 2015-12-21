<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.15.
 * Time: 11:27
 */
//<li><a href="<?=model::header("url")"<?=model::selected><?=model::label</a></li>
//$selected = ($runner->functions['selected_menu']($runner->model->cs_menu_id)) ? ' class="selected"' : '';
//$selected = model::call('selected_menu_class');
?>
<tr class="odd gradeX">
	<td>
		<input type="checkbox" class="checkboxes" value="1"/>
	</td>
	<td class="center">
		<?=model::property("reference")?>
	</td>
	<td>
		<?=model::property("date")?>
	</td>
	<td>
		<?=model::property("user")?>
	</td>
	<td>
		<?=model::property("model")?>
	</td>
	<td>
		<?=model::property("approved")?>
	</td>
	<td>
		<?=model::property("approver")?>
	</td>
</tr>
