Postulate...

### Installation:


Update config.ini with the config settings to be used for the site.

To test this locally run:

php -S 127.0.0.1:8888 server.php

When you are ready to put the site live, copy all the files to the live server and update config.ini with the correct settings.


### Summary

A blog engine that is how you build it to be...

Post types are defined as blueprints in the content/blueprints/ folder

For example:

content/blueprints/page.php

title: Page
type: page
fields:
	title: 
		label: Title
		type:  text
		adminonly:	true
	text: 
		label: Text
		type:  textarea
		size:  large
		adminonly:	true
		
### Field Types:

-	Text
-	Textarea
-	File
-	Dropdown
-	Radio
-	Checkbox
-	Related
	-	Specify a post type, this can be useful for establishing a relationship with other posts or pages, or for 
		associating an article with tags (which are also a post type)
	
Using blue prints, you can create a frontend / backend collection system.

There are four types blueprints included by default:

-	Blog Post
	-	for blog posts
-	Page
	-	for pages
-	Contact
	-	To demonstrate how to build forms on the frontend and store data for review in backend.
-	Tag
	-	For demonstrating how to build taxonomies.
	
	
### Layout:

Layout is handled inside the content/views folder, you will see it broken down by admin/, frontend/ and a few other misc files.

The admin/ folder handles the layout of the admin side and frontend/ folder handles layout of the frontend of the site.

