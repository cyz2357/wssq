<?php
// +----------------------------------------------------------------------
// | TOPThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://topthink.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Think;
class Csv
{
//导出csv文件
	public static function put_csv($list,$title){
		$file_name="CSV".date("mdHis",time()).".csv";
		header ( 'Content-Type: application/vnd.ms-excel' );
		header ( 'Content-Disposition: attachment;filename='.$file_name );
		header ( 'Cache-Control: max-age=0' );
		$file = fopen('php://output',"a");
		$limit=1000;
		$calc=0;
		foreach ($title as $v){
			$tit[]=iconv('UTF-8', 'GB2312//IGNORE',$v);
		}
		fputcsv($file,$tit);
		foreach ($list as $v){
			$calc++;
			if($limit==$calc){
				ob_flush();
				flush();
				$calc=0;
			}
			foreach ($v as $t){
				$tarr[]=iconv('UTF-8', 'GB2312//IGNORE',$t);
			}
			fputcsv($file,$tarr);
			unset($tarr);
		}
		unset($list);
		fclose($file);
		exit();
	}
}
