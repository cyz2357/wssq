<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

//错误提示弹出方法
function getAlert($detail = '',$url = '') {
	$str	= "<script>";
	if(!empty($detail)) $str	.= "alert('".$detail."');";

	if(!empty($url)) {
		if($url == 'back') {
			$str	.= "history.back(-1);";
		} elseif($url == 'reload') {
			$str	.= "location.reload();";
		} else {
			$str	.= "location.href='".$url."';";
		}
	}
	$str	.= "</script>";
	echo $str;
}

//清除数组中看空格
function trimArray($arr){
	if (!is_array($arr)){ return $arr; }
	while (list($key, $value) = each($arr)){
		if (is_array($value)){
			$arr[$key] = TrimArray($value);
		}
		else {
			$arr[$key] = trim($value);
		}
	}
	return $arr;
}

/**
 * post发送
 * @return 验证结果
 */
function php_post($url, $data, $optional_headers = null){
	$postdata = http_build_query($data);
	$params = array('http' => array(
			'method' => 'POST',
			'content' => $postdata
	));
	if ($optional_headers !== null) {
		$params['http']['header'] = $optional_headers;
	}
	$ctx = stream_context_create($params);
	$fp = @fopen($url, 'rb', false, $ctx);
	if (!$fp) {
		throw new Exception("Problem with $url");
	}
	$response = @stream_get_contents($fp);
	if ($response === false) {
		throw new Exception("Problem reading data from $url");
	}

	return $response;
}