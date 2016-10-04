<h3>Hello, <?= get_firstname($me)?></h3>
<div class="row">
	<div class="col-lg-6"><div class="well">
		<div class="list-group">
			<a href="#" class="list-group-item active">
				<h4 class="list-group-item-heading">Your Content</h4>
			</a>
<?php
	$app = \Jolt\Jolt::getInstance();
	$blueprints = $app->store('blueprints');
	foreach( $blueprints as $type=>$blueprint ){
		$cnt = count_posts($type,'any');
		$pcnt = count_posts($type,'published');
		$dcnt = count_posts($type,'draft');
?>
			<a href="<?= $uri ?>/admin/content/<?=$type?>" class="list-group-item">
				<h4 class="list-group-item-heading"><?=pluralize($blueprint['title'],2)?></h4>
<span class="badge"><?=$cnt?></span>
				<p class="list-group-item-text">
					View <?=pluralize($blueprint['title'],2)?>
				</p>
			</a>
<?php
	}
?>
		</div>
		<br />
		<div class="list-group">
			<a  class="list-group-item modalButton" data-toggle="modal" data-src="<?= $uri ?>/admin/user/edit/<?php echo $me['_id']?>?embed=1" data-title="Edit Profile" data-target="#modalbox">
				<h4 class="list-group-item-heading">Edit Profile</h4>
				<p class="list-group-item-text">
					Edit Your Profile
				</p>
			</a>
			<a class="list-group-item" href="<?=$uri?>/logout">
				<h4 class="list-group-item-heading">Logout</h4>
				<p class="list-group-item-text">
				</p>
			</a>
		</div>
	</div></div>
	<div class="col-lg-6"><div class=" well">
		ssss
	</div></div>
</div>