<?php
$controller = null;
$method     = 'index';

if ( isset( $_GET['controller'] ) ) {
	$possible_controller = $_GET['controller'];
} else {
}

if ( isset( $_GET['method'] ) ) {
	$method = $_GET['method'];
} else {
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Licensing system</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous" />
	</head>
	<body>
		<nav class="navbar navbar-expand navbar-dark bg-dark">
			<div class="container">
				<span class="navbar-brand">Licensing system</span>
			</div><!-- /.container -->
		</nav>
		<div class="container mt-3">
			<a href="?controller=licenses" class="btn btn-primary btn-block">Licenses</a>
			<a href="?controller=customers" class="btn btn-primary btn-block">Customers</a>
		</div><!-- /.container.mt-3 -->
	</body>
</html>
