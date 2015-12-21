<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.05.07.
 * Time: 19:12
 */
$bootstrap = \bootstrap::get();

/*
$main_url = "";
$temp_url = array_shift($bootstrap->urls);
if ($temp_url != $bootstrap->resource_url) {
	$main_url = $temp_url;
}
*/

$meta = array_merge(array(
	"social-title" => "",
	"social-image" => "",
	"social-description" => "",
	"social-type" => "",
), $bootstrap->pageproperties["meta"]);

$og_types = array(
	"article",
	"book",
	"profile",
	"video",
	"website",
	"music"
);

?>

<form name="routerunner-page-properties" id="<?=\Routerunner\Form::$id?>" class="routerunner-page-properties routerunner-form" role="form">
	<div class="resource">
		<?=form::input("reference")?>
		<?=form::input("resource_uri")?>
		<?=form::input("params")?>
		<?=form::input("lang")?>
	</div>
	<div class="horizontal-form page-prop-header">
		<div class="form-body">
			<div class="row">
				<div class="hidden-xs hidden-sm col-md-9 col-lg-5">
					<?=form::input("title", array("type" => "text-header"))?>
				</div>
				<div class="hidden-xs hidden-sm hidden-md col-lg-4">
					<?=form::input("url", array("type" => "text-header"))?>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
					<button type="button" id="page-properties" class="btn btn-circle default tooltips" data-container="body" data-placement="bottom" data-html="true" data-original-title="Page properties like SEO, Facebook share, etc."><span class="fa fa-code"></span><span class="btn-label"> Meta data</span></button>
				</div>
			</div>
		</div>
	</div>

	<div class="row form-horizontal extended hideable panel-hider">

		<div class="hidden-lg col-md-12 hidden-xs hidden-sm form-body">
			<div class="portlet light">
				<div class="portlet-title">
					<div class="caption font-red">
						<i class="icon-pin font-red"></i>
						<span class="caption-subject bold uppercase"> Page properties</span>
					</div>
				</div>
				<div class="portlet-body form">
					<?=form::input("url", array("input-id" => "url-md"))?>
				</div>
			</div>
		</div>

		<div class="hidden-lg hidden-md col-xs-12 col-sm-12 form-body">
			<div class="portlet light">
				<div class="portlet-title">
					<div class="caption font-red">
						<i class="icon-pin font-red"></i>
						<span class="caption-subject bold uppercase"> Page properties</span>
					</div>
				</div>
				<div class="portlet-body form">
					<?=form::input("title", array("input-id" => "title-sm"))?>
					<?=form::input("url", array("input-id" => "url-sm"))?>
				</div>
			</div>
		</div>

		<div class="col-lg-6 col-md-12 col-xs-12 col-sm-12 form-body">
			<div class="portlet light">
				<div class="portlet-title">
					<div class="caption font-red">
						<i class="icon-pin font-red"></i>
						<span class="caption-subject bold uppercase"> SEO properties</span>
					</div>
				</div>
				<div class="portlet-body form">
					<div class="form-body">
						<?=form::input("urls")?>
						<?=form::input("keywords")?>
						<?=form::input("description")?>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-6 col-md-12 col-xs-12 col-sm-12 form-body">
			<div class="portlet light">
				<div class="portlet-title">
					<div class="caption font-red">
						<i class="icon-pin font-red"></i>
						<span class="caption-subject bold uppercase"> Social properties</span>
					</div>
				</div>
				<div class="portlet-body form">
					<div class="form-body">
						<?=form::input("og-title")?>
						<?//=form::input("og-image")?>
						<?=form::input("og-description")?>
						<?=form::input("og-type")?>
					</div>
				</div>
			</div>
		</div>

	</div>
</form>
