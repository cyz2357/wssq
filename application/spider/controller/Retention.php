<?php
/*
 * 留存数据处理模块
 * 处理本地留存数据及从flurry上抓下来的数据
 * add by yang 2017-04-13
 */
namespace app\spider\controller;

use app\base\controller\Base;
use app\spider\model\Retention as RetentionModel;
use app\spider\model\UserAdd as UserAddModel;

use think\Db;

class Retention extends Base{

	public function index() {
		$oldData	= RetentionModel::test();
    }

    /*
	 * 从文件中读取日活配置入库
	 * www.mangadata.cc/index.php?s=spider/Retention/readRetentionFile
	 * 备注：需要爬虫先从flurry上抓数据，此处才能导入，爬虫文件为phpspider/demo/fluSpider.php
	 */
    public function readRetentionFile() {
		$path	= config('baseconfig')['RETENTION'];
		error_reporting(0);
		set_time_limit(0);
		$jsonPath = scandir($path);

		foreach($jsonPath as $key => $file) {
			if(in_array($file,array(".","..")) || !strstr($file,'.json')) continue;

			$filename	= $path."/$file";
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
					$ret1	= $this->addRetention($buffer,$version,$source,$appType);
					$ret2	= $this->addUserAdd($buffer,$version,$source,$appType);
				}
				fclose($handle);
			}
			@unlink($filename); 
		}
	}

	/*
	 * 从文件中读取日活配置入库
	 * www.mangadata.cc/index.php?s=spider/Retention/readRetentionFile
	 * 备注：需要爬虫先从flurry上抓数据，此处才能导入，爬虫文件为phpspider/demo/fluSpider.php
	 */
    public function readRetentionFileEn() {
		$path	= config('baseconfig')['RETENTION-EN'];
		error_reporting(0);
		set_time_limit(0);
		$jsonPath = scandir($path);

		foreach($jsonPath as $key => $file) {
			if(in_array($file,array(".","..")) || !strstr($file,'.json')) continue;

			$filename	= $path."/$file";
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
					$ret1	= $this->addRetention($buffer,$version,$source,$appType,'english');
					$ret2	= $this->addUserAdd($buffer,$version,$source,$appType,'english');
				}
				fclose($handle);
			}
			@unlink($filename); 
		}
	}

	//把flu上抓取到的json组装成对应格式入库，用户留存数据 表：of_user_retention
	public function addRetention($data,$version,$source,$appType,$language = 'all') {
		$appTypeArr	= config('baseconfig')['FLURRYAPPTYPE'];
		$data		= json_decode($data,true);

		if(!is_array($data) || empty($data) || !isset($data["series"]) || !is_array($data["series"]) || empty($data["series"])) return false;

		$select		= array(
							'version'	=> $version,
							'appType'	=> $appTypeArr[$appType],
							'source'	=> $source,
							'language'	=> $language,
						);

		$oldData	= RetentionModel::getAllData($select);
		$insertArr	= array();

		foreach($data["series"] as $key => $val) {
			if(!isset($val['id']) || !isset($appTypeArr[$val['id']]) || !isset($val['returnRate']) || empty($val['returnRate'])) continue;
			if($appType != 'all' && $appType != $val['id']) continue;

			$appType	= $appTypeArr[$val['id']];
			foreach($val['returnRate']['detail'] as $insertV) {
				$time			= substr($insertV[0],0,strlen($insertV[0])-3); 
				$date			= date("Y-m-d",$time);

				foreach($insertV[1] as $day => $number) {
					if($number === null) continue;
					$operation		= "insert";
					if(!empty($oldData)) {
						foreach($oldData as $oldVal) {
							if($date == $oldVal['date'] && $day == $oldVal["day"] && $appType == $oldVal["appType"]) {
								$operation = "update";
								//$operation = $number > $oldVal["retention"] ? "update" : "delete";
							}
						}
					}

					switch($operation) {
						case 'insert':
							$insertArr[]	= array(
											"date"		=> $date,
											"retention"	=> $number,
											"day"		=> $day,
											"version"	=> $version,
											"appType"	=> $appType,
											"language"	=> $language,
											"source"	=> $source,
											"addTime"	=> time(),
										);
							break;
						case "update":
							$update			= array("retention" => $number,"addTime" => time());
							$updateWhere	= array("date" => $date,"appType"=> $appType,'day' => $day,'version' => $version,'source' => $sourc,'language'=>$language);
							RetentionModel::updateData($update,$updateWhere);
							break;
						case "delete":
						default:
							break;
					}
				}
			}
		}

		if(!empty($insertArr)) {
			$ret	= RetentionModel::insertData($insertArr);
			if($ret) return true;
		}
		return true;
	}


	//把flu上抓取到的json组装成对应格式入库，用户新增数据 表名：of_user_add
	public function addUserAdd($data,$version,$source,$appType,$language = 'all') {
		$appTypeArr	= config('baseconfig')['FLURRYAPPTYPE'];
		$data		= json_decode($data,true);

		if(!is_array($data) || empty($data) || !isset($data["series"]) || !is_array($data["series"]) || empty($data["series"])) return false;

		$select		= array(
							'language'	=> $language,
							'version'	=> $version,
							'source'	=> $source,
						);
		$oldData	= UserAddModel::getAllData($select);
		$insertArr	= array();

		foreach($data["series"] as $key => $val) {
			if(!isset($val['id']) || !isset($appTypeArr[$val['id']]) || !isset($val['cohort']) || empty($val['cohort'])) continue;
			if($appType != 'all' && $appType != $val['id']) continue;

			$appType	= $appTypeArr[$val['id']];
			foreach($val['cohort'] as $insertV) {
				$time	= substr($insertV[0],0,strlen($insertV[0])-3); 
				$number	= $insertV[1];
				$date	= date("Y-m-d",$time);
				if($number === null) continue;

				$operation		= "insert";
				if(!empty($oldData)) {
					foreach($oldData as $oldVal) {
						if($date == $oldVal['date'] && $appType == $oldVal["appType"]) {
							$operation = $number > $oldVal["number"] ? "update" : "delete";
						}
					}
				}

				switch($operation) {
					case 'insert':
						$insertArr[]	= array(
										"date"		=> $date,
										"number"	=> $number,
										"version"	=> $version,
										"appType"	=> $appType,
										"language"	=> $language,
										"source"	=> $source,
										"addTime"	=> time(),
									);
						break;
					case "update":
						$update			= array("number" => $number,"addTime" => time());
						$updateWhere	= array("date" => $date,"appType"=> $appType,'version' => $version,'source' => $source,'language'=>$language);
						UserAddModel::updateData($update,$updateWhere);
						break;
					case "delete":
					default:
						break;
				}
			}
		}

		if(!empty($insertArr)) {
			$ret	= UserAddModel::insertData($insertArr);
			if($ret) return true;
		}
		return true;
	}
}