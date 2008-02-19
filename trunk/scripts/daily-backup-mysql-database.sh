#!/bin/sh
# stop mysql first
# ps xa | grep mysqld | awk -F" " '{ print $1 }'
DATA_DIR='/var/lib/mysql'
BACKUP_DATA_DIR='/develop_dir/backup_daily_database/mysql'
# find data directory
for i in `ls $DATA_DIR`
do
	if [ -d ${DATA_DIR}/${i} ];then
		rsync -a --delete ${DATA_DIR}/${i} ${BACKUP_DATA_DIR}
	fi
done