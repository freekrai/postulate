<?php
$_GET['route'] = isset($_GET['route']) ? '/'.$_GET['route'] : '/';

// Check for composer installed
if (file_exists('vendor/autoload.php')){
	include_once('vendor/autoload.php');
}else{
	echo '{"error":"Composer Install"}';
	header('HTTP/1.1 500 Internal Server Error', true, 500);
	return False;
}
use dflydev\markdown\MarkdownParser;
include("core/system/runtime.php");

$app = new Jolt\Jolt();
$app->option('source', 'config.ini');

//	Paths.... Theme location, and upload path...
$core_path = dirname(__FILE__) . '/core/';
$core_url = $app->option('site.url') . '/core/';

$upload_path = dirname(__FILE__) . '/content/uploads/'; 
$upload_url = $app->option('site.url') . '/content/uploads/';

$theme_path = dirname(__FILE__) . '/content/views/'; 
$theme_url = $app->option('site.url') . '/content/views/';

$app->store('core_path',$core_path);
$app->store('core_url',$core_url);
$app->store('theme_path',$theme_path);
$app->store('theme_url',$theme_url);
$app->store('upload_path',$upload_path);
$app->store('upload_url',$upload_url);

if( $app->option('pdo.enabled') != false ){
	ORM::configure( $app->option('pdo.connect') );
}else if( $app->option('db.enabled') != false ){
	ORM::configure('mysql:host='.$app->option('db.host').';dbname='.$app->option('db.name'));
	ORM::configure('username', $app->option('db.user') );
	ORM::configure('password', $app->option('db.pass') );
}

if( $app->option('schemaless.enabled') != false ){
#	$client = new Schemaless\Client( $app->option('schemaless.path') );
#	$app->db = $client->postulate;
	$server = config('schemaless.server');
	$app->db = function() use($app) {
		$client = new MongoHybrid( config("schemaless.server"), array(
			'db'=>config("schemaless.db") 
		));
		return $client;
	};
}

//	Store our Markdown Parser class so we can quickly access it later...
$md = new MarkdownParser();
$app->store('markdown',$md);

$blueprints = blueprints();
$app->store('blueprints', $blueprints);

//	Logout	--------------------------------------------------------------------------------------------

$app->get('/logout', function() use ($app){
	$app->store('user',0);
	$app->redirect( $app->getBaseUri().'/login');
});

//	Conditions	--------------------------------------------------------------------------------------------

$app->condition('signed_in', function () use ($app) {
	$app->redirect( $app->getBaseUri().'/login',!$app->store('user') );
});

//	Login	--------------------------------------------------------------------------------------------

$app->get('/login', function() use ($app){
	$app->render( 'login', array(),'blank' );
});
$app->post('/login', function() use ($app){
	user_login($_POST['user'],$_POST['pass'], $app->getBaseUri().'/admin' );
});
//	Register	--------------------------------------------------------------------------------------------

$app->get('/signup', function() use ($app){
	$app->render( 'register', array(),'blank' );
});
$app->post('/signup', 'signup#post' );

//	Logged in area	--------------------------------------------------------------------------------------------

$app->route_group('/admin', array(
//	dashboard
	array(
		'path'=>'',
		'method'=> 'get',
		'code' => function () use ($app){
			$app->condition('signed_in');
			$me= $app->db->findOne('user', array( '_id'=>$app->store('user') ));
			$app->render( 'admin/dashboard', array(
				"pageTitle" => "My Admin",
				"me" => $me,
			),'admin/inside' );
		}
	),
//	content
	array(
		'path'=>'/content/:posttype',
		'method'=>'get',
		'code'=> 'contentController#mylist'
	),
	array(
		'path'=>'/content/:posttype/edit/:post_id',
		'method'=>'get',
		'code'=> 'contentController#edit'
	),
	array(
		'path'=>'/content/:posttype/edit/:post_id',
		'method'=>'post',
		'code'=> 'contentController#save'
	),
	array(
		'path'=>'/content/:posttype/new',
		'method'=>'get',
		'code'=> 'contentController#addnew'
	),
	array(
		'path'=>'/content/:posttype/new',
		'method'=>'post',
		'code'=> 'contentController#savenew'
	),
	array(
		'path'=>'/content/:posttype/delete/:post_id',
		'method'=>'get',
		'code'=> 'contentController#delete'
	),
//	users
	array(
		'path'=>'/user',
		'method'=>'get',
		'code'=>'userController#mylist'
	),
	array(
		'path'=>'/user/edit/:user_id', 
		'method'=>'get',
		'code'=>'userController#edit'
	),
	array(
		'path'=>'/user/edit/:user_id', 
		'method'=>'post',
		'code'=>'userController#save'
	),
	array(
		'path'=>'/user/new', 
		'method'=>'get',
		'code'=>'userController#addnew'
	),
	array(
		'path'=>'/user/new', 
		'method'=>'post',
		'code'=>'userController#savenew'
	),
	array(
		'path'=>'/user/delete/:user_id', 
		'method'=>'get',
		'code'=>'userController#delete'
	)
));

//	front end --------------------------------------------------------------------------------------------

//	This group is connected to a specific content type called tags
$app->route_group('/tag', array(
    array(
    	'path'=>'/:slug/rss',
        'method'=>'get',
        'code' => function ($slug) use ($app){
			$app->store('tag',$slug);
			header('Content-Type: application/rss+xml');
		}
    ),
    array(
    	'path'=>'/:slug/rss/:page',
        'method'=>'get',
        'code' => function ($slug,$page) use ($app){
			$app->store('tag',$slug);
			header('Content-Type: application/rss+xml');
		}
    ),
    array(
    	'path'=>'/:slug/page/:page',
        'method'=>'get', // or post or route
        'code' => 'frontendController#tag'
    ),
    array(
    	'path'=>'/:slug',
        'method'=>'get', // or post or route
        'code' => 'frontendController#tag'
    )
));

//	Rss feeds
$app->get('/rss(/:slug)','frontendController#rss');
$app->get('/feed(/:slug)','frontendController#rss');

//	Home page
$app->route('/page/:page','frontendController#single');
$app->route('/','frontendController#homepage');

//	Single or 404
$app->route('/:slug','frontendController#single');

//	404 page  --------------------------------------------------------------------------------------------
/*
$app->get('.*',function() use ($app){
	$app->error(404, $app->render('404',  array(
		"pageTitle"=>"404 Not Found",
	),'layout'));
});
*/

function notfound(){
	$app = \Jolt\Jolt::getInstance();
	$app->error(404, $app->render('frontend/404',  array(
		"pageTitle"=>"404 Not Found",
	),'frontend/layout'));
}

$app->listen();