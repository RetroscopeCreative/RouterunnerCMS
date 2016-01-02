<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.12.04.
 * Time: 21:33
 */
$debug = 1;
$value = "";
if ($model && isset($model->$field_name)) {
	$value = $model->$field_name;
} elseif (!$value && !$runner->model && isset($fields[$field_name]["default"])) {
	$value = $fields[$field_name]["default"];
}
$help = '';
if (isset($field_data["help"]["panel"])) {
	$help = '<div class="help-block" style="opacity: 1;">' . $field_data["help"]["panel"] . '</div>';
}
?>
<div class="form-group form-md-line-input">
	<select name="<?=$field_name?>" id="property-<?=$field_name?>" class="input form-control <?=isset($field_data["class"]) ? $field_data["class"] : ""?>" <?=isset($field_data["multiple"]) ? $field_data["multiple"] : ""?>>
		<?php
		if (isset($field_data["delimiter"])) {
			$delimiter = $field_data["delimiter"];
			$value = explode($delimiter, $value);
		}
		$options = array();
		if (isset($field_data["options"]) && $field_data["options"]) {
			$options = $field_data["options"];
		} elseif (isset($field_data["SQL"]) && $field_data["SQL"]) {
			if ($result = \db::query($field_data["SQL"])) {
				foreach ($result as $row) {
					$option = array_shift($row);
					$label = array_shift($row);
					$options[$option] = $label;
				}
			}
		}
		if (isset($field_data["default"])) {
			if (!isset($options[key($field_data["default"])])) {
				$options[key($field_data["default"])] = current($field_data["default"]);
				ksort($options);
			}
			if (!$value) {
				$value = key($field_data["default"]);
			}
		}
		$is_sequential = (array_keys($options) === range(0, count($options) - 1));
		foreach ($options as $option => $label) {
			if ($is_sequential) {
				$option = $label;
			}
			if (isset($field_data["delimiter"])) {
				$selected = ((in_array($option, $value)) ? ' selected="selected"' : '');
			} else {
				$selected = ($option == $value ? ' selected="selected"' : '');
			}
			echo '	<option value="' . $option . '"' . $selected . '>' . $label . '</option>'.PHP_EOL;
		}
		?>
	</select>
	<label for="property-<?=$field_name?>"><?=$field_name?></label>
	<?=$help?>
</div>
