<?php
class frontendController extends \Jolt\Controller{

	public function tag( $slug, $page=1){
		$limit = config('posts.perpage');
		if (!$page) $page = 1;
		if( $page > 1 ){
			$offset = ($page - 1) * $limit;
		}else{
			$offset = 0;
		}
//		echo $slug;
		$tag = get_post_by_slug( '/tag/'.$slug );
//		get all posts related to this post....
		$options = array(
			'filter'=> array('status'=>'published'),
			'sort' => array("date"=>-1)
		);

		$posts = $this->app->db->find( 'post', $options );
		$tagged = array();
		foreach($posts as $post){
			$post = format_post($post);
			if( isset($post['tag']) ){
				if( in_array($tag['id'],$post['tag']) ){
					$tagged[] = $post;
				}
			}
		}
		$count = count( $tagged );
		$ret = array();
		$ret['total'] = $count;
		$ret['pages'] = ceil( $count / $limit );
		$ret['posts'] = $tagged;

		if ($ret['pages'] > 1 && $page < $ret['pages'])
			$ret['next'] = $page + 1;

		if (isset($ret['next']) && ($ret['next'] > $ret['pages']))
			$ret['next'] = $ret['pages'];
	
		if ($ret['pages']>1 && $page>1)
			$ret['prev'] = $page-1;

		$this->app->render( 'frontend/tag', array(
			"pageTitle" => config('site.name'),
			'meta_title' => config('site.name'),
			'meta_description' => '',
			'tag' => $tag,
			'posts' => $ret,
			'page' => $page
		),'frontend/layout' );

//		$count = $posts->count();
	}

	public function homepage( $page = 1 ){

		$this->app->render( 'frontend/home', array(
			"pageTitle" => config('site.name'),
			'meta_title' => config('site.name'),
			'meta_description' => '',
			'page' => $page
		),'frontend/layout' );
	}

	public function single( $slug ){
		$posts = $this->app->db->findOne('post', array('name'=>$slug) );
		if( $posts['_id'] ){
			if( isset($posts['views']) ){
				$posts['views']++;
			}else{
				$posts['views'] = 0;
			}
			$this->app->db->save('post', $posts );
			$md = $this->app->store('markdown');
			$post = format_post($posts);
			$this->app->store("pagedata",$post);
			$file = 'post';
#			echo '<pre>'.print_r( $post, true ).'</pre>';
			$file = 'frontend/single';
			if( $post['type'] != 'post' ){
				$fname = $this->app->store('theme_path').'frontend/page-'.$post['name'].'.php';
				if( file_exists($fname) ){
					$file = 'frontend/page-'.$post['name'];
				}else{
					$fname = $this->app->store('theme_path').'frontend/page.php';
					if( file_exists($fname) ){
						$file = 'frontend/page';
					}
				}
			}
			$this->app->render( $file, array(
				'body_class' => $post['type'].'-template',
				'post_class' => $post['type'],
				"pageTitle" => $post['title'],
				'meta_title' => $post['title'].' - '.config('site.name'),
				'meta_description' => '',
				'post' => $post,
			),'frontend/layout' );
		}else{
			notfound();
		}
		exit;
	}
	
	public function rss( $slug = '' ){
		header('Content-Type: application/rss+xml');
		$md = $this->app->store('markdown');
		$postList = array();
		if( $slug == '' ){
			$posts = get_posts('post','published',1,config('posts.perpage'));
			foreach( $posts['posts'] as $post ){
				$body = content_html($post['body']);
				$excerpt = the_excerpt( $body );
				$postList[] = array(
					'url'=>$post['permalink'],
					'date'=>date("F j, Y", strtotime($post['published_at'])),
					'title'=>$post['title'],
					'tags' => array(),
					'excerpt'=>$excerpt,
					'html'=>$body,
					'post_class' => 'post',
				);
			}
		}else{
			$posts = $this->app->db->findOne('post', array('name'=>$slug) );
			if( $posts['_id'] ){
				$md = $this->app->store('markdown');
				$post = format_post($posts);
				$body = content_html($post['body']);
				$excerpt = the_excerpt( $body );
		
				$postList[] = array(
					'url'=>$post['permalink'],
					'date'=>date("F j, Y", strtotime($post['published_at'])),
					'title'=>$post['title'],
					'tags' => array(),
					'excerpt'=>$excerpt,
					'html'=>$body,
					'post_class' => 'post',
				);
			}
		}
		$feed = new Suin\RSSWriter\Feed();
		$channel = new Suin\RSSWriter\Channel();
		$channel
			->title(config('blog.title'))
			->description(config('blog.description'))
			->url(site_url())
			->appendTo($feed);
		foreach($postList as $p){
			$item = new Suin\RSSWriter\Item();
			$item
				->title($p['title'])
				->description($p['html'])
				->url($p['url'])
				->appendTo($channel);
		}
		echo $feed;		
	}
}
?>