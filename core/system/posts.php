<?php
function format_post($post) {
	$date = $post['date'];
	$return = array(
		"id" => $post['_id'],
		"title" => $post['title'],
		"type" => $post['type'],
		"slug" => $post['name'],
		"name" => $post['name'],
		"permalink" => site_url().'/'.$post['name'],
		"views" => isset($post['views']) ? $post['views'] : 0,
		"status" => $post['status'],
		"updated_at" => date(DATE_W3C,strtotime($date)),
		"published_at" => date(DATE_W3C,strtotime($date)),
		"created_at" => date(DATE_W3C,strtotime($date)),
		"author_id" => $post['user_id'],
		"author" => '',//format_user( $post->post_author ),
	);
	foreach($post as $key=>$meta){
		if( isset($return[$key]) )	continue;
		$return[$key] = $meta;
	}
	return $return;
}

function get_post($post_id) {
	$ret = array();
	$app = \Jolt\Jolt::getInstance();
	$posts = $app->db->findOne('post', array('_id'=>$post_id) );
	if( $posts['_id'] ){
		$ret = format_post($posts);
	}
	return $ret;
}
function get_post_by_slug($slug) {
	$ret = array();
	$app = \Jolt\Jolt::getInstance();
	$posts = $app->db->findOne('post', array('name'=>$slug) );
	if( $posts['_id'] ){
		$ret = format_post($posts);
	}
	return $ret;
}


function get_field($key,$post_id){
	
}

function new_post( $type = 'post' ){
/*
	//	create draft post...
	$post = Model::factory('Post')->create();
	$post->status = 'draft';
	$post->created_at = time().'013';
	$post->created_by = 1;
	$post->author_id = 1;
	$post->markdown = 'Your Content Here';
	$post->html = 'Your Content Here';
	$post->uuid = generate_uuid();
	if( $type == 'page' ){	//	page...
		$post->page = 1;
		$post->title = 'Draft Page';
		$post->slug = new_slug( slugify( 'Draft Page' ) );
	}else{	//	post...
		$post->page = 0;
		$post->title = 'Draft Post';
		$post->slug = new_slug( slugify( 'Draft Post' ) );
	}
	$post->save();
	$id = $post->id;
	return $id;
*/
}

function upload_file( $id ){
/*
	$app = \Jolt\Jolt::getInstance();
	$upload_path = $app->store('upload_path');
	$upload_url = $app->store('upload_url');

	$post  = Model::factory('Post')->find_one( $id );

    $uploadedfile = $_FILES['uploadimage'];
    
    $ext = pathinfo($uploadedfile['name'], PATHINFO_EXTENSION);
    
	$new_file_name = $id.'-'.$uploadedfile['name'];	//	$post->slug.'.'.$ext;
	$new_file = $upload_path.$new_file_name;
	if( file_exists($new_file) ){
		unlink( $new_file );
	}
	$new_file_url = $upload_url.$new_file_name;
	move_uploaded_file($uploadedfile['tmp_name'], $new_file);
	//	upload file
	return $new_file_url;
*/
}

function new_slug($slug, $id = ''){
	$a = 1;
	$i = 1;
	while( $a == 1 ){
		$app = \Jolt\Jolt::getInstance();
		$post = $app->db->findOne('post', array('name'=>$slug) );
		if( isset($post['_id']) && ($post['_id'] != $id) ){
			$slug = $slug.'-'.$i;
		}else{
			$a = 2;
		}
		$i++;
	}
	return $slug;
}

function count_posts($type='post',$status='any',$page=1,$limit=15) {
	$app = \Jolt\Jolt::getInstance();
	if (!$status) $status = 'any';
	if (!$limit) $limit = 15;
	if (!$page) $page = 1;
	if (!$type) $type = 'post';
	if( $status != 'any' ){
		$count = $app->db->find('post', array("filter"=>array(
			'type'=>$type,
			'status'=>$status
		)) )->count();
	}else{
		$count = $app->db->find('post', array("filter"=>array(
			'type'=>$type
		)) )->count();
	}
	return $count;	
}
function get_posts($type='post',$status='any',$page=1,$limit=15) {
	$app = \Jolt\Jolt::getInstance();
	if (!$status) $status = 'any';
	if (!$limit) $limit = 15;
	if (!$page) $page = 1;
	if (!$type) $type = 'post';
	if( $page > 1 ){
		$offset = ($page - 1) * $limit;
	}else{
		$offset = 0;
	}

	if( $status != 'any' ){
		$filter= array(
			'type'=>$type,
			'status'=>$status
		);
	}else{
		$filter = array(
			'type'=>$type
		);
	}
	$options = array(
		'filter'=>$filter,
		'limit' => $limit,
		'skip' => $offset,
		'sort' => array('date'=>-1)		
	);
	$posts = $app->db->find('post', $options);
	$count = $posts->count();
	
	$ret = array();
	$ret['total'] = $count;
	$ret['pages'] = ceil( $count / $limit );

	foreach($posts as $post){
		$ret['posts'][] = format_post($post);
	}
	
	if ($ret['pages'] > 1 && $page < $ret['pages'])
		$ret['next'] = $page + 1;
		
	if (isset($ret['next']) && ($ret['next'] > $ret['pages']))
		$ret['next'] = $ret['pages'];

	if ($ret['pages']>1 && $page>1)
		$ret['prev'] = $page-1;

	return $ret;
}

function content_markdown($post_content) {
	return $post_content;
}

function content_html($post_content) {
	$app = \Jolt\Jolt::getInstance();
	return $app->store('markdown')->transformMarkdown( $post_content );
}

/*
	parse_post is used to grab any meta data defined at top of post between {{ and }}
*/
function parse_post($content){
	return $article;
}

function get_permalink($post_id){
	$app = \Jolt\Jolt::getInstance();
//	$url = $app->option('site.url');
//	$post = Model::factory('Post')->find_one( $post_id );	
//	return $url.'/'.$post->name;
}

//	Post processing utilities.....
function the_excerpt( $content ){
	$st = new Summarizer();
	$summary = $st->get_summary($content);
	if( empty($summary) ){
		$summary = excerpt_paragraph( $content );
	}
	return $summary;
}

function excerpt_paragraph($html, $max_char = 100, $trail='...' ){
	// temp var to capture the p tag(s)
	$matches= array();
	if ( preg_match( '/<p>[^>]+<\/p>/', $html, $matches) ){
		// found <p></p>
		$p = strip_tags($matches[0]);
	} else {
		$p = strip_tags($html);
	}
	//shorten without cutting words
	$p = short_str($p, $max_char );
	
	// remove trailing comma, full stop, colon, semicolon, 'a', 'A', space
	$p = rtrim($p, ',.;: aA' );
	
	// return nothing if just spaces or too short
	if (ctype_space($p) || $p=='' || strlen($p)<10) { return ''; }
	return '<p>'.$p.$trail.'</p>';
}
//

/**
* shorten string but not cut words
* 
**/
function short_str( $str, $len, $cut = false ){
	if ( strlen( $str ) <= $len ) { return $str; }
	$string = ( $cut ? substr( $str, 0, $len ) : substr( $str, 0, strrpos( substr( $str, 0, $len ), ' ' ) ) );
	return $string;
}
//

function get_title_from_content( $content ) {
	static $strlen =  null;
	if ( !$strlen ) {
		$strlen = function_exists( 'mb_strlen' )? 'mb_strlen' : 'strlen';
	}
	$max_len = 40;
	$title = $strlen( $content ) > $max_len? html_excerpt( $content, $max_len ) . '...' : $content;
	$title = trim( strip_tags( $title ) );
	$title = str_replace("\n", " ", $title);
	if ( !$title ) {
		if ( preg_match("/<object|<embed/", $content ) )
			$title = __( 'Video Post', 'p2' );
		elseif ( preg_match( "/<img/", $content ) )
			$title = __( 'Image Post', 'p2' );
	}
	return $title;
}

function strip_all_tags($string, $remove_breaks = false) {
	$string = preg_replace( '@<(script|style)[^>]*?>.*?@si', '', $string );
	$string = strip_tags($string);
	if ( $remove_breaks )	$string = preg_replace('/[\r\n\t ]+/', ' ', $string);
	return trim($string);
}

function html_excerpt( $str, $count ) {
	$str = strip_all_tags( $str, true );
	$str = mb_substr( $str, 0, $count );
	$str = preg_replace( '/&[^;\s]{0,6}$/', '', $str );
	return $str;
}

class Summarizer{
	public $sentences_dic = array();
	public $orginal;
	public $summary;
	
	public function __constructor(){
		return true;		
	}
	public function split_content_to_sentences($content){
        $content = str_replace("\n",". ",$content);
        return explode(". ",$content);
	}

    public function split_content_to_paragraphs($content){
		return explode("\n\n",$content);
	}

	public function sentences_intersection($sent1, $sent2){
		$s1 = explode(" ",$sent1);
		$s2 = explode(" ",$sent2);
		$cs1 = count($s1);
		$cs2 = count($s2);
		if( ($cs1 + $cs2) == 0 )	return 0;

		$i = count( array_intersect($s1,$s2) );
		$avg = $i / (($cs1+$cs2) / 2);
		return $avg;
	}
	public function format_sentence($sentence){
//		$sentence = preg_replace('/[^a-z\d ]/i', '', $sentence);
		$sentence = preg_replace("/[^a-zA-Z0-9\s]/", "", $sentence);
		$sentence = str_replace(" ","",$sentence);
        return $sentence;
	}
	public function get_sentences_ranks($content){
		$sentences = $this->split_content_to_sentences($content);
		$n = count( $sentences );
		$values = array();
		for($i = 0;$i <= $n;$i++){
			$s1 = $sentences[$i];
			for($j = 0;$j <= $n;$j++){
				if( isset($sentences[$j]) ){
					$s2 = $sentences[$j];
					$values[$i][$j] = $this->sentences_intersection($s1, $s2);
				}
			}
		}
		$sentences_dic = array();
		for($i = 0;$i <= $n;$i++){
			$score = 0;		
			for($j = 0;$j <= $n;$j++){
				if( $i == $j)	continue;
				$score = $score + $values[$i][$j];
			}
			$sentences_dic[ $this->format_sentence( $sentences[$i] ) ] = $score;
		}
		$this->sentences_dic = $sentences_dic;
		return $sentences_dic;
	}

	public function get_best_sentence($paragraph){
		$sentences = $this->split_content_to_sentences($paragraph);
		if( count($sentences) < 2 )	return "";
		$best_sentence = "";
		$max_value = 0;
		foreach( $sentences as $s){
			$strip_s = $this->format_sentence($s);
			if( !empty($strip_s) ){
				$me = $this->sentences_dic[$strip_s];
				if( $me > $max_value ){
					$max_value = $me;
					$best_sentence = $s;
				}
			}
		}
        return $best_sentence;
	}

    public function get_summary($content){
		$sentences_dic = $this->get_sentences_ranks($content);
		$paragraphs = $this->split_content_to_paragraphs($content);

		$this->original = $content;

		$summary = array();
		foreach( $paragraphs as $p ){
			$sentence = $this->get_best_sentence($p);
			if( !empty($sentence) ){
				$summary[$sentence] = $sentence;	
			}
		}
		$this->summary = implode("\n",$summary);
		return $this->summary;
	}
	function how_we_did(){
	    print "<hr />";
	    print "Original Length ". strlen($this->original);
		echo "<br />";
	    print "Summary Length ".strlen($this->summary);
		echo "<br />";
	    print "Summary Ratio: ".(100 - (100 * (strlen($this->summary) / (strlen($this->original)))));
		echo "<br />";
	}

}