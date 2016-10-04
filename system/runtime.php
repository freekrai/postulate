<?php
$_GET['route'] = isset($_GET['route']) ? '/'.$_GET['route'] : '/';
define ('DOCUMENT_ROOT', realpath(dirname(__FILE__)));
define("MAX_LENGTH", 6);

define('DEBUG',false);
#define('DEBUG',true);
if (DEBUG) {
	error_reporting(E_ALL | E_STRICT);
	ini_set('display_errors', 1);
} else {
	error_reporting(0);
	@ini_set('display_errors', 0);
}

include("core/system/mongohybrid.php");
include("core/system/utils.php");
include("core/system/yaml.php");
include("core/system/cache.php");
include("core/system/posts.php");
$cachetime = (60*60*24);			// 24 hours

date_default_timezone_set('America/Los_Angeles');

//	grab all files in the core/app/ folder and include them..
$load_list = glob('core/app/*.php');
foreach($load_list as $ll) {
	include_once $ll;
}

function pluralize($singular, $quantity=1, $plural=null) {
	if($quantity==1 || empty($singular)) return $singular;
	if($plural!==null) return $plural;
	$last_letter = strtolower($singular[strlen($singular)-1]);
	switch($last_letter) {
		case 'y':
			return substr($singular,0,-1).'ies';
		case 's':
			return $singular.'es';
		default:
			return $singular.'s';
	}
}


function blueprints(){
	$blueprints = array();
	$path = config( 'blueprints.root' );
	$load_list = glob($path.'/*.php');	
	foreach($load_list as $file) {
		$yaml = spyc_load_file ( $file );
		$name = $yaml['type'];
		$blueprints[ $name ] = $yaml;
	}
	return $blueprints;
}


//	Utility Functions	--------------------------------------------------------------------------------------------

function split_name( $name ){
	list($fname, $lname) = split(' ', $name,2);
	return array($fname,$lname);
}

function db($collection){
	$app = \Jolt\Jolt::getInstance();
#	return $app->db->postulate->{$collection};
}

function get_firstname($user){
	$name = $user['name'];
	list($fname, $lname) = explode(' ', $name);
	return $fname;
}
function get_lastname($user){
	$name = $user['name'];
	$name = explode(' ', $name);
	$lname = end( $name );
	return $lname;
}


/*
 * For when we want to implement front-end logins...
 */
function user_login($username,$password,$redirect='/'){
	$app = \Jolt\Jolt::getInstance();
	$user = $app->db->findOne('user', array('login'=>$username) );
	if( $user['pass'] == passhash($password) ){
		$app->store( "user",$user['_id'] );
		$app->redirect( $redirect );
	}else{
		$app->redirect( $app->getBaseUri().'/login');
	}

}
/*
	Is user logged in or not....
*/
function user_loggedin(){
	$app = \Jolt\Jolt::getInstance();
	if( !$app->store('user') ){
		return false;
	}
	return true;
//	$app->redirect( $app->getBaseUri().'/login',!$app->store('user') );
}


/*
 * Used mostly for views, let's us grab user information for displaying on dashboard
 */
 
function get_user( $user_id ){
	$app = \Jolt\Jolt::getInstance();
	return $app->store('db')->findOne('user', array( '_id'=>$user_id ));
}

/*
 *	shortcut function to grab the site.url variable we've stored in our config.ini file
 */
function site_url(){
	return config( 'site.url' );
}
/*
 *	return a value that matches the key we pass
 */
function config($key){
	$app = \Jolt\Jolt::getInstance();
	return $app->option($key);
}


function embeddedorno( $layout = 'inside' ){
	return ( isset($_REQUEST['embed']) ? 'embedded' : $layout );
}
//	local render() function, calls the Jolt Render function, after determining the layout to use.
//	Mostly involved in terms of checking if the page being rendered is embedded or not...
function render($view, $locals = null, $layout = 'inside' ){
	$app = \Jolt\Jolt::getInstance();
	$app->render( $view, $locals, embeddedorno( $layout ) );		
}

function pagination($options){
/*
	$app = \Jolt\Jolt::getInstance();
	$file = $app->store('core_path').'/helpers/tpl/pagination.hbs';
	if( file_exists($file) ){
		$template = file_get_contents($file);
	}else{
		$template = '';
	}
	$total = $options['total'];
	$page = $options['page'];
	$limit = $options['limit'];
	
	$pages = ceil( $total / $limit );
	if( $page > 1 ){
		$offset = ($page - 1) * $limit;
	}else{
		$offset = 0;
	}
	if( $page > 1 ){
		$prev = $page - 1;
	}
	if( $page < $pages ){
		$next = $page + 1;
	}

	$data = array(
		'prev'=>$prev,
		'next'=>$next,
		'page'=>$page,
		'pages'=>$pages,
	);
	ob_start();
		$phpStr = LightnCandy::compile($template, array(
		    'flags' => LightnCandy::FLAG_STANDALONE,
		    'basedir' => Array(
		        $app->store('core_path').'/helpers/tpl/',
		    ),
		    'fileext' => Array(
		        '.hbs'
		    ),
			'hbhelpers' => Array(
				'myeach' => function ($context, $options) {
					$ret = '';
					foreach ($context as $cx) {
						$ret .= $options['fn']($cx);
					}
					return $ret;
				}
			),
		    'helpers' => Array(
				'asset' => function ($url) {
					$app = \Jolt\Jolt::getInstance();
	            	return $app->store('theme_url').'/assets/'.$url;
				},
				'page_url' => function ($page) {
					$app = \Jolt\Jolt::getInstance();
					$url = site_url();
					$tag = $app->store('tag');
					if( isset( $tag ) && !empty($tag) ){
						$url = $url . '/tag/'.$tag.'/page/'.$page;
					}else{
						$url = $url . '/page/'.$page;
					}
					return $url;
				},
				'pageUrl' => function ($page) {
					$app = \Jolt\Jolt::getInstance();
					$url = site_url();
					$tag = $app->store('tag');
					if( isset( $tag ) && !empty($tag) ){
						$url = $url . '/tag/'.$tag.'/page/'.$page;
					}else{
						$url = $url . '/page/'.$page;
					}
					return $url;
				},
		    )
		));
		$renderer = LightnCandy::prepare($phpStr);
	#	print_r($phpStr);
		echo $renderer($data);
	$output = ob_get_contents();
	ob_end_clean();	
	return $output;
*/
}

function link_tags( $tags ){
/*
	$app = \Jolt\Jolt::getInstance();
	$file = $app->store('core_path').'/helpers/tpl/tags.hbs';
	if( file_exists($file) ){
		$template = file_get_contents($file);
	}else{
		$template = '';
	}
	$data = array(
		'tags'=>$tags
	);
	ob_start();
		$phpStr = LightnCandy::compile($template, array(
		    'flags' => LightnCandy::FLAG_STANDALONE,
		    'basedir' => Array(
		        $app->store('core_path').'/helpers/tpl/',
		    ),
		    'fileext' => Array(
		        '.hbs'
		    ),
		    'helpers' => Array(
				'asset' => function ($url) {
					$app = \Jolt\Jolt::getInstance();
	            	return $app->store('theme_url').'/assets/'.$url;
				},
				'page_url' => function ($page) {
					$app = \Jolt\Jolt::getInstance();
					$url = site_url();
					$tag = $app->store('tag');
					if( isset( $tag ) && !empty($tag) ){
						$url = $url . '/tag/'.$tag.'/page/'.$page;
					}else{
						$url = $url . '/page/'.$page;
					}
					return $url;
				},
				'pageUrl' => function ($page) {
					$app = \Jolt\Jolt::getInstance();
					$url = site_url();
					$tag = $app->store('tag');
					if( isset( $tag ) && !empty($tag) ){
						$url = $url . '/tag/'.$tag.'/page/'.$page;
					}else{
						$url = $url . '/page/'.$page;
					}
					return $url;
				},
		    )
		));
		$renderer = LightnCandy::prepare($phpStr);
		echo $renderer($data);
	$output = ob_get_contents();
	ob_end_clean();	

	return $output;	
*/
}

function post_head(){
	$app = \Jolt\Jolt::getInstance();
	ob_start();
	$url = site_url().$app->getUri();
	$pagedata = $app->store("pagedata");
/*
?>
	<meta property="og:type" content="article">
	<meta property="article:author" content="503440548">
	<meta property="article:tag" content="dayone">
	<meta property="article:tag" content="nvremind">
	<meta property="article:tag" content="reminders">
	<meta property="article:tag" content="github">
	<meta property="article:tag" content="markdown">
	
	<meta name="twitter:card" content="summary">
	<meta name="twitter:site" content="@ttscoff">
	<meta name="twitter:creator" content="@ttscoff">
	<meta name="twitter:title" content="nvremind + Day One and more - BrettTerpstra.com">
	<meta name="twitter:description" content="In case you missed it this weekend, I wrote a little script called &quot;nvremind&quot; (pronounced &quot;nevermind&quot;). It scans folders of text files for occurrences of @remind(YYYY-mm-dd) tags and triggers">
	<meta property="og:title" content="nvremind + Day One and more - BrettTerpstra.com">
	<meta property="og:site_name" content="BrettTerpstra.com">
	
	<meta name="twitter:image" content="http://cdn3.brettterpstra.com/uploads/2013/06/nvreminddayone_lg.jpg" />
	<meta property="og:image" content="http://cdn3.brettterpstra.com/uploads/2013/06/nvreminddayone_lg.jpg" />
	<meta property="og:image:type" content="image/jpeg" />
	<meta property="og:image:width" content="750" />
	<meta property="og:image:height" content="206" />
<?php
*/

	$pagedata['excerpt'] = excerpt_paragraph( $pagedata['body'] );
	$pagedata['excerpt'] = strip_tags( $pagedata['excerpt'] );
?>
	<meta property="og:title" content="<?php echo $pagedata['title'];?>">
	<meta property="og:site_name" content="<?php echo config('site.name');?>">
	<meta property="og:url" content="<?php echo $url;?> ">
	<meta property="og:description" content="<?php echo $pagedata['excerpt'];?>">
	<meta property="og:locale" content="en_US">
	<meta name="generator" content="Postulate 1" />
	<link rel="alternate" type="application/rss+xml" title="<?php echo config('blog.title');?>" href="<?php echo site_url()?>/rss">
	<link rel="canonical" href="<?php echo $url;?>" />
<?php
	$header = ob_get_contents();
	ob_end_clean();	
	echo $header;
}

function post_foot(){
	$app = \Jolt\Jolt::getInstance();
	$footer = '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>';
	echo $footer;
}

function handlebars($template,$data){
/*
	$app = \Jolt\Jolt::getInstance();
	$logo = config('blog.logo');
	$cover = config('blog.cover');
	
	$data['@blog'] = array(
		'title' => config('site.name'),
		'description' => config('blog.description'),
		'url' => config('site.url'),
		'logo' => $logo,
		'cover' => $cover
	);

	$file = $app->store('theme_path').$template.'.hbs';
	if( file_exists($file) ){
		$template = file_get_contents($file);
	}else{
		$template = '';
	}
	$app->store('handle',$data);
		ob_start();
			$phpStr = LightnCandy::compile($template, array(
			    'flags' => LightnCandy::FLAG_STANDALONE,
			    'basedir' => Array(
			        $app->store('theme_path'),
			        $app->store('theme_path').'/partials/',
#			        $app->store('core_path').'/helpers/tpl/',
			    ),
			    'fileext' => Array(
			        '.hbs'
			    ),
			    'helpers' => Array(
					'asset' => function ($url) {
						$app = \Jolt\Jolt::getInstance();
		            	return $app->store('theme_url').'/assets/'.$url;
					},
					'ghost_head' => function () {
						$app = \Jolt\Jolt::getInstance();
						ob_start();
						$url = site_url().$app->getUri();
						$pagedata = $app->store("pagedata");
	<meta property="og:type" content="article">
	<meta property="article:author" content="503440548">
	<meta property="article:tag" content="dayone">
	<meta property="article:tag" content="nvremind">
	<meta property="article:tag" content="reminders">
	<meta property="article:tag" content="github">
	<meta property="article:tag" content="markdown">
	
	<meta name="twitter:card" content="summary">
	<meta name="twitter:site" content="@ttscoff">
	<meta name="twitter:creator" content="@ttscoff">
	<meta name="twitter:title" content="nvremind + Day One and more - BrettTerpstra.com">
	<meta name="twitter:description" content="In case you missed it this weekend, I wrote a little script called &quot;nvremind&quot; (pronounced &quot;nevermind&quot;). It scans folders of text files for occurrences of @remind(YYYY-mm-dd) tags and triggers">
	<meta property="og:title" content="nvremind + Day One and more - BrettTerpstra.com">
	<meta property="og:site_name" content="BrettTerpstra.com">
	
	<meta name="twitter:image" content="http://cdn3.brettterpstra.com/uploads/2013/06/nvreminddayone_lg.jpg" />
	<meta property="og:image" content="http://cdn3.brettterpstra.com/uploads/2013/06/nvreminddayone_lg.jpg" />
	<meta property="og:image:type" content="image/jpeg" />
	<meta property="og:image:width" content="750" />
	<meta property="og:image:height" content="206" />
	$pagedata['excerpt'] = excerpt_paragraph( $pagedata['excerpt'] );
	$pagedata['excerpt'] = strip_tags( $pagedata['excerpt'] );
?>
	<meta property="og:title" content="<?php echo $pagedata['title'];?>">
	<meta property="og:site_name" content="<?php echo config('site.name');?>">
	<meta property="og:url" content="<?php echo $url;?> ">
	<meta property="og:description" content="<?php echo $pagedata['excerpt'];?>">
	<meta property="og:locale" content="en_US">
	<meta name="generator" content="Posulate <?php echo GUST_VERSION?>" />
	<link rel="alternate" type="application/rss+xml" title="<?php echo config('blog.title');?>" href="<?php echo site_url()?>/rss">
	<link rel="canonical" href="<?php echo $url;?>" />
<?php
						$header = ob_get_contents();
						ob_end_clean();
						return Array($header, 'raw');
					},
					'ghost_foot' => function () {
						$app = \Jolt\Jolt::getInstance();
						$footer = '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>';
						return Array($footer, 'raw');
					},
					'page_url' => function ($page) {
						$app = \Jolt\Jolt::getInstance();
						$url = site_url();
						$tag = $app->store('tag');
						if( isset( $tag ) && !empty($tag) ){
							$url = $url . '/tag/'.$tag.'/page/'.$page;
						}else{
							$url = $url . '/page/'.$page;
						}
						return $url;
					},
					'pageUrl' => function ($page) {
						$app = \Jolt\Jolt::getInstance();
						$url = site_url();
						$tag = $app->store('tag');
						if( isset( $tag ) && !empty($tag) ){
							$url = $url . '/tag/'.$tag.'/page/'.$page;
						}else{
							$url = $url . '/page/'.$page;
						}
						return $url;
					},
					'raw' => function ($str) {
						$app = \Jolt\Jolt::getInstance();
						return Array($str, 'raw');
					},
					'encode' => function ($str) {
						$app = \Jolt\Jolt::getInstance();
						return Array($str, 'enc');
					},
			    )
			));
			$renderer = LightnCandy::prepare($phpStr);
		#	print_r($phpStr);
			echo $renderer($data);
		$output = ob_get_contents();
		ob_end_clean();
	echo $output;
*/
}

function store($key,$val=''){
	$app = \Jolt\Jolt::getInstance();
	if( $val = '' ){
		$app->store($key, $val);
	}
	return $app->store($key);
}
	
function computeSignature($url, $data = array()) {
	$app = \Jolt\Jolt::getInstance();
	$authToken = $app->option('cookies.secret');
	// sort the array by keys
	ksort($data);
	
	// append the data array to the url string, with no delimiters
	foreach ($data as $key => $value) {
		$url = $url . $key . $value;
	}
	// This function calculates the HMAC hash of the data with the key
	// passed in
	// Note: hash_hmac requires PHP 5 >= 5.1.2 or PECL hash:1.1-1.5
	// Or http://pear.php.net/package/Crypt_HMAC/
	$hmac = hash_hmac("sha1", $url, $authToken, true);
	return base64_encode($hmac);
}


function get_numbers(){
	$numbers = array();
	$users  = Model::factory('User')->find_many();
	foreach( $users as $user ){
		$numbers[] = $user->phone;
	}
	return $numbers;
}

function be_nice($status, $sep = '-') {
	return ucwords(str_replace($sep, ' ', $status));
}
function who_called($number) {
	if (preg_match('|^client:|', $number) ){
		$number = str_replace('client:','',$number);
		$ret = $number.' (client)';
	}else{
		$ret = format_phone($number);
	}
	return $ret;
}
function nice_date($date){
	$timestamp = strtotime($date);
	return date('M j, Y', $timestamp).'<br />'.date('H:i:s T', $timestamp );
}

/*
 *	format telephone number for display
 */
function format_telephone($phone = '', $convert = true, $trim = true){
	if ( empty($phone) ) {
		return false;
	}
	$phone = preg_replace("/[^0-9A-Za-z]/", "", $phone);
	$OriginalPhone = $phone;
	if ( $trim == true && (strlen($phone) > 11) ) {
		$phone = substr($phone, 0, 11);
	}

	if ( $convert == true && !is_numeric($phone) ) {
		$replace = array('2'=>array('a','b','c'),
			'3'=>array('d','e','f'),
			'4'=>array('g','h','i'),
			'5'=>array('j','k','l'),
			'6'=>array('m','n','o'),
			'7'=>array('p','q','r','s'),
			'8'=>array('t','u','v'),
			'9'=>array('w','x','y','z'));
		foreach($replace as $digit=>$letters) {
			$phone = str_ireplace($letters, $digit, $phone);
		}
	}
	
	$length = strlen($phone);
	switch ($length) {
		case 7:
			// Format: xxx-xxxx
			return preg_replace("/([0-9a-zA-Z]{3})([0-9a-zA-Z]{4})/", "$1-$2", $phone);
		case 10:
			// Format: (xxx) xxx-xxxx
			return preg_replace("/([0-9a-zA-Z]{3})([0-9a-zA-Z]{3})([0-9a-zA-Z]{4})/", "($1) $2-$3", $phone);
		case 11:
			// Format: x(xxx) xxx-xxxx
			return preg_replace("/([0-9a-zA-Z]{1})([0-9a-zA-Z]{3})([0-9a-zA-Z]{3})([0-9a-zA-Z]{4})/", "$1 ($2) $3-$4", $phone);
		default:
			// Return original phone if not 7, 10 or 11 digits long
			return $OriginalPhone;
	}
}
/*
 * format telephone number for storage in db, makes it easier to perform lookups when the phone number is cleaner.
 */
function clean_number($phone_number){
	return preg_replace("/[^0-9]/", "", $phone_number);
}
/*
 *	If the user member does not have a code, then we generate it for them.
 */
function generate_code( $digits_needed=8 ){
	$random_number=''; // set up a blank string
	$count=0;
	while ( $count < $digits_needed ) {
		$random_digit = mt_rand(0, 9);
		$random_number .= $random_digit;
		$count++;
	}
	return $random_number;
}

function passhash($password){
	$hash = config('password.hash');
	$salt = config('password.salt');
	switch( $hash ){
		case 'md5':		$password = md5($password);	break;
		case 'hash':	$password = md5($salt.sha1(md5($password)));	break;	//	md5ed with a salt of an sha1 of an md5..
		case 'hash2':	$password = hashit($password);
		default:		$password = $password;	break;
	}
	return $password;
}

function hashit($password){
	//	grab our default salt, create a unique salt of that salt plus entered password, then hash the password with the unique salt...
	$salt = config('password.salt');
	$salt = sha1( md5($password.$salt) );
	return md5( $password.$salt );	
}
 
function generateHashWithSalt($password) {
	$intermediateSalt = md5(uniqid(rand(), true));
	$salt = substr($intermediateSalt, 0, MAX_LENGTH);
	return hash("sha256", $password . $salt);
}

function generateHash($password) {
	if (defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH) {
		$salt = '$2y$11$' . substr(md5(uniqid(rand(), true)), 0, 22);
		return crypt($password, $salt);
	}
}

function verify($password, $hashedPassword) {
	return crypt($password, $hashedPassword) == $hashedPassword;
}

// make a series of digits into a properly formatted US phone number
function format_phone($number)
{
	$no = preg_replace('/[^0-9+]/', '', $number);

	if(strlen($no) == 11 && substr($no, 0, 1) == "1")
		$no = substr($no, 1);
	elseif(strlen($no) == 12 && substr($no, 0, 2) == "+1")
		$no = substr($no, 2);

	if(strlen($no) == 10)
		return "(".substr($no, 0, 3).") ".substr($no, 3, 3)."-".substr($no, 6);
	elseif(strlen($no) == 7)
		return substr($no, 0, 3)."-".substr($no, 3);
	else
		return $no;

}

function normalize_phone_to_E164($phone) {

	// get rid of any non (digit, + character)
	$phone = preg_replace('/[^0-9+]/', '', $phone);

	// validate intl 10
	if(preg_match('/^\+([2-9][0-9]{9})$/', $phone, $matches)){
		return "+{$matches[1]}";
	}

	// validate US DID
	if(preg_match('/^\+?1?([2-9][0-9]{9})$/', $phone, $matches)){
		return "+1{$matches[1]}";
	}


	// validate INTL DID
	if(preg_match('/^\+?([2-9][0-9]{8,14})$/', $phone, $matches)){
		return "+{$matches[1]}";
	}

	// premium US DID
	if(preg_match('/^\+?1?([2-9]11)$/', $phone, $matches)){
		return "+1{$matches[1]}";
	}

	return $phone;
}  

// return an abbreviated url string. ex: "http://example.com/123/page.htm" => "example.com...page.htm"
function short_url($string, $max_len = 30)
{
	$value = str_replace(array('http://', 'https://', 'ftp://'), '', $string);
	if(strlen($value) > $max_len) {
		$domain = reset(explode('/', $value));
		$domain_len = strlen($domain);
		if($domain_len + 3 >= $max_len) {
			return $domain;
		} else {
			$remaining = strlen($value) - $max_len - $domain_len + 3;
			return $domain . ($remaining > 0 ? '...' . substr($value, -$remaining) : '/');
		}
	} else {
		return $value;
	}
}

function random_str($length = 10) {
	$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_";

	$str = '';
	for($a = 0; $a < $length; $a++)
	{
		$str .= $chars[rand(0, strlen($chars) - 1)];
	}

	return $str;
}

function format_player_time($time_in_seconds) {
	$time_in_seconds = intval($time_in_seconds);
	$minutes = floor($time_in_seconds / 60);
	$seconds = $time_in_seconds - ($minutes * 60);

	return sprintf('%02s:%02s', $minutes, $seconds);
}

function format_time_difference($seconds='', $time='') {
	if(!is_numeric($seconds) || empty($seconds)) return true;

	$CI =& get_instance();
	$CI->lang->load('date');
	if(!is_numeric($time)) $time = date('U');
	$difference = abs($time-$seconds);
	$periods = array('date_second', 'date_minute', 'date_hour', 'date_day', 'date_week', 'date_month', 'date_year');
	$lengths = array('60','60','24');
	for($j=0; $difference >= $lengths[$j]; $j++) {
		if($j==count($lengths)-1) break;
		$difference /= $lengths[$j];
	}

	$difference = round($difference);
	if($difference == 0 && $j==0) $difference = 1;
	if($difference != 1) $periods[$j].= 's';

	if($j == 2 && $difference > 23)
		return date('M j g:i A', $seconds);
	return $difference.' '.strtolower($CI->lang->line($periods[$j])).' ago';
}

function sort_by_date($a, $b)
{
	$a_time = strtotime($a->created);
	$b_time = strtotime($b->created);
	if($a_time == $b_time)
	{
		return 0;
	}

	return ($a_time > $b_time)? -1 : 1;
}

function format_short_timestamp($time)
{
	$start_of_today = mktime(0, 0, 0, date("n"), date("j"), date("Y"));
	$start_of_this_year = mktime(0, 0, 0, 1, 1, date("Y"));

	// error_log("time: $time >>>> " . date("%r", $time));
	// error_log("start_of_today: $start_of_today >>>> " . date("%r", $start_of_today) );
	// error_log("start_of_this_year: $start_of_this_year >>>> " . date("%r", $start_of_this_year));

	if ($time > $start_of_today)
	{
		// return H:MM
		return date("g:i a", $time);
	}
	else if ($time > $start_of_this_year)
	{
		// return something like "Mar 3"
		return date("M j", $time);
	}
	else
	{
		// return M/D/YY
		return date("n/j/y", $time);
	}
}

function format_name($user)
{
	if(is_object($user))
	{
		if(!empty($user->first_name)
		   && !empty($user->last_name))
		{
			return "{$user->first_name} {$user->last_name}";
		}
		return $user->email;
	}

	if(is_array($user))
	{
		if(!empty($user['first_name'])
		   && !empty($user['last_name']))
		{
			return "{$user['first_name']} {$user['last_name']}";
		}

		return $user['email'];
	}

	return '';
}

function format_name_as_initials($user)
{
	if(is_object($user))
	{
		$initials = "";

		if ($user->first_name != '')
		{
			$initials .= substr($user->first_name, 0, 1);
		}

		if ($user->last_name != '')
		{
			$initials .= substr($user->last_name, 0, 1);
		}

		return strtoupper($initials);
	}

	return '';
}

function format_url($url)
{
	$str = $url;
	if(preg_match('/^https?:\/\/([^\/]+)\/.*\/([^\/]+)$/i', $url, $matches) > 0)
	{
		$str = $matches[1]
			.'/.../'
			. $matches[2];
	}

	return $str;
}

function html($data)
{
	if(is_string($data))
	{
		return htmlspecialchars($data, ENT_COMPAT, 'UTF-8', false);
	}

	if(is_array($data))
	{
		foreach($data as $key => $val)
		{
			if(is_string($val))
			{
				$data[$key] = htmlspecialchars($val, ENT_COMPAT, 'UTF-8', false);
			}
			else if(is_array($val))
			{
				$data[$key] = html($val);
			}
			else if(is_object($val))
			{
				$object_vars = get_object_vars($val);
				foreach($object_vars as $prop => $propval)
				{
					$data[$key]->{$prop} = html($propval);
				}
			}
		}
	}
	return $data;
}

function is_json($string) {
	return !empty($string) && is_string($string) && is_array(json_decode($string, true)) && json_last_error() == 0;
}

function generate_uuid() {
	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
		mt_rand( 0, 0xffff ),
		mt_rand( 0, 0x0fff ) | 0x4000,
		mt_rand( 0, 0x3fff ) | 0x8000,
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
	);
}

function slugify($text){
	$text = str_replace("?",'',$text);

	// replace non letter or digits by -
	$text = preg_replace('~[^\\pL\d]+~u', '-', $text);
	
	// trim
	$text = trim($text, '-');
	
	// transliterate
	if (function_exists('iconv')){
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
	}
	
	// lowercase
	$text = strtolower($text);
	
	// remove unwanted characters
	$text = preg_replace('~[^-\w]+~', '', $text);
	
	if (empty($text)){
		return 'n-a';
	}
	return $text;
}

class SiteConfig{
	private $settings;
	private $environment;
	private $configPath;
	
	public function __construct( $configPath = './config', $environment = '' ){
		$this->configPath = $configPath;
		$this->environment = $environment;
	}
	public function get($key){
		$key = explode('.',$key);
		$file = $key[0];
		$key = $key[1];
		$items = $this->load($file);
		if( isset($items[ $key ]) ){
			return $items[ $key ] ;
		}
	}
	private function load($file){
		$items = array();
		$filename = array();
		$filename[] = $this->configPath;
		if( $this->environment != '' ){
			$filename[] = $this->environment;
		}
		$filename[] = $file.'.php';
		$filename = implode('/',$filename);
		if( file_exists( $filename ) ){
			$items = $this->mergeEnvironment($items, $filename);
		}else{
			$filename = array();
			$filename[] = $this->configPath;
			$filename[] = $file.'.php';
			$filename = implode('/',$filename);
			if( file_exists( $filename ) ){
				$items = $this->mergeEnvironment($items, $filename);
			}
		}
		return $items;
	}
	protected function mergeEnvironment(array $items, $file){
		return array_replace_recursive($items, $this->getRequire($file));
	}
	private function getRequire($file){
		return require $file;		
	}
}


//	Form Builder Functions	--------------------------------------------------------------------------------------------
function generate_nonce(  ){
	$app = \Jolt\Jolt::getInstance();
	// Checks for an existing nonce before creating a new one
	if (empty($_SESSION['nonce']) ) {
		$app->nonce = base64_encode(uniqid(NULL, TRUE));
		$_SESSION['nonce'] = $app->nonce;
	}
	return $app->nonce;
}
function check_nonce(  ){

echo $_SESSION['nonce'].' ---- '.$_POST['nonce'];

	if ( isset($_SESSION['nonce']) && !empty($_SESSION['nonce'])  && isset($_POST['nonce']) && !empty($_POST['nonce']) && $_SESSION['nonce']===$_POST['nonce']
	) {
		$_SESSION['nonce'] = NULL;
		return TRUE;
	} else {
		return FALSE;
	}
}

//	This will create a form that will display fields based on the blueprint used.

function content_form($post_type,$post_id = '',$action='',$thankyou = array()){
	$app = \Jolt\Jolt::getInstance();
	//	grab blueprint based on content type..
	$blueprint = $app->store('blueprints');
	$blueprint = $blueprint[ $post_type ];
//	print_r( $blueprint );
	$nonce = generate_nonce();

	if( isset($_POST['title']) ){
//		print_r($_POST);
		$c_url = site_url().$_SERVER['REQUEST_URI'];
		$post 				= array();
		$post['title'] 		= $_POST['title'];
		$post['name'] 		= new_slug( slugify( $_POST['title'] ) );
		$post['type'] 		= $post_type;
		$post['user_id'] 	= '';
		$post['date'] 		= date('Y-m-d H:i:s');
		$post['modified']	= date('Y-m-d H:i:s');
		$post['password'] 	= '';
		$post['status'] 	= 'draft';	//	regardless of post type, we never want a post actually published from here..
		$post['parent'] 	= 0;
		$post['guid'] 		= generate_uuid();
		$post['category'] 	= '';
		$restricted = array('_id','title','name','user_id','date','modified','guid','type','status','parent','password');
		foreach($blueprint['fields'] as $fname=>$field ){
			if( isset($post[$k]) )	continue;
			//	There are fields we don't want to change... This makes sure we don't...
			if( in_array($restricted,$fname) )	continue;
			if( !isset($_POST[ $fname ]) ){
				if( $field['type'] == 'related' ){
					$_POST[ $fname ] = array();
				}else{
					$_POST[ $fname ] = '';
				}
			}
		}
		foreach($_POST as $k=>$v){
			if( isset($post[$k]) )	continue;
			if( in_array($restricted,$k) )	continue;
			$post[$k] = $v;
		}
		if( isset($_FILES) ){
			foreach($_FILES as $k=>$file){
				if( in_array($restricted,$k) )	continue;
				$uploaddir = $this->app->store('upload_path');
				$uploadfile = $uploaddir . basename($file['name']);
				$uploadurl = $this->app->store('upload_url') . basename($file['name']);
				if( file_exists($uploadfile) ){
					$post[$k] = $uploadurl;
				}else{
					if ( move_uploaded_file($file['tmp_name'], $uploadfile) ) {
						$post[$k] = $uploadurl;
					}
				}
			}
		}
		$app->db->insert('post', $post );
		unset($_POST);
		if( isset($thankyou['url']) ){
			echo '<script>top.location.href="'.$thankyou['url'].'";</script>';			
		}else if( isset($thankyou['msg']) ){
			echo '<p>'.$thankyou['msg'].'</p>';
		}else{
			echo '<p><strong>Thank you, your post has been saved.</strong></p>';
		}
	}
?>
	<form role="form"  method="POST" action="<?php echo $action?>" enctype="multipart/form-data">
	<input type="hidden" name="nonce" value="<?php echo $nonce?>" />
	<fieldset>
		<div class="form-group">
			<label for="title"><?=( isset($blueprint['titlelabel']) ? $blueprint['titlelabel'] : "Title")?></label>
			<input type="text" class="form-control" id="title" name="title" placeholder="<?=( isset($blueprint['titlelabel']) ? $blueprint['titlelabel'] : "Enter Title")?>" value="<?php echo ( isset($post) ? $post['title'] : '');?>" required>
		</div>
<?php
		foreach($blueprint['fields'] as $fname=>$field ){
			if( $field['adminonly'] )	continue;
			form_field($fname,$field,$post);
		}
?>
		<button type="submit" class="btn btn-primary">Save</button>
	</fieldset>
	</form>
<?php
}

function form_field($fname,$field,$post = array()){
	$flabel = $field['label'];
	$ftype = $field['type'];
	
	switch($ftype){
		case 'related':
#			print_r($field);
#			print_r( $post[$fname] );
			$ptype = (String) $field['posttype'];
			$posts = get_posts($ptype,'any',1,1000);
			if( count( $posts['posts'] ) ){
?>
<div class="form-group">
	<label for="title"><?=$flabel?></label>
		<div style="background: none repeat scroll 0 0 #fff;border: 1px solid #DDDDDD;height: 105px;overflow-y: scroll;padding: 10px;">
			<div class="row">
<?php
 				foreach( $posts['posts'] as $p ){
 					$c = $p['id'];
 					$sel = '';
 					if( isset($post) ){
	 					if( isset($post[$fname]) ){
		 					foreach( $post[$fname] as $k ){
		 						if( $c == $k ){
				 					$sel = " CHECKED=TRUE ";
				 					break;
				 				}
		 					}
		 				}
 					}
?>
					<div class="col-xs-6" style="margin-bottom:5px;">
						<div class="input-group">
							<span class="input-group-addon">
								<input name="<?=$fname?>[]" id="<?=$fname?>[]" type="checkbox" value="<?=$c?>" <?=$sel?>>
							</span>
							<input type="text" class="form-control" readonly="true" style="background:#fff;" value="<?=$p['title']?>">
						</div><!-- /input-group -->
					</div><!-- /.col-lg-6 -->

<?php 			
				}
?>
			</div>
		</div>
</div>
<?php
				break;
			}
		case 'file':
?>
			<div class="form-group">
				<label for="title"><?=$flabel?></label>				
				<input type="file" class="form-control <?=$flcass?>" id="<?= $fname ?>" name="<?= $fname ?>" >
				<p class="help-block">
<?php 	if( isset($post) ){	?>
	Current File: <a class="modalButton" data-toggle="modal" data-src="<?=$post[$fname]?>" data-title="<?=$post[$fname]?>" data-target="#modalbox"><?=$post[$fname]?></a>
<?php 	}	?>					
				</p>
			</div>
<?php
			break;
		case 'dropdown':
?>
			<div class="form-group">
				<label for="title"><?=$flabel?></label>				
				<select class="form-control <?=$flcass?>" id="<?= $fname ?>" name="<?= $fname ?>">
<?php
				$options = explode(";",$field['options']);
				foreach( $options as $option ){
					$sel = '';
					if( isset($post) ){
						if( $post[$fname] == $option  ){
							$sel = 'SELECTED';
						}
					}
?>
						<option <?=$sel?> value="<?= $option ?>"><?=$option?></option>
<?php			
				}
?>
				</select>
			</div>
<?php
			break;
		case 'radio':
?>
			<div class="form-group">
				<label for="title"><?=$flabel?></label>				
			</div>
<?php
			$options = explode(";",$field['options']);
			foreach( $options as $option ){
				$sel = '';
				if( isset($post) ){
					if( $post[$fname] == $option  ){
						$sel = 'CHECKED=TRUE';
					}
				}
?>
				<div class="form-group">
					<label for="<?= $id ?>">
						<input <?=$sel?> type="radio" id="<?= $fname ?>" name="<?= $fname ?>" value="<?= $option ?>">&nbsp;&nbsp;<?=$option?>
					</label>
				</div>
<?php			
			}
			break;
		case 'checkbox':
?>
			<div class="form-group">
				<label for="title"><?=$flabel?></label>				
			</div>
<?php
			$options = explode(";",$field['options']);
			foreach( $options as $option ){
				$sel = '';
				if( isset($post[$fname]) ){
					foreach( $post[$fname] as $k ){
						if( $option == $k ){
		 					$sel = " CHECKED=TRUE ";
		 					break;
		 				}
					}
				}
?>
<div class="form-group">
				<label for="<?= $id ?>">
					<input <?=$sel?> type="checkbox" id="<?= $fname ?>[]" name="<?= $fname ?>[]" value="<?= $option ?>">&nbsp;&nbsp;<?=$option?>
				</label>
</div>
<?php			
			}
			break;
		case 'hidden':
?>
			<input type="hidden" id="<?=$fname?>" name="<?=$fname?>" value="<?php echo ( isset($post) ? $post[$fname] : '');?>" />
<?php
		case 'section':
			echo '<div class="col-md-12">';
			echo '<h4>'.$flabel.'</h4>';
			echo '</div>';
			break;
		case 'textarea':
?>
<div class="form-group">
	<label for="title"><?=$flabel?></label>
<textarea class="form-control" id="<?=$fname?>" name="<?=$fname?>" rows="5">
<?php echo ( isset($post) ? $post[$fname] : '');?>
</textarea>
</div>
<?php
			break;
		case 'text':
		default:
?>
<div class="form-group">
	<label for="title"><?=$flabel?></label>
	<input type="text" class="form-control" id="<?=$fname?>" name="<?=$fname?>" placeholder="Enter <?=$fname?>" value="<?php echo ( isset($post) ? $post[$fname] : '');?>">
</div>
<?php			
			break;
	}
}

function form_field_old($field,$label, $id,$name,$val=''){
	if( $field['type'] == 'section' ){
		echo '<div class="col-md-12">';
		echo '<h4>'.$label.'</h4>';
		echo '</div>';
	}else{
		$classn = 'form-group';
		if( $field['type'] == 'checkbox'){
			$classn = 'checkbox';
		}else if( $field['type'] == 'radio'){
			$classn = 'radio';
		}
		$flcass = '';
		if( isset($field['fclass']) ){
			$flcass = $field['fclass'];
		}
?>
		<div class="<?=$field['class']?>">
			<div class="<?=$classn?>">
<?php 	switch( $field['type'] ){
 			case 'select':
?>
				<label for="<?= $id ?>"><?=$label?></label>
				<select class="form-control <?=$flcass?>" id="<?= $id ?>" name="<?= $name ?>" <?=$field['extras']?>>
<?php			foreach( $field['default'] as $v ){	?>
<?php
					$sel = '';
					if( $val == $v ){
						$sel = "SELECTED";
					}
?>
					<option value="<?=$v?>" <?=$sel?>><?= ucwords($v)	?></option>
<?php 			}	?>				
				</select>
<?php
				break;
 			case 'yesno':
?>
				<label for="<?= $id ?>"><?=$label?></label>
				<select class="form-control <?=$flcass?>" id="<?= $id ?>" name="<?= $name ?>" <?=$field['extras']?>>
<?php			foreach( $field['default'] as $v ){	?>
<?php
					$sel = '';
					if( $val == $v ){
						$sel = "SELECTED";
					}
?>
					<option value="<?=$v?>" <?=$sel?>><?= ucwords($v)	?></option>

<?php 			}	?>				
				</select>
<?php
				break;
			case 'checkbox':
			case 'radio':
				$sel = '';
				if( $val == $field['default']  ){
					$sel = 'CHECKED=TRUE';
				}
?>
				<label for="<?= $id ?>"><input <?=$sel?> type="<?= $field['type']?>" id="<?= $id ?>" name="<?= $name ?>" value="<?= $field['default'] ?>" <?=$field['extras']?> class="<?=$flcass?>" ><?=$label?></label>
<?php			
				break;
			case 'textarea':
?>
<label for="<?= $id ?>"><?=$label?></label>
<textarea class="form-control <?=$flcass?>" id="<?= $id ?>" name="<?= $name ?>" <?=$field['extras']?>><?= $field['default'] ?></textarea>
<?php 	

				break;
			default:
?>
				<label for="<?= $id ?>"><?=$label?></label>
				<input type="<?= $field['type']?>" class="form-control <?=$flcass?>" id="<?= $id ?>" name="<?= $name ?>" value="<?= $field['default'] ?>"<?=$field['extras']?>>
<?php 	
				break;
		}	
?>
			</div>
		</div>
<?php	
	}
}

//	\Form Builder Functions	--------------------------------------------------------------------------------------------