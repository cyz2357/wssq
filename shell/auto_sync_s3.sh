#!/bin/sh

BasePath=$(cd `dirname $0`; pwd)
CONFIG="${BasePath}/auto_sync_s3.cfg"
ANALYSE_EXEC="${BasePath}/mergeServer.js"
STAT_HOUR="${BasePath}/statistics_server.js"
source "${BasePath}/aws_s3.cfg"
APIS3LOGSPATH="s3://usw-mangamax/logs/api"

LogsPath="/s3log/s3log/mangaData"

olddate=`date -d "+0days" "+%Y%m%d"`
olddate2=`date -d "-1days" "+%Y%m%d"`

if [ ! -d "$LogsPath" ]; then
    echo "${LogsPath} not found !!!"
	exit 1
fi

#IMPORT_DATE="${LogsPath}/import.txt"
IMPORT_LOG="${LogsPath}/importlog/"

if [ ! -d "$IMPORT_LOG" ]; then
	mkdir "$IMPORT_LOG"
fi

sync () {
	# sync s3 logs
	while read logName
	do
		echo "sync $logName ... "
		aws s3 sync "${APIS3LOGSPATH}/${logName}/${olddate2}" "${LogsPath}/${logName}/${olddate2}"
		aws s3 sync "${APIS3LOGSPATH}/${logName}/${olddate}" "${LogsPath}/${logName}/${olddate}"
	done < "$CONFIG"
	echo "sync completed !"
}

# decompress lzo log then read logs
decompressAndImport () {
	local filedir logpath filename t n
	filedir=$1
	curdate=$2

	logpath=${filedir%.*}
	#logpath=${logpath//$LOGSPATH/$EXP_PATH}
	filename=${filedir##*-}
	cname=${filename%.*.*.*}
	#echo $filedir
	#echo $logpath 
	#echo $filename $cname
	# log.data -> log -> import log
	if [ ! -e $logpath ];then
		lzop -dv ${filedir}
		echo "mongoimport -d $DBNAME -c ${filename%.*.*.*} --file ${logpath}" >> "$IMPORT_LOG${filename%.*.*.*}.${curdate: -2}.log"
		mongoimport -d $DBNAME -c ${filename%.*.*.*} --file "${logpath}"
	else
		echo "logs already decompress."
	fi
}

# scan files and import
scandir () {
	local cur_dir parent_dir workdir
	workdir=$1
	cd ${workdir}
	if [ ${workdir} = "/" ]
	then
		cur_dir=""
	else
		cur_dir=$(pwd)
	fi

	for dirlist in $(ls ${cur_dir})
	do
		if test -d ${dirlist};then
			cd ${dirlist}
			scandir ${cur_dir}/${dirlist}
			cd ..
		else
			if [ ${dirlist##*.} = "lzo" ];then
				# only operate 
				echo "scan ${dirlist} and decompress ... ...."
				# ${cur_dir}/${dirlist} 文件路径 ${dirlist: -14:10} 10位日期时间 2016010101
				decompressAndImport ${cur_dir}/${dirlist} ${dirlist: -14:10}
			fi
		fi
	done
}

scanHour () {
    local logpath
    # 仅导入上一个钟头的数据
    while read logName
    do
        # echo $LogsPath"/"$logName
        # logs file path
        if [ -d $LogsPath"/"$logName ]; then
            statdate=`date +%Y%m%d`
            stathour=`date -d "-1 hour" +%H`
            #stathour=`date +%H`
            echo "$statdate/$stathour"
            if [ ${stathour} == 23 ]; then
                statdate=`date -d "-1 day" +%Y%m%d`
            fi

            logpath="$LogsPath/$logName/$statdate"
            echo $logpath
            if [ -d $logpath ]; then
                scandir $logpath
            fi
        fi
    done < $CONFIG
}

analyse() {
    node $ANALYSE_EXEC
    node $STAT_HOUR
}

echo "sync start ..."
sync
#echo "scan import logs"
#scanHour
#echo "analyse logs"
#analyse 
