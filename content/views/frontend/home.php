<?php
	$posts = get_posts('post','published',$page,config('posts.perpage'));

	foreach( $posts['posts'] as $post ){
		$url = $post['permalink'];
		$classn = '';
		if( isset($post['link']) && !empty($post['link']) ){
			$url = $post['link'];
			$classn = "link";
		}
?>
		<div class="blog-post <?=$classn?>">
			<h2 class="blog-post-title">
				<a href="<?=$url?>"><?=$post['title']?></a>
<?php echo ($classn == 'link' ? '<span class="linkarrow">&rarr;</span>' : '' )?>
			</h2>
            <p class="blog-post-meta">
				<span class="glyphicon glyphicon-time"></span> 
				Posted on <?= date("F j, Y", strtotime($post['published_at']))?>
<?php 	if( $classn == 'link' ){ 	?>
			<a class="permalink" title="permalink" href="<?=$post['permalink']?>">&infin;</a>
<?php 	}	?>
			</p>
	        <hr>
			<?=content_html($post['body'])?>
<?php
		if( isset($post['tag'])){
			$tags = array();
			foreach($post['tag'] as $t){
				$tag = get_post($t);
				$tags[] = '<a href="'.$tag['permalink'].'">'.$tag['title'].'</a>';
			}
			echo '<p class="cat">Tagged in: '.implode(", ",$tags).'</p>';
		}
?>
		</div>
		<div class="fin">~•~</div>
<?php
	}
	if( $posts['pages'] > 1 ){
		$prev = isset($posts['prev']) ? $posts['prev'] : 0;
		$next = isset($posts['next']) ? $posts['next'] : 0;
?>
<nav class="pagination text-center" role="navigation">
<?php 	if( $prev > 0 ){	?>
        <a class="newer-posts" href="<?= site_url().'/page/'.$prev?>">&larr; Newer Posts</a>
<?php 	}	?>
    <span class="page-number">Page <?=$page?> of <?=$posts['pages']?></span>
<?php 	if( $next > 0 ){	?>
        <a class="older-posts" href="<?= site_url().'/page/'.$next?>">Older Posts &rarr;</a>
<?php 	}	?>
</nav>
<?php
	}else{
?>
<nav class="pagination text-center" role="navigation">
    <span class="page-number">Page <?=$page?> of <?=$posts['pages']?></span>
</nav>
<?php		
	}
?>