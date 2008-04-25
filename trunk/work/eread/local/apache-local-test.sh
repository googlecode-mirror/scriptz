#/bin/sh
# $Id$
#
SERVER_LIST="s211 s212 s213 s214 s217"
for i in $SERVER_LIST
do
	$i "cd server-packages; cd httpd-2.2.8; make clean; ./configure --prefix=/opt/eread/apache2-event --enable-so --enable-mods-shared=most --with-mpm=prefork;make; sudo make install; "
done
