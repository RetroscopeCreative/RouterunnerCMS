<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.09.10.
 * Time: 16:00
 */
require '../../Routerunner/Routerunner.php';
use \Routerunner\Routerunner as runner;

$contextid = (isset($_POST['id']) ? $_POST['id'] : false);
$reference = (isset($_POST['reference']) ? $_POST['reference'] : false);

new runner(function() use ($contextid, $reference) {
	$router = false;
	$title = '';
	if ($model = model::load(array('self' => array('reference' => $reference)), false, $router)) {
		$title = '<li><a href="javascript:;" class="label label-primary label-s">';
		$title .= '<span class="label" title="ref: ' . $model->reference
			. ', class: \'' . get_class($model) . '\'">model: \'' . $model->class . '/' . $model->table_id . '\'';
		if (isset($model->label)) {
			$title .= '<br>label: ' . $model->label;
		} elseif (isset($model->name)) {
			$title .= '<br>name: ' . $model->name;
		} elseif (isset($model->title)) {
			$title .= '<br>title: ' . $model->title;
		}
		$title .= '</span></a></li>';
	}

	$draft_history = '';

	$params = array(':reference' => $reference);

	$SQL = 'SELECT COUNT(drafts.id) AS no FROM {PREFIX}drafts AS drafts WHERE drafts.reference = :reference AND drafts.approved IS NULL';
	if (($result = \db::query($SQL, $params)) && $result[0]['no']) {
		$no = $result[0]['no'];
		$draft_history .= <<<HTML
<li><a id="rr_draft_{$contextid}" href="#">
	<i class="fa fa-sitemap"></i> Vázlatok <span class="badge badge-success">{$no} </span></a>
</li>

HTML;

	}

	$SQL = 'SELECT COUNT(history.id) AS no FROM {PREFIX}history AS history WHERE history.reference = :reference';
	if (($result = \db::query($SQL, $params)) && $result[0]['no']) {
		$no = $result[0]['no'];
		$draft_history .= <<<HTML
<li><a id="rr_history_{$contextid}" href="#">
	<i class="fa fa-history"></i> Naplózás <span class="badge badge-success">{$no} </span></a>
</li>

HTML;

	}

	if ($draft_history) {
		$draft_history .= '<li class="divider"></li>' . PHP_EOL;
	}

	echo <<<HTML
{$title}
<li><a id="rr_edit_{$contextid}" href="#"><i class="fa fa-edit"></i> Szerkesztés </a></li>
<li class="divider"></li>
{$draft_history}
<li><a id="rr_delete_{$contextid}" href="#"><i class="fa fa-eraser"></i> Törlés </a></li>
<li class="divider"></li>
<li><a id="rr_close_{$contextid}" href="#"><i class="fa fa-times"></i> Bezárás </a></li>

HTML;
});
?>


