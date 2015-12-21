<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.09.08.
 * Time: 15:33
 */
require '../../Routerunner/Routerunner.php';
use \Routerunner\Routerunner as runner;

$reference = (isset($_POST['reference']) ? $_POST['reference'] : false);
$history = (isset($_POST['history']) ? $_POST['history'] : false);

new runner(function() use ($reference, $history) {
	$SQL = <<<SQL
SELECT history.model, models.model_class, models.table_from, models.table_id FROM `{PREFIX}history` AS history
LEFT JOIN `{PREFIX}models` AS models ON models.reference = history.reference
WHERE history.id = :id AND history.reference = :reference

SQL;
	$update = array();

	if ($result = \db::query($SQL, array(':id' => $history, ':reference' => $reference))) {
		$model_data = array_shift($result);
		$pk = false;

		if ($model = json_decode($model_data['model'])) {
			$SQL = <<<SQL
SELECT k.column_name
FROM information_schema.table_constraints t
LEFT JOIN information_schema.key_column_usage k
USING (constraint_name,table_schema,table_name)
WHERE t.constraint_type = 'PRIMARY KEY'
    AND t.table_schema = DATABASE()
    AND t.table_name = :table

SQL;
			if ($cols = \db::query($SQL, array(':table' => $model_data['table_from']))) {
				$pk = $cols[0]['column_name'];
			}

			$SQL = <<<SQL
SELECT column_name, is_nullable, data_type, character_maximum_length, column_default,
CASE WHEN column_key LIKE 'PRI%' THEN 1 ELSE 0 END AS primary_key
FROM information_schema.columns
WHERE table_schema = DATABASE() AND table_name = :table

SQL;
			if ($pk && ($cols = \db::query($SQL, array(':table' => $model_data['table_from'])))) {
				$columns = array();
				foreach ($cols as $column) {
					$columns[$column['column_name']] = $column;
				}

				$i = 1;
				$SQL = 'UPDATE `' . $model_data['table_from'] . '` SET ' . PHP_EOL;
				foreach ($model as $field => $value) {
					if (isset($columns[$field])) {
						$column = $columns[$field];

						// check field
						if (true) {
							$SQL .= '`' . $field . '` = :' . $field . (($i < count($model)) ? ', ' : '') . PHP_EOL;
							$update[$field] = $value;
						}
					}
					$i++;
				}
				$SQL .= 'WHERE `' . $pk . '` = :table_id';
				$update[':table_id'] = $model_data['table_id'];
				\db::query($SQL, $update);

				unset($update[':table_id']);
				$SQL = <<<SQL
INSERT INTO `{PREFIX}history` (`reference`, `date`, `user`, `model`) VALUES (:reference, :date, :user, :model)

SQL;
				$params = array(
					':reference' => $reference,
					':date' => time(),
					':user' => 1,
					':model' => json_encode($update),
				);
				\db::query($SQL, $params);
			}
		}
	}
});