#!/bin/sh
SERVER_LIST="s211 s212 s213 s214 s217"
if [ $# != 1 ];then
	exit 0
fi
for i in $SERVER_LIST
do
	echo '======================='
	grep $i /etc/hosts
	echo '-----------------------'
	scp $1 $i:server-packages/
done
