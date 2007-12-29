#!/bin/sh
# $Id$
# stop work server and start test server env
# zhuzhu@perlchina.org
#

if [ ! -n "$1" ]; then
	echo " Usage: `basename $0` argument1 "
	exit 0
fi	
APACHECTL=/usr/local/apache2/bin/apachectl
MYSQLCTL=/etc/init.d/mysql.server
MYSQLD_SAFE=/usr/bin/mysqld_safe
TEST_APACHE_CONF="/usr/local/apache2/conf/httpd.conf-test"
TEST_MYSQL_CONF="/etc/my.cnf-test"

if [ $1 == 'start' ]; then
	# stop working Apache and MySQL server
	$APACHECTL stop
	echo "Stop working Apache server ok!"
	$MYSQLCTL stop
	echo "Stop working MySQL server ok!"
	# start testing Apache adn MySQL server
	$APACHECTL -f $TEST_APACHE_CONF -k start
	echo "Start testing Apache server ok!"
	$MYSQLD_SAFE --defaults-file=$TEST_MYSQL_CONF --user=mysql &
	echo "Start testing MySQL server ok!"
fi

if [ $1 == 'stop' ];then
	# start testing Apache and MySQL server
	$APACHECTL stop
	echo "Stop testing Apache server ok!"
	$MYSQLCTL stop
	 echo "Stop testing MySQL server ok!"
	# start woring Apache and MySQL server
	$APACHECTL start
	echo "Start working Apache server ok!"
	$MYSQLCTL start
	 echo "Start working MySQL server ok!"
fi


