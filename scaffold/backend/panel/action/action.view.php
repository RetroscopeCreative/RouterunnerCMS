<!-- BEGIN ACTION MENU -->
<div class="top-menu action-panel">
	<ul class="nav navbar-nav pull-right">
		<!-- BEGIN NEW ELEM DROPDOWN -->
		<li id="action-new-model" class="action-new btn-group btn-group-circle">
			<button id="action-add-model" type="button" class="btn btn-default"><span class="fa fa-plus"></span> <span id="action-btn-label" class="btn-label">... choose</span></button>
			<button id="action-model-selector" type="button" class="btn btn-circle-right btn-default dropdown-toggle" data-toggle="dropdown"><span class="fa fa-angle-down"></span></button>
			<ul class="dropdown-menu" role="menu"></ul>
		</li>
		<!-- END NEW ELEM DROPDOWN -->
		<li class="droddown dropdown-separator hidden-xs">
			<span class="separator"></span>
		</li>
		<!-- BEGIN BROWSE MODE ACTION PANEL -->
		<li class="action-browse">
			<button id="action-mode-edit" type="button" class="btn btn-circle btn-default tooltips" data-container="body" data-placement="bottom" data-html="true" data-original-title="Edit current page">
				<span class="fa fa-pencil"></span><span class="btn-label"> Edit</span>
			</button>
		</li>
		<!-- END BROWSE MODE ACTION PANEL -->
		<!-- BEGIN EDIT MODE ACTION PANEL -->
		<li class="action-edit hidden-xs" style="display: none;">
			<button id="action-mode-changes" type="button" class="btn btn-circle btn-default tooltips" data-container="body" data-placement="bottom" data-html="true" data-original-title="Changes to be applied/reverted">
				<span class="icon fa fa-history"></span> <span class="btn-label" data-error="Error" data-changes="Changes">Changes</span>
			</button>
		</li>
		<li class="action-edit action-apply bgr-success" style="display: none;">
			<button id="action-mode-apply" type="button" class="btn btn-circle btn-success tooltips" data-container="body" data-placement="bottom" data-html="true" data-original-title="Apply changes">
				<span class="fa fa-check"></span><span class="btn-label"> Apply</span>
			</button>
		</li>
		<li class="action-edit action-apply bgr-warning" style="display: none;">
			<button id="action-mode-revert" type="button" class="btn btn-circle btn-danger tooltips" data-container="body" data-placement="bottom" data-html="true" data-original-title="Revert changes">
				<span class="fa fa-ban"></span><span class="btn-label"> Revert</span>
			</button>
		</li>
		<li class="action-changes-waiting bgr-warning" style="display: none;">
			<div class="alert alert-warning">
				<strong>Changes!</strong> Waiting for apply...
			</div>
		</li>
		<!-- END EDIT MODE ACTION PANEL -->
	</ul>
</div>
<!-- END ACTION MENU -->
