#!/bin/sh
YESTERDAY=`date -d yesterday +%F`
/usr/bin/gzip -c /var/lib/mysql/log_slow > /develop_dir/logs/mysql_slow_query/slow-${YESTERDAY}.log.gz
##  > /var/lib/mysql/log_slow
echo '' > /var/lib/mysql/log_slow

