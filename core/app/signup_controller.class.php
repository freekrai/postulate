<?php
class Signup extends \Jolt\Controller{
    public function my_name($name = 'default'){
        $this->app->render( 'page', array(
            "pageTitle"=>"Greetings ".$this->sanitize($name)."!",
            'title'=>'123',
            "body"=>"Greetings ".$this->sanitize($name)."!"
        ));
    }

	public function get(){
		$this->app->condition('signed_in');
		$me = $this->app->db->findOne('user', array( '_id'=>$this->app->store('user') ));
		$this->app->render( 'settings', array(
			"pageTitle"=>"Edit Profile",
			'me'=>$me,
			"myname" => ucwords( $me->display_name ),
		));
	}
	public function post(){
		$user = $this->app->db->findOne( 'user', array('login'=>$_POST['email']) );
		if( isset($user['_id']) ){
			echo "Error: This user already exists";
			$this->app->redirect( $this->app->getBaseUri().'/signup?error=1');
		}else{
			$user 					= array();
			$user['login'] 			= $_POST['email'];
			$user['email'] 			= $_POST['email'];
			$user['name'] 			= $_POST['name'];
			$user['display_name'] 	= $_POST['name'];
			$user['pass']			= passhash( $_POST['pass'] );
			$user['status'] 		= 1;
			$user['phone'] 			= $_POST['phone'];
			$user['activation_key'] = md5(uniqid(mt_rand(), true));
			$user['registered'] 	= date('Y-m-d H:i:s');
			$user['type']			= 'user';
			foreach($_POST as $k=>$v){
				if( isset($user[$k]) )	continue;
				$user[$k] = $v;
			}
			$this->app->db->insert('user', $user );
			$uid = $user['_id'];
		}
		$this->app->redirect( $this->app->getBaseUri().'/login');
	}
}