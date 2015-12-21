<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.05.08.
 * Time: 8:52
 */
 
 ?>

<div class="form-group form-md-line-input input-data-holder <?=input("class")?>" data-routerunner-input='<?=input("data")?>'>
	<select class="pageprop-input form-control" id="<?=input("input-id")?>" name="<?=input("field")?>">
		<?php
		$value = input("value");
		$options = input("options");
		if (!is_array($options)) {
			$options = json_decode($options,true);
		}
		if (is_array($options)) {
			foreach ($options as $key => $label) {
				$selected = ($value == $key)
					? ' selected="selected"' : '';
				echo '	<option value="' . $key . '"' . $selected . '>' . $label . '</option>' . PHP_EOL;
			}
		}
		?>
	</select>
	<label for="<?=input("input-id")?>"<?=input("label")?></label>
	<?=input("help")?>
</div>
