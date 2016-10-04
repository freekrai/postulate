<html>
<head>
	<title><?=$pageTitle?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">
	
	<link rel="stylesheet" href="<?= $uri ?>/assets/home.css">

	<link rel="alternate" type="application/rss+xml" title="<?php echo config('blog.title')?>  Feed" href="<?php echo site_url()?>/rss" />
	<link href="http://fonts.googleapis.com/css?family=Open+Sans+Condensed:700&subset=latin,cyrillic-ext" rel="stylesheet" />
	
	<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<!-- Latest compiled and minified JavaScript -->
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
</head>
<body>
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
    <div class="container">
       <div class="row">
            <div class="col-lg-8">
				<?=$pageContent?>
            </div>
			<div class="col-md-4">
				<div class="well">
					<h4><?php echo config('blog.title')?></h4>
					<?php echo config('blog.description')?>
				</div>
<?php
/*
                <div class="well">
                    <h4>Blog Search</h4>
                    <div class="input-group">
                        <input type="text" class="form-control">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button">
                                <span class="glyphicon glyphicon-search"></span>
                        </button>
                        </span>
                    </div>
                    <!-- /.input-group -->
                </div>

                <!-- Blog Categories Well -->
                <div class="well">
                    <h4>Blog Categories</h4>
                    <div class="row">
                        <div class="col-lg-6">
                            <ul class="list-unstyled">
                                <li><a href="#">Category Name</a>
                                </li>
                                <li><a href="#">Category Name</a>
                                </li>
                                <li><a href="#">Category Name</a>
                                </li>
                                <li><a href="#">Category Name</a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-lg-6">
                            <ul class="list-unstyled">
                                <li><a href="#">Category Name</a>
                                </li>
                                <li><a href="#">Category Name</a>
                                </li>
                                <li><a href="#">Category Name</a>
                                </li>
                                <li><a href="#">Category Name</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="well">
                    <h4>Side Widget Well</h4>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Inventore, perspiciatis adipisci accusamus laudantium odit aliquam repellat tempore quos aspernatur vero.</p>
                </div>
*/
?>
            </div>
       </div>
		<hr />
		<footer>
			<p>&copy; <?=$sitename?> - <?=date("Y")?></p>
		</footer>
	</div>
</body>
</html>