<?php
namespace app\index\controller;
use think\Controller;
use think\Session;
class Login extends Controller {
    public function login(){
        if(!empty(input('post.'))){
            $login = model('Login');
            $admin = $login->login(input('post.'));
            $arr['flag'] = 0;
            if(!!$admin){
                $arr['flag'] = 1;
            }
            return json($arr);
        }
        echo $this->fetch();
    }
    public function logout() {
        session::delete('admin');
        getAlert(null,'/index/Login/login');
        exit(0);
    }
}