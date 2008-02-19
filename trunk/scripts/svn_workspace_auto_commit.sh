#!/bin/sh
# $Id$
# this is a automatic SVN commit shell script,
# it can fix error svn version file in workspace
# write by zhuzhu@perlchina.org (zhuzhu@isoshu.net)
# utf-8 charset
# 

SVN='/usr/local/bin/svn'
TMP_DIR='/tmp/svn_auto_commit_tmp'
SVN_SERVER='http://192.168.1.249/svn'
COPY='/bin/cp'

if [ -z $1 ] || [ ! -d $1 ];then
	echo "error, set a svn workspace directory like this: ";
	echo "`basename $0` [directory]";
	exit 0;
fi

if [ -d ${TMP_DIR}/$1 ];then
	echo "${TMP_DIR}/$1 have created, delete it first ...";
	`rm -rf $TMP_DIR/$1`;
	echo "directory removed ok.";
fi

# checkout old svn from svn server
$SVN co ${SVN_SERVER}/$1/trunk ${TMP_DIR}/$1
# delete old version files
if [ `ls ${TMP_DIR}/$1 | wc -l` -ne '0' ];then
	$SVN del ${TMP_DIR}/$1/*
	# commit the version
	$SVN ci ${TMP_DIR}/$1 -m "$1 delete version `date +%F`"
fi

# copy new files to tmp directory
mkdir -p ${TMP_DIR}/${1}-tmp
$COPY -rf $1/* ${TMP_DIR}/$1-tmp
# delete .svn directorys
find ${TMP_DIR}/$1-tmp -name ".svn" | xargs rm -rf
$COPY -rf ${TMP_DIR}/$1-tmp/* ${TMP_DIR}/$1/

# add new files to svn
$SVN add ${TMP_DIR}/$1/*
$SVN ci ${TMP_DIR}/$1/ -m "new fix $1 version, `date +%F`"

# delete the tmp dir
# rm -rf ${TMP_DIR}/$1

echo "all done. new fixed version build."