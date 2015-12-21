<?php
$debug = 1;
$url = \bootstrap::get("url");

$backend_uri = false;
$backend_session = false;
$code = false;
//backend_mode($backend_uri, $backend_session, $code);
?>

<body class="page-md page-header-top-fixed">
<div class="backend-wrapper">
	<?php
	\runner::route('/backend/frame/header');
	?>

	<!-- BEGIN CONTAINER -->
	<div class="page-container row container-fluid container-margin">
		<h1>User settings</h1>
		<?php
		if (\runner::now("subpage")) {
			\runner::route(\runner::now("subpage"));
		}
		?>
	</div>
</div>
</body>