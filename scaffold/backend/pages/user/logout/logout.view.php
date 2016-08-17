<?php
$debug = 1;

\user::logout();

\runner::stack_js('setTimeout(function() { window.location.href = "' . \runner::config('BASE') . 'admin/user/loggedout"; }, 100);');
?>

<body class="page-md page-header-top-fixed">
<div class="backend-wrapper">
	<!-- BEGIN CONTAINER -->
	<div class="page-container row container-fluid container-margin">
		<h1>You've been successfully logged out!</h1>
	</div>
</div>
</body>