<?php
namespace app\goods\model;
use think\Model;
class Capital extends Model{

    public static function capital_a($method){

        $map['state']        = 0;
        $map['type']       = $method;
        $res = Db('capital')
            ->where($map)
            ->order('sendtime desc')
            ->select();
        return $res;
    }





    //支付数据增加
    public static function getadd($data){
        if(!is_array($data))return false;
        return Db('capital')->strict(false)->insert($data);
    }
    public static function getfind($map){
        return Db('capital')->where($map)->find();
    }
}