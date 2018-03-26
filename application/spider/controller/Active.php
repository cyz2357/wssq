<?php
/*
 * 日活数据处理模块
 * 处理本地留存数据及从flurry上抓下来的数据
 * add by yang 2017-04-13
 */
namespace app\spider\controller;

use app\base\controller\Base;
use app\spider\model\Active as ActiveModel;
use think\Db;

class Active extends Base{
	public function index() {
    }

    /*
	 * 从文件中读取日活配置入库
	 * www.mangadata.cc/index.php?s=spider/Active/readActiveFile
	 * 备注：需要爬虫先从flurry上抓数据，此处才能导入，爬虫文件为phpspider/demo/fluSpider.php
	 */
    public function readActiveFile() {
		$path		= config('baseconfig')['ACTIVEUSER'];

		error_reporting(0);
		set_time_limit(0);
		$jsonPath = scandir($path);
		
		foreach($jsonPath as $key => $val) {
			if(in_array($val,array(".",".."))) continue;

			//获取数据的日期类型
			$type	= $val;

			$fliePath	= $path."/$val";
			$filesnames = scandir($fliePath);
			foreach($filesnames as $file) {
				if(in_array($file,array(".","..")) || !strstr($file,'.json')) continue;

				$filename	= $fliePath."/$file";
				echo $filename." begin!<br>";

				$fileEX		= explode("~",$file);
				$version	= $fileEX[0];
				$appType	= $fileEX[1];
				$source		= "flurry";
				
				if(!is_file($filename)) continue;

				$handle = @fopen($filename, "r");
				if ($handle) {
					while (!feof($handle)) {
						$buffer = trim(fgets($handle));
						if(empty($buffer)) continue;
						$ret	= $this->addActive($buffer,$type,$version,$source,$appType);
					}
					fclose($handle);
				}
				@unlink($filename); 
			}
		}
	}

	/*
	 * 从文件中读取北美版本日活配置入库
	 * www.mangadata.cc/index.php?s=spider/Active/readActiveFile
	 * 备注：需要爬虫先从flurry上抓数据，此处才能导入，爬虫文件为phpspider/demo/fluSpider.php
	 */
    public function readActiveFileEn() {
		$path		= config('baseconfig')['ACTIVEUSER-EN'];

		error_reporting(0);
		set_time_limit(0);
		$jsonPath = scandir($path);
		
		foreach($jsonPath as $key => $val) {
			if(in_array($val,array(".",".."))) continue;

			//获取数据的日期类型
			$type	= $val;

			$fliePath	= $path."/$val";
			$filesnames = scandir($fliePath);
			foreach($filesnames as $file) {
				if(in_array($file,array(".","..")) || !strstr($file,'.json')) continue;

				$filename	= $fliePath."/$file";
				echo $filename." begin!<br>";

				$fileEX		= explode("~",$file);
				$version	= $fileEX[0];
				$appType	= $fileEX[1];
				$source		= "flurry";
				
				if(!is_file($filename)) continue;

				$handle = @fopen($filename, "r");
				if ($handle) {
					while (!feof($handle)) {
						$buffer = trim(fgets($handle));
						if(empty($buffer)) continue;
						$ret	= $this->addActive($buffer,$type,$version,$source,$appType,'english');
					}
					fclose($handle);
				}
				@unlink($filename); 
			}
		}
	}


	//把flu上抓取到的json组装成对应格式入库
	public function addActive($data,$type,$version,$source,$appType,$language = 'all') {
		$appTypeArr	= config('baseconfig')['FLURRYAPPTYPE'];
		$data		= json_decode($data,true);

		if(!is_array($data) || empty($data) || !isset($data["series"]) || !is_array($data["series"]) || empty($data["series"])) return false;

		$select		= array(
							'type'		=> $type,
							'version'	=> $version,
							'source'	=> $source,
							'language'	=> $language,
						);
		$oldData	= ActiveModel::getAllData($select);
		$insertArr	= array();

		foreach($data["series"] as $key => $val) {
			if(!isset($val['id']) || !isset($appTypeArr[$val['id']]) || !isset($val['data']) || empty($val['data'])) continue;
			if($appType != 'all' && $appType != $val['id']) continue;

			$appType	= $appTypeArr[$val['id']];
			foreach($val['data'] as $insertV) {
				$time			= substr($insertV[0],0,strlen($insertV[0])-3); 
				$date			= date("Y-m-d",$time);
				
				$operation		= "insert";
				if(!empty($oldData)) {
					foreach($oldData as $oldVal) {
						if($date == $oldVal['date'] && $appType == $oldVal["appType"]) {
							$operation = $insertV[1] > $oldVal["number"] ? "update" : "delete";
						}
					}
				}

				switch($operation) {
					case 'insert':
						$insertArr[]	= array(
										"date"		=> $date,
										"number"	=> $insertV[1],
										"version"	=> $version,
										"appType"	=> $appType,
										"language"	=> $language,
										"type"		=> $type,
										"source"	=> $source,
										"addTime"	=> time(),
									);
						break;
					case "update":
						$update			= array("number" => $insertV[1],"addTime" => time());
						$updateWhere	= array("date" => $date,"appType"=> $appType,'type' => $type,'version' => $version,'source' => $source,'language'=>$language);
						ActiveModel::updateData($update,$updateWhere);
						break;
					case "delete":
					default:
						break;
				}
			}
		}
		if(!empty($insertArr)) {
			$ret	= ActiveModel::insertData($insertArr);
			if($ret) return true;
		}
		return true;
	}

	//新增本地数据至user_active表中
	public function insertUserActive($data,$date) {
		$ret		= true;
		$insertArr	= array();
		//插入新增数据
		$select		= array(
						'date'		=> $date,
						'type'		=> 'day',
						'source'	=> 'local',
					);
		$oldData	= ActiveModel::getAllData($select);
		foreach($data as $key => $val) {
			$operation		= "insert";
			if(!empty($oldData)) {
				foreach($oldData as $oldVal) {
					if($val['version'] == $oldVal["version"] && $val['appType'] == $oldVal["appType"]) {
						$id			= $oldVal["id"];
						$operation	= "update";
					}
				}
			}

			$arr	= array(
							"date"		=> $date,
							"number"	=> $val['number'],
							"version"	=> $val['version'],
							"appType"	=> $val['appType'],
							"language"	=> 'all',
							"type"		=> "day",
							"source"	=> "local",
							"addTime"	=> time(),
						);

			switch($operation) {
				case 'insert':
					$insertArr[]	= $arr;
					break;
				case "update":
					$updateWhere	= array("id" => $id);
					ActiveModel::updateData($arr,$updateWhere);
					break;
				default:
					break;
			}
		}
		if(!empty($insertArr)) {
			$ret	= ActiveModel::insertData($insertArr);
		}
		return $ret;
	}
}