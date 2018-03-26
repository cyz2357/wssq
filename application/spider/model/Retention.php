<?php
namespace app\spider\model;
use think\Model;

class Retention extends Model {

	public static function test() {
		$sql	= "SELECT id FROM `md_user_retention` WHERE isDelete = '0' GROUP BY `date`,`day`,`appType`,`version`,`source` ORDER BY id DESC";
		$data	= \think\Db::query($sql);

		if(empty($data)) {
			echo "empty! \r\n";exit;
		}
		$idArr	= array();
		foreach($data as $v) {
			$idArr[]	= $v['id'];
		}

		$sql	= "DELETE FROM `md_user_retention` WHERE id NOT IN('".implode("','",$idArr)."')";
		$ret	= \think\Db::execute($sql);
	}

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
		
		$sql	= "INSERT INTO `md_user_retention` $sqlKey VALUES $sqlValue";
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

		$sql	= "SELECT * FROM `md_user_retention` WHERE $where";
		//echo $sql."\r\n";
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

		$sql	= "UPDATE `md_user_retention` SET $set WHERE $where";
		$ret	= \think\Db::execute($sql);
		if(!$ret) {
			return false;
		}
		return true;
	}
}