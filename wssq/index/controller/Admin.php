<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/15
 * Time: 13:32
 */

namespace app\index\controller;
use think\Controller;
use app\index\controller\Base;
use app\index\model\Admin as adminModel;
use think\Session;
class Admin extends Base{
    public function admin(){
        echo $this->fetch();
    }
    public function login(){
        echo $this->fetch();
    }


    /*
     * 主页左部
     * 根据权限显示左边列表
     */
    public function head() {
        if($this->admin['prompt'] == 1){
            $head = adminModel::head();
            $this->assign('head',$head);

        }
        $this->assign('admin',$this->admin);
        echo $this->fetch();
    }
    public function left() {
        $left = adminModel::left($this->admin);

        $this->assign('title',$left['title']);
        $this->assign('list',$left['list']);
        echo $this->fetch();
    }
    public function right() {
        echo $this->fetch();
    }
    public function refresh() {
        $arr = adminModel::refresh($this->admin);
        return json($arr);
    }

}