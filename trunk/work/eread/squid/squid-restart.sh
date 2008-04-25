#!/bin/sh
killall -9 squid
sleep 2
/usr/local/squid3/sbin/squid -D
