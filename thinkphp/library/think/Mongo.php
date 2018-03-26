<?php
/**
 * Created by PhpStorm.
 * User: zsq
 * Date: 17-5-25
 * Time: 下午4:35
 */

namespace think;

use MongoDB;
use think\cache\Driver;

class Mongo {

    /**
     * Mongodb 对象句柄
     *
     * @var object Mongo
     */
    private $_conn = null;

    /**
     * 当前选择的数据库
     *
     * @var object MongoDB
     */
    private $_db = null;


    /**
     * 构造函数
     *
     * @param array $config 服务器配置,默认为:
     * array(
     * 'host'=>'localhost', // 主机名或IP地址
     * 'port'=>27017, // 端口
     * 'cmd'=>'$', // 修改器命令前缀
     * )
     */
    public function __construct($config = array('host' => '127.0.0.1', 'port' => 27017,'db' => 'xxx',  'cmd' => '$')){
        $server = sprintf("mongodb://%s:%s/%s",$config['host'], $config['port'], $config['db']);
//        echo "connect\n". $server;
        try {
            $this->_conn = new MongoDB\Driver\Manager($server);
            $this->_db   = $config["db"];
        }catch (MongoDB\Driver\Exception $e){
            if(self::DEBUG) {
                echo $e->getMessage();
            }
            return false;
        }
    }

    public function insert($table,$data){
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->insert($data);
        return $this->_conn->executeBulkWrite($this->_db .'.'. $table,$bulk);
    }

    public function query($table,$filter,$options=[]){
        $query = new MongoDB\Driver\Query($filter,$options);
        $cursor =$this->_conn->executeQuery($this->_db .'.'. $table,$query,new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_PRIMARY_PREFERRED));
        $result=array();
        foreach ($cursor as $item => $document) {
            $result[$item] = (array)$document;
        }
        return $result;
    }

	//更新数据
	public function update($table,$where,$data){
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->update($where,$data);
        return $this->_conn->executeBulkWrite($this->_db .'.'. $table,$bulk);
    }
}