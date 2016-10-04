<?php
/*
 *	Custom template for the Contact page, to demonstrate how to use custom template files and also how to use 
 *	the content_form function to collect information such as contact forms.
 *
 */
?>
<div class="blog-post">
	<h2 class="blog-post-title"><?=$post['title']?></h2>
	<?=content_html($post['body'])?>
	<div class="well">
<?php
//	we want to have a form that can be used to gather contact info...
	content_form(
		'contact',	//	post type
		'',			//  post id
		'',			//	action
		array('msg'=>'Thank you, we will be in touch')	//	thank you
	);
?>
	</div>
</div>