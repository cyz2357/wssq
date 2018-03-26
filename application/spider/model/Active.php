<?php
namespace app\spider\model;
use think\Model;

class Active extends Model {

	//批量插入数据
	public static function insertData($data) {
		if(!is_array($data) || empty($data)) {
			return false;
		}

		$sqlKey		= "";
		foreach($data[0] as $key => $val) {
			$sqlKey	.= empty($sqlKey) ? "`$key`" : ",`$key`";
		}
		$sqlKey		= "(".$sqlKey.")";

		$sqlValue	= '';
		foreach($data as $key => $val) {
			$sqlValue	.= empty($sqlValue) ? "" : ",";
			$sqlValue	.= "('".implode("','",$val)."')";
		}
		
		$sql	= "INSERT INTO `md_user_active` $sqlKey VALUES $sqlValue";
		$ret	= \think\Db::execute($sql);
		if(!$ret) {
			return false;
		}
		return true;
	}

	//获取旧数据
	public static function getAllData($data = array(),$timeArr = array()) {
		$where	= " isDelete = '0' ";
		foreach($data as $key => $val) {
			$where	.= " AND `".$key."` = '".$val."'";
		}
		if(!empty($timeArr)) {
			$where	.= " AND `date` IN('".implode("','",$timeArr)."')";
		}

		$sql	= "SELECT * FROM `md_user_active` WHERE $where";
		$data	= \think\Db::query($sql);
		return $data;
	}

	//更新数量
	public static function updateData($update,$data) {
		$set	= '';
		foreach($update as $key => $val) {
			$set	.= empty($set) ? "" : " , ";
			$set	.= "`$key` = '$val'";
		}

		$where	= " isDelete = '0' ";
		foreach($data as $key => $val) {
			$where	.= " AND `".$key."` = '".$val."'";
		}

		$sql	= "UPDATE `md_user_active` SET $set WHERE $where";
		$ret	= \think\Db::execute($sql);
		if(!$ret) {
			return false;
		}
		return true;
	}
}