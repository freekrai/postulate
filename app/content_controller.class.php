<?php
/*
 * Content Controller
 * Handles all functions related to Content.
 */
class contentController extends \Jolt\Controller{
	public $type;
    public function my_name($name = 'default'){
		$this->app->render( 'page', array(
			"pageTitle"=>"Greetings ".$this->sanitize($name)."!",
			'title'=>'123',
			"body"=>"Greetings ".$this->sanitize($name)."!"
		));
    }
	public function mylist($posttype){
		$this->type = $posttype;
		$this->app->condition('signed_in');
		$blueprint = $this->app->store('blueprints');
		$blueprint = $blueprint[ $posttype ];
		$me = $this->app->db->findOne('user', array( '_id'=>$this->app->store('user') ));
	
		//	query post for post types that match this post type
		$posts = $this->app->db->find('post', array('filter'=>array('type'=>$posttype)) );
		$this->app->render( 'admin/content/list', array(
			"pageTitle" => $blueprint['title'],
			"me" => $me,
			'blueprint' => $blueprint,
			'type' => $posttype,
			'posts' => $posts
		),'admin/inside');
	}
	public function edit( $posttype, $post_id ){
		$this->app->condition('signed_in');
		$me = $this->app->db->findOne('user', array( '_id'=>$this->app->store('user') ));

		$blueprint = $this->app->store('blueprints');
		$blueprint = $blueprint[ $posttype ];

		$post = $this->app->db->findOne('post', array( '_id'=>$post_id ));
		$this->app->render( 'admin/content/form', array(
			"pageTitle" => "Edit ".$post['title'],
			"me" => $me,
			'type'=> $posttype,
			'blueprint' => $blueprint,
			"action" => site_url()."/admin/content/".$posttype."/edit/".$post_id.( isset($_REQUEST['embed']) ? '?embed='.$_REQUEST['embed'] : null ),
			'post' => $post
		),
		( isset($_REQUEST['embed']) ? 'admin/embedded' : 'admin/inside' ) );
	}
	public function save( $posttype, $post_id ){
		$this->app->condition('signed_in');

		$blueprint = $this->app->store('blueprints');
		$blueprint = $blueprint[ $posttype ];

		$me = $this->app->db->findOne('user', array( '_id'=>$this->app->store('user') ));
		$slug = slugify( $_POST['title'] );
		if( isset($_POST['prefix']) ){
			$slug = $_POST['prefix'].$slug;
		}
		$post = $this->app->db->findOne('post', array( '_id'=>$post_id ));
		$post['title'] 		= $_POST['title'];
		$post['name'] 		= new_slug( $slug, $post_id);
		$post['modified'] = date('Y-m-d H:i:s');
		$post['status'] 	= $_POST['status'];
		$restricted = array('_id','title','name','user_id','date','modified','guid','type','status','parent','password');
		foreach($blueprint['fields'] as $fname=>$field ){
			//	There are fields we don't want to change... This makes sure we don't...
			if( isset($post[$k]) )	continue;
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
			//	There are fields we don't want to change... This makes sure we don't...
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
		$this->app->db->save('post', $post );
		$url = site_url().'/admin/content/'.$posttype;
		if( !isset($_REQUEST['embed']) ){
			$url = site_url()."/admin/content/".$posttype."/edit/".$post_id;
		}
		echo '<script>top.location.href="'.$url.'";</script>';
		exit;
	}
	public function addnew( $posttype ){
		$this->app->condition('signed_in');
		$me = $this->app->db->findOne('user', array( '_id'=>$this->app->store('user') ));

		$blueprint = $this->app->store('blueprints');
		$blueprint = $blueprint[ $posttype ];

		$this->app->render( 'admin/content/form', array(
			"pageTitle" => "Add New ".$blueprint['title'],
			"me" => $me,
			'type' => $posttype,
			'blueprint' => $blueprint,
			"action" => site_url()."/admin/content/".$posttype."/new".( isset($_REQUEST['embed']) ? '?embed='.$_REQUEST['embed'] : null ),
		),
		( isset($_REQUEST['embed']) ? 'admin/embedded' : 'admin/inside' ) );
#		print_r($user->id);
	}
	public function savenew( $posttype ){
		$this->app->condition('signed_in');

		$blueprint = $this->app->store('blueprints');
		$blueprint = $blueprint[ $posttype ];

		$me = $this->app->db->findOne('user', array( '_id'=>$this->app->store('user') ));
		$post 				= array();
		$post['title'] 		= $_POST['title'];
		$post['name'] 		= new_slug( slugify( $_POST['title'] ) );
		$post['type'] 		= $posttype;
		$post['user_id'] 	= $this->app->store('user');
		$post['date'] 		= date('Y-m-d H:i:s');
		$post['modified']	= date('Y-m-d H:i:s');
		$post['password'] 	= '';
		$post['status'] 	= $_POST['status'];
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
		$this->app->db->insert('post', $post );

		$post_id = $post['_id'];
		$url = site_url().'/admin/content/'.$posttype;
		if( !isset($_REQUEST['embed']) ){
			$url = site_url()."/admin/content/".$posttype."/edit/".$post_id;
		}
		echo '<script>top.location.href="'.$url.'";</script>';
		exit;
	}
	public function delete( $posttype, $post_id ){
		$this->app->condition('signed_in');
		$me = $this->app->db->findOne('user', array( '_id'=>$this->app->store('user') ));
		$this->app->db->remove('post', array( '_id'=>$post_id ));
		header("Location: ".site_url()."/admin/content/".$posttype);
		exit;
	}
}