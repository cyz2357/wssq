#!/bin/sh

echo "auto sync s3 logs and analyze"

BasePath=$(cd `dirname $0`; pwd)
CONFIG="${BasePath}/auto_sync_s3.cfg"
ANALYSE_EXEC="${BasePath}/mergeServer.js"
STAT_DAY="${BasePath}/statistics_server.js"
source "${BasePath}/aws_s3.cfg"
APIS3LOGSPATH="s3://usw-mangamax/logs/api"

LOGS_PATH="/s3log/s3log/mangaData"

if [ ! -d "$LOGS_PATH" ]; then
    echo "${LOGS_PATH} not found !!!"
	exit 1
fi

IMPORT_DATE="${LOGS_PATH}/import.txt"
IMPORT_LOG="${LOGS_PATH}/importlog/"

if [ ! -d "$IMPORT_LOG" ]; then
	mkdir "$IMPORT_LOG"
fi

DBNAME="openfin_logs_analyse"

LastDate=`cat $IMPORT_DATE`
if [[ -z "$LastDate" ]]; then
	LastDate="0000000000"
fi

SaveDate=$LastDate

sync () {
	# sync s3 logs
	while read logName
	do
		echo "sync $logName ... "
		echo aws s3 sync "${APIS3LOGSPATH}/${logName}" "${LOGS_PATH}/${logName}"
		sleep 20
		exit
		aws s3 sync "${APIS3LOGSPATH}/${logName}" "${LOGS_PATH}/${logName}"
	done < "$CONFIG"
	echo "sync completed !"
}

sync
echo "import logs to db"


