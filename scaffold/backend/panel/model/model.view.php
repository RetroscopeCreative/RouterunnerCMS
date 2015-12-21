<?php
$debug = 1;
$relative_url = \Routerunner\Bootstrap::$relUri;
?>
<div class="routerunner-panel">
	<div id="routerunner-model-navbar" class="navbar navbar-default navbar-static" role="navigation" data-url="<?=$relative_url?>/">
		<div class="navbar-header">
			<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".bs-js-navbar-scrollspied">
				<span class="sr-only">Toggle navigation </span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="javascript:;">Model</a>
		</div>
		<div class="collapse navbar-collapse bs-js-navbar-scrollspied">
			<ul class="nav navbar-nav">
				<li id="model-panel-properties-menu" class="active"><a href="#routerunner-properties" data-target="#routerunner-properties"><span class="section-icon fa fa-file-text-o" title="Properties"></span><span class="section-text">Properties</span></a></li>
				<li id="model-panel-movement-menu"><a href="#routerunner-movement" data-target="#routerunner-movement"><span class="section-icon fa fa-arrows" title="Movement"></span><span class="section-text">Movement</span></a></li>
				<li id="model-panel-visibility-menu"><a href="#routerunner-visibility" data-target="#routerunner-visibility"><span class="section-icon fa fa-eye-slash" title="Visibility"></span><span class="section-text">Visibility</span></a></li>
				<li id="model-panel-drafts-menu"><a href="#routerunner-drafts" data-target="#routerunner-drafts"><span class="section-icon fa fa-copy" title="Drafts"></span><span class="section-text">Drafts</span></a></li>
				<li id="model-panel-history-menu"><a href="#routerunner-history" data-target="#routerunner-history"><span class="section-icon fa fa-undo" title="History"></span><span class="section-text">History</span></a></li>
				<li id="model-panel-remove-menu"><a href="#routerunner-remove" data-target="#routerunner-remove"><span class="section-icon fa fa-trash-o" title="Remove"></span><span class="section-text">Remove</span></a></li>
			</ul>
		</div>
	</div>
	<div id="routerunner-model" data-target="#routerunner-model-navbar" data-offset="10" class="scrollspied panel-content">
	</div>
</div>
