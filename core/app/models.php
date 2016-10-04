<?php
/*
	We use Idiorm and Paris for our ORM system, so these are our basic models that we use to set up active record....
*/
/*
class User extends Model{
	public function usermeta(){
		return $this->has_many('Usermeta');
	}
	public function get_firstname(){
		$name = $this->name;
		list($fname, $lname) = explode(' ', $name);
		return $fname;
	}
	public function get_lastname(){
		$name = $this->name;
		$name = explode(' ', $name);
		$lname = end( $name );
		return $lname;
	}
	public function get_meta( $key = '' ){
		$meta = array();
		$userm = $this->usermeta()->find_many();
		foreach($userm as $um){
			$meta[ $um->mkey ] = $um->mvalue;
		}
		if( $key != '' ){
			if( isset( $meta[ $key ] ) ){
				return $meta[ $key ];
			}else{
				return '';
			}
		}else{
			return $meta;
		}
		return $meta;
	}
}
class Usermeta extends Model{
    public function user() {
        return $this->belongs_to('User');
    }
}

class Post extends Model{
    public function user() {
        return $this->belongs_to('User');
    }
	public function postmeta(){
		return $this->has_many('Postmeta');
	}

	public function get_meta( $key = '' ){
		$meta = array();
		$postm = $this->postmeta()->find_many();
		foreach($postm as $um){
			$mkey = $um->mkey;
			$mvalue = $um->mvalue;
			if( is_json($mvalue) ){
				$mvalue = json_decode( $mvalue );
			}
			$meta[ $um->mkey ] = $mvalue;
		}
		if( $key != '' ){
			if( isset( $meta[ $key ] ) ){
				return $meta[ $key ];
			}else{
				return '';
			}
		}else{
			return $meta;
		}
		return $meta;
	}
}
class Postmeta extends Model{
    public function post() {
        return $this->belongs_to('Post');
    }
}
*/
