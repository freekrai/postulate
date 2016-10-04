<?php
	$url = $post['permalink'];
	$classn = '';
	if( isset($post['link']) && !empty($post['link']) ){
		$url = $post['link'];
		$classn = "link";
	}
?>
<div class="blog-post <?=$classn?>">
	<h2 class="blog-post-title">
<?php 	if( $classn == 'link' ){ 	?>
		<a href="<?=$url?>"><?=$post['title']?></a>
		<span class="linkarrow">&rarr;</span>
<?php 	}else{	?>
		<?=$post['title']?>
<?php 	}	?>
	</h2>
    <p class="blog-post-meta">
		<span class="glyphicon glyphicon-time"></span> 
		Posted on <?= date("F j, Y", strtotime($post['published_at']))?>
<?php 	if( $classn == 'link' ){ 	?>
	<a class="permalink" title="permalink" href="<?=$post['permalink']?>">&infin;</a>
<?php 	}	?>
    </p>
	<hr>
	<?= content_html($post['body']) ?>
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