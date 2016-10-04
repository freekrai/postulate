<!doctype html>
<html>
<head>
	<title><?=$pageTitle?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="<?= $uri ?>//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= $uri ?>/assets/inside.css">
	<link href="<?= $uri ?>//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">

	<script src="<?= $uri ?>//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<!-- Latest compiled and minified JavaScript -->
	<script src="<?= $uri ?>//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
	<script src="<?= $uri ?>/assets/app.js"></script>

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body>
	<header>
		<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="<?= $uri ?>/admin">Postulate Admin</a>
				</div>
				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-right">
						<li><a href="<?= $uri ?>/admin">Dashboard</a></li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Your Content <b class="caret"></b></a>
							<ul class="dropdown-menu">
<?php
	$app = \Jolt\Jolt::getInstance();
	$blueprints = $app->store('blueprints');
	foreach( $blueprints as $type=>$blueprint ){
?>
		<li><a href="<?= $uri ?>/admin/content/<?=$type?>"><?=pluralize($blueprint['title'],2)?></a></li>
<?php
	}
?>
							</ul>
						</li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Users <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="<?= $uri ?>/admin/user">Users</a></li>
								<li><a class="modalButton" data-toggle="modal" data-src="<?= $uri ?>/admin/user/new?embed=1" data-title="Add New User" data-target="#modalbox">Add New User</a></li>
							</ul>
						</li>
						<li><a class="modalButton" data-toggle="modal" data-src="<?= $uri ?>/admin/user/edit/<?php echo $me['_id']?>?embed=1" data-title="Edit Profile" data-target="#modalbox">Edit Profile</a></li>
						<li><a href="<?= $uri ?>/logout">Logout</a></li>
					</ul>
				</div>
			</div>
		</div>
	</header>
	<div class="container" id="main">
		<?=$pageContent?>
		<br /><br />
		<br /><br />
	</div>
	<div class="modal fade" id="modalbox">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="modaltitle">Modal title</h4>
				</div>
				<div class="modal-body">
					<iframe width="100%" height="450px" frameborder="0" scrolling="yes" allowtransparency="true"></iframe>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</body>
</html>