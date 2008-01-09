#!/bin/bash
# $Id$
# a script to build development server
#
# It will donwload lastest 
HTTPD_VERSION=2.2.6
PHP_VERSION=5.2.5
MYSQL_VERSION=5.0.51
PYTHON_VERSION=2.5.1
RUBY_VERSION=1.8.6-p111
DOWNLOAD_DIR=$HOME/download/eread
SRC_DIR=$HOME/src

INSTALL_DIR=/opt/eread

# functions follow
#
download () {
	if [ -z "$1" ]
	then
		echo "download function need a URL parameter"
		exit 0
	else
		if [ ! -d $DOWNLOAD_DIR ];then
			echo "$DOWNLOAD_DIR not here, create first."
			mkdir -p $DOWNLOAD_DIR
		fi
		wget $1 -cP $DOWNLOAD_DIR
	fi

	return 0
}



# download file URL
HTTPD_URL="http://apache.mirror.phpchina.com/httpd/httpd-${HTTPD_VERSION}.tar.bz2"
PHP_URL="http://cn2.php.net/distributions/php-${PHP_VERSION}.tar.bz2"
MYSQL_URL="http://ftp.iij.ad.jp/pub/db/mysql/Downloads/MySQL-5.0/mysql-${MYSQL_VERSION}.tar.gz"
PYTHON_URL="http://www.python.org/ftp/python/2.5.1/Python-${PYTHON_VERSION}.tar.bz2"
RUBY_URL="ftp://ftp.ruby-lang.org/pub/ruby/1.8/ruby-${RUBY_VERSION}.tar.gz"

# DOWNLOAD files
download $HTTPD_URL
download $PHP_URL
download $MYSQL_URL

# complie files
cd $SRC_DIR
echo "now src dir is $PWD"
#tar jxf $DOWNLOAD_DIR/httpd-${HTTPD_VERSION}.tar.bz2

tar zxf $DOWNLOAD_DIR/mysql-${MYSQL_VERSION}.tar.gz

#tar jxf $DOWNLOAD_DIR/php-${PHP_VERSION}.tar.bz2
