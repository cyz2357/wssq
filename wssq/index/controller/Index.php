<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/23
 * Time: 17:29
 */

namespace app\index\controller;

use think\Controller;
class Index extends Controller{
    public function index(){

        $this->assign('out_orderid','YRJ'.date("ymdHis").rand(1000,9999));
        $row=Db('record')->where('state',1)->select();
        $this->assign('data',$row);
        echo $this->fetch();
    }
    public function goods(){
        $arr['res']=0;
      $data['out_orderid']=input('out_orderid');
      $data['num']=input('num');
      $data['amount']=input('amount');
        $data['name']=input('name');
      $data['goods_name']=input('goods_name');
      $data['phone']=input('phone');
      $data['gddh']=input('gddh');
      $data['time']=input('time');
      $data['pay_type']=input('pay');
        $data['cardprovince']=input('province');
        $data['cardcity']=input('city');
        $data['county']=input('county');
        $data['address']=input('address');
        $data['message']=input('message');
        $data['type']='deposit';
        $data['state']=0;
        $data['sendtime'] = date('Y-m-d H:i:s',time());
        $data1['out_orderid']=input('out_orderid');
        $data1['num']=input('num');
        $data1['amount']=input('amount');
        $data1['name']=input('name');
        $data1['goods_name']=input('goods_name');
        $data1['phone']=input('phone');
        $data1['gddh']=input('gddh');
        $data1['time']=input('time');
        $data1['pay_type']=input('pay');
        $data1['cardprovince']=input('province');
        $data1['cardcity']=input('city');
        $data1['county']=input('county');
        $data1['address']=input('address');
        $data1['message']=input('message');
        $data1['type']='deposit';
        $data1['state']=0;
        $data1['sendtime'] = date('Y-m-d H:i:s',time());
        $data1['s_phone']=substr(input('phone'), 0, 3) . '****' . substr(input('phone'), 7, strlen(input('phone')));
        Db('record')->strict(false)->insert($data1);
        $row=Db('capital')->strict(false)->insert($data);
        if($row){
            $arr['res']=1;
        }
        return json($arr);
    }
    public function pl(){
        $data=input('post.');
        $row=Db('comments')->strict(false)->insert($data);
        if($row){
            $res['reg']=1;
        }
        return json($res);
    }
    public function get_comments(){
        $row=Db('comments')->order('time','desc')->select();
        return json($row);
    }
    public function get_comments1(){
        $row=Db('comments')->where('reply_id',input('id'))->order('time','desc')->find();
        return json($row);
    }
    public function num(){
        $data['number']=input('num');
        $row=Db('comments')->where('id',input('id'))->update($data);
    }
    public function reply(){
        $res['reg']=0;
        $data=input('post.');
//       $data['reply_id']=input('id');
//        $data['reply_title']=input('reply_title');
//        $data['reply_name']=input('reply_name');
//        $data['‏reply_time']=input('post.‏reply_time');

        $row=Db('comments')->where('id',input('id'))->strict(false)->update($data);
        if($row){
            $res['reg']=1;
        }
        return json($res);
    }

}