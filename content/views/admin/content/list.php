<h1 class="page-header"><?=pluralize($pageTitle,2)?></h1>
<div class="pull-right" style="margin-top:-60px;">
	<a href="<?=$uri?>/admin/content/<?=$type?>/new" class="btn btn-primary">Add New <?=pluralize($pageTitle,1)?></a>
</div>
<div class="table-responsive">
	<table class="table table-striped">
	<thead>
	<tr>
		<th><?=( isset($blueprint['titlelabel']) ? $blueprint['titlelabel'] : "Title")?></th>
		<th>Views</th>
		<th>Status</th>
		<th>&nbsp;</th>
	</tr>
	</thead>
	<tbody>
<?php foreach($posts as $u){	?>
	<tr>
		<td><?php echo $u['title']?></td>
		<td><?php echo isset($u['views']) ? $u['views'] : 0?></td>
		<td><?php echo $u['status']?></td>
		<td>
			<a class="btn btn-primary" href="<?=$uri?>/admin/content/<?=$type?>/edit/<?php echo $u['_id']?>" data-title="<?php echo $u['title']?>">Edit</a>
			<a class="btn btn-success" href="<?=site_url().'/'.$u['name']?>" target="blank">Preview</a>
			<a class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this post?');" href="<?=$uri?>/admin/content/<?=$type?>/delete/<?php echo $u['_id']?>">Delete</a>		
		</td>
	</tr>
<?php 	}	?>
	</tbody>
	</table>
</div>