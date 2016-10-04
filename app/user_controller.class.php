<?php
/*
 * User Controller
 * Handles all functions related to User.
 */
class userController extends \Jolt\Controller{
    public function my_name($name = 'default'){
		$this->app->render( 'page', array(
			"pageTitle"=>"Greetings ".$this->sanitize($name)."!",
			'title'=>'123',
			"body"=>"Greetings ".$this->sanitize($name)."!"
		));
    }
	public function mylist(){
		$this->app->condition('signed_in');
		$me = $this->app->db->findOne('user', array( '_id'=>$this->app->store('user') ));
		//	query user
		$user  = $this->app->db->find('user');
		$this->app->render( 'admin/user/list', array(
			"pageTitle" => "User List",
			"me" => $me,
			'fromNumber'=> format_telephone( $this->app->store('fromNumber') ),
			'user' => $user
		),'admin/inside');
	}
	public function edit( $user_id ){
		$this->app->condition('signed_in');
		$me = $this->app->db->findOne('user', array( '_id'=>$this->app->store('user') ));
		$user  = $this->app->db->findOne('user', array( '_id'=>$user_id ));
		$this->app->render( 'admin/user/form', array(
			"pageTitle" => "Edit User",
			"me" => $me,
			"action" => "/admin/user/edit/".$user_id.( isset($_REQUEST['embed']) ? '?embed='.$_REQUEST['embed'] : null ),
			'user' => $user
		),
		( isset($_REQUEST['embed']) ? 'admin/embedded' : 'admin/inside' ) );
	}
	public function save( $user_id ){
		$this->app->condition('signed_in');
		$me = $this->app->db->findOne('user', array( '_id'=>$this->app->store('user') ));
		//	update user member
		$user  = $this->app->db->findOne('user', array( '_id'=>$user_id ));
		if( isset($_POST['pass1']) && isset($_POST['pass2']) ){
			if( !empty($_POST['pass1']) && !empty($_POST['pass2']) ){
				$pass1 = $_POST['pass1'];
				$pass2 = $_POST['pass2'];
				if( $pass1 == $pass2 ){
					$_POST['pass'] = $pass1;
				}else{
					header("Location: /admin/user/edit/{$user_id}".( isset($_REQUEST['embed']) ? '?embed='.$_REQUEST['embed'] : null ));
					exit;
				}
			}
		}

		$user['login'] 		= $_POST['login'];
		$user['email'] 		= $_POST['login'];
		$user['name'] 		= $_POST['name'];
		$user['display_name'] = $_POST['name'];
		$user['phone'] 		= $_POST['phone'];
		if( isset($_POST['pass']) ){
			$user['pass']	= passhash( $_POST['pass'] );
		}
		foreach($_POST as $k=>$v){
			if( isset($user[$k]) )	continue;
			$user[$k] = $v;
		}
		$this->app->db->save('user', $user );
		$url = site_url().'/admin/user/';
		if( !isset($_REQUEST['embed']) ){
			$url = site_url()."/admin/user/edit/".$user_id;
		}
		echo '<script>top.location.href="'.$url.'";</script>';
		exit;
	}
	public function addnew( ){
		$this->app->condition('signed_in');
		$me = $this->app->db->findOne('user', array( '_id'=>$this->app->store('user') ));
		$this->app->render( 'admin/user/form', array(
			"pageTitle" => "Add New User",
			"me" => $me,
			"action" => "/admin/user/new".( isset($_REQUEST['embed']) ? '?embed='.$_REQUEST['embed'] : null ),
		),
		( isset($_REQUEST['embed']) ? 'admin/embedded' : 'admin/inside' ) );
#		print_r($user->id);
	}
	public function savenew(  ){
		$this->app->condition('signed_in');
		$me = $this->app->db->findOne('user', array( '_id'=>$this->app->store('user') ));
		if( isset($_POST['pass1']) && isset($_POST['pass2']) ){
			if( !empty($_POST['pass1']) && !empty($_POST['pass2']) ){
				$pass1 = $_POST['pass1'];
				$pass2 = $_POST['pass2'];
				if( $pass1 == $pass2 ){
					$_POST['pass'] = $pass1;
				}else{
					header("Location: /admin/user/new".( isset($_REQUEST['embed']) ? '?embed='.$_REQUEST['embed'] : null ));
					exit;
				}
			}
			$user 				= array();
			$user['login'] 		= $_POST['email'];
			$user['email'] 		= $_POST['email'];
			$user['name'] 		= $_POST['name'];
			$user['display_name'] = $_POST['name'];
			$user['pass']			= passhash( $_POST['pass'] );
			$user['status'] 		= 1;
			$user['phone'] 		= $_POST['phone'];
			$user['activation_key'] = md5(uniqid(mt_rand(), true));
			$user['registered'] 	= date('Y-m-d H:i:s');
			$user['type']			= 'user';
			foreach($_POST as $k=>$v){
				if( isset($user[$k]) )	continue;
				$user[$k] = $v;
			}
			$this->app->db->insert('user', $user );

			$user_id = $user['_id'];
#			header("Location: /user");
			$url = site_url().'/admin/user/';
			if( !isset($_REQUEST['embed']) ){
				$url = site_url()."/admin/user/edit/".$user_id;
			}
			echo '<script>top.location.href="'.$url.'";</script>';
//			echo '<script>top.location.href="/admin/user";</script>';
			exit;
		}
	}
	public function delete( $user_id ){
		$this->app->condition('signed_in');
		$me = $this->app->db->findOne('user', array( '_id'=>$this->app->store('user') ));
		$this->app->db->user->remove( 'id', $user_id );
		header("Location: /admin/user");
		exit;
	}
}