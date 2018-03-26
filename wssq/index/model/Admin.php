<?php
namespace app\index\model;
use think\Model;
use think\Session;
class Admin extends Model{
	public static function is_admin($data){
		if(!is_array($data) || empty($data)) {
			return false;
		}
		$map['user']     = $data['user'];
		$map['password'] = $data['password'];
		$admin = db('adminuser')->where($map)->find();
		if(!!$admin){
			return $admin;
		}else{
			return false;
		}
	}
    public static function head(){
        $capital = Db('capital');
        $head = array();
        $head['deposit']    = $capital->where("state=0 AND (type='deposit') ")->count();
        return $head;
    }
	public static function left($data) {
		$left = array();
		$adminlist = Db('adminlist');
    	$adminuser = Db('adminuser');
    	$title_num = explode(',',$data['path']);
    	$list_num  = explode(',',$data['list']);
    	foreach($title_num as $key => $v){
	    	$title_name = $adminlist->where('url',$v)->where('del',0)->order('order_num')->value('name');
	    	$left['title'][$key] = $title_name;
    		if($list_num[0]!='all'){
    			foreach($list_num as $list_key => $list_v){
    				$map_list['id']   = $list_v;
    				$map_list['path'] = $v;
    				$list_row = $adminlist->where($map_list)->where('del',0)->order('order_num')->find();
    				if(!!$list_row){
    					$left['list'][$key][$list_key] = $list_row;
    				}
    			}
	    	}else{
	    		$left['list'][$key] = $adminlist->where('path',$v)->where('del',0)->order('order_num')->select();
    		}

    	}

    	return $left;
	}
    public static function refresh(){
        $capital = Db('capital');
        $deposit=$capital->where("state=0 AND (type='deposit') ")->count();

        $arr = [
            'deposit'    => $deposit,
        ];
        return $arr;
    }
}