<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.15.
 * Time: 11:27
 */
$debug = 1;
		if ($runner->context["allowed"]) {
			?>
			<li class="text-warning"><?= $runner->model->model_class . "/" . $runner->model->table_id ?></li>
		<?php
		}
