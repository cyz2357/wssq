<?php
namespace app\index\model;
use think\Model;
use think\Session;
class Base extends Model{
	
	public static function power($action,$admin) {
		if(!is_array($admin) || empty($admin)) {

			return false;
		}
		$map['url'] = $action;
		$adminlist  = Db('adminlist')->where($map)->where('del',0)->find();
		if(empty($adminlist)){
			return true;
		}
		$pos1 = strpos($admin['path'],$adminlist['path']);
		if($admin['list'] != 'all'){
			$pos2 = strpos($admin['list'],strval($adminlist['id']));
		}else{
			$pos2 = true;
		}
		if($pos1!==false && $pos2!==false){
			return true;
		}else{
			return false;
		}
	}
}