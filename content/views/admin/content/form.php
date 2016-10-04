<link href="<?=$uri?>/assets/admin/css/jquery.pagedown-bootstrap.css" rel="stylesheet">
<style>
div.wmd-panel{
	
}
div.wmd-button-bar{
	margin-bottom:10px;
}
textarea.wmd-input{
	
}
div.wmd-preview{
	margin-top: 10px;	
}
</style>
<script type="text/javascript" src="<?=$uri?>/assets/admin/js/jquery.pagedown-bootstrap.combined.min.js"></script>
<script type="text/javascript">
$(document).ready(function (){
	$("textarea").attr("rows",10).wrap("<div class='well'></div>");
	$("textarea").pagedownBootstrap();
});
</script>
<h1 class="page-header"><?php echo $pageTitle?></h1>
<form role="form"  method="POST" action="<?php echo $action?>" enctype="multipart/form-data">
<fieldset>
	<div class="form-group">
		<label for="title"><?=( isset($blueprint['titlelabel']) ? $blueprint['titlelabel'] : "Title")?></label>
		<input type="text" class="form-control" id="title" name="title" placeholder="Enter title" value="<?php echo ( isset($post) ? $post['title'] : '');?>" required>
	</div>
<?php
	foreach($blueprint['fields'] as $fname=>$field ){
		form_field($fname,$field,$post);
	}
	$status = array(
		'draft'=>"Hidden",
		'published'=>"Published"
	);
?>
	<div class="form-group">
		<label for="title">Status</label>
		<select class="form-control" id="status" name="status" required>
<?php
		foreach( $status as $s=>$l ){
			$sel = '';
			if( isset($post) ){
				if( $s == $post['status'] ){
					$sel = 'SELECTED';
				}
			}
?>
			<option <?= $sel ?> value="<?= $s ?>"><?= $l ?></option>
<?php	}	?>
		</select>
	</div>	
<?php
	if( isset($post) ){
		if( isset($blueprint['prefix']) && !empty($blueprint['prefix']) ){
			echo '<input type="hidden" name="prefix" id="prefix" value="'.$blueprint['prefix'].'" />';
		}
?>
	<div class="form-group">
		<label for="title">Slug</label>
		<div class="well">
			<?=$post['name']?>
		</div>
	</div>
<?php	
	}
?>
	<button type="submit" class="btn btn-primary">Save</button>
</fieldset>
</form>