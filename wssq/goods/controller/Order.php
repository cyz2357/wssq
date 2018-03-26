<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/24
 * Time: 15:28
 */

namespace app\goods\controller;
use think\Controller;
use app\index\controller\Base;
use think\Session;
use app\goods\model\Capital as CapitalModel;
class Order extends Base{
  public function sq_order(){
      $deposit_x = CapitalModel::capital_a('deposit');
      $this->assign('deposit_x',$deposit_x);
      return $this->fetch();
  }
    public function detail_order(){

        if(input('id')){
            $deposit     = CapitalModel::get(input('id'));
            $this->assign('deposit',$deposit);
            return $this->fetch();
        }else{
            echo "<center><img src='/Public/img/404.gif'></center>";
        }
    }
    public function deposit_success(){
        $time    = date("Y-m-d H:i:s",time());
        $arr['res']=0;
        if(!empty(input('success'))) {
            $data['state'] = 1;
            $data['replytime'] = $time;
            $row = Db('capital')->where('id', input('success'))->update($data);
            if (!!$row) {
                $record_data['state']      = '1';
                $record_data['replytime'] = date("Y-m-d H:i:s",time());
               Db('record')->where(['out_orderid' => $_POST['out_orderid']])->update($record_data);
                $arr['res']=1;

            }

        }
        return json($arr);
    }
    public function sq_record(){
        $capital = Db('capital');
        $input   = input('');
        if(!empty($input['start_date'])){
            $map['replytime'] = ['between time',array($input['start_date'],$input['end_date'])];
        }else{
            $map['replytime'] = ['> time',date('Y-m-d H:i:s',strtotime('-1 day'))];
            $this->assign('start_date',date('Y-m-d H:i:s',strtotime('-1 day')));
            $this->assign('end_date',date('Y-m-d H:i:s',time()));
        }
        if(!empty($input['condition']) && !empty($input['value'])){

            if($input['condition']=='amount'){
                $map[$input['condition']] = $input['value'];
            }elseif($input['condition']=='out_orderid'){
                $map[$input['condition']] = array('like','%'.$input['value']);
            }else{
                $map[$input['condition']] = array('like',$input['value'].'%');
            }

        }
        if(!empty($input['state']) && $input['state']===1){
            $map['state'] = $input['state'];
        }else if(!empty($input['state']) && $input['state']!=-1){
            $map['state'] = $input['state'];
        }


        $map['type'] = array('in','deposit,1');
        $deposit = Db('capital')->where($map)->order('replytime desc')->paginate(30,false,['query' => input('param.')]);
        $page    = $deposit->render();
        $this->assign('deposit',$deposit);
        $this->assign('page',$page);
        return $this->fetch();
    }
    public function detail_record_sq(){
        if(input('id')){
            $deposit     = CapitalModel::get(input('id'));
            $this->assign('deposit',$deposit);
            return $this->fetch();
        }else{
            echo "<center><img src='/Public/img/404.gif'></center>";
        }

    }

}