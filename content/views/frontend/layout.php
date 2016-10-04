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
	<?php post_head(); ?>
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
			<a class="blog-nav-item pull-right hidden-xs" href="<?php echo site_url()?>/rss"><i class="fa fa-rss"></i> RSS Feed</a>
		</nav>
	</div>
</header>
<?php
/*
	<header>
		<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="<?=$uri?>/"><?= $sitename ?></a>
				</div>
				<div class="collapse navbar-collapse">
					<ul class="nav navbar-nav">
						<li class="<?=( $cpage == '/' ? 'active' : '' )?>"><a href="<?=$uri?>/">Home</a></li>
<?php
	$posts = get_posts('page','published',$page,config('posts.perpage'));
	$pages = array();
	foreach( $posts['posts'] as $post ){
		ob_start();
?>
		<li class="<?=( '/'.$post['slug'] == $cpage ? 'active' : '' )?>"><a href="<?=$post['permalink']?>"><?=$post['title']?></a></li>
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
					</ul>
				</div><!--/.nav-collapse -->
			</div>
		</div>
	</header>
*/
?>
<div class="container">
	<div class="blog-header">
		<h1 class="blog-title"><?= $sitename ?></h1>
		<p class="lead blog-description"></p>
	</div>
	<div class="row">
		<div class="col-sm-8 blog-main">
			<?=$pageContent?>
		</div>
		<div class="col-sm-3 col-sm-offset-1 blog-sidebar">
			<div class="sidebar-module sidebar-module-inset">
				<h4><?php echo config('blog.title')?></h4>
				<?php echo config('blog.description')?>
			</div>
<!--
          <div class="sidebar-module">
            <h4>Archives</h4>
            <ol class="list-unstyled">
              <li><a href="#">March 2014</a></li>
              <li><a href="#">February 2014</a></li>
              <li><a href="#">January 2014</a></li>
              <li><a href="#">December 2013</a></li>
              <li><a href="#">November 2013</a></li>
              <li><a href="#">October 2013</a></li>
              <li><a href="#">September 2013</a></li>
              <li><a href="#">August 2013</a></li>
              <li><a href="#">July 2013</a></li>
              <li><a href="#">June 2013</a></li>
              <li><a href="#">May 2013</a></li>
              <li><a href="#">April 2013</a></li>
            </ol>
          </div>
          <div class="sidebar-module">
            <h4>Elsewhere</h4>
            <ol class="list-unstyled">
              <li><a href="#">GitHub</a></li>
              <li><a href="#">Twitter</a></li>
              <li><a href="#">Facebook</a></li>
            </ol>
          </div>
-->
		</div>
	</div>
</div>
<footer class="blog-footer">
	<p class="text-left">&copy; <?=$sitename?> - <?=date("Y")?></p>
	<p>
		<a href="#">Back to top</a>
	</p>
</footer>
</body>
</html>