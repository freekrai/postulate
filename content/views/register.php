<!doctype html>
<html>
<head>
	<title>Register - <?=$sitename?></title>
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
		<form class="form-signin" role="form" method="POST" autocomplete="off">
			<h2 class="form-signin-heading">Please sign up</h2>
			<input type="email" name="email" id="email" class="form-control" placeholder="Enter email" required autofocus>
			<input type="text" name="name" id="name" class="form-control" placeholder="Your name">
			<input type="tel" name="phone" id="phone" class="form-control" placeholder="Your Phone Number" required>
			<input type="password" name="pass" id="pass" class="form-control" placeholder="Password">
			<button class="btn btn-lg btn-primary btn-block" type="submit">Sign up</button>
		</form>
	</div> <!-- /container -->
</body>
</html>