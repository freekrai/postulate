<!doctype html>
<html>
<head>
	<title>Login - <?=$sitename?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">
	
	<link rel="stylesheet" href="<?= $uri ?>/assets/login.css">
	
	<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<!-- Latest compiled and minified JavaScript -->
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
</head>
<body>
	<div class="container">
		<form class="form-signin" role="form" method="POST" action="/login">
			<h2 class="form-signin-heading">Please sign in</h2>
			<input type="text" name="user" class="form-control" placeholder="User name" required autofocus>
			<input type="password" name="pass" class="form-control" placeholder="Password" required>
			<label class="checkbox">
				<input type="checkbox" value="remember-me"> Remember me
			</label>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
		</form>
	</div> <!-- /container -->
</body>
</html>