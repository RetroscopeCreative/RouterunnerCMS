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

new runner(function() use ($reference) {
	$response = '';

	$SQL = <<<SQL
SELECT drafts.id, drafts.date, user.name AS uname, user.email,
models.model_class, models.table_id, drafts.model
FROM `{PREFIX}drafts` AS drafts
LEFT JOIN `{PREFIX}models` AS models ON models.reference = drafts.reference
LEFT JOIN `{PREFIX}user` AS user ON user.user_id = drafts.user
WHERE drafts.reference = :reference AND drafts.approved IS NULL
ORDER BY drafts.date DESC
SQL;
	$params = array(
		':reference' => $reference,
	);

	$response = <<<HTML
<table class="table table-hover">
	<thead><tr><th>#</th><th>Date</th><th>User</th><th>Model</th><th>Modification</th><th width="100">Action</th></tr></thead>
	<tbody>

HTML;

	if ($result = \db::query($SQL, $params)) {
		$date = strftime('%Y-%m-%d');
		foreach ($result as $row) {
			if ($json = json_decode($row['model'], true)) {
				$model = $row['model_class'] . '/' . $row['table_id'];
				$user = '<a href="mailto:' . $row['email'] . '" target="_blank">' . $row['uname'] . '</a>';
				$modifications = implode(', ', array_keys($json));

				if (strftime('%Y-%m-%d', $row['date']) != $date) {
					$date = strftime('%Y-%m-%d', $row['date']);
					$response .= '		<tr class="bg-grey-steel"><td></td><td colspan="5"><strong>' . $date . '</strong></td></tr>';
				}

				$response .= '		<tr>';
				$response .= '<td>' . $row['id'] . '</td>';
				$response .= '<td>' . strftime('%H:%M:%S', $row['date']) . '</td>';
				$response .= '<td>' . $user . '</td>';
				$response .= '<td>' . $model . '</td>';
				$response .= '<td>' . $modifications . '</td>';

				$response .= '<td>';
				$response .= '<a href="javascript:;" data-draft_id="' . $row["id"] . '" class="load btn btn-xs yellow" title="load modification"><i class="fa fa-eye"></i></a> ';
				$response .= '<a href="javascript:;" data-draft_id="' . $row["id"] . '" class="apply btn btn-xs green" title="apply modification"><i class="fa fa-check"></i></a> ';
				$response .= '<a href="javascript:;" data-draft_id="' . $row["id"] . '" class="delete btn btn-xs red" title="delete modification"><i class="fa fa-times"></i></a>';
				$response .= '</td>';

				$response .= '</tr>' . PHP_EOL;
			}
		}
	} else {
		$response .= '		<tr class="bg-grey-steel"><td colspan="6"><strong>Not found...</strong></td></tr>';
	}

	$response .= <<<HTML
	</tbody>
</table>

HTML;

	echo $response;
});