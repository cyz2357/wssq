<?php
namespace app\index\model;
use think\Model;
use think\Session;
class Login extends Model{
	public function login($data) {
		if(!is_array($data) || empty($data)) {
			return false;
		}
		$map['user']	 = $data['user'];
		$map['password'] = $data['password'];
		$admin = db('adminuser')->where($map)->find();
		if(!!$admin){
			session::set('admin',$admin);
			return $admin;
		}else{
			return false;
		}	
	}
}