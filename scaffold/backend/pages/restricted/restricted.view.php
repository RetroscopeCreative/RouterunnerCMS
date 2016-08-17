<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

<?php
\runner::route('head');
?>

<body class="page-md page-header-top-fixed">
<div class="backend-wrapper">
	<?php
	\runner::route('/backend/frame/header');
	?>

	<!-- BEGIN CONTAINER -->
	<div class="page-container row container-fluid container-margin">
		<h1>Restricted area</h1>
		<p>You don't have permission to reach this content!</p>
	</div>
</div>
</body>
<?php
\runner::route('foot');
?>
</html>