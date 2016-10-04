<html>
<head>
	<title><?=$pageTitle?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">
	
	<link rel="stylesheet" href="<?= $uri ?>/assets/blog.css">

	<link rel="alternate" type="application/rss+xml" title="<?php echo config('blog.title')?>  Feed" href="<?php echo site_url()?>/rss" />
	<link href="http://fonts.googleapis.com/css?family=Open+Sans+Condensed:700&subset=latin,cyrillic-ext" rel="stylesheet" />
	
	<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<!-- Latest compiled and minified JavaScript -->
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
</head>
<body>
<header class="blog-masthead">
	<div class="container">
		<nav class="blog-nav">
			<a class="blog-nav-item <?=( $cpage == '/' ? 'active' : '' )?>"href="<?=$uri?>/">Home</a>
<?php
	$posts = get_posts('page','published',$page,config('posts.perpage'));
	$pages = array();
	foreach( $posts['posts'] as $post ){
		ob_start();
?>
		<a class="blog-nav-item <?=( '/'.$post['slug'] == $cpage ? 'active' : '' )?>" href="<?=$post['permalink']?>"><?=$post['title']?></a>
<?php
		if( isset( $pages[ $post['sort'] ] )){
			$post['sort']++;
		}
		$pages[ $post['sort'] ] = ob_get_contents();
		ob_end_clean();
	}
	ksort( $pages );
	foreach( $pages as $page ){
		echo $page;
	}
?>

		</nav>
	</div>
</header>
<div class="container">
	<div class="blog-header">
		<h1 class="blog-title"><?= $sitename ?></h1>
		<p class="lead blog-description"></p>
	</div>
	<div class="row">
		<div class="col-sm-12 blog-main">
			<?=$pageContent?>
		</div>
	</div>
	<hr />
	<footer>
		<p>&copy; <?=$sitename?> - <?=date("Y")?></p>
	</footer>
</div>
</body>
</html>