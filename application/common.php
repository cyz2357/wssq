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



use think\log;
use think\Config;
use think\Request;
use think\Db;



use app\common\util\GuidHelper;

//生成日志ID
function generateLogId(){
    return GuidHelper::GUID16();
}

//生成日志ID日志，一般程序入口处调用一次即可
function traceLogId($strLogID){
	Log::record('[ LogID ] ' . $strLogID, 'info');
	Log::record('[ LogID ] ' . $strLogID, 'error');
}

//写入Info日志
function traceInfo($strContent, $strArgName = 'PRV'){
	traceLog('info', $strContent, $strArgName);
}

//写入Error日志
function traceError($strContent, $strArgName = 'PRV'){
	traceLog('error', $strContent, $strArgName);
}

//写入日志
function traceLog($logLevel, $strContent, $strArgName = 'PRV'){
    if(Config::get('app_debug') !== true) {

        // 获取基本信息
        if (isset($_SERVER['HTTP_HOST'])) {
            $current_uri = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        } else {
            $current_uri = "cmd:" . implode(' ', $_SERVER['argv']);
        }
                        
        $runtime    = round(microtime(true) - THINK_START_TIME, 10);
        $reqs       = $runtime > 0 ? number_format(1 / $runtime, 2) : '∞';
        $time_str   = ' [运行时间：' . number_format($runtime, 6) . 's][吞吐率：' . $reqs . 'req/s]';
        $memory_use = number_format((memory_get_usage() - THINK_START_MEM) / 1024, 2);
        $memory_str = ' [内存消耗：' . $memory_use . 'kb]';
        $file_load  = ' [文件加载：' . count(get_included_files()) . ']';

        $message = $current_uri . $time_str . $memory_str . $file_load ; 
        Log::record($message, $logLevel);      
        //Log::record('[ URL ] ' . var_export(request()->url(), true), $logLevel);
        Log::record('[ PARAM ] ' . var_export(request()->param(), true), $logLevel);
    }

    if(is_array($strContent))
    {
        $strContent = json_encode($strContent);
    }
    Log::record('[ '. $strArgName . ' ] '. var_export($strContent, true), $logLevel);
}

