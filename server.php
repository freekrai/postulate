<?php
//	php -S 127.0.0.1:8888 server.php
if ( preg_match( '/\.(?:png|jpg|jpeg|gif|css|js|php|html)$/', $_SERVER["REQUEST_URI"] ) ) {
	return false;
} else {
	include __DIR__ . '/index.php';
}
/*
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$uri = urldecode($uri);

$paths = require __DIR__.'/bootstrap/paths.php';

$requested = $paths['public'].$uri;

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' and file_exists($requested))
{
	return false;
}

require_once $paths['public'].'/index.php';


*/