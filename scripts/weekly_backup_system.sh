#!/bin/sh
# $Id$
# Weekly backup system server and system config file
#
ETC_DIR=/etc
HTTPD_DIR=/usr/local/apache2
MYSQL_DIR=/usr/local/mysql-5.0.45-linux-i686

BACKUP_DIR=/develop_dir/backup_weekly_system

for i in $ETC_DIR $HTTPD_DIR $MYSQL_DIR
do
	rsync -a --delete $i ${BACKUP_DIR}
done
