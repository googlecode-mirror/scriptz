If you want to connect to an Oracle database with PHP, you can use Oracle's Instant Client and the oci8 module from pear.

Download the Basic and the SDK packages from http://www.oracle.com/technology/tec...antclient.html. At the time of this writing, the filenames are instantclient-basic-linux32-10.2.0.1-20050713.zip and instantclient-sdk-linux32-10.2.0.1-20050713.zip.

Unzip these files in a new directory, e.g. /opt/oracle/instantclient.
```
mkdir -p /opt/oracle/instantclient
cd /opt/oracle/instantclient
unzip instantclient-basic-linux32-10.2.0.1-20050713.zip
unzip instantclient-sdk-linux32-10.2.0.1-20050713.zip
echo /opt/oracle/instantclient >> /etc/ld.so.conf
ldconfig
```
The previous two lines are supposed to create symlinks named libclntsh.so and libocci.so which we will need later. In my case these symlinks were not created by ldconfig, so I created them manually.
```
ln -s libclntsh.so.10.1 libclntsh.so
ln -s libocci.so.10.1 libocci.so
```
In the next step we will download the oci8 module with pear. Pear is in the php-pear package.
```
apt-get install php-pear
```
"Normally" we should be able to just use pear install oci8 now, but apparently pear is not able to figure out where the instantclient libraries are. So we will just download the oci8 module and build it on our own. or download oci8 [here](http://pecl.php.net/package/oci8).
```
mkdir -p /usr/local/src
cd /usr/local/src
pear download oci8
tar xzf oci8-1.1.1.tgz
cd oci8-1.1.1
phpize
./configure --with-oci8=shared,instantclient,/opt/oracle/instantclient
make
make install
```
The oci8-1.1.1.tgz filename will of course change for newer releases.
To enable the oci8 module in the php.ini (/etc/php5/apache2/php.ini and /etc/php5/cli/php.ini), add a line
```
extension=oci8.so
```
(put this line after the examples starting with ;extension).

Now stop and start Apache. You should see the oci8 module in the output of phpinfo().