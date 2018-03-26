#!/bin/sh
BasePath=$(cd `dirname $0`; pwd)
CONFIG="${BasePath}/auto_sync_s3.cfg"
LOGS_PATH="/s3log/s3log/mangaData"
name=`date -d -15days +20"%y%m%d"`

# decompress lzo log then read logs
decompressAndImport () {
	local filedir logpath filename oldLogo
	filedir=$1
	curdate=$2
	lastdir=$3
	oldLogo='old-';
	

	logpath=${filedir%.*}
	#logpath=${logpath//$LOGSPATH/$EXP_PATH}
	#filename=${filedir##*-}
	#cname=${logpath%.*.*.*}

	cname=${logpath##*/}
	#echo $filedir
	#echo $logpath
	#echo $filename $cname
	# log.data -> log -> import log

	if [ ! -e ${lastdir}${cname} ] && [ ! -e ${lastdir}${oldLogo}${cname} ];then
		#echo ${filedir};
		lzop -d ${filedir}
		echo "lzop -d ${filedir}" 
		mv ${logpath} ${lastdir}
	else
		echo "logs already decompress."
	fi
}

# scan files and import
scandir () {
	local cur_dir workdir
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
			sstr=`echo ${cur_dir} | cut -d \/ -f 5`
			if [ -n "$sstr" ] && [ ${sstr} -lt ${name} ];then
				continue
			fi

			cd ${dirlist}
			scandir ${cur_dir}/${dirlist} ${name}
			cd ..
		else
			if [ ${dirlist##*.} = "lzo" ];then
				# only operate
				if [ ${dirlist: -14:2} = "20" ]; then
					echo "scan ${dirlist} and decompress ... ...."
					# ${cur_dir}/${dirlist} 文件路径 ${dirlist: -14:10} 10位日期时间 2016010101
					decompressAndImport ${cur_dir}/${dirlist} ${dirlist: -14:10} ${cur_dir}/../
				fi
			fi
		fi
	done
}

start(){
    while read logName
	do
		echo "sync $logName ... "
		scandir "${LOGS_PATH}/${logName}"
	done < "$CONFIG"
	echo "sync completed !"
}



echo "import logs to db"
scandir $LOGS_PATH $name
#start



