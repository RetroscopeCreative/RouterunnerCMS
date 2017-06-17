<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.15.
 * Time: 11:27
 */

$allowed = true;
$model = null;
if (\runner::stack("models_created") &&
	isset($runner->context["reference"], \runner::stack("models_created")[$runner->context["reference"]])) {
	$model_data = \runner::stack("models_created")[$runner->context["reference"]];

	$model = new \Routerunner\BaseModel($model_data['route'], $model_data);
	$model->permission = $model_data['permission'];

	if ($model && is_object($model) && $model->permission && !$model->activate_allowed()) {
		$allowed = false;
	} elseif ($model && !is_object($model)) {
		$allowed = false;
	}
	if ($allowed) {
		$context = array(
			"direct" => $runner->context["reference"],
			"session" => \runner::stack("session_id"),
			"silent" => true,
		);

		$model_route = "/model/" . $runner->context["model_class"];
		\runner::redirect_route($model_route, \runner::config("scaffold"), true, $context, $router, $model);
		if (is_array($model)) {
			$model = array_shift($model);
		}

		$runner->context["model"] = $model;
	}
} elseif (isset($runner->context["reference"], $runner->context["model_class"])) {
	$router = false;
	$context = array(
		"direct" => $runner->context["reference"],
		"session" => \runner::stack("session_id"),
		"silent" => true,
	);

	$model_route = "/model/" . $runner->context["model_class"];
	\runner::redirect_route($model_route, \runner::config("scaffold"), true, $context, $router, $model);
	if (is_array($model)) {
		$model = array_shift($model);
	}

	$runner->context["model"] = $model;

	if ($model && $model->permission && !$model->activate_allowed()) {
		$allowed = false;
	}
}



$reference = model::property("model");
$data = ' data-reference="' . $reference . '"';

?>

<div class="portlet light">
	<div class="portlet-title">
		<div class="caption font-red col-md-3">
			<i class="fa fa-eye-slash font-red"></i>
			<span class="caption-subject bold uppercase"> Visibility</span>
		</div>
		<?php
		if ($allowed) {
			$active = (!is_null(model::state("active", $model)) ? model::state("active", $model) :
                (isset($model_data['states']['active']) ? $model_data['states']['active'] : true));
			$begin = (model::state("begin", $model) ? filter_var(model::state("begin", $model), FILTER_VALIDATE_INT) : null);
			$end = (model::state("end", $model) ? filter_var(model::state("end", $model), FILTER_VALIDATE_INT) : null);
			?>
			<div class="col-md-9">
				<div class="btn-group btn-group-circle pull-right">
					<button type="button" class="btn btn-info active" data-section="visibility-simple"
							data-enable="#routerunner-visibility .simple"><i class="fa fa-check-circle-o"></i> Simple
					</button>
					<button type="button" class="btn btn-info" data-section="visibility-date"
							data-enable="#routerunner-visibility .date"><i class="fa fa-clock-o"></i> Date
					</button>
					<?php
					/*
					<button type="button" class="btn btn-info" data-section="visibility-user"
							data-enable="#routerunner-visibility .user"><i class="fa fa-users"></i> User
					</button>
					<div class="btn-group">
						<button type="button" class="btn btn-info btn-circle-right dropdown-toggle" data-toggle="dropdown">
							<i class="fa fa-ellipsis-horizontal"></i> More <i class="fa fa-angle-down"></i>
						</button>
						<ul class="dropdown-menu">
							<li>
								<a href="javascript:;">
									Dropdown link </a>
							</li>
							<li>
								<a href="javascript:;">
									Dropdown link </a>
							</li>
						</ul>
					</div>
					*/
					?>
				</div>
			</div>
		<?php
		}
		?>
	</div>
	<div class="portlet-body form">
		<form name="routerunner-visibility" id="routerunner-visibility" role="form" class="form-horizontal"<?=$data?>>
			<div class="form-body">
	<?php
	if ($allowed) {
		$active_check = ($active ? " checked" : "");
		$begin_check = ((!is_null($begin) && $begin) ? " checked" : "");
		$begin_value = ($begin ? strftime("%Y.%m.%d. %H:%M", $begin) : "");
		$end_check = ((!is_null($end) && $end) ? " checked" : "");
		$end_value = ($end ? strftime("%Y.%m.%d. %H:%M", $end) : "");
		?>
		<div id="visibility-simple" class="form-section simple well">
			<div class="form-group">
				<label class="col-md-2">Simple visibility</label>

				<div class="col-md-10">
					<input type="checkbox" name="active" id="simple_active"
						   class="make-switch form-control need-to-disable" data-on-text="&nbsp;Visible&nbsp;"
						   data-off-text="&nbsp;Hidden&nbsp;" value="1" <?= $active_check ?>/>
				</div>
			</div>
		</div>

		<div id="visibility-date" class="form-section date form-group form-md-line-input row well disabled">
			<div class="visibility-cols col-md-12 col-lg-6">
				<label class="col-md-12">Visible from</label>

				<div class="col-md-5">
					<input type="checkbox" data-enable="#visible_begin" id="begin" class="make-switch need-to-disable"
						   data-on-text="&nbsp;From&nbsp;" value="1" <?= $begin_check ?>/>
				</div>
				<div class="col-md-7">
					<div class="input-icon right">
						<input type="text" id="visible_begin" name="begin"
							   class="bs-md-datetimepicker form-control need-to-disable" value="<?= $begin_value ?>">
						<i class="fa fa-calendar"></i>

						<div class="form-control-focus">
						</div>
					</div>
				</div>
			</div>
			<div class="visibility-cols col-md-12 col-lg-6">
				<label class="col-md-12">Visible until</label>

				<div class="col-md-5">
					<input type="checkbox" data-enable="#visible_end" id="end" class="make-switch need-to-disable"
						   data-on-text="&nbsp;Until&nbsp;" value="1" <?= $end_check ?>/>
				</div>
				<div class="col-md-7">
					<div class="input-icon right">
						<input type="text" id="visible_end" name="end"
							   class="bs-md-datetimepicker form-control need-to-disable" value="<?= $end_value ?>">
						<i class="fa fa-calendar"></i>

						<div class="form-control-focus">
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		/*
		<div id="visibility-user" class="form-section user well disabled">
			<h3>Under construction!</h3>

			<p>Please come back later</p>
		</div>
		*/
		?>
	<?php
	} else {
		echo '	<h3 class="text-danger">Visibility not allowed for this model!</h3>';
	}
				?>
			</div>
		</form>
	</div>
</div>
